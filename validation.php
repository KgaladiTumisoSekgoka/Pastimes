<?php
session_start();
include "DBConn.php"; // Ensure the DB connection file name matches

$message = ''; // Initialize message variable
$row = null; 

if (isset($_POST['username']) && isset($_POST['password'])) {

    // Function to sanitize input data
    function validate($data)
    {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    $username = validate($_POST['username']);
    $password = validate($_POST['password']);

    // Error handling for empty username or password
    if (empty($username)) {
        $message = 'Username is required!';
    } elseif (empty($password)) {
        $message = 'Password is required!';
    } else {
        // Secure SQL query using prepared statements
        $stmt = $conn->prepare("SELECT * FROM tblUser WHERE Username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            // Verify the password using password_verify
            if (password_verify($password, $row['PasswordHash'])) {
                // Check if the account is approved
                if (isset($row['isApproved'])) { // Check if the key exists
                    if ($row['isApproved'] == 1) {
                        // User is approved
                        $_SESSION['user_name'] = $row['Username'];
                        $_SESSION['name'] = $row['Name'];
                        $_SESSION['id'] = $row['UserID'];
                        $message = 'Login successful! Welcome, ' . $_SESSION['user_name'] . '. You may now use the web app as a customer';
                    } else {
                        // User not approved yet
                        $_SESSION['user_name'] = $row['Username'];
                        $_SESSION['id'] = $row['UserID'];
                        $message = 'Your account is waiting for admin approval.';
                    }
                } else {
                    $message = 'Account approval status is unknown.';
                }
            } else {
                $message = 'Incorrect Username or Password!';
            }
        } else {
            $message = 'User not found!';
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
        content="Discover unique and stylish second-hand clothing at Pastimes. Shop sustainable fashion that revives your style and relives the past, with a variety of curated pieces for every occasion.">
    <meta name="keywords"
        content="Pastimes, second-hand clothing, sustainable fashion, vintage clothes, thrift shop, eco-friendly apparel, affordable fashion, unique styles, clothing resale, preloved clothing, fashion for all">
    <meta name="robots" content="index, follow">
    <meta name="author" content="Pastimes">
    <meta name="revisit-after" content="30 days">
    <link rel="stylesheet" href="css_assets/styles.css">
    <title>Login Response Pastimes | Revive Your Style, Relive The Past</title>
</head>

<body>
    <div class="login_container">
        <div class="box left_box">
            <h1>Pastimes</h1>
            <h3>Revive Your Style,</h3>
            <h3>Relive The Past</h3>
        </div>

        <!-- Right Box (Response Message) -->
        <div class="box right_box">
            <h1>Login Response</h1>
            <h3><?php echo htmlspecialchars($message); ?></h3>

            <div class="account_prompt">
                <p>Create Account</p>
                <button class="login_register_btn"><a href="user_register.php">Create Account</a></button>
            </div>

            <div class="account_prompt">
                <p>Return to login?</p>
                <button class="login_register_btn"><a href="user_login.php">Login</a></button>
            </div>

            <!-- Catalog Button -->
            <?php if (isset($_SESSION['user_name']) && $_SESSION['id'] && $row !== null && isset($row['isApproved']) && $row['isApproved'] == 1) : ?>
                <div class="account_prompt">
                    <p>Open Homepage</p>
                    <button class="login_register_btn"><a href="index.php">View Home Page</a></button>
                </div>
            <?php endif; ?>
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