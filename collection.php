<?php

session_start();

require_once "db.php";


/* =====================================================
   GET PRODUCTS FROM DATABASE
===================================================== */

$sql = "SELECT
            product_id,
            product_name,
            description,
            price,
            image,
            category,
            stock
        FROM products
        ORDER BY product_id ASC";

$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Collections | DURAN'S Apparel</title>

    <link
        rel="stylesheet"
        href="style.css"
    >

    <link
        rel="stylesheet"
        href="logout.css"
    >

    <link
        rel="stylesheet"
        href="collection.css"
    >

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

        <a
            href="collection.php"
            class="active"
        >
            COLLECTIONS
        </a>

        <a href="contact.php">
            CONTACT US
        </a>

    </nav>


    <div class="account">

        <?php if (isset($_SESSION["user_id"])): ?>

            <span class="user-name">

                Hello,
                <?php
                echo htmlspecialchars(
                    $_SESSION["full_name"]
                );
                ?>

            </span>


            <a
                href="logout.php"
                class="login"
                onclick="openLogoutPopup(event);"
            >
                Log Out
            </a>

        <?php else: ?>

            <a
                href="login.php"
                class="login"
            >
                Log in
            </a>

            <a
                href="signup.php"
                class="signup"
            >
                Sign up
            </a>

        <?php endif; ?>


        <a
            href="cart.php"
            class="cart"
        >
            🛒
            <span id="cartCount">0</span>
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


        <?php if ($result && $result->num_rows > 0): ?>


            <?php while ($product = $result->fetch_assoc()): ?>

                <div
                    class="collection-card"
                    data-product-id="<?php
                        echo $product["product_id"];
                    ?>"
                >


                    <!-- =====================================
                         PRODUCT IMAGE
                    ====================================== -->

                    <div class="collection-image">

                        <?php
                        if ($product["product_id"] == 1):
                        ?>

                            <span class="product-badge">
                                NEW!
                            </span>

                        <?php endif; ?>


                        <img
                            src="<?php
                                echo htmlspecialchars(
                                    $product["image"]
                                );
                            ?>"
                            alt="<?php
                                echo htmlspecialchars(
                                    $product["product_name"]
                                );
                            ?>"
                        >

                    </div>



                    <!-- =====================================
                         PRODUCT INFORMATION
                    ====================================== -->

                    <div class="product-info">


                        <h3>

                            <?php
                            echo htmlspecialchars(
                                $product["product_name"]
                            );
                            ?>

                        </h3>


                        <p class="product-type">

                            <?php
                            echo htmlspecialchars(
                                $product["category"]
                            );
                            ?>

                        </p>



                        <div class="product-bottom">


                            <span class="price">

                                ₱<?php
                                echo number_format(
                                    $product["price"],
                                    2
                                );
                                ?>

                            </span>


                            <span
                                class="quantity"
                                id="stock-<?php
                                    echo $product["product_id"];
                                ?>"
                            >

                                Stock:
                                <?php
                                echo $product["stock"];
                                ?>

                            </span>


                        </div>



                        <!-- =================================
                             QUANTITY SELECTOR
                        ================================== -->

                        <?php if ($product["stock"] > 0): ?>

                            <div class="quantity-selector">

                                <button
                                    type="button"
                                    onclick="changeQuantity(
                                        <?php
                                        echo $product["product_id"];
                                        ?>,
                                        -1
                                    )"
                                >
                                    −
                                </button>


                                <input
                                    type="number"
                                    id="quantity-<?php
                                        echo $product["product_id"];
                                    ?>"
                                    value="1"
                                    min="1"
                                    max="<?php
                                        echo $product["stock"];
                                    ?>"
                                    readonly
                                >


                                <button
                                    type="button"
                                    onclick="changeQuantity(
                                        <?php
                                        echo $product["product_id"];
                                        ?>,
                                        1
                                    )"
                                >
                                    +
                                </button>

                            </div>



                            <!-- =================================
                                 ADD TO CART
                            ================================== -->

                            <button
                                type="button"
                                class="add-cart"
                                onclick="addToCart(
                                    <?php
                                    echo $product["product_id"];
                                    ?>
                                )"
                            >

                                ADD TO CART 🛒

                            </button>


                        <?php else: ?>

                            <button
                                type="button"
                                class="add-cart"
                                disabled
                            >

                                OUT OF STOCK

                            </button>

                        <?php endif; ?>


                    </div>

                </div>

            <?php endwhile; ?>


        <?php else: ?>

            <p>
                No products available.
            </p>

        <?php endif; ?>


    </div>

