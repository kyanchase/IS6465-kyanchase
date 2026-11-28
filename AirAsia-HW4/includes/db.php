<?php

$dbPath = __DIR__ . '/../database/rewards.db';

try {
    $pdo = new PDO('sqlite:' . $dbPath, null, null, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    $pdo->exec('PRAGMA foreign_keys = ON');
} catch (PDOException $e) {
    error_log($e->getMessage());
    http_response_code(500);
    exit('The database is temporarily unavailable.');
}
