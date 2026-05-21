<?php
// db.php - Database connection using PDO

$host = 'localhost';
$dbname = 'ceylonlands_db';
$username = 'root'; // Update this with your live database username
$password = ''; // Update this with your live database password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    
    // Set PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Set default fetch mode to associative array
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
} catch(PDOException $e) {
    // If the database doesn't exist yet, we'll try to connect without it first to create it
    if ($e->getCode() == 1049) { // 1049 is "Unknown database"
        try {
            $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname`");
            $pdo->exec("USE `$dbname`");
        } catch(PDOException $e2) {
            die("Database Connection failed: " . $e2->getMessage());
        }
    } else {
        die("Database Connection failed: " . $e->getMessage());
    }
}
?>
