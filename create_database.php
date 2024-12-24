<?php
$dsn = "sqlite:/path/to/phishing_detector.sqlite3";
try {
    $pdo = new PDO($dsn);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $sql = "
        CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT NOT NULL UNIQUE,
            email TEXT NOT NULL UNIQUE,
            password TEXT NOT NULL
        );
    ";
    $pdo->exec($sql);
    echo "Database and table created successfully.";
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>
