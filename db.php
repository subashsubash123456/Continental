<?php

$host = 'localhost'; // Your database host
$db   = 'continental_school'; // Your database name
$user = 'root'; // Your database username
$pass = 'mysql'; // Your database password 
$charset = 'utf8mb4'; // Character set

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Throw exceptions on errors
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Set default fetch mode to associative array
    PDO::ATTR_EMULATE_PREPARES   => false, // Disable emulated prepared statements
];

try { 
    // Create a new PDO instance
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    // Handle connection errors
    echo "Connection failed: " . $e->getMessage(); // Output error message
}
?>
