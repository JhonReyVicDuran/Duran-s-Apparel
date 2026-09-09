<?php

session_start();

require_once "db.php";


// =====================================================
// CHECK IF USER IS LOGGED IN
// =====================================================

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$user_id = (int) $_SESSION["user_id"];


// =====================================================
// GET USER INFORMATION
// =====================================================

$user_sql = "
    SELECT full_name, email
    FROM users
    WHERE user_id = ?
";

$user_stmt = $conn->prepare($user_sql);

if (!$user_stmt) {
    die("Database error: " . $conn->error);
}

$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();

$user_result = $user_stmt->get_result();

if ($user_result->num_rows === 0) {
    session_destroy();
    header("Location: login.php");
    exit;
}

$user = $user_result->fetch_assoc();

$default_name = $user["full_name"];
$default_email = $user["email"];

$user_stmt->close();


// =====================================================
// GET CART ITEMS
// =====================================================

$cart_sql = "
    SELECT
        cart_items.cart_item_id,
        cart_items.product_id,
        cart_items.quantity,
        cart_items.size,
        products.product_name,
        products.description,
        products.price,
        products.image,
        products.stock
    FROM cart_items
    INNER JOIN products
        ON cart_items.product_id = products.product_id
    WHERE cart_items.user_id = ?
    ORDER BY cart_items.cart_item_id ASC
";

$cart_stmt = $conn->prepare($cart_sql);

if (!$cart_stmt) {
    die("Database error: " . $conn->error);
}

$cart_stmt->bind_param("i", $user_id);
$cart_stmt->execute();

$cart_result = $cart_stmt->get_result();


// =====================================================
// CHECK IF CART IS EMPTY
// =====================================================

if ($cart_result->num_rows === 0) {
    header("Location: cart.php");
    exit;
}


// =====================================================
// STORE CART ITEMS
// =====================================================

$cart_items = [];
$total = 0;

while ($item = $cart_result->fetch_assoc()) {

    $quantity = (int) $item["quantity"];
    $price = (float) $item["price"];

    /*
       If an old cart item does not have a size,
       use M as a fallback.
    */

    if (empty($item["size"])) {
        $item["size"] = "M";
    }

    $subtotal = $price * $quantity;

    $item["subtotal"] = $subtotal;

    $cart_items[] = $item;

    $total += $subtotal;
}

$cart_stmt->close();


// =====================================================
// FORM VALUES
// =====================================================

$customer_name = $default_name;
$email = $default_email;
$phone = "";
$address = "";

$error = "";

$success = false;
$order_id = null;


