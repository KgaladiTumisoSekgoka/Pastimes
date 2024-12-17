<?php
session_start();
require_once "DBConn.php";

// Check if the user is logged in
if (!isset($_SESSION['user_name'])) {
    echo '<p>You must be logged in to view your profile. <a href="user_login.php">Login here</a>.</p>';
    exit;
}

// Get the user's username from the session
$username = $_SESSION['user_name'];

// Fetch user information from the tblUser table
$stmt = $conn->prepare("SELECT UserID, Name, Surname, Email, Address FROM tblUser WHERE Username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

$user = null; // Initialize user variable
if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    echo '<p>Error retrieving user information.</p>';
}

$stmt->close();

// Fetch user order history from the tblOrder table based on UserID
$orderStmt = $conn->prepare("
    SELECT OrderID, ClothingName, Quantity, Price, TotalPrice, OrderDate 
    FROM tblOrder 
    WHERE UserID = ?
");
$orderStmt->bind_param("i", $user['UserID']); // Bind UserID for security
$orderStmt->execute();
$ordersResult = $orderStmt->get_result();

// Initialize total amount spent
$totalSpent = 0;

// Function to add item to cart and reduce stock
function addToCart($clothingID, $quantity)
{
    global $conn;

    $stmt = $conn->prepare("SELECT Quantity FROM tblClothes WHERE ClothingID = ?");
    $stmt->bind_param("i", $clothingID);
    $stmt->execute();
    $result = $stmt->get_result();
    $item = $result->fetch_assoc();
    $stmt->close();

    if ($item && $item['Quantity'] >= $quantity) {
        // Proceed with reducing the stock
        $newQuantity = $item['Quantity'] - $quantity;

        // Update the quantity in the tblClothes table
        $updateStmt = $conn->prepare("UPDATE tblClothes SET Quantity = ? WHERE ClothingID = ?");
        $updateStmt->bind_param("ii", $newQuantity, $clothingID);
        $updateStmt->execute();
        $updateStmt->close();

        echo "<p>Added $quantity of item ID $clothingID to cart. Stock updated.</p>";
    } else {
        echo "<p>Sorry, there is not enough stock for this item.</p>";
    }
}

// Example usage: Add 1 quantity of item with ID 1 to the cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['clothingID'], $_POST['quantity'])) {
    $clothingID = intval($_POST['clothingID']);
    $quantity = intval($_POST['quantity']);
    addToCart($clothingID, $quantity);
}
$orderStmt->close();
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
    <title>User Profile | Pastimes - Revive Your Style, Relive The Past</title>
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
            <h1>User Profile</h1>
            <br>
            <?php if ($user): ?>
                <h2>Profile Information</h2>
                <p><strong>User ID:</strong> <?php echo htmlspecialchars($user['UserID']); ?></p> <!-- Display UserID -->
                <p><strong>Name:</strong> <?php echo htmlspecialchars($user['Name']); ?></p>
                <p><strong>Surname:</strong> <?php echo htmlspecialchars($user['Surname']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($user['Email']); ?></p>
                <p><strong>Address:</strong> <?php echo htmlspecialchars($user['Address']); ?></p>
            <?php else: ?>
                <p>No user information found.</p>
            <?php endif; ?>
            <br>
            <br>
            <h2>Order History</h2>
            <?php if ($ordersResult && $ordersResult->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Clothing Name</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>Total Price</th>
                            <th>Order Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($order = $ordersResult->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($order['OrderID']); ?></td>
                                <td><?php echo htmlspecialchars($order['ClothingName']); ?></td>
                                <td><?php echo htmlspecialchars($order['Quantity']); ?></td>
                                <td><?php echo htmlspecialchars(number_format($order['Price'], 2)); ?></td>
                                <td><?php echo htmlspecialchars(number_format($order['TotalPrice'], 2)); ?></td>
                                <td><?php echo htmlspecialchars($order['OrderDate']); ?></td>
                            </tr>
                        <?php
                            // Accumulate total spent
                            $totalSpent += $order['TotalPrice'];
                        endwhile; ?>
                    </tbody>
                </table>
                <h3>Total Spent: R<?php echo htmlspecialchars(number_format($totalSpent, 2)); ?></h3>
            <?php else: ?>
                <p>No order history found.</p>
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