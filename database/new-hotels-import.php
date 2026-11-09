<?php
/**
 * Club Yatri - load database/stuba-hotel-directory.csv into the new_hotels table.
 *
 *   php database/new-hotels-import.php          (local / SSH)
 *   or called from deploy-migrate.php on every deploy (skips when the CSV has not changed)
 *
 * The import replaces the table contents inside one transaction, so a failure leaves the old data intact.
 */

declare(strict_types=1);

const NEW_HOTELS_CSV = __DIR__ . '/stuba-hotel-directory.csv';

/** @return int number of rows imported */
function new_hotels_import(PDO $pdo, string $csv = NEW_HOTELS_CSV): int
{
    $fh = fopen($csv, 'rb');
    if (!$fh) {
        throw new RuntimeException('CSV not found: ' . $csv);
    }
    // Skip UTF-8 BOM, then the header row
    if (fread($fh, 3) !== "\xEF\xBB\xBF") {
        rewind($fh);
    }
    $header = fgetcsv($fh, 0, ',', '"', '');
    if (!$header || array_slice($header, 0, 3) !== ['country', 'city', 'hotel']) {
        throw new RuntimeException('Unexpected CSV header.');
    }

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->beginTransaction();
    try {
        $pdo->exec('DELETE FROM new_hotels');
        $total = 0;
        $batch = [];
        $flush = static function () use ($pdo, &$batch): void {
            if (!$batch) {
                return;
            }
            $sql = 'INSERT INTO new_hotels (country, city, hotel_name) VALUES ' . implode(',', array_fill(0, intdiv(count($batch), 3), '(?,?,?)'));
            $pdo->prepare($sql)->execute($batch);
            $batch = [];
        };
        while (($r = fgetcsv($fh, 0, ',', '"', '')) !== false) {
            if (count($r) < 3 || trim($r[2]) === '') {
                continue;
            }
            array_push($batch, mb_substr(trim($r[0]), 0, 100), mb_substr(trim($r[1]), 0, 150), mb_substr(trim($r[2]), 0, 255));
            $total++;
            if ($total % 1000 === 0) {
                $flush();
            }
        }
        $flush();
        $pdo->commit();
    } catch (Throwable $e) {
        $pdo->rollBack();
        throw $e;
    } finally {
        fclose($fh);
    }
    return $total;
}

if (PHP_SAPI === 'cli' && isset($_SERVER['SCRIPT_FILENAME']) && realpath($_SERVER['SCRIPT_FILENAME']) === realpath(__FILE__)) {
    require_once __DIR__ . '/../includes/db.php';
    $pdo = db();
    if (!$pdo) {
        fwrite(STDERR, "Database connection failed\n");
        exit(1);
    }
    $pdo->exec((string)file_get_contents(__DIR__ . '/migrations/005_new_hotels.sql'));
    echo 'Imported ' . number_format(new_hotels_import($pdo)) . " hotels into new_hotels\n";
}
