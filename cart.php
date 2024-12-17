<?php
session_start();

// Database connection (assuming you have a db_connection.php file)
include 'DBConn.php';

// Check if the cart is empty
$cartEmpty = !isset($_SESSION['cart']) || count($_SESSION['cart']) == 0;

// Initialize total price
$totalPrice = 0;

// If cart is not empty, retrieve cart items and calculate total price
if (!$cartEmpty) {
    $cartItems = $_SESSION['cart']; // Get cart items from session

    // Calculate total price and ensure each item price reflects quantity
    foreach ($cartItems as $item) {
        $totalPrice += $item['price'] * $item['quantity']; // Multiply price by quantity for total
    }

    // Prepare and insert order into the database
    $userId = $_SESSION['id'];
    $username = $_SESSION['user_name'];
    $orderDate = date("Y-m-d H:i:s");

    // Insert into tblOrder for each item
    foreach ($cartItems as $item) {
        $clothingName = $item['name'];
        $quantity = $item['quantity'];
        $price = $item['price'];

        // Insert each item into tblOrder
        $query = "INSERT INTO tblOrder (UserID, Username, ClothingName, Quantity, Price, TotalPrice, OrderDate) 
                  VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("issidss", $userId, $username, $clothingName, $quantity, $price, $totalPrice, $orderDate);

        if ($stmt->execute()) {
            //echo "Order item for $clothingName has been placed successfully!<br>";
        } else {
            echo "Error placing order for $clothingName: " . $stmt->error;
        }
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description"
        content="Discover unique and stylish second-hand clothing at Pastimes. Shop sustainable fashion that revives your style and relives the past, with a variety of curated pieces for every occasion.">
    <meta name="keywords"
        content="Pastimes, second-hand clothing, sustainable fashion, vintage clothes, thrift shop, eco-friendly apparel, affordable fashion, unique styles, clothing resale, preloved clothing, fashion for all">
    <meta name="robots" content="index, follow">
    <meta name="author" content="Pastimes">
    <meta name="revisit-after" content="30 days">
    <link rel="stylesheet" href="css_assets/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Cart | Pastimes</title>
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
        <section class="heading_section">
            <h1>Your Cart</h1>
            <?php if (isset($_SESSION['user_name'])) { ?>
                <p>Logged in as: <strong><?php echo htmlspecialchars($_SESSION['user_name']); ?></strong></p>
            <?php } ?>
        </section>
        <section class="cart_container">
            <?php if ($cartEmpty) { ?>
                <p>Your cart is empty!</p>
                <a href="catalog.php" class="btn">Go back to Catalog</a>
            <?php } else { ?>
                <div class="cart_left">
                    <table>
                        <thead>
                            <tr>
                                <th>Item Name</th>
                                <th>Price (R)</th>
                                <th>Quantity</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cartItems as $item) { ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($item['name']); ?></td>
                                    <td>R <?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                                    <td><?php echo $item['quantity']; ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
                <div class="cart_right">
                    <h3>Total: R <?php echo number_format($totalPrice, 2); ?></h3>
                    <a href="checkout.php" class="btn">Proceed to Checkout</a>
                </div>
            <?php } ?>
        </section>
    </main>
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="footer-col">
                    <h4>Quick Links</h4>
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
                    <h4>Contact Us</h4>
                    <ul>
                        <li>Email: <a href="mailto:pastimes@gmail.com">pastimes@gmail.com</a></li>
                        <li>Phone: <a href="tel:+27 65 937 7912">+27 65 937 7912</a></li>
                        <li>Address: 7 Wolf Street, Ferdale, Fourways, South Africa</li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Follow Us</h4>
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