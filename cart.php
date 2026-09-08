<?php
session_start();
require_once "db.php";

/* =====================================================
   CHECK LOGIN
===================================================== */

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$user_id = intval($_SESSION["user_id"]);


/* =====================================================
   GET USER CART
===================================================== */

$sql = "
    SELECT 
        c.cart_item_id,
        c.product_id,
        c.quantity,
        p.product_name,
        p.description,
        p.price,
        p.image,
        p.stock
    FROM cart_items c
    INNER JOIN products p
        ON c.product_id = p.product_id
    WHERE c.user_id = ?
    ORDER BY c.created_at DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();

$cart_items = [];

while ($row = $result->fetch_assoc()) {

    /*
       Prevent displaying a quantity greater
       than the current available stock.
    */

    if ($row["quantity"] > $row["stock"]) {
        $row["quantity"] = intval($row["stock"]);
    }

    $cart_items[] = $row;
}

$stmt->close();


/* =====================================================
   CALCULATE TOTAL
===================================================== */

$subtotal = 0;
$total_items = 0;

foreach ($cart_items as $item) {

    $item_total =
        floatval($item["price"]) *
        intval($item["quantity"]);

    $subtotal += $item_total;

    $total_items += intval($item["quantity"]);
}

$total = $subtotal;


/* =====================================================
   USER INFORMATION
===================================================== */

$full_name = $_SESSION["full_name"] ?? "User";
$email = $_SESSION["email"] ?? "";


/* =====================================================
   CART COUNT
===================================================== */

$cart_count = 0;

$count_stmt = $conn->prepare("
    SELECT COALESCE(SUM(quantity), 0) AS total
    FROM cart_items
    WHERE user_id = ?
");

$count_stmt->bind_param("i", $user_id);
$count_stmt->execute();

$count_result = $count_stmt->get_result();
$count_row = $count_result->fetch_assoc();

$cart_count = intval($count_row["total"]);

$count_stmt->close();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Your Cart | DURAN'S Apparel</title>

    <link rel="stylesheet" href="style.css">

    <link rel="stylesheet" href="logout.css">

    <link rel="stylesheet" href="cart.css">

</head>

<body>


<!-- =====================================================
     NAVBAR
===================================================== -->

<header class="navbar">

    <div class="logo">

        <a href="index.php">

            <img
                src="images/logo.png"
                alt="DURAN'S Apparel Logo"
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

        <a href="collection.php">
            COLLECTIONS
        </a>

        <a href="contact.php">
            CONTACT US
        </a>

    </nav>


    <div class="account">

        <span class="user-name">
            Hello, <?= htmlspecialchars($full_name); ?>
        </span>


        <!-- LOG OUT -->

        <a
            href="logout.php"
            class="login logout-link"
            onclick="openLogoutPopup(event);"
        >
            Log Out
        </a>


        <!-- CART -->

        <a
            href="cart.php"
            class="cart cart-active"
        >

            🛒

            <span id="cartCount">
                <?= $cart_count; ?>
            </span>

        </a>

    </div>

</header>



<!-- =====================================================
     CART PAGE
===================================================== -->

<main class="cart-page">

    <div class="cart-wrapper">


        <!-- =================================================
             TITLE
        ================================================= -->

        <div class="cart-title">

            <h1>
                Your <span>Cart</span>
            </h1>

            <p>
                Review your selected DURAN'S Apparel products before checkout.
            </p>

        </div>



        <?php if (count($cart_items) > 0): ?>


        <!-- =================================================
             CART CONTENT
        ================================================= -->

        <div class="cart-content">


            <!-- =================================================
                 CART ITEMS
            ================================================= -->

            <section class="cart-items-section">


                <div class="section-header">

                    <h2>
                        Shopping Cart
                    </h2>


                    <button
                        type="button"
                        class="clear-cart"
                        onclick="clearCart();"
                    >
                        CLEAR CART
                    </button>

                </div>



                <div id="cartItems">


                    <?php foreach ($cart_items as $item): ?>

                    <?php

                    $cart_item_id =
                        intval($item["cart_item_id"]);

                    $product_id =
                        intval($item["product_id"]);

                    $quantity =
                        intval($item["quantity"]);

                    $stock =
                        intval($item["stock"]);

                    $price =
                        floatval($item["price"]);

                    $item_total =
                        $price * $quantity;

                    ?>


                    <div
                        class="cart-item"
                        id="cart-item-<?= $cart_item_id; ?>"
                    >


                        <!-- PRODUCT IMAGE -->

                        <div class="product-image">

                            <?php if (!empty($item["image"])): ?>

                                <img
                                    src="<?= htmlspecialchars($item["image"]); ?>"
                                    alt="<?= htmlspecialchars($item["product_name"]); ?>"
                                >

                            <?php else: ?>

                                <div class="no-image">
                                    NO IMAGE
                                </div>

                            <?php endif; ?>

                        </div>



                        <!-- PRODUCT INFO -->

                        <div class="product-info">

                            <h3>
                                <?= htmlspecialchars($item["product_name"]); ?>
                            </h3>

                            <p class="product-price">

                                ₱<?= number_format($price, 2); ?>

                            </p>

                            <p class="stock-info">

                                <?= $stock; ?> available

                            </p>

                        </div>



                        <!-- QUANTITY -->

                        <div class="quantity">

                            <button
                                type="button"
                                onclick="changeQuantity(
                                    <?= $cart_item_id; ?>,
                                    -1,
                                    <?= $stock; ?>
                                );"
                            >
                                −
                            </button>


                            <span
                                id="quantity-<?= $cart_item_id; ?>"
                            >
                                <?= $quantity; ?>
                            </span>


                            <button
                                type="button"
                                onclick="changeQuantity(
                                    <?= $cart_item_id; ?>,
                                    1,
                                    <?= $stock; ?>
                                );"
                            >
                                +
                            </button>

                        </div>



                        <!-- ITEM TOTAL -->

                        <div class="item-total">

                            ₱<?= number_format($item_total, 2); ?>

                        </div>



                        <!-- REMOVE -->

                        <button
                            type="button"
                            class="remove-button"
                            title="Remove item"
                            onclick="removeItem(
                                <?= $cart_item_id; ?>
                            );"
                        >
                            ×
                        </button>


                    </div>

                    <?php endforeach; ?>


                </div>

            </section>



            <!-- =================================================
                 ORDER SUMMARY
            ================================================= -->

            <aside class="cart-summary">

                <h2>
                    Order Summary
                </h2>


                <div class="summary-row">

                    <span>
                        Items
                    </span>

                    <span id="summaryItems">
                        <?= $total_items; ?>
                    </span>

                </div>


                <div class="summary-row">

                    <span>
                        Subtotal
                    </span>

                    <span id="summarySubtotal">
                        ₱<?= number_format($subtotal, 2); ?>
                    </span>

                </div>


                <div class="summary-row">

                    <span>
                        Shipping
                    </span>

                    <span>
                        FREE
                    </span>

                </div>


                <div class="summary-line"></div>


                <div class="summary-total">

                    <span>
                        TOTAL
                    </span>

                    <span id="summaryTotal">
                        ₱<?= number_format($total, 2); ?>
                    </span>

                </div>


                <!-- CHECKOUT -->

                <button
                    type="button"
                    class="checkout-button"
                    onclick="goToCheckout();"
                >
                    PROCEED TO CHECKOUT
                </button>


                <a
                    href="collection.php"
                    class="continue-shopping"
                >
                    ← CONTINUE SHOPPING
                </a>

            </aside>


        </div>


        <?php else: ?>


        <!-- =================================================
             EMPTY CART
        ================================================= -->

        <section class="cart-items-section empty-cart">

            <div class="empty-cart-icon">
                🛒
            </div>

            <h2>
                Your Cart Is Empty
            </h2>

            <p>
                You haven't added any products to your cart yet.
            </p>

            <a href="collection.php">
                SHOP NOW
            </a>

        </section>


        <?php endif; ?>


    </div>

