<?php
require 'db_config.php';
require 'constants.php';

// Create MySQL connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "<h3>ClubHub Database Setup</h3>";

$conn->query("DROP DATABASE IF EXISTS `" . DB_NAME . "`");
echo "Dropped existing database.<br>";

// ---------------------------
// Create database
// ---------------------------
$sql = "CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "`";
if ($conn->query($sql) === TRUE) {
    echo "Database `" . DB_NAME . "` ready.<br>";
} else {
    die("Error creating database: " . $conn->error);
}

// Select the database
$conn->select_db(DB_NAME);


// ---------------------------
// Helper: Run SQL file
// ---------------------------
function runSqlFile(mysqli $conn, string $filePath)
{
    if (!file_exists($filePath)) {
        die("SQL file not found: $filePath");
    }

    echo "<br><b>Running:</b> $filePath<br>";

    $sql = file_get_contents($filePath);

    // multi_query handles procedures, triggers, delimiters
    if ($conn->multi_query($sql)) {

        // Flush all result sets
        do {
            if ($result = $conn->store_result()) {
                $result->free();
            }
        } while ($conn->more_results() && $conn->next_result());

        echo "Successfully executed.<br>";
    } else {
        echo "Error executing file: " . $conn->error . "<br>";
    }
}


// ---------------------------
// Run create_tables.sql
// ---------------------------
runSqlFile($conn, __DIR__ . '/../scripts/sql/initialize/create_tables.sql');

// ---------------------------
// Run populate_tables.sql
// ---------------------------
runSqlFile($conn, __DIR__ . '/../scripts/sql/initialize/populate_tables.sql');

// ---------------------------
// Run stored procedures
// ---------------------------
runSqlFile($conn, __DIR__ . '/../scripts/sql/stored_procedures/all_sp.sql');

// ---------------------------
// Run triggers
// ---------------------------
runSqlFile($conn, __DIR__ . '/../scripts/sql/triggers/all_triggers.sql');

echo "<br><h3>Database setup complete!</h3>
<br>
<p>Continue: <a href='" . PUBLIC_URL . "index.php'>Go to Home</a></p>";


$conn->close();
?>
