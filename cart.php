<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Shopping Cart | DURAN'S Apparel</title>

    <link rel="stylesheet" href="style.css">

    <link rel="stylesheet" href="logout.css">

    <link rel="stylesheet" href="cart.css">
</head>

<body>

    <!-- ================= NAVBAR ================= -->

    <header class="navbar">

        <div class="logo">
            <a href="index.php">
                <img src="images/logo.png">
            </a>
        </div>

        <nav class="menu">

            <a href="index.php">HOME</a>

            <a href="about.php">ABOUT US</a>

            <a href="collection.php">COLLECTIONS</a>

            <a href="contact.php">CONTACT US</a>

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


    <!-- ================= CART PAGE ================= -->

    <main class="cart-page">

        <div class="cart-wrapper">

            <div class="cart-title">

                <h1>
                    YOUR <span>CART</span>
                </h1>

                <p>
                    Review your selected items before checkout.
                </p>

            </div>


            <!-- CART CONTENT -->

            <div class="cart-content">

                <!-- PRODUCTS -->

                <section class="cart-items-section">

                    <div class="section-header">

                        <h2>
                            Shopping Cart
                        </h2>

                        <button
                            class="clear-cart"
                            onclick="clearCart()"
                        >
                            Clear Cart
                        </button>

                    </div>


                    <div id="cartItems">

                        <!-- Products will appear here -->

                    </div>

                </section>


                <!-- ORDER SUMMARY -->

                <aside class="cart-summary">

                    <h2>
                        ORDER SUMMARY
                    </h2>

                    <div class="summary-row">

                        <span>
                            Subtotal
                        </span>

                        <span id="subtotal">
                            ₱0.00
                        </span>

                    </div>

                    <div class="summary-row">

                        <span>
                            Shipping
                        </span>

                        <span id="shipping">
                            FREE
                        </span>

                    </div>

                    <div class="summary-line"></div>

                    <div class="summary-total">

                        <span>
                            Total
                        </span>

                        <span id="total">
                            ₱0.00
                        </span>

                    </div>

                    <button
                        class="checkout-button"
                        onclick="checkout()"
                    >
                        PROCEED TO CHECKOUT
                    </button>

                    <a
                        href="collection.php"
                        class="continue-shopping"
                    >
                        ← Continue Shopping
                    </a>

                </aside>

            </div>

        </div>

    </main>

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


    <!-- ================= JAVASCRIPT ================= -->

    <script>

        let cart =
            JSON.parse(localStorage.getItem("duranCart")) || [];


        /* =====================================================
           DISPLAY CART
        ===================================================== */

        function displayCart() {

            const cartItems =
                document.getElementById("cartItems");

            if (cart.length === 0) {

                cartItems.innerHTML = `

                    <div class="empty-cart">

                        <div class="empty-cart-icon">
                            🛒
                        </div>

                        <h2>
                            Your cart is empty
                        </h2>

                        <p>
                            Looks like you haven't added
                            anything to your cart yet.
                        </p>

                        <a href="collection.php">
                            SHOP NOW
                        </a>

                    </div>

                `;

                updateSummary();

                return;
            }


            cartItems.innerHTML = "";


            cart.forEach((item, index) => {

                const itemTotal =
                    Number(item.price) * Number(item.quantity);


                const cartItem =
                    document.createElement("div");

                cartItem.className = "cart-item";


                cartItem.innerHTML = `

                    <div class="product-image">

                        <img
                            src="${item.image}"
                            alt="${item.name}"
                        >

                    </div>


                    <div class="product-info">

                        <h3>
                            ${item.name}
                        </h3>

                        <p class="product-price">
                            ₱${Number(item.price).toFixed(2)}
                        </p>

                    </div>


                    <div class="quantity">

                        <button
                            onclick="changeQuantity(${index}, -1)"
                        >
                            −
                        </button>

                        <span>
                            ${item.quantity}
                        </span>

                        <button
                            onclick="changeQuantity(${index}, 1)"
                        >
                            +
                        </button>

                    </div>


                    <div class="item-total">

                        ₱${itemTotal.toFixed(2)}

                    </div>


                    <button
                        class="remove-button"
                        onclick="removeItem(${index})"
                        title="Remove item"
                    >
                        ×
                    </button>

                `;


                cartItems.appendChild(cartItem);

            });


            updateSummary();

        }


        /* =====================================================
           CHANGE QUANTITY
        ===================================================== */

        function changeQuantity(index, amount) {

            cart[index].quantity += amount;


            if (cart[index].quantity <= 0) {

                cart.splice(index, 1);

            }


            saveCart();

        }


        /* =====================================================
           REMOVE ITEM
        ===================================================== */

        function removeItem(index) {

            cart.splice(index, 1);

            saveCart();

        }


        /* =====================================================
           CLEAR CART
        ===================================================== */

        function clearCart() {

            if (cart.length === 0) {
                return;
            }


            const confirmClear =
                confirm("Are you sure you want to clear your cart?");


            if (confirmClear) {

                cart = [];

                saveCart();

            }

        }


        /* =====================================================
           SAVE CART
        ===================================================== */

        function saveCart() {

            localStorage.setItem(
                "duranCart",
                JSON.stringify(cart)
            );

            displayCart();

            updateCartCount();

        }


        /* =====================================================
           UPDATE SUMMARY
        ===================================================== */

        function updateSummary() {

            let subtotal = 0;


            cart.forEach(item => {

                subtotal +=
                    Number(item.price) *
                    Number(item.quantity);

            });


            document.getElementById("subtotal").textContent =
                "₱" + subtotal.toFixed(2);


            document.getElementById("total").textContent =
                "₱" + subtotal.toFixed(2);

        }


        /* =====================================================
           UPDATE CART COUNT
        ===================================================== */

        function updateCartCount() {

            const count =
                cart.reduce(
                    (total, item) =>
                        total + Number(item.quantity),
                    0
                );


            document.getElementById("cartCount").textContent =
                count;

        }


        /* =====================================================
           CHECKOUT
        ===================================================== */

        function checkout() {

            if (cart.length === 0) {

                alert(
                    "Your cart is empty. Please add a product first."
                );

                return;

            }


            alert(
                "Checkout will be available soon!"
            );

        }


        /* =====================================================
           INITIALIZE
        ===================================================== */

        displayCart();

        updateCartCount();

    
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