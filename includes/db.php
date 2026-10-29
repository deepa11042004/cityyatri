<?php
/**
 * Club Yatri - Database connection (XAMPP MariaDB defaults)
 */

declare(strict_types=1);

function db(): ?PDO
{
    static $pdo = false;
    if ($pdo === false) {
        try {
            // Production: copy includes/db-config.sample.php to includes/db-config.php and fill it in
            $cfg = is_file(__DIR__ . '/db-config.php') ? require __DIR__ . '/db-config.php' : [];
            $host = $cfg['host'] ?? getenv('DB_HOST') ?: '127.0.0.1';
            $port = $cfg['port'] ?? getenv('DB_PORT') ?: '3307';
            $name = $cfg['name'] ?? getenv('DB_NAME') ?: 'club_yatri';
            $pdo = new PDO(
                "mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4",
                $cfg['user'] ?? getenv('DB_USER') ?: 'root',
                $cfg['pass'] ?? getenv('DB_PASS') ?: '',
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
            );
        } catch (PDOException $e) {
            error_log('DB connection failed: ' . $e->getMessage());
            $pdo = null;
        }
    }
    return $pdo;
}

function db_insert(string $sql, array $params): void
{
    try {
        if ($pdo = db()) {
            $pdo->prepare($sql)->execute($params);
        }
    } catch (PDOException $e) {
        error_log('DB insert failed: ' . $e->getMessage());
    }
}
