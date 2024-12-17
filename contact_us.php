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
    <link rel="stylesheet" type="text/css" href="css_assets/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Contact Us | Pastimes - Revive Your Style, Relive The Past</title>
</head>

<body>
    <header>
        <div class="navbar">
            <div class="logo">
                <a href="index.php"><img src="_images/logo.png" alt="Logo" style="height: 30px;"></a>
            </div>
            <div class="search-bar">
                <form method="GET" action="search_result.php">
                    <input type="text" name="query" placeholder="Search">
                    <button type="submit"><i class="fa fa-search" aria-hidden="true"></i></button>
                </form>
            </div>
            <nav>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="catalog.php">Catalog</a></li>
                    <li><a href="about_us.php">About Us</a></li>
                    <li><a href="contact_us.php">Contact Us</a></li>
                    <li><a href="login_register_selection.php">Login/Register</a></li>
                    <li><a href="user_messaging.php" target="_blank"><i class="fa fa-comments" aria-hidden="true"></i></a></li>
                    <li><a href="user_profile.php" target="_blank"><i class="fa fa-user" aria-hidden="true"></i></a></li>
                </ul>
            </nav>
        </div>
    </header>
    <main></main>
    <h1 class="head_text" align="center">Contact <span>Us</span></h1>
    <article id="col1">
        <iframe
            src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d3582.1223388244894!2d27.9020848!3d-26.1275538!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x1e959fedad33971b%3A0x922bd56169f1057c!2sClearwater%20Mall!5e0!3m2!1sen!2sza!4v1727864946822!5m2!1sen!2sza"
            width="1400" height="400" style="border:0;" allowfullscreen="" loading="lazy"
            referrerpolicy="no-referrer-when-downgrade"></iframe>
    </article>
    <article id="col2" align="left">
        <div class="form-container">
            <h2 class="head_text">Join Our Services</h2>
            <form id="google_form" onsubmit="return validateForm(event)">
                <table>
                    <tr>
                        <td><label for="myName">Name:</label></td>
                        <td><input type="text" name="myName" id="myName" placeholder="Your name here" required></td>
                    </tr>
                    <tr>
                        <td><label for="myEmail">Email:</label></td>
                        <td><input type="email" name="myEmail" id="myEmail" placeholder="Your email here" required></td>
                    </tr>
                    <tr>
                        <td><label for="myProv">Province:</label></td>
                        <td>
                            <select name="myProv" id="myProv" required>
                                <option value="">Choose..</option>
                                <option value="1">Gauteng</option>
                                <option value="2">KwaZulu-Natal</option>
                                <option value="3">Western Cape</option>
                                <option value="4">Northern Cape</option>
                                <option value="5">Eastern Cape</option>
                                <option value="6">Free State</option>
                                <option value="7">Limpopo</option>
                                <option value="8">Mpumalanga</option>
                                <option value="9">North West</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><label for="address">Address:</label></td>
                        <td><input type="text" id="address" name="address" required></td>
                    </tr>
                    <tr>
                        <td><label for="contactMethod">Preferred Contact Method:</label></td>
                        <td>
                            <select name="contactMethod" id="contactMethod" required>
                                <option value="">Choose..</option>
                                <option value="email">Email</option>
                                <option value="phone">Phone</option>
                                <option value="sms">SMS</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><label>Clothing Style Interested in:</label></td>
                        <td>
                            <input type="checkbox" value="formalwear" name="services">Formalwear<br>
                            <input type="checkbox" value="streetwear" name="services">Streetwear<br>
                            <input type="checkbox" value="bohemian" name="services">Bohemian<br>
                            <input type="checkbox" value="traditional" name="services">Traditional Clothing<br>
                        </td>
                    </tr>
                    <tr>
                        <td><label>Availability for Purchasing Clothing:</label></td>
                        <td>
                            <input type="radio" name="availability" value="morning" required>Morning<br>
                            <input type="radio" name="availability" value="afternoon" required>Afternoon<br>
                            <input type="radio" name="availability" value="evening" required>Evening<br>
                        </td>
                    </tr>
                    <tr>
                        <td><label for="additionalInfo">Additional Information:</label></td>
                        <td><textarea id="additionalInfo" name="additionalInfo" rows="4" cols="50"></textarea></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>
                            <input type="reset" value="Clear">
                            <input type="submit" value="Join">
                        </td>
                    </tr>
                </table>
            </form>
        </div>
    </article>
    <article id="col3"></article>
    <br class="clearFix">
    <!-- Google Forms -->
    <iframe
        src="https://docs.google.com/forms/d/e/1FAIpQLSfTq0YYy_FjYK1ZyuH2DsYTqF33n5mEGr5Ys_wUwL1r4DFi5w/viewform?embedded=true"
        width="700" height="520" frameborder="0" marginheight="0" marginwidth="0">Loading…</iframe>
    </main>
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="footer-col">
                    <h4>quick links</h4>
                    <ul>
                        <li><a href="index.php">Home</a></li>
                        <li><a href="catalog.php">Catalog</a></li>
                        <li><a href="about_us.php">About Us</a></li>
                        <li><a href="contact_us.php">Contact Us</a></li>
                        <li><a href="login_register_selection.php">Login/Register</a></li>
                        <li><a href="logout.php">Logout</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>contact us</h4>
                    <ul>
                        <li>Email: pastimes@gmail.com <a href="mailto:pastimes@gmail.com"></a></li>
                        <li>Phone: +27 65 937 7912 <a href="tel:+27 65 937 7912"></a></li>
                        <li>Address: 7 Wolf Street, Ferdale, Fourways, South Africa</li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>follow us</h4>
                    <div class="social-links">
                        <a href="https://web.facebook.com/BMWSA" target="_blank"><i class="fab fa-facebook-f"></i></a>
                        <a href="https://x.com/BMW_SA" target="_blank"><i class="fab fa-twitter"></i></a>
                        <a href="https://www.instagram.com/bmwsouthafrica/" target="_blank"><i
                                class="fab fa-instagram"></i></a>
                        <a href="https://www.linkedin.com/showcase/bmwsouthafrica/posts/?feedView=all"
                            target="_blank"><i class="fab fa-linkedin"></i></a>
                    </div>
                </div>
                <div class="footer-col">
                    <h4>Working Hours</h4>
                    <ul>
                        <li>Monday - Friday: 9 AM - 6 PM</li>
                        <li>Saturday: 10 AM - 4 PM</li>
                        <li>Sunday: Closed</li>
                    </ul>
                </div>
            </div>
        </div>
    </footer>

    <script>
        function validateForm(event) {
            event.preventDefault(); 

            var name = document.getElementById("myName").value;
            var email = document.getElementById("myEmail").value;
            var province = document.getElementById("myProv").value;
            var address = document.getElementById("address").value;
            var contactMethod = document.getElementById("contactMethod").value;
            var additionalInfo = document.getElementById("additionalInfo").value;

            if (name == "" || name == "Your name here") {
                alert("Please enter your name.");
                return false;
            }
            if (email == "" || email == "Your email here") {
                alert("Please enter a valid email address.");
                return false;
            }
            if (province == "0") {
                alert("Please select your province.");
                return false;
            }
            if (address == "") {
                alert("Please enter your address.");
                return false;
            }
            if (contactMethod == "0") {
                alert("Please select a preferred contact method.");
                return false;
            }
            alert("Form submitted successfully!");
            return true;
        }
    </script>
</body>

</html>