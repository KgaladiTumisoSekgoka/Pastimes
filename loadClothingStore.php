<?php
// Include the database connection
include 'DBConn.php'; // Ensure the DB connection is properly established

$sqlFile = 'myClothingStore.sql';

// Enable error reporting for mysqli to show detailed error messages
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    // Check if the SQL file exists
    if (file_exists($sqlFile)) {
        // Read the SQL file contents
        $sqlCommands = file_get_contents($sqlFile);

        // Separate commands by the semicolon delimiter
        $sqlStatements = explode(';', $sqlCommands);

        // Disable foreign key checks to handle drops and recreations
        $conn->query('SET FOREIGN_KEY_CHECKS = 0;');

        // Begin SQL processing
        foreach ($sqlStatements as $sql) {
            $sql = trim($sql);
            if (!empty($sql)) {
                // Execute the SQL statement
                $conn->query($sql);
                echo 'Executed: ' . htmlspecialchars($sql) . '<br>';
            }
        }

        // Re-enable foreign key checks
        $conn->query('SET FOREIGN_KEY_CHECKS = 1;');
    } else {
        echo 'SQL file not found: ' . $sqlFile . '<br>';
    }
} catch (Exception $e) {
    // Display the error
    echo 'Error: ' . $e->getMessage() . '<br>';
}

// Close the database connection
$conn->close();
