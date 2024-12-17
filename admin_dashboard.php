<?php
session_start();
include('DBConn.php'); // Include your database connection

// Redirect if the admin is not logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Add Product
    if (isset($_POST['add_product'])) {
        $productName = $_POST['product_name'];
        $productPrice = $_POST['product_price'];
        $productDescription = $_POST['product_description'];
        $brandName = $_POST['brand_name'];
        $category = $_POST['category'];
        $quantity = $_POST['quantity'];
        $sizes = $_POST['sizes'];

        if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
            $productImage = $_FILES['product_image']['name'];
            $targetDir = __DIR__ . "/_images/";
            $targetFilePath = $targetDir . basename($productImage);

            if (move_uploaded_file($_FILES['product_image']['tmp_name'], $targetFilePath)) {
                $imagePathInDb = "_images/" . $productImage;
                $sql = "INSERT INTO tblClothes (Name, Price, Description, Brand, Category, Quantity, Sizes, ImagePath, SellerID) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sdsssissi", $productName, $productPrice, $productDescription, $brandName, $category, $quantity, $sizes, $imagePathInDb, $sellerId);

                if ($stmt->execute()) {
                    header("Location: admin_dashboard.php");
                    exit();
                } else {
                    echo "Error: " . $stmt->error;
                }
            } else {
                echo "Error moving the uploaded file.";
            }
        } else {
            echo "Error uploading file or no file selected.";
        }
    }

    // Update Product
    elseif (isset($_POST['update_product'])) {
        $productId = $_POST['update_product_id'];
        $name = $_POST['update_product_name'];
        $brand = $_POST['update_brand_name'];
        $description = $_POST['update_product_description'];
        $category = $_POST['update_category'];
        $quantity = $_POST['update_quantity'];
        $price = $_POST['update_product_price'];
        $sizes = $_POST['update_sizes'];

        $imagePath = null;
        if (isset($_FILES['update_product_image']) && $_FILES['update_product_image']['error'] === UPLOAD_ERR_OK) {
            $image = $_FILES['update_product_image']['name'];
            $targetFilePath = __DIR__ . "/_images/" . basename($image);
            if (move_uploaded_file($_FILES['update_product_image']['tmp_name'], $targetFilePath)) {
                $imagePath = "_images/" . $image;
            } else {
                echo "Error uploading the product image.";
            }
        }

        $sql = "UPDATE tblClothes SET Name = ?, Price = ?, Description = ?, Brand = ?, Category = ?, Quantity = ?, Sizes = ?";
        if ($imagePath) {
            $sql .= ", ImagePath = ?";
        }
        $sql .= " WHERE ClothesID = ?";

        $stmt = $conn->prepare($sql);
        if ($imagePath) {
            $stmt->bind_param("sdsssissi", $name, $price, $description, $brand, $category, $quantity, $sizes, $imagePath, $productId);
        } else {
            $stmt->bind_param("sdsssisii", $name, $price, $description, $brand, $category, $quantity, $sizes, $productId);
        }

        if ($stmt->execute()) {
            header("Location: admin_dashboard.php");
            exit();
        } else {
            echo "Error updating product: " . $stmt->error;
        }
    }

    // Delete Product
    elseif (isset($_POST['delete_product'])) {
        $productId = $_POST['product_id'];
        $sql = "DELETE FROM tblClothes WHERE ClothesID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $productId);

        if ($stmt->execute()) {
            header("Location: admin_dashboard.php");
            exit();
        } else {
            echo "Error deleting product: " . $stmt->error;
        }
    }
}
// Handle customer approval, rejection, deletion, addition, and update logic
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['approve_user'])) {
        $userID = $_POST['user_id'];
        $sql = "UPDATE tblUser SET isApproved = 1 WHERE UserID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $userID);
        $stmt->execute();
    } elseif (isset($_POST['reject_user'])) {
        $userID = $_POST['user_id'];
        $sql = "UPDATE tblUser SET isApproved = -1 WHERE UserID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $userID);
        $stmt->execute();
    } elseif (isset($_POST['delete_user'])) {
        $userID = $_POST['user_id'];
        $sql = "DELETE FROM tblUser WHERE UserID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $userID);
        $stmt->execute();
    } elseif (isset($_POST['add_user'])) {
        $name = $_POST['name'];
        $surname = $_POST['surname'];
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $address = $_POST['address'];

        // Check for duplicates
        $duplicateCheck = $conn->prepare("SELECT * FROM tblUser WHERE Username = ? OR Email = ?");
        $duplicateCheck->bind_param("ss", $username, $email);
        $duplicateCheck->execute();
        $result = $duplicateCheck->get_result();

        if ($result->num_rows > 0) {
            echo "<script>alert('Error: Username or Email already exists. Please try again.');</script>";
        } else {
            $sql = "INSERT INTO tblUser (Name, Surname, Username, Email, PasswordHash, Address, isApproved) VALUES (?, ?, ?, ?, ?, ?, 0)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssss", $name, $surname, $username, $email, $password, $address);
            $stmt->execute();
        }
    } elseif (isset($_POST['update_user'])) {
        $userID = $_POST['user_id'];
        $name = $_POST['name'];
        $surname = $_POST['surname'];
        $username = $_POST['username'];
        $email = $_POST['email'];
        $address = $_POST['address'];

        $sql = "UPDATE tblUser SET Name=?, Surname=?, Username=?, Email=?, Address=? WHERE UserID=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssi", $name, $surname, $username, $email, $address, $userID);
        $stmt->execute();
    }
}
// Fetch all users (including approved, rejected, and deleted)
$customers = $conn->query("SELECT * FROM tblUser");

