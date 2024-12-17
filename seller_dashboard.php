<?php
session_start();
include('DBConn.php');

// Check if seller or admin is logged in
if (!isset($_SESSION['seller_id']) && !isset($_SESSION['admin_id'])) {
    header("Location: seller_login.php");
    exit();
}

// Set seller_id if admin is logged in
if (isset($_SESSION['admin_id']) && !isset($_SESSION['seller_id'])) {
    // Replace with actual admin logic to select or set seller_id
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['selected_seller_id'])) {
        $_SESSION['seller_id'] = $_POST['selected_seller_id'];
    } else {
        // Display form to select a seller if one hasn't been selected yet
        echo '<form method="post" action="">';
        echo '<label for="selected_seller_id">Select Seller:</label>';
        echo '<select name="selected_seller_id" required>';

        // Fetch sellers from the database
        $sellers = $conn->query("SELECT SellerID, SellerName FROM tblseller");
        if ($sellers && $sellers->num_rows > 0) {
            while ($seller = $sellers->fetch_assoc()) {
                echo '<option value="' . $seller['SellerID'] . '">' . htmlspecialchars($seller['SellerName']) . '</option>';
            }
        } else {
            echo '<option disabled>No sellers available</option>';
        }

        echo '</select>';
        echo '<button type="submit">Select Seller</button>';
        echo '</form>';
        exit();
    }
}

// Confirm seller_id is set
if (!isset($_SESSION['seller_id'])) {
    echo "Error: Seller ID is not set in the session.";
    exit();
}

// Define seller_id for queries
$seller_id = $_SESSION['seller_id'];

// Handle product addition, update, and deletion
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Add Product
    if (isset($_POST['add_product'])) {
        $name = $_POST['product_name'] ?? '';
        $brand = $_POST['brand_name'] ?? '';
        $description = $_POST['product_description'] ?? '';
        $category = $_POST['category'] ?? '';
        $quantity = $_POST['quantity'] ?? 0;
        $price = $_POST['product_price'] ?? 0.0;
        $sizes = $_POST['sizes'] ?? '';
        $imagePath = null;

        // Handle image upload
        if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
            $image = $_FILES['product_image']['name'];
            $targetFilePath = __DIR__ . "/_images/" . basename($image);
            if (move_uploaded_file($_FILES['product_image']['tmp_name'], $targetFilePath)) {
                $imagePath = "_images/" . $image;
            } else {
                echo "Error uploading the product image.";
            }
        }

        // Insert new product
        $sql = "INSERT INTO tblClothes (Name, Price, Description, Brand, Category, Quantity, Sizes, ImagePath, SellerID) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sdsssissi", $name, $price, $description, $brand, $category, $quantity, $sizes, $imagePath, $seller_id);

        if ($stmt->execute()) {
            header("Location: seller_dashboard.php");
            exit();
        } else {
            echo "Error adding product: " . $stmt->error;
        }
    }

    // Update Product
    if (isset($_POST['update_product'])) {
        $productId = $_POST['update_product_id'];
        $name = $_POST['update_product_name'] ?? '';
        $brand = $_POST['update_brand_name'] ?? '';
        $description = $_POST['update_product_description'] ?? '';
        $category = $_POST['update_category'] ?? '';
        $quantity = $_POST['update_quantity'] ?? 0;
        $price = $_POST['update_product_price'] ?? 0.0;
        $sizes = $_POST['update_sizes'] ?? '';
        $imagePath = null;

        // Handle image upload for update
        if (isset($_FILES['update_product_image']) && $_FILES['update_product_image']['error'] === UPLOAD_ERR_OK) {
            $image = $_FILES['update_product_image']['name'];
            $targetFilePath = __DIR__ . "/_images/" . basename($image);
            if (move_uploaded_file($_FILES['update_product_image']['tmp_name'], $targetFilePath)) {
                $imagePath = "_images/" . $image;
            } else {
                echo "Error uploading the product image.";
            }
        }

        // Update SQL query, conditional for image path
        $sql = "UPDATE tblClothes SET Name = ?, Price = ?, Description = ?, Brand = ?, Category = ?, Quantity = ?, Sizes = ?";
        if ($imagePath) {
            $sql .= ", ImagePath = ?";
        }
        $sql .= " WHERE ClothesID = ? AND SellerID = ?";

        // Prepare statement with optional image path
        $stmt = $conn->prepare($sql);
        if ($imagePath) {
            $stmt->bind_param("sdsssissii", $name, $price, $description, $brand, $category, $quantity, $sizes, $imagePath, $productId, $seller_id);
        } else {
            $stmt->bind_param("sdsssisii", $name, $price, $description, $brand, $category, $quantity, $sizes, $productId, $seller_id);
        }

        if ($stmt->execute()) {
            header("Location: seller_dashboard.php");
            exit();
        } else {
            echo "Error updating product: " . $stmt->error;
        }
    }

    // Delete Product
    if (isset($_POST['delete_product'])) {
        $productId = $_POST['product_id'];

        $sql = "DELETE FROM tblClothes WHERE ClothesID = ? AND SellerID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $productId, $seller_id);
        $stmt->execute();

        header("Location: seller_dashboard.php");
        exit();
    }
}

