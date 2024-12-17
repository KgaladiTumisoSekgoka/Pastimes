<?php
session_start();
require_once "DBConn.php";

// Check if the user is logged in
if (!isset($_SESSION['user_name'])) {
    echo '<p>You must be logged in to send a message. <a href="user_login.php">Login here</a>.</p>';
    exit;
}

// Initialize user variables
$username = $_SESSION['user_name'];

// Handle form submission
// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $messageText = trim($_POST['message']);
    $sellerId = isset($_POST['sellerId']) ? (int)$_POST['sellerId'] : 0;

    // Debugging output
    var_dump($sellerId, $messageText);

    // Validate the message and seller ID
    if (!empty($messageText) && $sellerId > 0) {
        $stmt = $conn->prepare("INSERT INTO tblMessages (SenderID, ReceiverID, MessageText, Timestamp) 
                                VALUES ((SELECT UserID FROM tblUser WHERE Username = ?), ?, ?, NOW())");
        $stmt->bind_param("sis", $username, $sellerId, $messageText);

        // Execute the statement and check for success
        if ($stmt->execute()) {
            header("Location: user_messaging.php");
            exit;
        } else {
            echo '<p>Error sending message: ' . htmlspecialchars($conn->error) . '</p>';
        }

        $stmt->close();
    } else {
        echo '<p>Please enter a message and select a valid seller.</p>';
    }
}

$conn->close();
