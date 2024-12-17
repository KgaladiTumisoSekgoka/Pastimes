<?php
session_start();
include 'DBConn.php';

if (isset($_GET['ClothesID'])) {
    $ClothesID = intval($_GET['ClothesID']);
    // echo "ClothesID received: " . htmlspecialchars($ClothesID) . "<br>";

    $stmt = $conn->prepare("SELECT Name, Price, Quantity, Description, ImagePath, Brand, Sizes, Category FROM tblClothes WHERE ClothesID = ?");
    $stmt->bind_param("i", $ClothesID);

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $product = $result->fetch_assoc();
        } else {
            echo "Product not found for ClothesID: " . htmlspecialchars($ClothesID);
            exit();
        }
    } else {
        echo "Error executing query: " . $stmt->error;
        exit();
    }
} else {
    echo "No product selected.";
    exit();
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $itemName = $_POST['itemName'];
    $itemPrice = floatval($_POST['itemPrice']);
    $quantity = intval($_POST['quantity']);

    // Get ClothesID and current quantity from tblClothes
    $stmt = $conn->prepare("SELECT ClothesID, Quantity FROM tblClothes WHERE Name = ?");
    $stmt->bind_param("s", $itemName);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $ClothesID = $row['ClothesID'];
        $currentQuantity = $row['quantity'];

        // Check if enough stock is available
        if ($quantity <= $currentQuantity) {
            // Reduce quantity in tblClothes
            $newQuantity = $currentQuantity - $quantity;
            $updateStmt = $conn->prepare("UPDATE tblClothes SET Quantity = ? WHERE ClothesID = ?");
            $updateStmt->bind_param("ii", $newQuantity, $ClothesID);
            if ($updateStmt->execute()) {
                // Respond with success message and item details
                echo json_encode([
                    'success' => true,
                    'name' => $itemName,
                    'price' => $itemPrice,
                    'newQuantity' => $newQuantity
                ]);
            } else {
                //echo json_encode(['success' => false, 'message' => 'Failed to update quantity.']);
            }
        } else {
            //echo json_encode(['success' => false, 'message' => 'Not enough stock available.']);
        }
    } else {
        //echo json_encode(['success' => false, 'message' => 'Item not found.']);
    }
} else {
    //echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css_assets/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title><?php echo htmlspecialchars($product['Name']); ?> - Pastimes</title>
</head>


<body>
    <header>
        <div class="navbar">
            <div class="logo">
                <a href="index.php"><img src="_images/logo.png" alt="Logo" style="height: 30px;"></a>
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
        <div class="login_container">

            <div class="left_box">
                <div class="product_image">
                    <img class="product-img" src="<?php echo htmlspecialchars($product['ImagePath']); ?>" alt="<?php echo htmlspecialchars($product['Name']); ?>">
                </div>

            </div>

            <div class="right_box">
                <!-- Back button to catalog -->
                <div class="back-button">
                    <a href="catalog.php" class="btn-back">← Back to Catalog</a>
                </div>
                <!-- Display product details -->
                <div class="product-details">
                    <h2><?php echo htmlspecialchars($product['Name']); ?></h2>
                    <p class="product-description"><?php echo htmlspecialchars($product['Description']); ?></p>
                    <p class="product-brand">Brand: <?php echo htmlspecialchars($product['Brand']); ?></p>
                    <p class="product-category">Category: <?php echo htmlspecialchars($product['Category']); ?></p>
                    <!-- Size Selection Dropdown -->
                    <label for="size-select">Size:</label>
                    <select id="size-select" name="size">
                        <?php
                        $sizes = explode(',', $product['Sizes']); // Convert sizes string to an array
                        foreach ($sizes as $size): ?>
                            <option value="<?php echo htmlspecialchars(trim($size)); ?>"><?php echo htmlspecialchars(trim($size)); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <p class="product-price">Price: R<?php echo number_format($product['Price'], 2); ?></p>

                    <!-- Quantity selection and Add to Cart button -->
                    <label for="quantity">Quantity:</label>
                    <input type="number" id="quantity" name="quantity" min="1" max="<?php echo $product['Quantity']; ?>" value="1">

                    <div class="button-group">
                        <button type="button" onclick="addToCart('<?php echo $product['Name']; ?>', <?php echo $product['Price']; ?>)">Add to Cart</button>
                    </div>
                </div>

            </div>
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
                        <a href="https://www.instagram.com/bmwsouthafrica/" target="_blank"><i
                                class="fab fa-instagram"></i></a>
                        <a href="https://www.linkedin.com/showcase/bmwsouthafrica/posts/?feedView=all"
                            target="_blank"><i class="fab fa-linkedin"></i></a>
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
    </footer>

    <script>
        // Toggle cart side panel
        function toggleCart() {
            document.getElementById('cartSidePanel').classList.toggle('open');
        }

        function addToCart(itemName, itemPrice) {
            let quantity = parseInt(document.getElementById('quantity').value); // Parse quantity as integer
            let formData = new FormData();
            formData.append('itemName', itemName);
            formData.append('itemPrice', itemPrice);
            formData.append('quantity', quantity);

            fetch('add_to_cart.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(`Added ${itemName} (Quantity: ${quantity}) to the cart. Price: R ${itemPrice}`);
                        location.reload(); // Reload to reflect cart count or any other updates
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => console.error('Error:', error));
        }


        // Function to handle removing an item from the cart
        function removeFromCart(itemName) {
            let formData = new FormData();
            formData.append('action', 'remove');
            formData.append('itemName', itemName);

            fetch('catalog.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    alert(data.message);
                    location.reload(); // Reload to update cart
                })
                .catch(error => console.error('Error:', error));
        }

        function updateQuantity(itemName, action) {
            let formData = new FormData();
            formData.append('itemName', itemName);
            formData.append('action', action);

            fetch('catalog.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    alert(data.message);
                    location.reload(); // Reload page to update cart count
                })
                .catch(error => console.error('Error:', error));
        }
        // Function to handle Buy Now
        function buyNow(itemName, itemPrice) {
            let formData = new FormData();
            formData.append('itemName', itemName);
            formData.append('itemPrice', itemPrice);

            fetch('add_to_cart.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.href = 'cart.php'; // Redirect to cart page after adding item
                    } else {
                        alert("There was an issue adding the item to your cart.");
                    }
                })
                .catch(error => console.error('Error:', error));
        }
    </script>
</body>

</html>