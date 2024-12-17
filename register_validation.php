<?php
session_start();
include "DBConn.php"; // Ensure the DB connection file name matches

$message = ''; // Initialize message variable

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Function to sanitize input data
    function sanitizeInput($data)
    {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    // Capture and sanitize input data
    $name = sanitizeInput($_POST['name']);
    $surname = sanitizeInput($_POST['surname']);
    $username = sanitizeInput($_POST['username']);
    $email = sanitizeInput($_POST['email']);
    $password = sanitizeInput($_POST['password']);
    $address = sanitizeInput($_POST['address']);

    // Store input values in session for sticky forms
    $_SESSION['name'] = $name;
    $_SESSION['surname'] = $surname;
    $_SESSION['username'] = $username;
    $_SESSION['email'] = $email;
    $_SESSION['address'] = $address;

    // Error handling for empty fields
    if (empty($name)) {
        $message = 'Name is required!';
    } elseif (empty($surname)) {
        $message = 'Surname is required!';
    } elseif (empty($username)) {
        $message = 'Username is required!';
    } elseif (empty($email)) {
        $message = 'Email is required!';
    } elseif (empty($password)) {
        $message = 'Password is required!';
    } elseif (strlen($password) < 8) { // Check password length
        $message = 'Password must be at least 8 characters!';
    } elseif (empty($address)) {
        $message = 'Address is required!';
    } else {
        // Secure password hashing
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Check for duplicate username or email
        $duplicateCheck = $conn->prepare("SELECT * FROM tblUser WHERE Username = ? OR Email = ?");
        $duplicateCheck->bind_param("ss", $username, $email);
        $duplicateCheck->execute();
        $result = $duplicateCheck->get_result();

        if ($result->num_rows > 0) {
            $message = 'Username or Email already exists. Please try again with different credentials.';
        } else {
            // Secure SQL query using prepared statements
            $stmt = $conn->prepare("INSERT INTO tblUser (Name, Surname, Username, Address, Email, PasswordHash) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssss", $name, $surname, $username, $address, $email, $hashed_password);

            // Execute the statement and check for success
            if ($stmt->execute()) {
                $message = 'Registration successful! You may now log in.';
                // Clear session values upon successful registration
                session_unset();
            } else {
                $message = 'Registration failed. Please try again.';
            }

            $stmt->close(); // Close statement
        }

        $duplicateCheck->close(); // Close duplicate check statement
    }
}

$conn->close(); // Close the database connection

// Retrieve stored session values for sticky forms
$name = isset($_SESSION['name']) ? $_SESSION['name'] : '';
$surname = isset($_SESSION['surname']) ? $_SESSION['surname'] : '';
$username = isset($_SESSION['username']) ? $_SESSION['username'] : '';
$email = isset($_SESSION['email']) ? $_SESSION['email'] : '';
$address = isset($_SESSION['address']) ? $_SESSION['address'] : '';
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
    <title>Registration Response Pastimes | Revive Your Style, Relive The Past</title>
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
            <h1>Registration Response</h1>

            <!-- Display the dynamic registration message -->
            <h3><?php echo htmlspecialchars($message); ?></h3>

            <div class="account_prompt">
                <p>Return to Registration </p>
                <button class="login_register_btn"><a href="user_register.php">Register</a></button>
            </div>

            <div class="account_prompt">
                <p>Return to Login</p>
                <button class="login_register_btn"><a href="user_login.php">Login</a></button>
            </div>
        </div>
    </div>

    <footer id="footers">
        <nav id="bottomLinks">
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="catalog.php">Catalog</a></li>
                <li><a href="about_us.html">About Us</a></li>
                <li><a href="contact_us.html">Contact Us</a></li>
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