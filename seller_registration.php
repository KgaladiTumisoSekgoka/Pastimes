<?php
// Start the session
session_start();
include "DBConn.php"; // Include your database connection file

// Initialize variables for sticky forms
$sellerName = '';
$email = '';
$contactNo = '';
$address = '';
$username = '';

// Check if the form has been submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Function to validate and sanitize input data
    function validate($data)
    {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    // Retrieve and validate the input values
    $sellerName = validate($_POST['sellerName']);
    $username = validate($_POST['username']);
    $email = validate($_POST['email']);
    $password = validate($_POST['password']);
    $contactNo = validate($_POST['contactNo']);
    $address = validate($_POST['address']);

    // Check for empty fields
    if (empty($sellerName) || empty($username) || empty($email) || empty($password) || empty($contactNo) || empty($address)) {
        header("Location: seller_registration.php?error=All fields are required&sellerName=$sellerName&username=$username&email=$email&contactNo=$contactNo&address=$address");
        exit();
    }

    // Check if the email already exists in the database
    $stmt = $conn->prepare("SELECT * FROM tblSeller WHERE Email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // If email already exists, return an error
        header("Location: seller_registration.php?error=Email is already registered&sellerName=$sellerName&username=$username&email=$email&contactNo=$contactNo&address=$address");
        exit();
    } else {
        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Insert the new seller record into the tblSeller table
        $stmt = $conn->prepare("INSERT INTO tblSeller (SellerName, Username, Email, Password, ContactNo, Address) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $sellerName, $username, $email, $hashedPassword, $contactNo, $address);

        if ($stmt->execute()) {
            // Redirect to login page with a JavaScript alert after successful registration
            echo "<script>
                    alert('You have successfully registered as a seller.');
                    window.location.href = 'seller_login.php';
                  </script>";
            exit();
        } else {
            header("Location: seller_registration.php?error=An error occurred during registration. Please try again.&sellerName=$sellerName&username=$username&email=$email&contactNo=$contactNo&address=$address");
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description"
        content="Discover unique and stylish second-hand clothing at Pastimes. Shop sustainable fashion that revives your style and relives the past.">
    <meta name="keywords"
        content="Pastimes, second-hand clothing, sustainable fashion, vintage clothes, thrift shop, eco-friendly apparel, affordable fashion, unique styles, clothing resale, preloved clothing, fashion for all">
    <meta name="robots" content="index, follow">
    <meta name="author" content="Pastimes">
    <meta name="revisit-after" content="30 days">
    <link rel="stylesheet" href="css_assets/styles.css">
    <title>Seller Registration | Revive Your Style, Relive The Past</title>
</head>

<body>
    <div class="login_container">
        <!-- Left Box -->
        <div class="box left_box">
            <h1>Pastimes</h1>
            <h3>Revive Your Style,</h3>
            <h3>Relive The Past</h3>
        </div>

        <!-- Right Box (Login Form) -->
        <div class="box right_box">
            <div class="account_prompt">
                <p>Already have an account?</p>
                <a href="seller_login.php"><button class="register_btn">Login</button></a>
            </div>
            <form class="login_form" action="seller_registration.php" method="post">
                <h1>Seller Registration:</h1>
                <h3>Enter your details below</h3>

                <!-- Error message -->
                <?php
                if (isset($_GET['error'])) {
                    echo '<p class="error">' . $_GET['error'] . '</p>';
                }
                ?>

                <label for="sellerName">Seller Name</label>
                <input type="text" id="sellerName" name="sellerName" placeholder="Enter your seller name" value="<?php echo htmlspecialchars($sellerName); ?>" required>

                <label for="username">Username</label>
                <input type="text" id="username" name="username" placeholder="Enter your username" value="<?php echo htmlspecialchars($username); ?>" required>

                <label for="email">Email:</label>
                <input type="email" id="email" name="email" placeholder="Enter your email" value="<?php echo htmlspecialchars($email); ?>" required>

                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>

                <label for="contactNo">Contact No</label>
                <input type="text" id="contactNo" name="contactNo" placeholder="Enter your contact number" value="<?php echo htmlspecialchars($contactNo); ?>" required>

                <label for="address">Address</label>
                <input type="text" id="address" name="address" placeholder="Enter your address" value="<?php echo htmlspecialchars($address); ?>" required>

                <button type="submit">REGISTER</button>
            </form>
        </div>
    </div>
    
</body>

</html>