// Fetch seller's products
$query = "SELECT * FROM tblClothes WHERE SellerID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $seller_id);
$stmt->execute();
$sellerProducts = $stmt->get_result();

// Fetch seller's orders
$query = "SELECT o.UserID, o.Username, o.ClothingName, o.Quantity, o.Price, o.TotalPrice, o.OrderDate 
          FROM tblOrder o 
          JOIN tblClothes c ON o.ClothingName = c.Name  
          WHERE c.SellerID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $seller_id);
$stmt->execute();
$sellerOrders = $stmt->get_result();

// Fetch seller's messages
$query = "SELECT m.MessageID, m.MessageText, m.Timestamp, u.Name AS CustomerName, m.SenderID 
          FROM tblMessages m
          JOIN tblUser u ON m.SenderID = u.UserID
          WHERE m.ReceiverID = ?  
          ORDER BY m.Timestamp DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $seller_id);
$stmt->execute();

$sellerMessages = $stmt->get_result();
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['response_text'])) {
    $customerId = (int)$_POST['customer_id']; // Get the customer ID
    $responseText = trim($_POST['response_text']);

    if (!empty($responseText) && $customerId > 0) {
        $stmt = $conn->prepare("INSERT INTO tblMessages (SenderID, ReceiverID, MessageText, Timestamp) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("iis", $seller_id, $customerId, $responseText); // Assuming $seller_id is defined earlier in your code

        if ($stmt->execute()) {
            // Optionally, redirect back or display a success message
            header("Location: seller_dashboard.php?success=1"); // Redirect to avoid resubmission
            exit;
        } else {
            echo '<p>Error sending response: ' . htmlspecialchars($conn->error) . '</p>';
        }

        $stmt->close();
    } else {
        echo '<p>Please enter a valid response.</p>';
    }
}
$seller_id = $_SESSION['seller_id'];

// Fetch seller's information
$query = "SELECT SellerName, Username, Email, ContactNo, Address FROM tblseller WHERE SellerID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $seller_id);
$stmt->execute();
$result = $stmt->get_result();
$sellerInfo = $result->fetch_assoc();

if (!$sellerInfo) {
    echo "Error: Unable to retrieve seller information.";
    exit();
}

// Update seller information if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $sellerName = validate($_POST['sellerName']);
    $username = validate($_POST['username']);
    $email = validate($_POST['email']);
    $contactNo = validate($_POST['contactNo']);
    $address = validate($_POST['address']);

    $updateQuery = "UPDATE tblseller SET SellerName = ?, Username = ?, Email = ?, ContactNo = ?, Address = ? WHERE SellerID = ?";
    $updateStmt = $conn->prepare($updateQuery);
    $updateStmt->bind_param("sssssi", $sellerName, $username, $email, $contactNo, $address, $seller_id);

    if ($updateStmt->execute()) {
        echo "Profile updated successfully!";
        header("Refresh:0"); // Refresh the page to show updated info
    } else {
        echo "Error updating profile.";
    }
}