// Approve or Decline Seller
if (isset($_GET['action']) && isset($_GET['id'])) {
    $sellerID = $_GET['id'];
    $action = $_GET['action'];

    if ($action === 'approve') {
        $isApproved = 1; // Approved
    } elseif ($action === 'decline') {
        $isApproved = -1; // Declined
    }

    // Update seller's isApproved status in the database
    $stmt = $conn->prepare("UPDATE tblSeller SET isApproved = ? WHERE SellerID = ?");
    $stmt->bind_param("ii", $isApproved, $sellerID);
    $stmt->execute();
    $stmt->close();

    // Redirect back to avoid resubmission
    header("Location: admin_dashboard.php");
    exit();
}
// Update Seller Details (Admin can edit details)
if (isset($_POST['updateSeller']) && isset($_POST['sellerID'])) {
    $sellerID = $_POST['sellerID'];
    $sellerName = $_POST['sellerName'];
    $username = $_POST['username'];
    $address = $_POST['address'];

    // Update seller's details in the database
    $stmt = $conn->prepare("UPDATE tblSeller SET SellerName = ?, Username = ?, Address = ? WHERE SellerID = ?");
    $stmt->bind_param("sssi", $sellerName, $username, $address, $sellerID);
    $stmt->execute();
    $stmt->close();

    // Redirect back to the dashboard
    header("Location: admin_dashboard.php");
    exit();
}

// Delete Seller (Admin can delete a seller)
if (isset($_GET['delete']) && isset($_GET['id'])) {
    $sellerID = $_GET['id'];

    // Delete seller from the database
    $stmt = $conn->prepare("DELETE FROM tblSeller WHERE SellerID = ?");
    $stmt->bind_param("i", $sellerID);
    $stmt->execute();
    $stmt->close();

    // Redirect back to the dashboard
    header("Location: admin_dashboard.php");
    exit();
}

// Fetch all sellers
$sellers = $conn->query("SELECT SellerID, SellerName, Email, isApproved FROM tblSeller");


// Start session if not already started
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php"); // Redirect to login if admin is not logged in
    exit();
}

