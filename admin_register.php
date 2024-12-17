<?php
// Start the session
session_start();
include "DBConn.php"; // Include your database connection file

// Initialize variables for sticky forms
$name = '';
$email = '';

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
    $name = validate($_POST['name']);
    $email = validate($_POST['email']);
    $password = validate($_POST['password']);

    // Check for empty fields
    if (empty($name)) {
        header("Location: admin_register.php?error=Name is required&name=$name&email=$email");
        exit();
    } elseif (empty($email)) {
        header("Location: admin_register.php?error=Email is required&name=$name&email=$email");
        exit();
    } elseif (empty($password)) {
        header("Location: admin_register.php?error=Password is required&name=$name&email=$email");
        exit();
    }

    // Check if the email already exists in the database
    $stmt = $conn->prepare("SELECT * FROM tblAdmin WHERE Email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // If email already exists, return an error
        header("Location: admin_register.php?error=Email is already registered&name=$name&email=$email");
        exit();
    } else {
        // If email is new, hash the password and insert the new admin into the database
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Insert the new admin record into the tblAdmin table
        $stmt = $conn->prepare("INSERT INTO tblAdmin (Name, Email, PasswordHash) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $hashedPassword);

        if ($stmt->execute()) {
            // Redirect to login page with a JavaScript alert after successful registration
            echo "<script>
                    alert('You have successfully been registered as admin');
                    window.location.href = 'admin_login.php';
                  </script>";
            exit();
        } else {
            header("Location: admin_register.php?error=An error occurred during registration. Please try again.&name=$name&email=$email");
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
        content="Discover unique and stylish second-hand clothing at Pastimes. Shop sustainable fashion that revives your style and relives the past, with a variety of curated pieces for every occasion.">
    <meta name="keywords"
        content="Pastimes, second-hand clothing, sustainable fashion, vintage clothes, thrift shop, eco-friendly apparel, affordable fashion, unique styles, clothing resale, preloved clothing, fashion for all">
    <meta name="robots" content="index, follow">
    <meta name="author" content="Pastimes">
    <meta name="revisit-after" content="30 days">
    <link rel="stylesheet" href="css_assets/styles.css">
    <title>Admin Registration Pastimes | Revive Your Style, Relive The Past</title>
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
                <a href="admin_login.php"><button class="register_btn">Login</button></a>
            </div>
            <form class="login_form" action="admin_register.php" method="post">
                <h1>Registration for Admin:</h1>
                <h3>Enter your login details below</h3>

                <!-- Error message -->
                <?php
                if (isset($_GET['error'])) {
                    echo '<p class="error">' . $_GET['error'] . '</p>';
                }
                ?>
                <label for="name">Name</label>
                <input type="text" id="name" name="name" placeholder="Enter your name here" value="<?php echo htmlspecialchars($name); ?>" required>

                <label for="email">Email:</label>
                <input type="email" name="email" placeholder="Enter email..." id="email" value="<?php echo htmlspecialchars($email); ?>" required>

                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password here" required>

                <button type="submit">REGISTER</button>
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