// Function to validate input
function validate($data)
{
    return htmlspecialchars(stripslashes(trim($data)));
}


$seller_id = $_SESSION['seller_id'];

// Fetch seller's orders by joining tblorder and tblclothes
$query = "
    SELECT 
        o.OrderID, 
        o.UserID, 
        o.Username, 
        o.ClothingName, 
        o.Quantity, 
        o.TotalPrice, 
        o.OrderDate
    FROM 
        tblorder o
    JOIN 
        tblclothes c ON o.ClothingName = c.Name
    WHERE 
        c.SellerID = ?
    ORDER BY 
        o.OrderDate DESC
";

$stmt = $conn->prepare($query);
if (!$stmt) {
    die("Preparation failed: " . htmlspecialchars($conn->error));
}

$stmt->bind_param("i", $seller_id);
$stmt->execute();
$sellerOrders = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Seller Dashboard for managing your products and orders.">
    <meta name="keywords" content="seller, dashboard, manage products, orders">
    <meta name="robots" content="noindex, nofollow">
    <link rel="stylesheet" href="css_assets/admin_dashboard_styles.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <title>Seller Dashboard</title>
</head>

<body>
    <header class="navbar">
        <div class="logo">
            <a href="index.php"><img src="_images/logo.png" alt="Logo" style="height: 40px;"></a>
        </div>
        <nav>
            <ul class="nav-menu">
                <li><a href="seller_dashboard.php">Home</a></li>
                <li><a href="catalog.php">Catalog</a></li>
                <li><a href="seller_orders.php">Orders</a></li>
                <li><a href="seller_profile.php">Profile</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <div class="dashboard-container">
        <aside class="sidebar">
            <ul>
                <li><a href="#" id="dashboard-link">Dashboard Overview</a></li>
                <li><a href="#" id="products-link">Manage Products</a></li>
                <li><a href="#" id="orders-link">View Orders</a></li>
                <li><a href="#" id="messaging-link">Messages</a></li>
                <li><a href="#" id="profile-link">Seller Profile</a></li>
            </ul>
        </aside>

        <main class="main-content" id="content">
            <!-- Dashboard Section -->
            <section id="dashboard-section">
                <h2>Dashboard Overview</h2>
                <p>Welcome to your seller dashboard. Manage your products, view orders, and update your profile.</p>
            </section>

            <section id="products-section" style="display:none;">
                <h2>Manage Products</h2>
                <table border="1">
                    <tr>
                        <th>Product ID</th>
                        <th>Product Name</th>
                        <th>Price</th>
                        <th>Description</th>
                        <th>Brand</th>
                        <th>Category</th>
                        <th>Quantity</th>
                        <th>Sizes</th>
                        <th>Image</th>
                        <th>Action</th>
                    </tr>
                    <?php if ($sellerProducts->num_rows > 0): ?>
                        <?php while ($row = $sellerProducts->fetch_assoc()): ?>
                            <tr>
                                <td><?= $row['ClothesID'] ?></td>
                                <td><?= $row['Name'] ?></td>
                                <td><?= $row['Price'] ?></td>
                                <td><?= $row['Description'] ?></td>
                                <td><?= $row['Brand'] ?></td>
                                <td><?= $row['Category'] ?></td>
                                <td><?= $row['Quantity'] ?></td>
                                <td><?= $row['Sizes'] ?></td>
                                <td><img src="<?= $row['ImagePath'] ?>" style="height: 40px;"></td>
                                <td>
                                    <form method="post" action="seller_dashboard.php">
                                        <input type="hidden" name="product_id" value="<?= $row['ClothesID'] ?>">
                                        <button type="button" onclick="prepareUpdateForm('<?= $row['ClothesID'] ?>', '<?= addslashes($row['Name']) ?>', '<?= $row['Price'] ?>', '<?= addslashes($row['Description']) ?>', '<?= addslashes($row['Brand']) ?>', '<?= addslashes($row['Category']) ?>', '<?= $row['Quantity'] ?>', '<?= addslashes($row['Sizes']) ?>', '<?= $row['ImagePath'] ?>')">Update</button>
                                        <button type="submit" name="delete_product">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="10">No products are listed yet.</td>
                        </tr>
                    <?php endif; ?>
                </table>

                <button onclick="toggleForm('add-product-form')">Add New Product</button>

                <div id="add-product-form" style="display:none;">
                    <form method="post" action="seller_dashboard.php" enctype="multipart/form-data" class="user-form">
                        <h3>Add New Product</h3>
                        <div class="form-group">
                            <label for="product_name">Product Name:</label>
                            <input type="text" name="product_name" id="product_name" required>
                        </div>
                        <div class="form-group">
                            <label for="brand_name">Brand Name:</label>
                            <input type="text" name="brand_name" id="brand_name" required>
                        </div>
                        <div class="form-group">
                            <label for="product_description">Description:</label>
                            <input type="text" name="product_description" id="product_description" required>
                        </div>
                        <div class="form-group">
                            <label for="category">Category:</label>
                            <input type="text" name="category" id="category" required>
                        </div>
                        <div class="form-group">
                            <label for="quantity">Quantity:</label>
                            <input type="number" name="quantity" id="quantity" required min="1">
                        </div>
                        <div class="form-group">
                            <label for="product_price">Product Price:</label>
                            <input type="number" name="product_price" id="product_price" step="0.01" required>
                        </div>
                        <div class="form-group">
                            <label for="sizes">Sizes (comma-separated):</label>
                            <input type="text" name="sizes" id="sizes" required>
                        </div>
                        <div class="form-group">
                            <label for="product_image">Product Image:</label>
                            <input type="file" name="product_image" id="product_image" accept="image/*" required>
                        </div>
                        <button type="submit" name="add_product">Add Product</button>
                    </form>
                </div>


                <div id="update-product-form" style="display:none;">
                    <form method="post" action="seller_dashboard.php" enctype="multipart/form-data" class="user-form">
                        <h3>Update Product</h3>
                        <input type="hidden" name="update_product_id" id="update_product_id">
                        <div class="form-group">
                            <label for="update_product_name">Product Name:</label>
                            <input type="text" name="update_product_name" id="update_product_name" required>
                        </div>
                        <div class="form-group">
                            <label for="update_brand_name">Brand Name:</label>
                            <input type="text" name="update_brand_name" id="update_brand_name" required>
                        </div>
                        <div class="form-group">
                            <label for="update_product_description">Description:</label>
                            <input type="text" name="update_product_description" id="update_product_description" required>
                        </div>
                        <div class="form-group">
                            <label for="update_category">Category:</label>
                            <input type="text" name="update_category" id="update_category" required>
                        </div>
                        <div class="form-group">
                            <label for="update_quantity">Quantity:</label>
                            <input type="number" name="update_quantity" id="update_quantity" required>
                        </div>
                        <div class="form-group">
                            <label for="update_product_price">Product Price:</label>
                            <input type="number" name="update_product_price" id="update_product_price" step="0.01" required>
                        </div>
                        <div class="form-group">
                            <label for="update_sizes">Sizes (comma-separated):</label>
                            <input type="text" name="update_sizes" id="update_sizes" required>
                        </div>
                        <div class="form-group">
                            <label for="update_product_image">Product Image:</label>
                            <input type="file" name="update_product_image" accept="image/*">
                        </div>
                        <button type="submit" name="update_product">Update Product</button>
                    </form>
                </div>
            </section>

            <script>
                function prepareUpdateForm(id, name, price, description, brand, category, quantity, sizes, imagePath) {
                    document.getElementById('update_product_id').value = id;
                    document.getElementById('update_product_name').value = name;
                    document.getElementById('update_product_price').value = price;
                    document.getElementById('update_product_description').value = description;
                    document.getElementById('update_brand_name').value = brand;
                    document.getElementById('update_category').value = category;
                    document.getElementById('update_quantity').value = quantity;
                    document.getElementById('update_sizes').value = sizes;

                    document.getElementById('update-product-form').style.display = 'block';
                }
            </script>

            <section id="orders-section" style="display:none;">
                <h2>Your Orders</h2>
                <table class="message-table">
                    <tr>
                        <th>Order ID</th>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Total Price</th>
                        <th>Customer</th>
                        <th>Order Date</th>
                    </tr>
                    <?php if ($sellerOrders && $sellerOrders->num_rows > 0): ?>
                        <?php while ($order = $sellerOrders->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($order['OrderID']) ?></td>
                                <td><?= htmlspecialchars($order['ClothingName']) ?></td>
                                <td><?= htmlspecialchars($order['Quantity']) ?></td>
                                <td><?= htmlspecialchars(number_format($order['TotalPrice'], 2)) ?></td>
                                <td><?= htmlspecialchars($order['Username']) ?></td>
                                <td><?= htmlspecialchars(date("Y-m-d H:i:s", strtotime($order['OrderDate']))) ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6">No orders found.</td>
                        </tr>
                    <?php endif; ?>
                </table>
            </section>

            <section id="messaging-section" style="display:none;">
                <h2>Messaging</h2>
                <?php if ($sellerMessages->num_rows > 0): ?>
                    <table border="1" style="margin: 0 auto;">
                        <tr>
                            <th>Customer</th>
                            <th>Message</th>
                            <th>Timestamp</th>
                            <th>Response</th>
                        </tr>
                        <?php while ($message = $sellerMessages->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($message['CustomerName']) ?></td>
                                <td><?= htmlspecialchars($message['MessageText']) ?></td>
                                <td><?= htmlspecialchars($message['Timestamp']) ?></td>
                                <td>
                                    <form action="seller_dashboard.php" method="POST">
                                        <input type="hidden" name="customer_id" value="<?= htmlspecialchars($message['SenderID']) ?>">
                                        <textarea name="response_text" required></textarea>
                                        <input type="submit" value="Send Message">
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </table>
                <?php else: ?>
                    <p>No messages available.</p>
                <?php endif; ?>
            </section>

            <section id="profile-section" style="display:none;">
                <h2>Your Information</h2>
                <table>
                    <tr>
                        <td><strong>Name:</strong></td>
                        <td><?php echo htmlspecialchars($sellerInfo['SellerName']); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Username:</strong></td>
                        <td><?php echo htmlspecialchars($sellerInfo['Username']); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Email:</strong></td>
                        <td><?php echo htmlspecialchars($sellerInfo['Email']); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Contact No:</strong></td>
                        <td><?php echo htmlspecialchars($sellerInfo['ContactNo']); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Address:</strong></td>
                        <td><?php echo htmlspecialchars($sellerInfo['Address']); ?></td>
                    </tr>
                </table>
                <button onclick="toggleEditForm()">Edit Information</button>
            </section>
            <section id="edit-form" style="display:none;">
                <h2>Edit Your Information</h2>
                <div class="form-group">
                    <form method="post" action="" class="user-form">
                        <div class="form-group">
                            <label for="sellerName">Name:</label>
                            <input type="text" id="sellerName" name="sellerName" value="<?php echo htmlspecialchars($sellerInfo['SellerName']); ?>" required><br>
                        </div>
                        <div class="form-group">
                            <label for="username">Username:</label>
                            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($sellerInfo['Username']); ?>" required><br>
                        </div>
                        <div class="form-group">
                            <label for="email">Email:</label>
                            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($sellerInfo['Email']); ?>" required><br>
                        </div>
                        <div class="form-group">
                            <label for="contactNo">Contact No:</label>
                            <input type="text" id="contactNo" name="contactNo" value="<?php echo htmlspecialchars($sellerInfo['ContactNo']); ?>" required><br>
                        </div>
                        <div class="form-group">
                            <label for="address">Address:</label>
                            <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($sellerInfo['Address']); ?>" required><br>
                        </div>
                        <div class="form-group">
                            <button type="submit">Save Changes</button>
                            <br>
                            <br>

                            <button type="button" onclick="document.getElementById('edit-form').style.display='none'; document.getElementById('profile-section').style.display='block';">Cancel</button>
                        </div>
                    </form>
                </div>
            </section>
            <script>
                function toggleEditForm() {
                    document.getElementById("profile-section").style.display = "none";
                    document.getElementById("edit-form").style.display = "block";
                }
            </script>
        </main>
    </div>

    <script>
        function toggleAddProductForm() {
            var form = document.getElementById('add-product-form');
            form.style.display = form.style.display === 'none' ? 'block' : 'none';
        }

        function toggleForm(formId) {
            var form = document.getElementById(formId);
            form.style.display = form.style.display === 'none' ? 'block' : 'none';
        }



        document.getElementById('products-link').onclick = function() {
            document.getElementById('dashboard-section').style.display = 'none';
            document.getElementById('products-section').style.display = 'block';
            document.getElementById('orders-section').style.display = 'none';
            document.getElementById('profile-section').style.display = 'none';
        };

        document.getElementById('orders-link').onclick = function() {
            document.getElementById('dashboard-section').style.display = 'none';
            document.getElementById('products-section').style.display = 'none';
            document.getElementById('orders-section').style.display = 'block';
            document.getElementById('profile-section').style.display = 'none';
        };

        document.getElementById('profile-link').onclick = function() {
            document.getElementById('dashboard-section').style.display = 'none';
            document.getElementById('products-section').style.display = 'none';
            document.getElementById('orders-section').style.display = 'none';
            document.getElementById('profile-section').style.display = 'block';
        };

        document.getElementById('dashboard-link').onclick = function() {
            document.getElementById('dashboard-section').style.display = 'block';
            document.getElementById('products-section').style.display = 'none';
            document.getElementById('orders-section').style.display = 'none';
            document.getElementById('profile-section').style.display = 'none';
        };

        document.getElementById('messaging-link').addEventListener('click', function() {
            document.getElementById('dashboard-section').style.display = 'none';
            document.getElementById('messaging-section').style.display = 'block';
            document.getElementById('products-section').style.display = 'none';
            document.getElementById('orders-section').style.display = 'none';
            document.getElementById('profile-section').style.display = 'none';
        });


        function showAddProductForm() {
            document.getElementById('add-product-form').style.display = 'block';
            document.getElementById('update-product-form').style.display = 'none';
        }

        function showUpdateProductForm(product) {
            document.getElementById('add-product-form').style.display = 'none';
            document.getElementById('update-product-form').style.display = 'block';
            // Populate fields with product data if necessary
            document.getElementById('update_product_id').value = product.id;
            document.getElementById('update_product_name').value = product.name;
            document.getElementById('update_brand_name').value = product.brand;
            document.getElementById('update_product_description').value = product.description;
            document.getElementById('update_category').value = product.category;
            document.getElementById('update_quantity').value = product.quantity;
            document.getElementById('update_product_price').value = product.price;
            document.getElementById('update_sizes').value = product.sizes;
        }
    </script>
</body>

</html>