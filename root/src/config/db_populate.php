<?php
require 'db_config.php';

// Path to SQL script containing sample data insertion commands
$sqlFile = __DIR__ . '\..\scripts\sql\initialize\populate_tables.sql';

if (!file_exists($sqlFile)) {
    die("SQL file not found: $sqlFile");
}

// Read the SQL commands from the file
$sqlCommands = file_get_contents($sqlFile);

if ($sqlCommands === false) {
    die("Failed to read SQL file: $sqlFile");
}

// Connect to MySQL database
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Execute SQL commands
if ($conn->multi_query($sqlCommands)) {
    do {
        // Store first result set
        if ($result = $conn->store_result()) {
            $result->free();
        }
        // Prepare for next result set
    } while ($conn->more_results() && $conn->next_result());

    echo "Sample data populated successfully!";
} else {
    echo "Error populating tables: " . $conn->error;
}

// Close connection
$conn->close();
?>