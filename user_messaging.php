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

// Fetch the UserID of the logged-in user
$userQuery = $conn->prepare("SELECT UserID FROM tblUser WHERE Username = ?");
$userQuery->bind_param("s", $username);
$userQuery->execute();
$userResult = $userQuery->get_result();
$userRow = $userResult->fetch_assoc();
$loggedInUserID = $userRow['UserID']; 
$userQuery->close();

// Fetch sellers from the database
$sellers = []; // Initialize the sellers array
$sellerQuery = $conn->query("SELECT SellerID, Username FROM tblSeller");
while ($row = $sellerQuery->fetch_assoc()) {
    $sellers[] = $row; // Populate the sellers array
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the message and seller ID from the POST request
    $messageText = trim($_POST['message']);
    $sellerId = isset($_POST['sellerId']) ? (int)$_POST['sellerId'] : 0;

    // Validate the message and seller ID
    if (!empty($messageText) && $sellerId > 0) {
        // Prepare and execute the SQL statement to insert the message
        $stmt = $conn->prepare("INSERT INTO tblMessages (SenderID, ReceiverID, MessageText, Timestamp) 
                                VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("iis", $loggedInUserID, $sellerId, $messageText);

        // Execute the statement and check for success
        if ($stmt->execute()) {
            // Redirect back to the messages page with success message
            header("Location: user_messaging.php");
            exit;
        } else {
            echo '<p>Error sending message: ' . htmlspecialchars($conn->error) . '</p>';
        }

        $stmt->close(); // Close the statement
    } else {
        echo '<p>Please enter a message and select a valid seller.</p>';
    }
}

// Fetch the most recent message between the logged-in user and the selected seller
if (isset($_GET['sellerId'])) {
    $selectedSellerId = (int)$_GET['sellerId'];

    if ($selectedSellerId > 0) {
        $messageHistoryQuery = $conn->prepare("SELECT m.MessageText, m.Timestamp, u.Username AS SenderUsername
                                               FROM tblMessages m
                                               JOIN tblUser u ON u.UserID = m.SenderID
                                               WHERE (m.SenderID = ? AND m.ReceiverID = ?)
                                                  OR (m.SenderID = ? AND m.ReceiverID = ?)
                                               ORDER BY m.Timestamp DESC
                                               LIMIT 1");  // Limit to 1 to get the most recent message only
        $messageHistoryQuery->bind_param("iiii", $loggedInUserID, $selectedSellerId, $selectedSellerId, $loggedInUserID);
        $messageHistoryQuery->execute();
        $messageHistoryResult = $messageHistoryQuery->get_result();

        if ($messageHistoryResult->num_rows > 0) {
            $messageRow = $messageHistoryResult->fetch_assoc();  // Fetch the single most recent message
        } else {
            echo '<p>No recent messages found for this seller.</p>';
        }

        $messageHistoryQuery->close();
    }
}

$conn->close(); // Close the database connection
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Pastimes: Revive your style and relive the past with our collection of pre-loved clothing. Find unique, affordable, and stylish pieces.">
    <meta name="keywords" content="pre-loved clothing, affordable fashion, sustainable fashion, second-hand clothes, men's clothing, women's clothing, kids clothing, accessories">
    <meta name="robots" content="index, follow">
    <meta name="author" content="Pastimes">
    <link rel="stylesheet" href="css_assets/message_styles.css">
    <link rel="stylesheet" href="css_assets/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>User Messages | Revive Your Style, Relive The Past</title>
</head>

<body>
    <header>
        <div class="navbar">
            <div class="logo">
                <a href="index.php"><img src="_images/logo.png" alt="Logo" style="height: 30px;"></a>
            </div>
            <div class="search-bar">
                <form method="GET" action="search_result.php">
                    <input type="text" name="query" placeholder="Search">
                    <button type="submit"><i class="fa fa-search" aria-hidden="true"></i></button>
                </form>
            </div>
            <nav>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="catalog.php">Catalog</a></li>
                    <li><a href="about_us.php">About Us</a></li>
                    <li><a href="contact_us.php">Contact Us</a></li>
                    <li><a href="login_register_selection.php">Login/Register</a></li>
                    <li><a href="user_messaging.php" target="_blank"><i class="fa fa-comments" aria-hidden="true"></i></a></li>
                    <li><a href="user_profile.php" target="_blank"><i class="fa fa-user" aria-hidden="true"></i></a></li>
                </ul>
            </nav>
        </div>
    </header>
    <main>
        <div class="message_container">
            <h1>Contact Seller</h1>
            <!-- Message Form -->
            <form action="" method="POST" class="message-form">
                <div class="form-group">
                    <label for="seller">Select Seller:</label>
                    <select id="seller" name="sellerId" required>
                        <option value="">Select a seller</option>
                        <?php foreach ($sellers as $seller): ?>
                            <option value="<?php echo htmlspecialchars($seller['SellerID']); ?>"
                                <?php echo isset($selectedSellerId) && $selectedSellerId == $seller['SellerID'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($seller['Username']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="message">Your Message:</label>
                    <textarea id="message" name="message" rows="5" required></textarea>
                </div>
                <button type="submit" class="send-button">Send Message</button>
            </form>
            <!-- Display Most Recent Message -->
            <?php if (isset($messageRow)): ?>
                <h2>Most Recent Message</h2>
                <table class="message-table">
                    <tr>
                        <th>Sender</th>
                        <th>Message</th>
                        <th>Timestamp</th>
                    </tr>
                    <tr>
                        <td><?php echo htmlspecialchars($messageRow['SenderUsername']); ?></td>
                        <td><?php echo nl2br(htmlspecialchars($messageRow['MessageText'])); ?></td>
                        <td><?php echo htmlspecialchars($messageRow['Timestamp']); ?></td>
                    </tr>
                </table>
            <?php else: ?>
                <?php if (isset($_SESSION['user_name'])): ?>
                    <!-- Display Most Recent Message -->
                    <?php if (isset($messageRow)): ?>
                        <h2>Most Recent Message</h2>
                        <table class="message-table">
                            <tr>
                                <th>Sender</th>
                                <th>Message</th>
                                <th>Timestamp</th>
                            </tr>
                            <tr>
                                <td><?php echo htmlspecialchars($messageRow['SenderUsername']); ?></td>
                                <td><?php echo nl2br(htmlspecialchars($messageRow['MessageText'])); ?></td>
                                <td><?php echo htmlspecialchars($messageRow['Timestamp']); ?></td>
                            </tr>
                        </table>
                    <?php else: ?>
                        <p>No recent messages found.</p>
                    <?php endif; ?>
                <?php else: ?>
                    <p>You must be logged in to view the chat history. <a href="user_login.php">Login here</a>.</p>
                <?php endif; ?>

            <?php endif; ?>

        </div>
    </main>
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="footer-col">
                    <h4>quick links</h4>
                    <ul>
                        <li><a href="index.php">Home</a></li>
                        <li><a href="catalog.php">Catalog</a></li>
                        <li><a href="about_us.php">About Us</a></li>
                        <li><a href="contact_us.php">Contact Us</a></li>
                        <li><a href="login_register_selection.php">Login/Register</a></li>
                        <li><a href="logout.php">Logout</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>contact us</h4>
                    <ul>
                        <li>Email: pastimes@gmail.com <a href="mailto:pastimes@gmail.com"></a></li>
                        <li>Phone: +27 65 937 7912 <a href="tel:+27 65 937 7912"></a></li>
                        <li>Address: 7 Wolf Street, Ferdale, Fourways, South Africa</li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>follow us</h4>
                    <div class="social-links">
                        <a href="https://web.facebook.com/BMWSA" target="_blank"><i class="fab fa-facebook-f"></i></a>
                        <a href="https://x.com/BMW_SA" target="_blank"><i class="fab fa-twitter"></i></a>
                        <a href="https://www.instagram.com/bmwsouthafrica/" target="_blank"><i class="fab fa-instagram"></i></a>
                        <a href="https://www.linkedin.com/showcase/bmwsouthafrica/posts/?feedView=all" target="_blank"><i class="fab fa-linkedin"></i></a>
                    </div>
                </div>
                <div class="footer-col">
                    <h4>Working Hours</h4>
                    <ul>
                        <li>Monday - Friday: 9 AM - 6 PM</li>
                        <li>Saturday: 10 AM - 4 PM</li>
                        <li>Sunday: Closed</li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2024 Pastimes | All Rights Reserved.</p>
        </div>
    </footer>
</body>

</html>