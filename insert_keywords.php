<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Connect to SQLite database
try {
    $db = new PDO('sqlite:phishing_detector.sqlite3');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Function to insert a keyword into the database
function insertKeyword($db, $keyword) {
    $sql = "INSERT INTO phishing_keywords (keyword) VALUES (:keyword)";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':keyword', $keyword);
    if ($stmt->execute()) {
        echo "Keyword '$keyword' inserted successfully.<br>";
    } else {
        echo "Error inserting keyword '$keyword'.<br>";
    }
}

// List of keywords to insert
$keywords = [
    'urgent',
    'account suspended',
    'verify your identity',
    'prize won'
];

// Insert each keyword into the database
foreach ($keywords as $keyword) {
    insertKeyword($db, $keyword);
}
?>
