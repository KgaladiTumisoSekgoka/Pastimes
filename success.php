<?php
session_start();

// Get the session ID
$sessionId = session_id();

// Clear the cart after successful payment
$cartItems = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$totalAmount = array_sum(array_column($cartItems, 'price')); // Calculate total amount
unset($_SESSION['cart']); // Clear the cart

// Generate a unique order ID
$orderId = uniqid("order_");

// Assuming you have seller ID and user ID stored in the session
$sellerId = isset($_SESSION['seller_id']) ? $_SESSION['seller_id'] : 'N/A';
$userId = isset($_SESSION['id']) ? $_SESSION['id'] : 'N/A';

// Database connection
require 'DBConn.php'; // Use your DBConn.php for connection

// Insert order into tblOrders
$stmt = $conn->prepare("INSERT INTO tblorders (order_id, user_id, seller_id, session_id, total_amount) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("ssssd", $orderId, $userId, $sellerId, $sessionId, $totalAmount);

if ($stmt->execute()) {
    // Order inserted successfully, now insert order items and update stock
    $stmtItems = $conn->prepare("INSERT INTO tblOrderItems (order_id, id, item_quantity, item_price) VALUES (?, ?, ?, ?)");
    $stmtItems->bind_param("ssis", $orderId, $clothesId, $quantity, $price); // Adjust types based on your data

    foreach ($cartItems as $item) {
        // Check if 'id' key exists in $item
        $clothesId = isset($item['id']) ? $item['id'] : null; // Handle missing 'id'
        $quantity = isset($item['quantity']) ? $item['quantity'] : 1; // Default to 1 if quantity is not set
        $price = isset($item['price']) ? $item['price'] : 0; // Default to 0 if price is not set

        if ($clothesId !== null) {
            // Insert item into tblOrderItems
            if ($stmtItems->execute()) {
                // Update the quantity in tblClothes using ClothesID
                $updateStmt = $conn->prepare("UPDATE tblclothes SET quantity = quantity - ? WHERE ClothesID = ?");
                $updateStmt->bind_param("is", $quantity, $clothesId); // 'i' for integer (quantity), 's' for string (ClothesID)
                if (!$updateStmt->execute()) {
                    // Handle error
                    echo "Error updating stock: " . $updateStmt->error;
                }
                $updateStmt->close();
            } else {
                // Handle error
                echo "Error inserting item: " . $stmtItems->error;
            }
        }
    }
} else {
    // Handle error
    echo "Error inserting order: " . $stmt->error;
}

// Close the statements and connection
$stmtItems->close();
$stmt->close();
$conn->close();
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
    <link rel="stylesheet" href="css_assets/styles.css">
    <title>Payment Successful | Revive Your Style, Relive The Past</title>
</head>

<body>
    <div class="login_container">
        <div class="box left_box">
            <h1>Pastimes</h1>
            <h3>Revive Your Style,</h3>
            <h3>Relive The Past</h3>
        </div>
        <div class="box right_box">
            <h2>Payment Successful!</h2>
            <p>Thank you for your purchase. Your order is being processed.</p>
            <h3>Order Receipt</h3>
            <p>Order ID: <?php echo htmlspecialchars($orderId); ?></p>
            <p>Session ID: <?php echo htmlspecialchars($sessionId); ?></p>

            <table>
                <thead>
                    <tr>
                        <th>Item Name</th>
                        <th>Price (ZAR)</th>
                        <th>Quantity</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($cartItems)): ?>
                        <?php foreach ($cartItems as $item): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['name']); ?></td>
                                <td><?php echo number_format($item['price'], 2); ?></td>
                                <td><?php echo isset($item['quantity']) ? $item['quantity'] : 1; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3">No items in the cart.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            <h4>Total Amount: ZAR <?php echo number_format($totalAmount, 2); ?></h4>
            <a href="catalog.php">Continue Shopping</a>
        </div>
    </div>
</body>

</html>