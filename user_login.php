<?php
session_start(); // Start session to retain form values

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Capture submitted values
    $_SESSION['username'] = isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '';
}

// Check if there is any existing session data for sticky forms
$username = isset($_SESSION['username']) ? $_SESSION['username'] : '';
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
    <title>Login Page Pastimes | Revive Your Style, Relive The Past</title>
    <script src="javascript_assets/custom_alerts.js"></script> <!-- Include your custom alert script -->
    <script>
        // Check for error messages in URL parameters and show alerts
        window.onload = function() {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('error')) {
                const errorMessage = urlParams.get('error');
                customAlert.alert(errorMessage);
            }

            // Simulate a response from the server for the sake of example
            const response = JSON.parse('<?php echo isset($_GET['response']) ? $_GET['response'] : 'null'; ?>');
            if (response) {
                if (response.status === 'approved') {
                    customAlert.alert(response.message);
                    window.location.href = 'dashboard.php'; // Redirect after successful login
                } else if (response.status === 'waiting') {
                    customAlert.alert(response.message); // Inform user their account is waiting for approval
                } else if (response.status === 'error') {
                    customAlert.alert(response.message); // Show error messages
                }
            }
        };
    </script>
</head>

<body>
    <div class="login_container">
        <div class="box left_box">
            <h1>Pastimes</h1>
            <h3>Revive Your Style,</h3>
            <h3>Relive The Past</h3>
        </div>

        <div class="box right_box">
            <div class="account_prompt">
                <p>Don't have an account?</p>
                <a href="user_register.php"><button class="register_btn">Create Account</button></a>
            </div>
            <form class="login_form" action="validation.php" method="post">
                <h1>Log into Pastimes</h1>
                <h3>Enter your login details below</h3>

                <!-- Error message -->
                <?php
                if (isset($_GET['error'])) {
                    echo '<p class="error">' . htmlspecialchars($_GET['error']) . '</p>';
                }
                ?>

                <label for="username">Username</label>
                <input type="text" id="username" name="username" placeholder="Enter your username" value="<?php echo $username; ?>">

                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password">

                <button type="submit">LOGIN</button>
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
                var theDate = new Date();
                document.write("&copy; " + theDate.getFullYear());
                document.write(" - Pastimes | Last modified: ");
                var theModifiedDate = new Date(document.lastModified);
                document.write(theModifiedDate.toDateString());
            </script>
            <br>Powered by "Pastimes" Web Hosting
        </p>
    </footer>
</body>

</html>