<?php
session_start();
include('DBConn.php');

$email = isset($_POST['email']) ? $_POST['email'] : '';
$error_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $password = $_POST['password'];

    // Query to check seller credentials
    $sql = "SELECT * FROM tblSeller WHERE Email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $seller = $result->fetch_assoc();

        if (password_verify($password, $seller['Password'])) {
            if ($seller['isApproved'] == 1) {
                // If approved, proceed to login
                $_SESSION['seller_id'] = $seller['SellerID'];
                $_SESSION['seller_email'] = $seller['Email'];

                header("Location: seller_dashboard.php");
                exit();
            } else {
                // If not approved, show a message
                $error_message = "Your account is awaiting approval from the admin. Please check back later.";
            }
        } else {
            $error_message = "Invalid email or password.";
        }
    } else {
        $error_message = "Invalid email or password.";
    }
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
    <title>Seller Login Pastimes | Revive Your Style, Relive The Past</title>
</head>

<body>
    <div class="login_container">
        <div class="box left_box">
            <h1>Pastimes</h1>
            <h3>Revive Your Style,</h3>
            <h3>Relive The Past</h3>
        </div>

        <?php if (!empty($error_message)) {
            echo "<p style='color:red;'>$error_message</p>";
        } ?>

        <div class="box right_box">
            <div class="account_prompt">
                <p>Don't have an account?</p>
                <a href="seller_registration.php"><button class="register_btn">Become Seller</button></a>
            </div>

            <form class="login_form" method="post" action="seller_login.php">
                <h1>Log as Seller</h1>
                <h3>Enter your login details below</h3>

                <label for="email">Email:</label>
                <input type="email" name="email" placeholder="Enter your email here" id="email" value="<?php echo htmlspecialchars($email); ?>" required>

                <label for="password">Password:</label>
                <input type="password" name="password" placeholder="Enter your password here" id="password" required>

                <button type="submit">Login</button>
            </form>
        </div>
    </div>

    <footer id="footers">
        <nav id="bottomLinks">
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="catalog.php">Catalog</a></li>
                <li><a href="about_us.php">About Us</a></li>
                <li><a href="contact_us.php">Contact Us</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
        <p>
            <script>
                var theDate = new Date()
                document.write("&copy; " + theDate.getFullYear())
                document.write(" - Pastimes | Last modified: ")
                var theModifiedDate = new Date(document.lastModified)
                document.write(theModifiedDate.toDateString())
            </script>
            <br>Powered by "Pastimes" Web Hosting
        </p>
    </footer>
</body>

</html>