// Handle addition of seller into the database
if (isset($_POST['add_seller'])) {

    // Check and assign each input value to avoid undefined key errors
    $sellerName = isset($_POST['sellerName']) ? $_POST['sellerName'] : '';
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $contactNo = $_POST['contactNo'];
    $address = $_POST['address'];

    // Generate a secure hashed password
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // Prepare SQL query to insert new seller
    $sql = "INSERT INTO tblSeller (Username, Email, Password, SellerName, ContactNo, Address, isApproved) VALUES (?, ?, ?, ?, ?, ?, 0)";
    $stmt = $conn->prepare($sql);

    // Bind parameters
    $stmt->bind_param("ssssss", $username, $email, $hashedPassword, $sellerName, $contactNo, $address);

    // Execute and handle potential errors
    if ($stmt->execute()) {
        echo "Seller added successfully!";
    } else {
        echo "Error adding seller: " . $stmt->error;
    }
}

// Handle product addition, update, and deletion
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['add_product'])) {
        // Collecting product details from the form
        $productName = $_POST['product_name'] ?? '';
        $productPrice = $_POST['product_price'] ?? 0;
        $productDescription = $_POST['product_description'] ?? '';
        $brandName = $_POST['brand_name'] ?? '';
        $category = $_POST['category'] ?? '';
        $quantity = $_POST['quantity'] ?? 0;
        $sizes = $_POST['sizes'] ?? '';

        // Ensure the image is provided
        if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
            $productImage = $_FILES['product_image']['name'];
            $targetDir = __DIR__ . "/_images/";
            $targetFilePath = $targetDir . basename($productImage);

            // Move the uploaded file to the _images directory
            if (move_uploaded_file($_FILES['product_image']['tmp_name'], $targetFilePath)) {
                // Set the image path to be stored in the database
                $imagePathInDb = "_images/" . $productImage;

                // Prepare and execute the insert statement
                $sql = "INSERT INTO tblClothes (Name, Price, Description, Brand, Category, Quantity, Sizes, ImagePath, SellerID) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sdsssissi", $productName, $productPrice, $productDescription, $brandName, $category, $quantity, $sizes, $imagePathInDb, $sellerId);
                if ($stmt->execute()) {
                    header("Location: admin_dashboard.php");
                    exit();
                } else {
                    echo "Error: " . $stmt->error;
                }
            } else {
                echo "Error moving the uploaded file.";
            }
        } else {
            echo "Error uploading file or no file selected.";
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
        $price = $_POST['update_product_price'] ?? 0;
        $sizes = $_POST['update_sizes'] ?? '';

        $imagePath = null;
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
        $sql .= " WHERE ClothesID = ?";

        // Prepare statement
        $stmt = $conn->prepare($sql);
        if ($imagePath) {
            $stmt->bind_param("sdsssissi", $name, $price, $description, $brand, $category, $quantity, $sizes, $imagePath, $productId);
        } else {
            $stmt->bind_param("sdsssisii", $name, $price, $description, $brand, $category, $quantity, $sizes, $productId);
        }

        // Execute and check for errors
        if ($stmt->execute()) {
            header("Location: admin_dashboard.php");
            exit();
        } else {
            echo "Error updating product: " . $stmt->error;
        }
    } elseif (isset($_POST['delete_product'])) {
        // Delete product code
        $productId = $_POST['product_id'];

        // Delete query
        $sql = "DELETE FROM tblClothes WHERE ClothesID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $productId);
        $stmt->execute();

        // Redirect after deletion
        header("Location: admin_dashboard.php");
        exit();
    } elseif (isset($_POST['update_product'])) {
        // Collect product details for updating
        $productId = $_POST['product_id'];
        $productName = $_POST['product_name'] ?? '';
        $productPrice = $_POST['product_price'] ?? 0;
        $productDescription = $_POST['product_description'] ?? '';
        $brandName = $_POST['brand_name'] ?? '';
        $category = $_POST['category'] ?? '';
        $quantity = $_POST['quantity'] ?? 0;
        $sizes = $_POST['sizes'] ?? '';

        // Optional: Handle image upload (if applicable)
        if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
            $productImage = $_FILES['product_image']['name'];
            $targetDir = __DIR__ . "/_images/";
            $targetFilePath = $targetDir . basename($productImage);
            move_uploaded_file($_FILES['product_image']['tmp_name'], $targetFilePath);
            $imagePathInDb = "_images/" . $productImage;

            // Update product with new image
            $sql = "UPDATE tblClothes SET Name=?, Price=?, Description=?, Brand=?, Category=?, Quantity=?, Sizes=?, ImagePath=? WHERE ClothesID=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sdsssissi", $productName, $productPrice, $productDescription, $brandName, $category, $quantity, $sizes, $imagePathInDb, $productId);
        } else {
            // Update product without changing the image
            $sql = "UPDATE tblClothes SET Name=?, Price=?, Description=?, Brand=?, Category=?, Quantity=?, Sizes=? WHERE ClothesID=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sdssssi", $productName, $productPrice, $productDescription, $brandName, $category, $quantity, $sizes, $productId);
        }

        if ($stmt->execute()) {
            header("Location: admin_dashboard.php");
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
    }
}

