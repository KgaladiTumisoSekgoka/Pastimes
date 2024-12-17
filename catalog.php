<?php
session_start();
include 'DBConn.php';

// Check login status
$isLoggedIn = isset($_SESSION['user_name']);
$username = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : '';

// Fetch clothes from the tblClothes table
$sql = "SELECT * FROM tblClothes";
$result = $conn->query($sql);

// Initialize the cart session if it's not set
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle add to cart requests
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['itemName']) && isset($_POST['itemPrice'])) {
    $itemName = $_POST['itemName'];
    $itemPrice = (float)$_POST['itemPrice']; // Ensure price is treated as a float

    // Check if the item is already in the cart
    $found = false;
    foreach ($_SESSION['cart'] as &$cartItem) {
        if ($cartItem['name'] === $itemName) {
            $cartItem['quantity']++;
            $found = true;
            break;
        }
    }

    // If not found, add the item to the cart
    if (!$found) {
        $_SESSION['cart'][] = [
            'name' => $itemName,
            'price' => $itemPrice,
            'quantity' => 1
        ];
    }

    echo json_encode(['message' => 'Item added to cart!', 'name' => $itemName, 'price' => number_format($itemPrice, 2)]);
    exit;
}

// Handle remove item requests
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'remove') {
    $itemName = $_POST['itemName'];

    // Remove the item from the cart
    foreach ($_SESSION['cart'] as $key => $cartItem) {
        if ($cartItem['name'] === $itemName) {
            unset($_SESSION['cart'][$key]);
            $_SESSION['cart'] = array_values($_SESSION['cart']); // Re-index the array
            break;
        }
    }

    echo json_encode(['message' => 'Item removed from cart!']);
    exit;
}