</section>



<!-- =====================================================
     LOGOUT POPUP
===================================================== -->

<div
    class="logout-overlay"
    id="logoutOverlay"
>

    <div class="logout-popup">

        <h2>
            Log Out?
        </h2>

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
     LOGIN REQUIRED POPUP
===================================================== -->

<div
    class="login-required-overlay"
    id="loginRequiredOverlay"
>

    <div class="login-required-popup">

        <div class="login-required-icon">
            🔒
        </div>

        <h2>
            Login Required
        </h2>

        <p>
            You need to log in to your DURAN'S Apparel
            account before adding products to your cart.
        </p>

        <div class="login-required-buttons">

            <button
                type="button"
                class="login-required-login"
                onclick="goToLogin();"
            >
                LOG IN
            </button>

            <button
                type="button"
                class="login-required-cancel"
                onclick="closeLoginRequired();"
            >
                CANCEL
            </button>

        </div>

    </div>

</div>

<!-- =====================================================
     FOOTER
===================================================== -->

<footer>

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

            <a href="#">
                f
            </a>

            <a href="#">
                ◎
            </a>

            <a href="#">
                ♪
            </a>

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

        <a href="track_order.php">
            Track Order
        </a>

    </div>



    <div class="copyright">

        <span>
            © 2026 DURAN'S Apparel.
            All Rights Reserved
        </span>

        <span>
            Privacy Policy |
            Terms & Conditions |
            Cookies Policy
        </span>

    </div>

</footer>



<!-- =====================================================
     JAVASCRIPT
===================================================== -->

<script>

/* =====================================================
   CHANGE QUANTITY
===================================================== */

function changeQuantity(productId, change) {

    const quantityInput =
        document.getElementById(
            "quantity-" + productId
        );


    if (!quantityInput) {
        return;
    }


    let quantity =
        parseInt(quantityInput.value);


    const max =
        parseInt(quantityInput.max);


    quantity += change;


    if (quantity < 1) {

        quantity = 1;

    }


    if (quantity > max) {

        quantity = max;

    }


    quantityInput.value = quantity;

}


/* =====================================================
   ADD TO CART
===================================================== */

function addToCart(productId) {

    const quantityInput =
        document.getElementById(
            "quantity-" + productId
        );


    if (!quantityInput) {
        return;
    }


    const quantity =
        parseInt(quantityInput.value);


    if (!quantity || quantity < 1) {

        alert(
            "Please select a valid quantity."
        );

        return;

    }


    /* =============================================
       SEND TO PHP
    ============================================= */

    const formData =
        new FormData();

    formData.append(
        "product_id",
        productId
    );

    formData.append(
        "quantity",
        quantity
    );


    fetch(
        "add_to_cart.php",
        {
            method: "POST",
            body: formData
        }
    )


    .then(response => response.json())


    .then(data => {


        /* =============================================
           LOGIN REQUIRED
        ============================================= */

        if (data.login_required) {

    openLoginRequired();

    return;

}

        /* =============================================
           ERROR
        ============================================= */

        if (!data.success) {

            alert(data.message);

            return;

        }


        /* =============================================
           SUCCESS
        ============================================= */


        /* =============================================
           RESET QUANTITY
        ============================================= */

        quantityInput.value = 1;


        /* =============================================
           UPDATE CART COUNT
        ============================================= */

        updateCartCount();

    })


    .catch(error => {

        console.error(error);

        alert(
            "Something went wrong while adding the product."
        );

    });

}


/* =====================================================
   CART COUNT
===================================================== */

function updateCartCount() {

    fetch("get_cart_count.php")

        .then(response =>
            response.json()
        )

        .then(data => {

            const cartCount =
                document.getElementById(
                    "cartCount"
                );


            if (cartCount) {

                cartCount.textContent =
                    data.count;

            }

        })

        .catch(error => {

            console.error(error);

        });

}


updateCartCount();


/* =====================================================
   LOGOUT POPUP
===================================================== */

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

    window.location.href =
        "logout.php";

}



</script>


</body>

</html>