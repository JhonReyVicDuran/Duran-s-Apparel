<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Collections | DURAN'S Apparel</title>

    <link rel="stylesheet" href="style.css">

    <link rel="stylesheet" href="logout.css">

    <link rel="stylesheet" href="collection.css">

</head>

<body>


<!-- =====================================================
     NAVIGATION
===================================================== -->

<header class="navbar">

    <div class="logo">

        <a href="index.php">

            <img
                src="images/logo.png"
                alt="DURAN'S Logo"
            >

        </a>

    </div>


    <nav class="menu">

        <a href="index.php">
            HOME
        </a>

        <a href="about.php">
            ABOUT US
        </a>

        <a href="collection.php" class="active">
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
     COLLECTION HERO
===================================================== -->

<section class="collection-hero">

    <div class="collection-hero-content">

        <p>
            DURAN'S APPAREL
        </p>

        <h1>
            OUR <span>PRODUCTS</span>
        </h1>

        <div class="hero-line"></div>

        <p class="hero-description">
            Discover our latest products.
            Designed for comfort, style, and everyday wear.
            Be what you wear.
        </p>

    </div>

</section>



<!-- =====================================================
     PRODUCTS
===================================================== -->

<section class="collection-page">

    <div class="collection-heading">

        <h2>
            DISCOVER OUR COLLECTIONS
        </h2>

    </div>


    <div class="collection-grid">


        <!-- =================================================
             HOODIE
        ================================================== -->

        <div class="collection-card">

            <div class="collection-image">

                <span class="product-badge">
                    NEW!
                </span>

                <img
                    src="images/hoodie.png"
                    alt="DURAN'S Hoodie"
                >

            </div>

            <div class="product-info">

                <h3>
                    Hoodie
                </h3>

                <p class="product-type">
                    Hoodie
                </p>

                <div class="product-bottom">

                    <span class="price">
                        ₱599
                    </span>

                    <span class="quantity">
                        Stock: 10
                    </span>

                </div>

                <button class="add-cart">
                    ADD TO CART 🛒
                </button>

            </div>

        </div>



        <!-- =================================================
             CAP
        ================================================== -->

        <div class="collection-card">

            <div class="collection-image">

                <img
                    src="images/cap.png"
                    alt="DURAN'S Cap"
                >

            </div>

            <div class="product-info">

                <h3>
                    Cap
                </h3>

                <p class="product-type">
                    Cap
                </p>

                <div class="product-bottom">

                    <span class="price">
                        ₱159
                    </span>

                    <span class="quantity">
                        Stock: 10
                    </span>

                </div>

                <button class="add-cart">
                    ADD TO CART 🛒
                </button>

            </div>

        </div>



        <!-- =================================================
             T-SHIRT
        ================================================== -->

        <div class="collection-card">

            <div class="collection-image">

                <img
                    src="images/tshirt.png"
                    alt="DURAN'S T-Shirt"
                >

            </div>

            <div class="product-info">

                <h3>
                    T-Shirt
                </h3>

                <p class="product-type">
                    T-Shirt
                </p>

                <div class="product-bottom">

                    <span class="price">
                        ₱289
                    </span>

                    <span class="quantity">
                        Stock: 10
                    </span>

                </div>

                <button class="add-cart">
                    ADD TO CART 🛒
                </button>

            </div>

        </div>



        <!-- =================================================
             SHORTS
        ================================================== -->

        <div class="collection-card">

            <div class="collection-image">

                <img
                    src="images/shorts.png"
                    alt="DURAN'S Shorts"
                >

            </div>

            <div class="product-info">

                <h3>
                    Shorts
                </h3>

                <p class="product-type">
                    Shorts
                </p>

                <div class="product-bottom">

                    <span class="price">
                        ₱249
                    </span>

                    <span class="quantity">
                        Stock: 10
                    </span>

                </div>

                <button class="add-cart">
                    ADD TO CART 🛒
                </button>

            </div>

        </div>



        <!-- =================================================
             LONG SLEEVE
        ================================================== -->

        <div class="collection-card">

            <div class="collection-image">

                <img
                    src="images/long-sleeve.png"
                    alt="DURAN'S Long Sleeve"
                >

            </div>

            <div class="product-info">

                <h3>
                    Long Sleeve
                </h3>

                <p class="product-type">
                    Long Sleeve
                </p>

                <div class="product-bottom">

                    <span class="price">
                        ₱479
                    </span>

                    <span class="quantity">
                        Stock: 10
                    </span>

                </div>

                <button class="add-cart">
                    ADD TO CART 🛒
                </button>

            </div>

        </div>



        <!-- =================================================
             COMPRESSION SHIRT
        ================================================== -->

        <div class="collection-card">

            <div class="collection-image">

                <img
                    src="images/compression.png"
                    alt="DURAN'S Compression Shirt"
                >

            </div>

            <div class="product-info">

                <h3>
                    Compression Shirt
                </h3>

                <p class="product-type">
                    Compression Shirt
                </p>

                <div class="product-bottom">

                    <span class="price">
                        ₱299
                    </span>

                    <span class="quantity">
                        Stock: 10
                    </span>

                </div>

                <button class="add-cart">
                    ADD TO CART 🛒
                </button>

            </div>

        </div>


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

        <a href="collection.php">
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

        <a href="contact.php">
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