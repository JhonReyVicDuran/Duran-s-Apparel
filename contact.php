<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us | DURAN'S Apparel</title>

    <link rel="stylesheet" href="style.css">

    <link rel="stylesheet" href="logout.css">

    <link rel="stylesheet" href="contact.css">


</head>

<body>

<!-- =====================================================
     NAVBAR
===================================================== -->

<header class="navbar">

    <div class="logo">
        <a href="index.php">
            <img src="images/logo.png" alt="Logo">
        </a>
    </div>

    <nav class="menu">

        <a href="index.php">HOME</a>

        <a href="about.php">ABOUT US</a>

        <a href="collection.php">COLLECTIONS</a>

        <a href="contact.php" class="active">
            CONTACT US
        </a>

    </nav>

    <div class="account">

    <?php if (isset($_SESSION["user_id"])): ?>

    <span class="user-name">
        Hello, <?php echo htmlspecialchars($_SESSION["full_name"]); ?>
    </span>

    <a href="logout.php" class="login" onclick="openLogoutPopup(event);">
        Log Out
    </a>

<?php else: ?>

    <a href="login.php" class="login">Log in</a>

    <a href="signup.php" class="signup">Sign up</a>

<?php endif; ?>


    <a href="cart.php" class="cart">
        🛒
    </a>

</div>

</header>


<!-- =====================================================
     HERO
===================================================== -->

<section class="contact-page">

    <div class="contact-hero">

        <h1>
            CONTACT <span>US</span>
        </h1>

        <p>
            We'd love to hear from you.
            Send us a message anytime.
        </p>

    </div>


<!-- =====================================================
     CONTACT SECTION
===================================================== -->

    <div class="contact-container">

        <!-- CONTACT INFO -->

        <div class="contact-info">

            <h2>Get In Touch</h2>

            <div class="info-box">

                <h3>📍 Address</h3>

                <p>
                    Dumaguete City, Negros Oriental,
                    Philippines
                </p>

            </div>

            <div class="info-box">

                <h3>📞 Phone</h3>

                <p>
                    +63 912 345 6789
                </p>

            </div>

            <div class="info-box">

                <h3>✉ Email</h3>

                <p>
                    duransapparel@gmail.com
                </p>

            </div>

            <div class="info-box">

                <h3>🕒 Business Hours</h3>

                <p>
                    Monday - Saturday<br>
                    8:00 AM - 6:00 PM
                </p>

            </div>

        </div>


        <!-- CONTACT FORM -->

        <div class="contact-form">

            <h2>Send a Message</h2>

            <form>

                <input
                    type="text"
                    placeholder="Full Name"
                    required
                >

                <input
                    type="email"
                    placeholder="Email Address"
                    required
                >

                <input
                    type="text"
                    placeholder="Subject"
                    required
                >

                <textarea
                    placeholder="Your Message"
                    required
                ></textarea>

                <button type="submit">
                    SEND MESSAGE
                </button>

            </form>

        </div>

    </div>


<!-- =====================================================
     SOCIALS
===================================================== -->

    <div class="social-section">

        <h2>
            We Appreciate Your Response
        </h2>

    </div>

</section>

<div class="logout-overlay" id="logoutOverlay">

    <div class="logout-popup">

        <h2>Log Out?</h2>

        <p>
            Are you sure you want to log out?
        </p>

        <div class="logout-buttons">

            <button
                type="button"
                class="logout-confirm"
                onclick="confirmLogout();"
            >
                LOG OUT
            </button>

            <button
                type="button"
                class="logout-cancel"
                onclick="closeLogoutPopup();"
            >
                CANCEL
            </button>

        </div>

    </div>

</div>

<!-- =====================================================
     FOOTER
===================================================== -->

<footer id="contact">

    <div class="footer-brand">

        <img
            src="images/logo.png"
            alt="DURAN'S Apparel"
        >


        <p>
            DURAN'S Apparel is more than just apparel.
            It's a lifestyle. Minimal designs, premium
            quality, made just for you.
        </p>


        <div class="socials">

            <a href="#">f</a>

            <a href="#">◎</a>

            <a href="#">♪</a>

        </div>

    </div>



    <div class="footer-column">

        <h3>
            SHOP
        </h3>

        <a href="#collections">
            New Arrivals
        </a>

        <a href="#collections">
            T-Shirts
        </a>

        <a href="#collections">
            Hoodies
        </a>

        <a href="#collections">
            Pants
        </a>

        <a href="#collections">
            Jackets
        </a>

        <a href="#collections">
            Accessories
        </a>

        <a href="#collections">
            Sale
        </a>

    </div>



    <div class="footer-column">

        <h3>
            CUSTOMER CARE
        </h3>

        <a href="about.php">
            About Us
        </a>

        <a href="#">
            Size Guide
        </a>

        <a href="#">
            Shipping & Delivery
        </a>

        <a href="#">
            Returns & Exchanges
        </a>

        <a href="#">
            FAQs
        </a>

        <a href="#contact">
            Contact Us
        </a>

        <a href="#">
            Track Order
        </a>

    </div>



    <div class="copyright">

        <span>
            © 2026 DURAN'S Apparel. All Rights Reserved
        </span>

        <span>
            Privacy Policy |
            Terms & Conditions |
            Cookies Policy
        </span>

    </div>

</footer>

<script>
function confirmLogout() {
    return confirm("Are you sure you want to log out?");
}

function openLogoutPopup(event) {

    event.preventDefault();

    document
        .getElementById("logoutOverlay")
        .classList.add("active");

}


function closeLogoutPopup() {

    document
        .getElementById("logoutOverlay")
        .classList.remove("active");

}


function confirmLogout() {

    window.location.href = "logout.php";

}

</script>

</body>
</html>