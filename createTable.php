<?php
// Include the database connection
include 'DBConn.php';

// Temporarily disable foreign key checks
$conn->query("SET FOREIGN_KEY_CHECKS=0");

// Check if tblUser exists, if so, delete it
$sql = "DROP TABLE IF EXISTS tblUser";
if ($conn->query($sql) === TRUE) {
    echo "Table tblUser dropped successfully.<br>";
} else {
    echo "Error dropping table: " . $conn->error . "<br>";
}

// Re-enable foreign key checks
$conn->query("SET FOREIGN_KEY_CHECKS=1");

// Recreate tblUser table
$sql = "CREATE TABLE tblUser (
    UserID INT AUTO_INCREMENT PRIMARY KEY,
    Username VARCHAR(50) UNIQUE,
    Name VARCHAR(100),
    Surname VARCHAR(100),
    Address VARCHAR(100),
    Email VARCHAR(100) UNIQUE,
    PasswordHash VARCHAR(255),
    isApproved TINYINT(1) DEFAULT 0
)";

if ($conn->query($sql) === TRUE) {
    echo "Table tblUser created successfully.<br>";
} else {
    echo "Error creating table: " . $conn->error . "<br>";
}

// Load data from userData.txt into the table
$dataFile = fopen("C:/xampp/htdocs/WEDE6021_POE Part2_ST10262964_&_ST10302040/pastimes/_resources/userData.txt", "r");
if ($dataFile) {
    while (($line = fgets($dataFile)) !== false) {
        // Assuming userData.txt contains comma-separated values
        $data = explode(",", trim($line)); // Change delimiter if necessary

        // Ensure that data has enough values before assigning
        if (count($data) == 6) { // Adjust to match the number of fields
            $username = $data[0];
            $name = $data[1];
            $surname = $data[2];
            $address = $data[3];
            $email = $data[4];
            $passwordHash = password_hash(trim($data[5]), PASSWORD_BCRYPT); // Hash password

            // Insert data into the tblUser table using prepared statement
            $stmt = $conn->prepare("INSERT INTO tblUser (Username, Name, Surname, Address, Email, PasswordHash) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssss", $username, $name, $surname, $address, $email, $passwordHash);

            if ($stmt->execute()) {
                echo "New record created for $name.<br>";
            } else {
                echo "Error: " . $stmt->error . "<br>";
            }
            $stmt->close();
        } else {
            echo "Information in line is missing: $line<br>";
        }
    }
    fclose($dataFile);
} else {
    echo "Unable to open userData.txt file.<br>";
}

$conn->close();
?>
