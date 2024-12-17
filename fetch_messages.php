<?php
session_start();
require_once "DBConn.php";

if (!isset($_SESSION['user_name']) || !isset($_GET['sellerId'])) {
    exit; 
}

$username = $_SESSION['user_name'];
$sellerId = (int)$_GET['sellerId'];

// Fetch messages between the user and the specified seller
$stmt = $conn->prepare("SELECT SenderID, MessageText, Timestamp FROM tblMessages
                        WHERE (SenderID = (SELECT UserID FROM tblUser WHERE Username = ?) AND ReceiverID = ?)
                        OR (SenderID = ? AND ReceiverID = (SELECT UserID FROM tblUser WHERE Username = ?))
                        ORDER BY Timestamp DESC");
$stmt->bind_param("siis", $username, $sellerId, $sellerId, $username);

if ($stmt->execute()) {
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        echo '<div class="message ' . ($row['SenderID'] == $username ? 'user-message' : 'seller-message') . '">';
        echo '<p>' . htmlspecialchars($row['MessageText']) . '</p>';
        echo '<small>' . htmlspecialchars($row['Timestamp']) . '</small>';
        echo '</div>';
    }
}
$stmt->close();
?>