// Handle quantity updates
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && ($_POST['action'] == 'increase' || $_POST['action'] == 'decrease')) {
    $itemName = $_POST['itemName'];

    // Fetch the available quantity from tblClothes
    $sql = "SELECT Quantity FROM tblClothes WHERE Name = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $itemName);
    $stmt->execute();
    $result = $stmt->get_result();
    $availableQuantity = $result->num_rows > 0 ? $result->fetch_assoc()['Quantity'] : 0;

    foreach ($_SESSION['cart'] as &$cartItem) {
        if ($cartItem['name'] === $itemName) {
            // Update the quantity based on action
            if ($_POST['action'] == 'increase') {
                if ($cartItem['quantity'] < $availableQuantity) { // Check against available stock
                    $cartItem['quantity']++;
                } else {
                    echo json_encode(['message' => 'Cannot increase quantity beyond available stock!']);
                    exit;
                }
            } elseif ($_POST['action'] == 'decrease') {
                if ($cartItem['quantity'] > 1) {
                    $cartItem['quantity']--;
                }
            }

            // Calculate subtotal for the updated item
            $itemSubtotal = $cartItem['price'] * $cartItem['quantity'];

            // Calculate total price for the entire cart
            $totalPrice = 0;
            foreach ($_SESSION['cart'] as $item) {
                $totalPrice += $item['price'] * $item['quantity'];
            }

            // Return new quantity, subtotal for the item, and total cart price
            echo json_encode([
                'message' => 'Cart updated!',
                'newQuantity' => $cartItem['quantity'],
                'itemPrice' => number_format($cartItem['price'], 2),
                'itemSubtotal' => number_format($itemSubtotal, 2),
                'totalPrice' => number_format($totalPrice, 2) // Include total price in response
            ]);
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css_assets/styles.css">
    <link rel="stylesheet" href="css_assets/cart.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Catalog</title>
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
            <h1>Catalog</h1>
            <!-- Cart Icon -->
            <div class="cart-icon" onclick="toggleCart()">
                <img src="_images/shopping_cart.png" alt="Shopping cart icon" style="width: 50px; height: 50px;" />
                <span class="cart-count"><?php echo count($_SESSION['cart']); ?></span>
            </div>
            <br>
            <div class="filter-dropdown">
                <select id="filterSelect" onchange="applyFilter()">
                    <option value="">Filter By</option>
                    <option value="low">Price: Low to High</option>
                    <option value="high">Price: High to Low</option>
                    <option value="size">Size</option>
                    <option value="category">Category</option>
                </select>
            </div>

            <!-- Side Cart Widget -->
            <div id="cartSidePanel" class="cart-sidepanel">
                <a href="javascript:void(0)" class="closebtn" onclick="toggleCart()">&times;</a>
                <h2>Cart <i class="fa fa-shopping-cart"></i></h2>
                <ul>
                    <?php if (empty($_SESSION['cart'])): ?>
                        <li>Your cart is empty.</li>
                    <?php else: ?>
                        <?php
                        $subtotal = 0; // Initialize subtotal
                        foreach ($_SESSION['cart'] as $item):
                            $itemPrice = $item['price'] * $item['quantity']; // Calculate item price based on quantity
                            $subtotal += $itemPrice; // Add to subtotal
                        ?>
                            <li>
                                <div>
                                    <span><?php echo htmlspecialchars($item['name']); ?></span>
                                    <span>R <?php echo number_format($itemPrice, 2); ?></span> <!-- Display price based on quantity -->
                                    <span>Qty: <span id="quantity-<?php echo htmlspecialchars($item['name']); ?>"><?php echo isset($item['quantity']) ? htmlspecialchars($item['quantity']) : 1; ?></span></span>
                                    <button class="btn-quantity" onclick="updateQuantity('<?php echo htmlspecialchars($item['name']); ?>', 'increase')"><i class="fa fa-plus" aria-hidden="true"></i></button>
                                    <button class="btn-quantity" onclick="updateQuantity('<?php echo htmlspecialchars($item['name']); ?>', 'decrease')"><i class="fa fa-minus" aria-hidden="true"></i></button>
                                    <button class="btn-remove" onclick="removeFromCart('<?php echo htmlspecialchars($item['name']); ?>')"><i class="fa fa-trash" aria-hidden="true"></i></button>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>

                <?php if (!empty($_SESSION['cart'])): ?>
                    <!-- Display Subtotal -->
                    <div class="subtotal">
                        <strong>Subtotal: R <?php echo number_format($subtotal, 2); ?></strong>
                    </div>

                    <!-- Checkout Button -->
                    <div class="checkout-button-container">
                        <a href="cart.php" class="btn-checkout">Proceed to Checkout</a>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <!-- Refined Card Section -->
        <section class="card_container">
            <?php while ($row = $result->fetch_assoc()) { ?>
                <a href="selected_item.php?ClothesID=<?php echo htmlspecialchars($row['ClothesID']); ?>">
                    <div class="card">
                        <div class="card_image">
                            <img src="<?php echo $row['ImagePath']; ?>" alt="<?php echo $row['Name']; ?>">
                        </div>
                        <div class="card_content">
                            <h3><?php echo $row['Name']; ?></h3>
                            <p class="size">Size: <?php echo $row['Sizes']; ?></p>
                            <p class="price">Price: R <?php echo $row['Price']; ?></p>
                            <p class="category">Category: <?php echo $row['Category']; ?></p>

                            <!-- Form to send item to cart -->
                            <button class="btn" onclick="addToCart('<?php echo $row['Name']; ?>', '<?php echo $row['Price']; ?>')">
                                <i class="fa fa-shopping-cart"></i> Add to Cart
                            </button>
                        </div>
                    </div>
                </a>
            <?php } ?>
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

        // Add to cart function
        function addToCart(itemName, itemPrice, quantity) {
            fetch('your_cart_script.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        itemName: itemName,
                        itemPrice: itemPrice,
                        quantity: quantity
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message); // Notify user of success
                        updateCartWidget(data.cart); // Update cart widget with new cart data
                    } else {
                        alert(data.message); // Notify user of any errors
                    }
                    // Refresh the page after adding to cart
                    location.reload();
                })
                .catch(error => console.error('Error:', error));
        }

        function updateCartWidget(cartItems) {
            const cartList = document.querySelector('#cartSidePanel ul');
            cartList.innerHTML = ''; // Clear existing items in the cart list

            let subtotal = 0;

            // Loop through each item in the cart and display it
            cartItems.forEach(item => {
                const itemPrice = item.price * item.quantity;
                subtotal += itemPrice;

                const listItem = document.createElement('li');
                listItem.innerHTML = `
            <div>
                <span>${item.name}</span>
                <span>R ${itemPrice.toFixed(2)}</span>
                <span>Qty: ${item.quantity}</span>
            </div>
        `;
                cartList.appendChild(listItem);
            });

            document.querySelector('.subtotal').innerHTML = `Subtotal: R ${subtotal.toFixed(2)}`;
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
                    // Show alert with message after quantity update
                    alert('Quantity updated! Your cart has been updated.');

                    // Refresh the page to apply the changes
                    location.reload();
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