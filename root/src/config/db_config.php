<?php
define('DB_HOST', 'localhost');                 // Host name
define('DB_USER', 'root');                      // MySQL username
define('DB_PASS', '');                          // MySQL password
define('DB_NAME', 'comp-4150-group-project');   // MySQL database name

try {
    // Connect to the database using PDO
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Enable error reporting
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Fetch data as associative arrays
        PDO::ATTR_EMULATE_PREPARES => false, // Use real prepared statements
    ]);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>