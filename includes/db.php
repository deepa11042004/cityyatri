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
            $pdo = new PDO(
                'mysql:host=127.0.0.1;port=' . (getenv('DB_PORT') ?: '3307') . ';dbname=club_yatri;charset=utf8mb4',
                getenv('DB_USER') ?: 'root',
                getenv('DB_PASS') ?: '',
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
