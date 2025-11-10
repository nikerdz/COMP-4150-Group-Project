<?php
require 'db_config.php';

// Connect to MySQL server (without selecting a database first)
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Path to SQL script containing CREATE TABLE statements
$sqlFile = __DIR__ . '\..\scripts\sql\initialize\create_tables.sql';

// Create database if not exists
$sql = "CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "`";
if ($conn->query($sql) === TRUE) {
    echo "Database `" . DB_NAME . "` verified or created successfully.<br>";
} else {
    die("Error creating database: " . $conn->error);
}

// Select the database
$conn->select_db(DB_NAME);

if (!file_exists($sqlFile)) {
    die("SQL file not found: $sqlFile");
}

// Read SQL file
$sql = file_get_contents($sqlFile);

// Split commands by semicolon to execute them individually
$commands = explode(";", $sql);

foreach ($commands as $command) {
    $command = trim($command);
    if ($command) {
        if ($conn->query($command) === TRUE) {
            echo "Executed: " . substr($command, 0, 50) . "...<br>";
        } else {
            echo "<b>Error executing:</b> " . $command . "<br>";
            echo "<b>MySQL error:</b> " . $conn->error . "<br><br>";
        }
    }
}

// Close connection
$conn->close();
?>
