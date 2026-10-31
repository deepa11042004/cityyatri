<?php
/**
 * Club Yatri - Deploy-time database migrations.
 *
 * Called by the GitHub deploy workflow after the files are uploaded:
 *   POST /deploy-migrate.php   with header  X-Deploy-Token: <secret>
 *
 * Applies every database/migrations/*.sql file that has not been applied yet (or whose
 * contents changed). Migration files must therefore be idempotent (CREATE TABLE IF NOT EXISTS,
 * INSERT ... ON DUPLICATE KEY UPDATE, ...), end each statement with ';' at the end of a line,
 * and not contain strings that span several lines.
 *
 * The endpoint is disabled unless a deploy_token (24+ characters) is set in
 * includes/db-config.php or the DEPLOY_TOKEN environment variable.
 */

declare(strict_types=1);

require_once __DIR__ . '/includes/db.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

function migrate_respond(int $status, array $body): never
{
    http_response_code($status);
    echo json_encode($body, JSON_UNESCAPED_SLASHES);
    exit;
}

$cfg = is_file(__DIR__ . '/includes/db-config.php') ? (require __DIR__ . '/includes/db-config.php') : [];
$expected = (string)($cfg['deploy_token'] ?? getenv('DEPLOY_TOKEN') ?: '');

if (strlen($expected) < 24) {
    migrate_respond(404, ['ok' => false, 'error' => 'Not found']);
}
if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    migrate_respond(405, ['ok' => false, 'error' => 'POST required']);
}
if (!hash_equals($expected, (string)($_SERVER['HTTP_X_DEPLOY_TOKEN'] ?? ''))) {
    usleep(750000); // slow down guessing
    migrate_respond(403, ['ok' => false, 'error' => 'Forbidden']);
}

$pdo = db();
if ($pdo === null) {
    migrate_respond(500, ['ok' => false, 'error' => 'Database unavailable']);
}

/** Split a migration file into statements (a statement ends with ';' at the end of a line). */
function migrate_statements(string $sql): array
{
    $statements = [];
    $buffer = '';
    foreach (preg_split('/\r?\n/', $sql) as $line) {
        if ($buffer === '' && (trim($line) === '' || str_starts_with(ltrim($line), '--'))) {
            continue;
        }
        $buffer .= $line . "\n";
        if (str_ends_with(rtrim($line), ';')) {
            $statements[] = $buffer;
            $buffer = '';
        }
    }
    if (trim($buffer) !== '') {
        $statements[] = $buffer;
    }
    return $statements;
}

$applied = [];
$skipped = [];

try {
    $pdo->exec(
        'CREATE TABLE IF NOT EXISTS `schema_migrations` (
            `filename` VARCHAR(190) NOT NULL PRIMARY KEY,
            `checksum` CHAR(64) NOT NULL,
            `applied_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
    );
    $done = $pdo->query('SELECT filename, checksum FROM schema_migrations')->fetchAll(PDO::FETCH_KEY_PAIR);

    $files = glob(__DIR__ . '/database/migrations/*.sql') ?: [];
    sort($files, SORT_STRING);

    foreach ($files as $file) {
        $name = basename($file);
        $sum = hash_file('sha256', $file);
        if (($done[$name] ?? null) === $sum) {
            $skipped[] = $name;
            continue;
        }

        $count = 0;
        foreach (migrate_statements((string)file_get_contents($file)) as $statement) {
            $pdo->exec($statement);
            $count++;
        }
        $pdo->prepare(
            'INSERT INTO schema_migrations (filename, checksum) VALUES (?, ?)
             ON DUPLICATE KEY UPDATE checksum = VALUES(checksum), applied_at = CURRENT_TIMESTAMP'
        )->execute([$name, $sum]);
        $applied[] = ['file' => $name, 'statements' => $count];
    }
} catch (PDOException $e) {
    error_log('Migration failed: ' . $e->getMessage());
    migrate_respond(500, [
        'ok' => false,
        'error' => 'Migration failed',
        'file' => $name ?? null,
        'detail' => substr($e->getMessage(), 0, 300),
        'applied' => $applied,
    ]);
}

migrate_respond(200, ['ok' => true, 'applied' => $applied, 'skipped' => $skipped]);
