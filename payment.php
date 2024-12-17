<?php
session_start();
include 'DBConn.php';

// Check if ClothesID is set in the URL
if (isset($_GET['ClothesID'])) {
    $ClothesID = intval($_GET['ClothesID']); 

    // Fetch product details from the database
    $query = "SELECT * FROM tblClothes WHERE ClothesID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $ClothesID);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if a product was found
    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
    } else {
        echo "Product not found.";
        exit();
    }
} else {
    echo "No product selected.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment - <?php echo htmlspecialchars($product['Name']); ?></title>
    <link rel="stylesheet" href="css_assets/styles.css">
</head>

<body>
    <div class="container">
        <h2>Complete Your Purchase</h2>
        <div class="product-details">
            <h3>Product: <?php echo htmlspecialchars($product['Name']); ?></h3>
            <p>Description: <?php echo htmlspecialchars($product['Description']); ?></p>
            <p>Price: R<?php echo number_format($product['Price'], 2); ?></p>
        </div>

        <!-- Payment form -->
        <form class="payment-form" action="process_payment.php" method="post">
            <input type="hidden" name="ClothesID" value="<?php echo $ClothesID; ?>">
            <div class="form-group">
                <label for="cardNumber">Card Number:</label>
                <input type="text" id="cardNumber" name="cardNumber" required>
            </div>

            <div class="form-group">
                <label for="expiryDate">Expiry Date:</label>
                <input type="text" id="expiryDate" name="expiryDate" placeholder="MM/YY" required>
            </div>

            <div class="form-group">
                <label for="cvv">CVV:</label>
                <input type="text" id="cvv" name="cvv" required>
            </div>

            <button type="submit" class="pay-now-btn">Pay Now</button>
        </form>
    </div>
</body>

</html>