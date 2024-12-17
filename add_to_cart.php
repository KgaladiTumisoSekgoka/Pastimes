<?php
session_start();
include 'DBConn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['itemName'], $_POST['itemPrice'], $_POST['quantity'])) {
        $itemName = $_POST['itemName'];
        $itemPrice = floatval($_POST['itemPrice']);
        $quantity = intval($_POST['quantity']);

        // Fetch the item details from the database
        $stmt = $conn->prepare("SELECT ClothesID, Quantity FROM tblClothes WHERE Name = ?");
        $stmt->bind_param("s", $itemName);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $product = $result->fetch_assoc();

            // Check if enough quantity is available
            if ($product['Quantity'] >= $quantity) {
                // Update the quantity in the database
                $newQuantity = $product['Quantity'] - $quantity;
                $updateStmt = $conn->prepare("UPDATE tblClothes SET Quantity = ? WHERE ClothesID = ?");
                $updateStmt->bind_param("ii", $newQuantity, $product['ClothesID']);

                if ($updateStmt->execute()) {
                    // Update the session cart
                    if (!isset($_SESSION['cart'])) {
                        $_SESSION['cart'] = [];
                    }

                    // Check if the item is already in the cart and update quantity
                    $itemFound = false;
                    foreach ($_SESSION['cart'] as &$cartItem) {
                        if ($cartItem['name'] === $itemName) {
                            $cartItem['quantity'] += $quantity;
                            $itemFound = true;
                            break;
                        }
                    }
                    if (!$itemFound) {
                        // Add new item to cart session
                        $_SESSION['cart'][] = [
                            'name' => $itemName,
                            'price' => $itemPrice,
                            'quantity' => $quantity
                        ];
                    }

                    // Send JSON response with updated cart information
                    echo json_encode([
                        'success' => true,
                        'message' => "$itemName added to cart.",
                        'cart' => $_SESSION['cart']
                    ]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Error updating quantity.']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Not enough quantity in stock.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Product not found.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid product data.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