// Fetch all products from sellers
$sellerProductss = $conn->query("SELECT * FROM tblClothes");

// Fetch seller's orders if needed (optional)
$sellerOrders = $conn->query("SELECT o.OrderID, c.Name AS ProductName, o.Quantity, o.TotalPrice, u.Username AS CustomerName 
                               FROM tblOrder o 
                               JOIN tblClothes c ON o.OrderID = c.ClothesID 
                               JOIN tblUser u ON o.UserID = u.UserID");

// Fetch all messages from the database
/*$sql = "SELECT m.MessageText, m.Timestamp, u.Username AS SenderName 
        FROM tblMessages m
        JOIN tblUser u ON m.SenderID = u.UserID 
        ORDER BY m.Timestamp DESC";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<div class='messages'>";
    while ($row = $result->fetch_assoc()) {
        echo "<div class='message'>";
        echo "<strong>" . $row['SenderName'] . ":</strong> " . $row['MessageText'];
        echo "<br><small>" . $row['Timestamp'] . "</small>";
        echo "</div>";
    }
    echo "</div>";
} else {
    echo "No messages found.";
}*/

// Handle sending a message
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['send_message'])) {
    $messageContent = $_POST['message_content'];
    $senderID = $_SESSION['admin_id'];  // Admin's ID
    $receiverID = $_POST['receiver_id'];  // The user you're sending the message to (could be dynamic)

    // Insert message into the database
    $sql = "INSERT INTO tblMessages (SenderID, ReceiverID, MessageText) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iis", $senderID, $receiverID, $messageContent);

    if ($stmt->execute()) {
        echo "Message sent successfully.";
    } else {
        echo "Error sending message: " . $stmt->error;
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Discover unique and stylish second-hand clothing at Pastimes. Shop sustainable fashion that revives your style and relives the past, with a variety of curated pieces for every occasion.">
    <meta name="keywords" content="Pastimes, second-hand clothing, sustainable fashion, vintage clothes, thrift shop, eco-friendly apparel, affordable fashion, unique styles, clothing resale, preloved clothing, fashion for all">
    <meta name="robots" content="index, follow">
    <meta name="author" content="Pastimes">
    <meta name="revisit-after" content="30 days">
    <link rel="stylesheet" href="css_assets/admin_dashboard_styles.css">
    <title>Admin Dashboard | Pastimes Revive Your Style, Relive The Past</title>
</head>

<body>
    <header class="navbar">
        <div class="logo">
            <a href="index.php"><img src="_images/logo.png" alt="Logo" style="height: 40px;"></a>
        </div>
        <nav>
            <ul class="nav-menu">
                <li><a href="admin_dashboard.php">Home</a></li>
                <li><a href="catalog.php">Catalog</a></li>
                <li><a href="about_us.php">About Us</a></li>
                <li><a href="contact_us.php">Contact Us</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <div class="dashboard-container">
        <aside class="sidebar">
            <ul>
                <li><a href="#" id="dashboard-link">Dashboard</a></li>
                <li><a href="#" id="verify-users-link">Verify Users</a></li>
                <li><a href="#" id="verify-sellers-link">Verify Sellers</a></li>
                <li><a href="#" id="add-user-link">Add User</a></li>
                <li><a href="#" id="add-seller-link" onclick="toggleAddSellerSection()">Add Seller</a></li>
                <li><a href="#" id="items-link">Items</a></li>
                <li><a href="#" id="messaging-link">Messages</a></li>
            </ul>
        </aside>

        <main class="main-content" id="content">
            <section id="dashboard-section">
                <h2>Dashboard Overview</h2>
                <p>Welcome to the admin dashboard. Use the sidebar to navigate different sections.</p>
            </section>

            <section id="verify-sellers-section" style="display:none;">
                <h2>All Sellers</h2>
                <?php if ($sellers->num_rows > 0): ?>
                    <table>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                        <?php while ($seller = $sellers->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($seller['SellerName']); ?></td>
                                <td><?php echo htmlspecialchars($seller['Email']); ?></td>
                                <td>
                                    <?php
                                    if ($seller['isApproved'] == 0) echo 'Pending';
                                    elseif ($seller['isApproved'] == 1) echo 'Approved';
                                    elseif ($seller['isApproved'] == -1) echo 'Declined';
                                    ?>
                                </td>
                                <td>
                                    <?php if ($seller['isApproved'] == 0): ?>
                                        <a href="admin_dashboard.php?action=approve&id=<?php echo $seller['SellerID']; ?>">Approve</a>
                                        <a href="admin_dashboard.php?action=decline&id=<?php echo $seller['SellerID']; ?>">Decline</a>
                                    <?php else: ?>
                                        <span>No actions available</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </table>
                <?php else: ?>
                    <p>No sellers found.</p>
                <?php endif; ?>
            </section>

            <section id="verify-section" style="display:none;">
                <h2>All Users</h2>
                <table border="1" style="margin: 0 auto;">
                    <tr>
                        <th>UserID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Name</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                    <?php if ($customers->num_rows > 0): ?>
                        <?php while ($row = $customers->fetch_assoc()): ?>
                            <tr>
                                <td><?= $row['UserID'] ?></td>
                                <td><?= $row['Username'] ?></td>
                                <td><?= $row['Email'] ?></td>
                                <td><?= $row['Name'] ?></td>
                                <td>
                                    <?php
                                    if ($row['isApproved'] == 1) {
                                        echo 'Approved';
                                    } elseif ($row['isApproved'] == -1) {
                                        echo 'Rejected';
                                    } else {
                                        echo 'Pending';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <form method="post" action="admin_dashboard.php">
                                        <input type="hidden" name="user_id" value="<?= $row['UserID'] ?>">
                                        <?php if ($row['isApproved'] == 0): ?>
                                            <button type="submit" name="approve_user">Approve</button>
                                            <button type="submit" name="reject_user">Reject</button>
                                        <?php endif; ?>
                                        <button type="button" onclick="prepareUpdateForm('<?= $row['UserID'] ?>', '<?= $row['Name'] ?>', '<?= $row['Surname'] ?>', '<?= $row['Username'] ?>', '<?= $row['Email'] ?>', '<?= $row['Address'] ?>')">Update</button>
                                        <button type="submit" name="delete_user">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6">No users found.</td>
                        </tr>
                    <?php endif; ?>
                </table>
                <!-- Update Form (hidden initially) -->
                <div id="updateForm" style="display:none;">
                    <form method="post" action="admin_dashboard.php" class="user-form">
                        <div class="form-group">
                            <label>Name: <input type="text" name="name" id="updateName"></label>
                        </div>
                        <div class="form-group">
                            <label>Surname: <input type="text" name="surname" id="updateSurname"></label>
                        </div>
                        <div class="form-group">
                            <label>Username: <input type="text" name="username" id="updateUsername"></label>
                        </div>
                        <div class="form-group">
                            <label>Email: <input type="text" name="email" id="updateEmail"></label>
                        </div>
                        <div class="form-group">
                            <label>Address: <input type="text" name="address" id="updateAddress"></label>
                        </div>
                        <input type="hidden" name="user_id" id="updateUserId">

                        <button type="submit" name="update_user">Update</button>
                    </form>
                </div>

            </section>

            <section id="add-user-section" style="display:none;">
                <h2>Add New User</h2>
                <div class="form-group">
                    <form method="post" action="admin_dashboard.php" class="user-form">
                        <div class="form-group">
                            <label for="name">Name:</label>
                            <input type="text" id="name" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="surname">Surname:</label>
                            <input type="text" id="surname" name="surname" required>
                        </div>
                        <div class="form-group">
                            <label for="username">Username:</label>
                            <input type="text" id="username" name="username" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email:</label>
                            <input type="email" id="email" name="email" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Password:</label>
                            <input type="password" id="password" name="password" required>
                        </div>
                        <div class="form-group">
                            <label for="address">Address:</label>
                            <input type="text" id="address" name="address" required>
                            <button type="submit" name="add_user">Add User</button>
                        </div>
                    </form>
                </div>
            </section>

            <section id="add-seller-section" style="display:none;">
                <h2>Add New Seller</h2>
                <div class="form-group">
                    <form method="post" action="admin_dashboard.php" class="user-form">
                        <!-- Seller Name Field -->
                        <div class="form-group">
                            <label for="sellerName">Seller Name:</label>
                            <input type="text" id="sellerName" name="sellerName" required>
                        </div>

                        <!-- Username Field -->
                        <div class="form-group">
                            <label for="username">Username:</label>
                            <input type="text" id="username" name="username" required>
                        </div>

                        <!-- Email Field -->
                        <div class="form-group">
                            <label for="email">Email:</label>
                            <input type="email" id="email" name="email" required>
                        </div>

                        <!-- Password Field -->
                        <div class="form-group">
                            <label for="password">Password:</label>
                            <input type="password" id="password" name="password" required>
                        </div>

                        <!-- Contact Number Field -->
                        <div class="form-group">
                            <label for="contactNo">Contact No:</label>
                            <input type="text" id="contactNo" name="contactNo" required>
                        </div>

                        <!-- Address Field -->
                        <div class="form-group">
                            <label for="address">Address:</label>
                            <input type="text" id="address" name="address" required>
                        </div>
                        <br><br>
                        <!-- Submit Button -->
                        <div class="form-group">
                            <button type="submit" name="add_seller">Add Seller</button>
                        </div>
                    </form>
                </div>
            </section>


            <section id="items-section" style="display:none;">
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
                    <?php if ($sellerProductss->num_rows > 0): ?>
                        <?php while ($row = $sellerProductss->fetch_assoc()): ?>
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
                                    <form method="post" action="admin_dashboard.php">
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
                    <form method="post" action="admin_dashboard.php" enctype="multipart/form-data" class="user-form">
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
                    <form method="post" action="admin_dashboard.php" enctype="multipart/form-data" class="user-form">
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

            <section id="messaging-section" style="display:none;">
                <h2>Messaging</h2>
                <?php
                // Ensure that $admin_id is set from the session or your authentication logic
                if (isset($_SESSION['admin_id'])) {
                    $admin_id = $_SESSION['admin_id'];
                } else {
                    echo "Admin not logged in.";
                    exit;
                }

                // SQL query to fetch messages where the admin is either the sender or receiver
                $query = "SELECT m.MessageID, m.MessageText, u.Username AS Sender, m.Timestamp
              FROM tblMessages m
              JOIN tblUser u ON m.SenderID = u.UserID
              WHERE m.ReceiverID = ? OR m.SenderID = ?
              ORDER BY m.Timestamp ASC";

                // Prepare and execute the query
                $stmt = $conn->prepare($query);
                $stmt->bind_param("ii", $admin_id, $admin_id);
                $stmt->execute();
                $result = $stmt->get_result();

                // Check if there are any messages
                if ($result->num_rows > 0):
                    echo "<div class='messages-container'>"; // Container to wrap the messages
                    // Loop through the messages and display them
                    while ($message = $result->fetch_assoc()):
                        // Format timestamp for readability
                        $formatted_timestamp = date("Y-m-d H:i:s", strtotime($message['Timestamp']));
                ?>
                        <div class='message'>
                            <strong><?= htmlspecialchars($message['Sender']) ?>:</strong> <?= htmlspecialchars($message['MessageText']) ?>
                            <br><small><?= $formatted_timestamp ?></small>

                            <!-- Admin response form for each message -->
                            <form action="admin_dashboard.php" method="POST" style="margin-top: 10px;">
                                <input type="hidden" name="message_id" value="<?= htmlspecialchars($message['MessageID']) ?>">
                                <textarea name="response_text" required placeholder="Type your response here..."></textarea>
                                <br>
                                <input type="submit" value="Send Response">
                            </form>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>No messages available.</p>
                <?php endif; ?>
            </section>
        </main>
    </div>

    <script>
        function toggleForm(formId) {
            const form = document.getElementById(formId);
            form.style.display = form.style.display === "none" ? "block" : "none";
        }

        function prepareUpdateForm(id, name, price, description, brand, category, quantity, sizes, imagePath) {
            document.getElementById("update_product_id").value = id;
            document.getElementById("update_product_name").value = name;
            document.getElementById("update_brand_name").value = brand;
            document.getElementById("update_product_description").value = description;
            document.getElementById("update_category").value = category;
            document.getElementById("update_quantity").value = quantity;
            document.getElementById("update_product_price").value = price;
            document.getElementById("update_sizes").value = sizes;

            toggleForm('update-product-form'); // Display update form
        }
    </script>
    </main>
    </div>

    <script>
        document.getElementById('verify-users-link').onclick = function() {
            document.getElementById('verify-section').style.display = 'block';
            document.getElementById('verify-sellers-section').style.display = 'none';
            document.getElementById('add-user-section').style.display = 'none';
            document.getElementById('items-section').style.display = 'none';
            document.getElementById('dashboard-section').style.display = 'none';
        };

        document.getElementById('verify-sellers-link').onclick = function() {
            document.getElementById('verify-sellers-section').style.display = 'block';
            document.getElementById('verify-section').style.display = 'none';
            document.getElementById('add-user-section').style.display = 'none';
            document.getElementById('items-section').style.display = 'none';
            document.getElementById('dashboard-section').style.display = 'none';
        };

        document.getElementById('add-user-link').onclick = function() {
            document.getElementById('add-user-section').style.display = 'block';
            document.getElementById('add-seller-section').style.display = 'none';
            document.getElementById('verify-section').style.display = 'none';
            document.getElementById('verify-sellers-section').style.display = 'none';
            document.getElementById('items-section').style.display = 'none';
            document.getElementById('dashboard-section').style.display = 'none';
        };

        document.getElementById('add-seller-link').onclick = function() {
            document.getElementById('add-seller-section').style.display = 'block';
            document.getElementById('add-user-section').style.display = 'none';
            document.getElementById('verify-section').style.display = 'none';
            document.getElementById('verify-sellers-section').style.display = 'none';
            document.getElementById('items-section').style.display = 'none';
            document.getElementById('dashboard-section').style.display = 'none';
        };


        document.getElementById('items-link').onclick = function() {
            document.getElementById('items-section').style.display = 'block';
            document.getElementById('verify-section').style.display = 'none';
            document.getElementById('verify-sellers-section').style.display = 'none';
            document.getElementById('add-user-section').style.display = 'none';
            document.getElementById('dashboard-section').style.display = 'none';
        };

        document.getElementById('dashboard-link').onclick = function() {
            document.getElementById('dashboard-section').style.display = 'block';
            document.getElementById('verify-section').style.display = 'none';
            document.getElementById('verify-sellers-section').style.display = 'none';
            document.getElementById('add-user-section').style.display = 'none';
            document.getElementById('items-section').style.display = 'none';
        };
        document.getElementById('messaging-link').onclick = function() {
            document.getElementById('messaging-section').style.display = 'block';
            document.getElementById('products-section').style.display = 'none';
            document.getElementById('orders-section').style.display = 'none';
            document.getElementById('profile-section').style.display = 'none';
            document.getElementById('dashboard-section').style.display = 'none';
        };

        function toggleAddSellerSection() {
            const addUserSection = document.getElementById('add-user-section');
            if (addUserSection.style.display === 'none' || addUserSection.style.display === '') {
                addUserSection.style.display = 'block'; // Show the section
            } else {
                addUserSection.style.display = 'none'; // Hide the section
            }
        }
    </script>
</body>

</html>