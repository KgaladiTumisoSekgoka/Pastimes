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
    <title>User Login/Register Selection Pastimes | Revive Your Style, Relive The Past</title>
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
            <h3>Are you a...</h3>
            <button class="login_register_btn"><a href="user_login.php">Buyer</a></button>
            <button class="login_register_btn"><a href="seller_login.php">Seller</a></button>
            <button class="login_register_btn"><a href="admin_login.php">Admin</a></button>
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