<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_name'])) {
    // If not logged in, redirect to the login selection page
    header("Location: login_register_selection.php");
    exit();
}

// Database connection
//Be sure to change this if needed to do so!
$dsn = "mysql:host=localhost;dbname=myclothingstore;charset=utf8mb4";
$username = "root";
$password = "";

try {
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Fetch random featured items
$query = "SELECT * FROM tblClothes ORDER BY RAND() LIMIT 4";
$stmt = $pdo->prepare($query);
$stmt->execute();
$featuredItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch random men's items
$queryMen = "SELECT * FROM tblClothes WHERE Category = 'Men' ORDER BY RAND() LIMIT 4";
$stmtMen = $pdo->prepare($queryMen);
$stmtMen->execute();
$menItems = $stmtMen->fetchAll(PDO::FETCH_ASSOC);

// Fetch random women's items
$queryWomen = "SELECT * FROM tblClothes WHERE Category = 'Women' ORDER BY RAND() LIMIT 4";
$stmtWomen = $pdo->prepare($queryWomen);
$stmtWomen->execute();
$womenItems = $stmtWomen->fetchAll(PDO::FETCH_ASSOC);

// Fetch random kids' items
$queryKids = "SELECT * FROM tblClothes WHERE Category = 'Kids' ORDER BY RAND() LIMIT 4";
$stmtKids = $pdo->prepare($queryKids);
$stmtKids->execute();
$kidsItems = $stmtKids->fetchAll(PDO::FETCH_ASSOC);
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
    <link rel="stylesheet" href="css_assets/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Pastimes | Revive Your Style, Relive The Past</title>
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
        <section class="hero">
            <div class="left">
                <?php
                // Display a welcome message with the username
                echo '<p>Logged in as: <strong>' . htmlspecialchars($_SESSION['user_name']) . '</strong></p>';
                ?>
                <h3 class="head_text">Welcome to Pastimes</h3>
                <p class="body_text">Revive Your Style, Relive The Past</p>
                <a href="catalog.php"><button id="banner_btn">SHOP NOW</button></a>
            </div>
            <div class="right">
                <div class="img_container">
                    <img src="_images/pastime_banner.jpg" alt="" class="img" />
                </div>
            </div>
        </section>

        <h2 align="center" class="head_right">Welcome to Pastimes</h2>
        <p class="services_p" align="center">
            Your one-stop shop for high-quality, pre-loved clothing.<br>
            At Pastimes, we believe in giving clothes a second chance,<br>
            helping you find unique, affordable, and stylish pieces that suit your taste.<br>
            Whether you're looking to buy or sell, we're here to make the process easy, safe, and enjoyable.<br>
            Learn more about us
        </p>

        <!-- Featured Items -->
        <section class="heading_section">
            <h1>Featured Products</h1>
        </section>

        <!-- Cards Section -->
        <section class="card_container">
            <?php foreach ($featuredItems as $row) { ?>
                <a href="selected_item.php?ClothesID=<?php echo htmlspecialchars($row['ClothesID']); ?>">
                    <div class="card">
                        <div class="card_image">
                            <img src="<?php echo $row['ImagePath']; ?>" alt="<?php echo $row['Name']; ?>">
                        </div>
                        <div class="card_content">
                            <h3><?php echo htmlspecialchars($row['Name']); ?></h3>
                            <p class="size">Size: <?php echo htmlspecialchars($row['Sizes']); ?></p>
                            <p class="price">Price: R <?php echo htmlspecialchars($row['Price']); ?></p>
                            <p class="category">Category: <?php echo htmlspecialchars($row['Category']); ?></p>

                            <!-- Form to send item to cart -->
                            <button class="btn" onclick="addToCart('<?php echo htmlspecialchars($row['Name']); ?>', '<?php echo htmlspecialchars($row['Price']); ?>')">
                                <i class="fa fa-shopping-cart"></i> Add to Cart
                            </button>
                        </div>
                    </div>
                </a>
            <?php } ?>
        </section>

        <!-- Cards Section Displaying Men Items Randomly -->
        <section class="heading_section">
            <h1>Men's Clothing</h1>
        </section>
        <section class="card_container">
            <?php foreach ($menItems as $row) { ?>
                <a href="selected_item.php?ClothesID=<?php echo htmlspecialchars($row['ClothesID']); ?>">
                    <div class="card">
                        <div class="card_image">
                            <img src="<?php echo $row['ImagePath']; ?>" alt="<?php echo $row['Name']; ?>">
                        </div>
                        <div class="card_content">
                            <h3><?php echo htmlspecialchars($row['Name']); ?></h3>
                            <p class="size">Size: <?php echo htmlspecialchars($row['Sizes']); ?></p>
                            <p class="price">Price: R <?php echo htmlspecialchars($row['Price']); ?></p>
                            <p class="category">Category: <?php echo htmlspecialchars($row['Category']); ?></p>

                            <button class="btn" onclick="addToCart('<?php echo htmlspecialchars($row['Name']); ?>', '<?php echo htmlspecialchars($row['Price']); ?>')">
                                <i class="fa fa-shopping-cart"></i> Add to Cart
                            </button>
                        </div>
                    </div>
                </a>
            <?php } ?>
        </section>

        <!-- Cards Section Displaying Women Items Randomly -->
        <section class="heading_section">
            <h1>Women's Clothing</h1>
        </section>
        <section class="card_container">
            <?php foreach ($womenItems as $row) { ?>
                <a href="selected_item.php?ClothesID=<?php echo htmlspecialchars($row['ClothesID']); ?>">
                    <div class="card">
                        <div class="card_image">
                            <img src="<?php echo $row['ImagePath']; ?>" alt="<?php echo $row['Name']; ?>">
                        </div>
                        <div class="card_content">
                            <h3><?php echo htmlspecialchars($row['Name']); ?></h3>
                            <p class="size">Size: <?php echo htmlspecialchars($row['Sizes']); ?></p>
                            <p class="price">Price: R <?php echo htmlspecialchars($row['Price']); ?></p>
                            <p class="category">Category: <?php echo htmlspecialchars($row['Category']); ?></p>

                            <button class="btn" onclick="addToCart('<?php echo htmlspecialchars($row['Name']); ?>', '<?php echo htmlspecialchars($row['Price']); ?>')">
                                <i class="fa fa-shopping-cart"></i> Add to Cart
                            </button>
                        </div>
                    </div>
                </a>
            <?php } ?>
        </section>

        <!-- Cards Section Displaying Kids Items Randomly -->
        <section class="heading_section">
            <h1>Kids' Clothing</h1>
        </section>
        <section class="card_container">
            <?php foreach ($kidsItems as $row) { ?>
                <a href="selected_item.php?ClothesID=<?php echo htmlspecialchars($row['ClothesID']); ?>">
                    <div class="card">
                        <div class="card_image">
                            <img src="<?php echo $row['ImagePath']; ?>" alt="<?php echo $row['Name']; ?>">
                        </div>
                        <div class="card_content">
                            <h3><?php echo htmlspecialchars($row['Name']); ?></h3>
                            <p class="size">Size: <?php echo htmlspecialchars($row['Sizes']); ?></p>
                            <p class="price">Price: R <?php echo htmlspecialchars($row['Price']); ?></p>
                            <p class="category">Category: <?php echo htmlspecialchars($row['Category']); ?></p>

                            <button class="btn" onclick="addToCart('<?php echo htmlspecialchars($row['Name']); ?>', '<?php echo htmlspecialchars($row['Price']); ?>')">
                                <i class="fa fa-shopping-cart"></i> Add to Cart
                            </button>
                        </div>
                    </div>
                </a>
            <?php } ?>
        </section>
        <!-- Featured Items -->
        <section class="heading_section">
            <h1>Featured Services</h1>
        </section>
        <!-- Services of Pastimes -->
        <section class="extra_card_container">
            <div class="card">
                <img src="_images/delivery.png" alt="Product 4" class="img" />
                <div class="card_content">
                    <h3>FAST SHIPPING</h3>
                    <p>We Ship Your Clothing Fast.</p>
                </div>
            </div>
            <div class="card">
                <img src="_images/high-quality.png" alt="Product 5" class="img" />
                <div class="card_content">
                    <h3>PREMIUM QUALITY</h3>
                    <p>The Quality is Premium.</p>
                </div>
            </div>
            <div class="card">
                <img src="_images/customer-service.png" alt="Product 6" class="img" />
                <div class="card_content">
                    <h3>CUSTOMER SERVICE</h3>
                    <p>Professional Customer Service.</p>
                </div>
            </div>
        </section>
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
    <script>
        // Toggle cart side panel
        function toggleCart() {
            document.getElementById('cartSidePanel').classList.toggle('open');
        }

        // Add to cart function
        function addToCart(itemName, itemPrice) {
            let quantity = document.getElementById('quantity').value;
            let formData = new FormData();
            formData.append('itemName', itemName);
            formData.append('itemPrice', itemPrice);
            formData.append('quantity', quantity); // Send quantity

            fetch('add_to_cart.php', { // Make sure this is the correct PHP file handling the request
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(`Added ${data.name} (Quantity: ${data.quantity}) to the cart. Price: R ${data.price}`);
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

        // Function to update cart count in the header
        function updateCartCount() {
            fetch('cart_count.php') // Separate endpoint to get cart count
                .then(response => response.text())
                .then(count => {
                    document.querySelector('.cart-count').textContent = count;
                })
                .catch(error => console.error('Error updating cart count:', error));
        }

        // Function to handle increasing and decreasing quantity
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
                    if (data.newQuantity !== undefined) {
                        document.getElementById(`quantity-${itemName}`).textContent = data.newQuantity;
                    }
                    if (data.itemSubtotal !== undefined) {
                        document.querySelector(`#subtotal-${itemName}`).textContent = 'R ' + data.itemSubtotal;
                    }
                    if (data.totalPrice !== undefined) {
                        document.querySelector('.subtotal strong').textContent = 'Subtotal: R ' + data.totalPrice;
                    }
                })
                .catch(error => console.error('Error:', error));
        }

        // Function to apply filtering
        function applyFilter() {
            const filterValue = document.getElementById('filterSelect').value;
            let items = Array.from(document.querySelectorAll('.card'));

            items.sort((a, b) => {
                const priceA = parseFloat(a.querySelector('.price').textContent.replace(/[^\d.-]/g, ''));
                const priceB = parseFloat(b.querySelector('.price').textContent.replace(/[^\d.-]/g, ''));

                if (filterValue === 'low') {
                    return priceA - priceB; // Sort from low to high
                } else if (filterValue === 'high') {
                    return priceB - priceA; // Sort from high to low
                } else if (filterValue === 'size') {
                    const sizeA = a.querySelector('.size').textContent.split(': ')[1];
                    const sizeB = b.querySelector('.size').textContent.split(': ')[1];
                    return sizeA.localeCompare(sizeB);
                } else if (filterValue === 'category') {
                    const categoryA = a.querySelector('.category').textContent.split(': ')[1];
                    const categoryB = b.querySelector('.category').textContent.split(': ')[1];
                    return categoryA.localeCompare(categoryB);
                }
                return 0;
            });

            // Clear the container and re-append sorted items
            const container = document.querySelector('.card_container');
            container.innerHTML = '';
            items.forEach(item => container.appendChild(item));
        }
    </script>
</body>

</html>