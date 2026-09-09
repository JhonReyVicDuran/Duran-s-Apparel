<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>DURAN'S Apparel</title>

    <link rel="stylesheet" href="css/style.css">

    <link rel="stylesheet" href="css/logout.css">

</head>

<body>


<!-- =====================================================
     NAVIGATION
===================================================== -->

<header class="navbar">

    <div class="logo">
        <a href="index.php">
            <img src="admin/images/logo.png" 
            alt="DURAN'S Logo">
        </a>
    </div>


    <nav class="menu">

    <a href="index.php" class="active">
        HOME
    </a>

    <a href="about.php">
        ABOUT US
    </a>

    <a href="collection.php">
        COLLECTIONS
    </a>

    <a href="contact.php">
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
     HERO SECTION
===================================================== -->

<section class="hero" id="home">

    <img
        src="admin/images/hoodies.png"
        alt="DURAN'S Hoodies"
        class="hero-hoodies"
    >


    <div class="hero-content">

        <h2>
            DURAN'S Apparel
        </h2>

        <h3>
            New Collection
        </h3>

        <a href="collection.php" class="hero-button">

            Buy <span>now</span>

            <span>🛒</span>

        </a>

    </div>

</section>



<!-- =====================================================
     COLLECTIONS
===================================================== -->

<section class="collections" id="collections">

    <div class="section-heading">

        <h2>
            COLLECTIONS
        </h2>

    </div>


    <div class="products">


        <!-- HOODIE -->

        <div class="product">

            <span class="new">
                New!
            </span>

            <img
                src="admin/images/hoodie.png"
                alt="Hoodie"
            >

        </div>



        <!-- CAP -->

        <div class="product">

            <img
                src="admin/images/cap.png"
                alt="Cap"
            >

        </div>



        <!-- T-SHIRT -->

        <div class="product">

            <img
                src="admin/images/tshirt.png"
                alt="T-Shirt"
            >

        </div>



        <!-- SHORTS -->

        <div class="product">

            <img
                src="admin/images/shorts.png"
                alt="Shorts"
            >

        </div>



        <!-- LONG SLEEVE -->

        <div class="product">

            <img
                src="admin/images/long-sleeve.png"
                alt="Long Sleeve"
            >

        </div>



        <!-- COMPRESSION SHIRT -->

        <div class="product">

            <img
                src="admin/images/compression.png"
                alt="Compression"
            >

        </div>


    </div>

</section>



<!-- =====================================================
     ABOUT SECTION
===================================================== -->

<section class="about" id="about">

    <div class="about-text">

        <h2>
            About
            <span>DURAN'S</span>
            Apparel!
        </h2>


        <p>
            Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do
            eiusmod tempor incididunt ut labore et dolore magna aliqua.
            Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris
            nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in
            reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla
            pariatur. Excepteur sint occaecat cupidatat non proident, sunt in 
            culpa qui o cia deserunt mollit anim id est laborum.
        </p>


        <a href="about.php" class="red-button">
            MORE ABOUT US
        </a>

    </div>



    <div class="about-logo">

        <img
            src="admin/images/logored2.png"
            alt="DURAN'S Apparel Logo"
        >

    </div>

</section>



<!-- =====================================================
     ELEVATE YOUR STYLE
===================================================== -->

<section class="elevate">

    <div class="model">

        <img
            src="admin/images/model.png"
            alt="DURAN'S Fashion Model"
        >

    </div>


    <div class="elevate-text">

        <h2>
            ELEVATE YOUR
            <span>STYLE</span>
        </h2>


        <p>
            Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do
            eiusmod tempor incididunt ut labore et dolore magna aliqua.
            Ut enim ad minim veniam, quis nostrud exercitation ullamco 
            nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor 
            reprehenderit in voluptate velit esse cillum dolore eu fugiat.
        </p>


        <a href="collection.php" class="black-button">
            GET YOURS NOW
        </a>

    </div>

</section>



<!-- =====================================================
     FASHION STATEMENT
===================================================== -->

<section class="style-section">

    <div class="style-text">

        <h2>
            ELEVATE YOUR
            <span>STYLE</span>
        </h2>

        <h2>
            WALK WITH
            <span>FASHION</span>
        </h2>

        <h2>
            BE WHAT YOU
            <span>WEAR</span>
        </h2>

    </div>



    <div class="style-logo">

        <img
            src="admin/images/logored.png"
            alt="DURAN'S Logo"
        >

    </div>



    <div class="style-button">

        <a href="collection.php" class="red-button">
            SHOP NOW
        </a>

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
            src="admin/images/logo.png"
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

        <a href="collection.php">
            T-Shirts
        </a>

        <a href="collection.php">
            Hoodies
        </a>

        <a href="collection.php">
            Pants
        </a>

        <a href="collection.php">
            Jackets
        </a>

        <a href="collection.php">
            Accessories
        </a>

        <a href="collection.php">
            Sale
        </a>

    </div>



    <div class="footer-column">

        <h3>
            CUSTOMER CARE
        </h3>

        <a href="#about">
            About Us
        </a>

        <a href="size_guide.php">
            Size Guide
        </a>

        <a href="faq.php">
            FAQs
        </a>

        <a href="contact.php">
            Contact Us
        </a>

        <a href="track_order.php">
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