// =====================================================
// PROCESS CHECKOUT
// =====================================================

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $customer_name = trim($_POST["customer_name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $phone = trim($_POST["phone"] ?? "");
    $address = trim($_POST["address"] ?? "");


    // =================================================
    // VALIDATION
    // =================================================

    if ($customer_name === "") {

        $error = "Please enter your full name.";

    } elseif ($email === "") {

        $error = "Please enter your email address.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $error = "Please enter a valid email address.";

    } elseif ($phone === "") {

        $error = "Please enter your mobile number.";

    } elseif (!preg_match("/^[0-9+\-\s()]{10,30}$/", $phone)) {

        $error = "Please enter a valid mobile number.";

    } elseif ($address === "") {

        $error = "Please enter your delivery address.";

    } else {


        // =================================================
        // START TRANSACTION
        // =================================================

        $conn->begin_transaction();

        try {


            // =============================================
            // GET CART AGAIN
            // =============================================

            $cart_check_sql = "
                SELECT
                    cart_items.cart_item_id,
                    cart_items.product_id,
                    cart_items.quantity,
                    cart_items.size,
                    products.product_name,
                    products.price,
                    products.stock
                FROM cart_items
                INNER JOIN products
                    ON cart_items.product_id = products.product_id
                WHERE cart_items.user_id = ?
                FOR UPDATE
            ";

            $cart_check_stmt = $conn->prepare($cart_check_sql);

            if (!$cart_check_stmt) {

                throw new Exception(
                    "Unable to check cart: " . $conn->error
                );

            }

            $cart_check_stmt->bind_param("i", $user_id);

            $cart_check_stmt->execute();

            $cart_check_result = $cart_check_stmt->get_result();


            // =============================================
            // CHECK CART
            // =============================================

            if ($cart_check_result->num_rows === 0) {

                throw new Exception(
                    "Your cart is empty."
                );

            }


            // =============================================
            // RECALCULATE TOTAL
            // =============================================

            $final_total = 0;

            $checkout_items = [];


            while ($item = $cart_check_result->fetch_assoc()) {

                $product_id = (int) $item["product_id"];

                $quantity = (int) $item["quantity"];

                $price = (float) $item["price"];

                $stock = (int) $item["stock"];

                $size = trim($item["size"] ?? "");


                // =========================================
                // SIZE FALLBACK
                // =========================================

                if ($size === "") {

                    $size = "M";

                }


                // =========================================
                // CHECK QUANTITY
                // =========================================

                if ($quantity <= 0) {

                    throw new Exception(
                        "Invalid quantity for " .
                        $item["product_name"] . "."
                    );

                }


                // =========================================
                // CHECK STOCK
                // =========================================

                if ($quantity > $stock) {

                    throw new Exception(
                        "Not enough stock for " .
                        $item["product_name"] .
                        ". Available stock: " .
                        $stock
                    );

                }


                // =========================================
                // CALCULATE SUBTOTAL
                // =========================================

                $subtotal = $price * $quantity;

                $final_total += $subtotal;


                // =========================================
                // SAVE VALUES
                // =========================================

                $item["product_id"] = $product_id;

                $item["quantity"] = $quantity;

                $item["size"] = $size;

                $item["price"] = $price;

                $item["subtotal"] = $subtotal;


                $checkout_items[] = $item;

            }

            $cart_check_stmt->close();


            // =============================================
            // INSERT ORDER
            // =============================================

            $order_sql = "
                INSERT INTO orders
                (
                    user_id,
                    customer_name,
                    email,
                    address,
                    phone,
                    total_amount,
                    status
                )
                VALUES
                (?, ?, ?, ?, ?, ?, 'Pending')
            ";

            $order_stmt = $conn->prepare($order_sql);

            if (!$order_stmt) {

                throw new Exception(
                    "Unable to prepare order: " .
                    $conn->error
                );

            }


            $order_stmt->bind_param(
                "issssd",
                $user_id,
                $customer_name,
                $email,
                $address,
                $phone,
                $final_total
            );


            if (!$order_stmt->execute()) {

                throw new Exception(
                    "Unable to create order: " .
                    $order_stmt->error
                );

            }


            // =============================================
            // GET ORDER ID
            // =============================================

            $order_id = $conn->insert_id;

            $order_stmt->close();


            // =============================================
            // PREPARE ORDER ITEM INSERT
            // =============================================

            $order_item_sql = "
                INSERT INTO order_items
                (
                    order_id,
                    product_id,
                    quantity,
                    size,
                    price,
                    subtotal
                )
                VALUES (?, ?, ?, ?, ?, ?)
            ";

            $order_item_stmt = $conn->prepare($order_item_sql);

            if (!$order_item_stmt) {

                throw new Exception(
                    "Unable to prepare order items: " .
                    $conn->error
                );

            }


            // =============================================
            // PREPARE STOCK UPDATE
            // =============================================

            $stock_sql = "
                UPDATE products
                SET stock = stock - ?
                WHERE product_id = ?
                AND stock >= ?
            ";

            $stock_stmt = $conn->prepare($stock_sql);

            if (!$stock_stmt) {

                throw new Exception(
                    "Unable to prepare stock update: " .
                    $conn->error
                );

            }


            // =============================================
            // INSERT ORDER ITEMS + UPDATE STOCK
            // =============================================

            foreach ($checkout_items as $item) {

                $product_id = (int) $item["product_id"];

                $quantity = (int) $item["quantity"];

                $size = $item["size"];

                $price = (float) $item["price"];

                $subtotal = $price * $quantity;


                // =========================================
                // INSERT ORDER ITEM
                // =========================================

                $order_item_stmt->bind_param(
                    "iiisdd",
                    $order_id,
                    $product_id,
                    $quantity,
                    $size,
                    $price,
                    $subtotal
                );


                if (!$order_item_stmt->execute()) {

                    throw new Exception(
                        "Unable to save order item: " .
                        $order_item_stmt->error
                    );

                }


                // =========================================
                // DEDUCT STOCK
                // =========================================

                $stock_stmt->bind_param(
                    "iii",
                    $quantity,
                    $product_id,
                    $quantity
                );


                if (!$stock_stmt->execute()) {

                    throw new Exception(
                        "Unable to update stock for " .
                        $item["product_name"] . "."
                    );

                }


                // =========================================
                // MAKE SURE STOCK WAS UPDATED
                // =========================================

                if ($stock_stmt->affected_rows === 0) {

                    throw new Exception(
                        "Not enough stock for " .
                        $item["product_name"] . "."
                    );

                }

            }


            $order_item_stmt->close();

            $stock_stmt->close();


            // =============================================
            // CLEAR CART
            // =============================================

            $clear_cart_sql = "
                DELETE FROM cart_items
                WHERE user_id = ?
            ";

            $clear_cart_stmt = $conn->prepare($clear_cart_sql);

            if (!$clear_cart_stmt) {

                throw new Exception(
                    "Unable to clear cart: " .
                    $conn->error
                );

            }


            $clear_cart_stmt->bind_param(
                "i",
                $user_id
            );


            if (!$clear_cart_stmt->execute()) {

                throw new Exception(
                    "Unable to clear cart: " .
                    $clear_cart_stmt->error
                );

            }


            $clear_cart_stmt->close();


            // =============================================
            // COMMIT TRANSACTION
            // =============================================

            $conn->commit();


            // =============================================
            // SUCCESS
            // =============================================

            $success = true;

            $total = $final_total;


        } catch (Exception $e) {


            // =============================================
            // ROLLBACK
            // =============================================

            $conn->rollback();

            $error = $e->getMessage();

            $order_id = null;

        }

    }

}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Checkout - DURAN'S Apparel</title>

    <link rel="stylesheet" href="css/style.css">


    <style>

        /* =====================================================
           CHECKOUT PAGE
        ===================================================== */

        .checkout-page {
            padding: 140px 8% 80px;
            background: #f7f7f7;
            min-height: 100vh;
        }


        .checkout-title {
            text-align: center;
            margin-bottom: 45px;
        }


        .checkout-title h1 {
            font-size: 42px;
            font-weight: 900;
            color: #f80404;
            text-transform: uppercase;
            margin-bottom: 10px;
        }


        .checkout-title p {
            color: #777;
            font-size: 15px;
        }


        .checkout-container {
            max-width: 1100px;
            margin: auto;
            display: grid;
            grid-template-columns: 1.5fr 1fr;
            gap: 30px;
        }


        /* =====================================================
           CHECKOUT BOX
        ===================================================== */

        .checkout-box {
            background: #fff;
            padding: 35px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        }


        .checkout-box h2 {
            font-size: 22px;
            font-weight: 900;
            text-transform: uppercase;
            margin-bottom: 25px;
            color: #000;
        }


        /* =====================================================
           FORM
        ===================================================== */

        .checkout-field {
            margin-bottom: 20px;
        }


        .checkout-field label {
            display: block;
            font-size: 12px;
            font-weight: 800;
            margin-bottom: 8px;
            color: #000;
            text-transform: uppercase;
        }


        .checkout-field input,
        .checkout-field textarea {
            width: 100%;
            padding: 14px;
            border: 1px solid #ddd;
            outline: none;
            font-size: 14px;
            font-family: Arial, Helvetica, sans-serif;
            transition: 0.3s;
        }


        .checkout-field input:focus,
        .checkout-field textarea:focus {
            border-color: #000;
        }


        .checkout-field textarea {
            height: 120px;
            resize: vertical;
        }


        /* =====================================================
           ERROR
        ===================================================== */

        .checkout-error {
            background: #ffe5e5;
            color: #d00000;
            padding: 15px;
            margin-bottom: 25px;
            font-size: 14px;
            font-weight: 700;
            border-left: 4px solid #ff0000;
        }


        /* =====================================================
           ORDER SUMMARY
        ===================================================== */

        .order-summary {
            background: #fff;
            padding: 35px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            height: fit-content;
        }


        .order-summary h2 {
            font-size: 22px;
            font-weight: 900;
            color: red;
            text-transform: uppercase;
            margin-bottom: 25px;
        }


        .summary-item {
            display: flex;
            justify-content: space-between;
            gap: 15px;
            padding: 15px 0;
            border-bottom: 1px solid #eee;
        }


        .summary-item-name {
            font-size: 14px;
            font-weight: 700;
            color: #000;
        }


        .summary-item-qty {
            font-size: 12px;
            color: #777;
            margin-top: 5px;
        }


        /* =====================================================
           SIZE
        ===================================================== */

        .summary-item-size {
            font-size: 12px;
            color: #777;
            margin-top: 4px;
        }


        .summary-item-size strong {
            color: #000;
            font-weight: 800;
        }


        .summary-item-price {
            font-size: 14px;
            font-weight: 800;
            color: black;
            white-space: nowrap;
        }


        .summary-total {
            display: flex;
            justify-content: space-between;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 2px solid #000;
        }


        .summary-total span:first-child {
            font-size: 16px;
            font-weight: 900;
            color: red;
            text-transform: uppercase;
        }


        .summary-total span:last-child {
            font-size: 22px;
            font-weight: 900;
            color: #ff0505;
        }


        /* =====================================================
           PLACE ORDER BUTTON
        ===================================================== */

        .place-order-btn {
            width: 100%;
            margin-top: 25px;
            padding: 16px;
            background: #fa0505;
            color: #fff;
            border: none;
            font-size: 14px;
            font-weight: 900;
            cursor: pointer;
            transition: 0.3s;
            text-transform: uppercase;
        }


        .place-order-btn:hover {
            background: #020202;
        }


        .back-cart {
            display: block;
            text-align: center;
            margin-top: 15px;
            color: #000;
            font-size: 13px;
            font-weight: 700;
            text-decoration: none;
        }


        .back-cart:hover {
            color: #ff0505;
        }


        /* =====================================================
           SUCCESS PAGE
        ===================================================== */

        .checkout-success {
            max-width: 600px;
            margin: 80px auto;
            background: #fff;
            padding: 50px 35px;
            text-align: center;
            box-shadow: 0 5px 25px rgba(0,0,0,0.1);
        }


        .success-icon {
            width: 70px;
            height: 70px;
            margin: 0 auto 20px;
            background: #2bff00;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 35px;
            font-weight: 900;
        }


        .checkout-success h1 {
            font-size: 30px;
            font-weight: 900;
            color: red;
            text-transform: uppercase;
            margin-bottom: 10px;
        }


        .checkout-success p {
            color: #666;
            font-size: 14px;
            line-height: 1.7;
            margin-bottom: 8px;
        }


        .order-number {
            font-weight: 900;
            color: #ff0505;
        }


        .success-buttons {
            margin-top: 30px;
            display: flex;
            justify-content: center;
            gap: 12px;
            flex-wrap: wrap;
        }


        .success-buttons a {
            padding: 13px 25px;
            background: #020000;
            color: #fff;
            text-decoration: none;
            font-size: 13px;
            font-weight: 800;
            transition: 0.3s;
        }


        .success-buttons a:hover {
            background: #ff0101;
        }


        /* =====================================================
           MOBILE
        ===================================================== */

        @media (max-width: 800px) {

            .checkout-page {
                padding: 120px 5% 60px;
            }


            .checkout-container {
                grid-template-columns: 1fr;
            }


            .checkout-title h1 {
                font-size: 32px;
            }


            .checkout-box,
            .order-summary {
                padding: 25px;
            }

        }

    </style>

</head>


<body>


<?php if ($success): ?>


    <!-- =====================================================
         ORDER SUCCESS
    ===================================================== -->

    <main class="checkout-page">

        <div class="checkout-success">

            <div class="success-icon">
                ✓
            </div>


            <h1>
                Order Placed!
            </h1>


            <p>

                Thank you for your order,

                <strong>
                    <?php echo htmlspecialchars($customer_name); ?>
                </strong>.

            </p>


            <p>
                Your order has been successfully placed.
            </p>


            <p>

                Order Number:

                <span class="order-number">

                    #<?php echo htmlspecialchars($order_id); ?>

                </span>

            </p>


            <p>

                Total:

                <strong>

                    ₱<?php echo number_format($total, 2); ?>

                </strong>

            </p>


            <p>
                Your order will be delivered to:
            </p>


            <p>

                <strong>

                    <?php echo nl2br(
                        htmlspecialchars($address)
                    ); ?>

                </strong>

            </p>


            <div class="success-buttons">

                <a href="collection.php">
                    CONTINUE SHOPPING
                </a>

                <a href="index.php">
                    BACK TO HOME
                </a>

            </div>

        </div>

    </main>


<?php else: ?>


    <!-- =====================================================
         CHECKOUT
    ===================================================== -->

    <main class="checkout-page">


        <div class="checkout-title">

            <h1>
                Checkout
            </h1>

            <p>
                Enter your delivery information to complete your order.
            </p>

        </div>


        <div class="checkout-container">


            <!-- =================================================
                 CUSTOMER INFORMATION
            ================================================= -->

            <div class="checkout-box">

                <h2>
                    Delivery Information
                </h2>


                <?php if ($error !== ""): ?>

                    <div class="checkout-error">

                        <?php echo htmlspecialchars($error); ?>

                    </div>

                <?php endif; ?>


                <form
                    method="POST"
                    action="checkout.php"
                >


                    <!-- FULL NAME -->

                    <div class="checkout-field">

                        <label for="customer_name">
                            Full Name
                        </label>

                        <input
                            type="text"
                            id="customer_name"
                            name="customer_name"
                            value="<?php echo htmlspecialchars($customer_name); ?>"
                            placeholder="Enter your full name"
                            required
                        >

                    </div>


                    <!-- EMAIL -->

                    <div class="checkout-field">

                        <label for="email">
                            Email Address
                        </label>

                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="<?php echo htmlspecialchars($email); ?>"
                            placeholder="Enter your email"
                            required
                        >

                    </div>


                    <!-- PHONE -->

                    <div class="checkout-field">

                        <label for="phone">
                            Mobile Number
                        </label>

                        <input
                            type="text"
                            id="phone"
                            name="phone"
                            value="<?php echo htmlspecialchars($phone); ?>"
                            placeholder="09XXXXXXXXX"
                            required
                        >

                    </div>


                    <!-- ADDRESS -->

                    <div class="checkout-field">

                        <label for="address">
                            Delivery Address
                        </label>

                        <textarea
                            id="address"
                            name="address"
                            placeholder="Enter your complete delivery address"
                            required
                        ><?php echo htmlspecialchars($address); ?></textarea>

                    </div>


                    <!-- PLACE ORDER -->

                    <button
                        type="submit"
                        class="place-order-btn"
                    >
                        PLACE ORDER
                    </button>


                </form>


                <a
                    href="cart.php"
                    class="back-cart"
                >
                    ← BACK TO CART
                </a>

            </div>


            <!-- =================================================
                 ORDER SUMMARY
            ================================================= -->

            <div class="order-summary">

                <h2>
                    Your Order
                </h2>


                <?php foreach ($cart_items as $item): ?>

                    <div class="summary-item">


                        <div>

                            <!-- PRODUCT NAME -->

                            <div class="summary-item-name">

                                <?php
                                echo htmlspecialchars(
                                    $item["product_name"]
                                );
                                ?>

                            </div>


                            <!-- SIZE -->

                            <div class="summary-item-size">

                                Size:

                                <strong>

                                    <?php
                                    echo htmlspecialchars(
                                        $item["size"]
                                    );
                                    ?>

                                </strong>

                            </div>


                            <!-- QUANTITY -->

                            <div class="summary-item-qty">

                                Quantity:

                                <?php
                                echo (int)$item["quantity"];
                                ?>

                            </div>

                        </div>


                        <!-- PRICE -->

                        <div class="summary-item-price">

                            ₱<?php

                            echo number_format(
                                $item["subtotal"],
                                2
                            );

                            ?>

                        </div>


                    </div>

                <?php endforeach; ?>


                <!-- TOTAL -->

                <div class="summary-total">

                    <span>
                        Total
                    </span>

                    <span>

                        ₱<?php
                        echo number_format(
                            $total,
                            2
                        );
                        ?>

                    </span>

                </div>

            </div>


        </div>

    </main>


<?php endif; ?>


</body>

</html>