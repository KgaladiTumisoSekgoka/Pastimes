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
    <title>User Registration Pastimes | Revive Your Style, Relive The Past</title>
</head>

<body>
    <div class="login_container">
        <!-- Left Box -->
        <div class="box left_box">
            <h1>Pastimes</h1>
            <h3>Revive Your Style,</h3>
            <h3>Relive The Past</h3>
        </div>

        <!-- Right Box -->
        <div class="box right_box">
            <div class="account_prompt">
                <p>Already have an account?</p>
                <a href="user_login.php"><button class="register_btn">Login</button></a>
            </div>
            <form class="login_form" action="register_validation.php" method="post">
                <h1>Registration:</h1>
                <h3>Enter your details below to register</h3>

                <!-- Error message -->
                <?php
                if (isset($_GET['error'])) {
                    echo '<p class="error">' . htmlspecialchars($_GET['error']) . '</p>';
                }

                // Capture submitted values if available
                $name = isset($_POST['name']) ? htmlspecialchars($_POST['name']) : '';
                $surname = isset($_POST['surname']) ? htmlspecialchars($_POST['surname']) : '';
                $username = isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '';
                $address = isset($_POST['address']) ? htmlspecialchars($_POST['address']) : '';
                $email = isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '';
                ?>

                <label for="name">Name</label>
                <input type="text" id="name" name="name" placeholder="Enter your name here" value="<?php echo $name; ?>">

                <label for="surname">Surname</label>
                <input type="text" id="surname" name="surname" placeholder="Enter your surname here" value="<?php echo $surname; ?>">

                <label for="username">Username</label>
                <input type="text" id="username" name="username" placeholder="Enter your username here" value="<?php echo $username; ?>">

                <label for="address">Address</label>
                <input type="text" id="address" name="address" placeholder="Enter your address here" value="<?php echo $address; ?>">

                <label for="email">Email</label>
                <input type="text" id="email" name="email" placeholder="Enter your email here" value="<?php echo $email; ?>">

                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password here">

                <button type="submit">REGISTER</button>
            </form>
        </div>
    </div>
</body>

</html>