</main>



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
     JAVASCRIPT
===================================================== -->

<script>


/* =====================================================
   CHANGE QUANTITY
===================================================== */

function changeQuantity(
    cartItemId,
    change,
    maxStock
) {

    const quantityElement =
        document.getElementById(
            "quantity-" + cartItemId
        );


    if (!quantityElement) {
        return;
    }


    let currentQuantity =
        parseInt(
            quantityElement.textContent
        );


    let newQuantity =
        currentQuantity + change;


    /* Minimum */

    if (newQuantity < 1) {

        newQuantity = 1;

    }


    /* Maximum */

    if (newQuantity > maxStock) {

        return;
    }


    /* No change */

    if (newQuantity === currentQuantity) {

        return;

    }


    /* Disable buttons while updating */

    const cartItem =
        document.getElementById(
            "cart-item-" + cartItemId
        );


    const buttons =
        cartItem.querySelectorAll(
            ".quantity button"
        );


    buttons.forEach(button => {

        button.disabled = true;

    });


    /* Send update */

    fetch("update_cart.php", {

        method: "POST",

        headers: {
            "Content-Type":
                "application/x-www-form-urlencoded"
        },

        body:
            "cart_item_id=" +
            encodeURIComponent(cartItemId) +
            "&quantity=" +
            encodeURIComponent(newQuantity)

    })

    .then(response => response.json())

    .then(data => {

        if (data.success) {

            location.reload();

        } else {

            alert(
                data.message ||
                "Unable to update cart."
            );

            buttons.forEach(button => {

                button.disabled = false;

            });

        }

    })

    .catch(error => {

        console.error(error);

        alert(
            "Something went wrong while updating the cart."
        );

        buttons.forEach(button => {

            button.disabled = false;

        });

    });

}



/* =====================================================
   REMOVE ITEM
===================================================== */

function removeItem(cartItemId) {

    fetch("remove_from_cart.php", {

        method: "POST",

        headers: {
            "Content-Type":
                "application/x-www-form-urlencoded"
        },

        body:
            "cart_item_id=" +
            encodeURIComponent(cartItemId)

    })

    .then(response => response.json())

    .then(data => {

        if (data.success) {

            location.reload();

        } else {

            alert(
                data.message ||
                "Unable to remove item."
            );

        }

    })

    .catch(error => {

        console.error(error);

        alert(
            "Something went wrong while removing the item."
        );

    });

}



/* =====================================================
   CLEAR CART
===================================================== */

function clearCart() {

    fetch("clear_cart.php", {

        method: "POST"

    })

    .then(response => response.json())

    .then(data => {

        if (data.success) {

            location.reload();

        } else {

            alert(
                data.message ||
                "Unable to clear cart."
            );

        }

    })

    .catch(error => {

        console.error(error);

        alert(
            "Something went wrong while clearing your cart."
        );

    });

}



/* =====================================================
   CHECKOUT
===================================================== */

function goToCheckout() {

    window.location.href = "checkout.php";

}



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

    window.location.href = "logout.php";

}


/* =====================================================
   CLOSE LOGOUT POPUP OUTSIDE
===================================================== */

document
    .getElementById("logoutOverlay")
    .addEventListener(
        "click",
        function(event) {

            if (event.target === this) {

                closeLogoutPopup();

            }

        }
    );


</script>


</body>

</html>