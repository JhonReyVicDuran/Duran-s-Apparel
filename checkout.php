<?php
session_start();
require_once "db.php";

// Check if user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION["user_id"];

// Get user information
$user_sql = "SELECT full_name, email FROM users WHERE user_id = ?";
$user_stmt = $conn->prepare($user_sql);
$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();

$user_result = $user_stmt->get_result();
$user = $user_result->fetch_assoc();

if (!$user) {
    session_destroy();
    header("Location: login.php");
    exit;
}

// Get cart items
$cart_sql = "
    SELECT 
        c.cart_item_id,
        c.product_id,
        c.quantity,
        c.size,
        p.product_name,
        p.description,
        p.price,
        p.image,
        p.stock
    FROM cart_items c
    INNER JOIN products p 
        ON c.product_id = p.product_id
    WHERE c.user_id = ?
";

$cart_stmt = $conn->prepare($cart_sql);
$cart_stmt->bind_param("i", $user_id);
$cart_stmt->execute();

$cart_result = $cart_stmt->get_result();

$cart_items = [];
$total = 0;

while ($row = $cart_result->fetch_assoc()) {

    // Default size
    if (empty($row["size"])) {
        $row["size"] = "M";
    }

    $row["subtotal"] = $row["price"] * $row["quantity"];

    $total += $row["subtotal"];

    $cart_items[] = $row;
}

// Redirect if cart is empty
if (empty($cart_items)) {
    header("Location: cart.php");
    exit;
}

// Form values
$customer_name = $user["full_name"];
$email = $user["email"];
$phone = "";
$address = "";

// Payment method is always COD
$payment_method = "Cash on Delivery";

$error = "";
$success = "";
$order_id = null;


// =====================================================
// PLACE ORDER
// =====================================================

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $customer_name = trim($_POST["customer_name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $phone = trim($_POST["phone"] ?? "");
    $address = trim($_POST["address"] ?? "");

    // Always Cash on Delivery
    $payment_method = "Cash on Delivery";


    // =================================================
    // VALIDATION
    // =================================================

    if ($customer_name === "") {

        $error = "Please enter your full name.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $error = "Please enter a valid email address.";

    } elseif (!preg_match("/^[0-9+\-\s]{7,20}$/", $phone)) {

        $error = "Please enter a valid mobile number.";

    } elseif ($address === "") {

        $error = "Please enter your delivery address.";

    } else {

        try {

            // Start database transaction
            $conn->begin_transaction();


            // =============================================
            // GET CART AGAIN AND LOCK THE PRODUCTS
            // =============================================

            $cart_check_sql = "
                SELECT 
                    c.cart_item_id,
                    c.product_id,
                    c.quantity,
                    c.size,
                    p.product_name,
                    p.price,
                    p.stock
                FROM cart_items c
                INNER JOIN products p
                    ON c.product_id = p.product_id
                WHERE c.user_id = ?
                FOR UPDATE
            ";

            $cart_check_stmt = $conn->prepare($cart_check_sql);
            $cart_check_stmt->bind_param("i", $user_id);
            $cart_check_stmt->execute();

            $cart_check_result = $cart_check_stmt->get_result();

            $final_total = 0;
            $latest_cart = [];


            // =============================================
            // CHECK STOCK
            // =============================================

            while ($item = $cart_check_result->fetch_assoc()) {

                if (empty($item["size"])) {
                    $item["size"] = "M";
                }


                // Check if enough stock is available
                if ($item["stock"] < $item["quantity"]) {

                    throw new Exception(
                        "Not enough stock for " .
                        $item["product_name"] . "."
                    );
                }


                $subtotal = $item["price"] * $item["quantity"];

                $final_total += $subtotal;

                $item["subtotal"] = $subtotal;

                $latest_cart[] = $item;
            }


            // Make sure cart still contains products
            if (empty($latest_cart)) {

                throw new Exception("Your cart is empty.");
            }


            // =============================================
            // CREATE ORDER
            // =============================================

            $order_sql = "
                INSERT INTO orders
                (
                    user_id,
                    customer_name,
                    email,
                    address,
                    phone,
                    payment_method,
                    total_amount,
                    status
                )
                VALUES
                (?, ?, ?, ?, ?, ?, ?, 'Pending')
            ";

            $order_stmt = $conn->prepare($order_sql);

            $order_stmt->bind_param(
                "isssssd",
                $user_id,
                $customer_name,
                $email,
                $address,
                $phone,
                $payment_method,
                $final_total
            );

            $order_stmt->execute();


            // Get the newly created order ID
            $order_id = $conn->insert_id;


            // =============================================
            // INSERT ORDER ITEMS
            // =============================================

            $item_sql = "
                INSERT INTO order_items
                (
                    order_id,
                    product_id,
                    quantity,
                    size,
                    price,
                    subtotal
                )
                VALUES
                (?, ?, ?, ?, ?, ?)
            ";

            $item_stmt = $conn->prepare($item_sql);


            foreach ($latest_cart as $item) {

                $subtotal =
                    $item["price"] * $item["quantity"];


                $item_stmt->bind_param(
                    "iiisdd",
                    $order_id,
                    $item["product_id"],
                    $item["quantity"],
                    $item["size"],
                    $item["price"],
                    $subtotal
                );

                $item_stmt->execute();


                // =========================================
                // DEDUCT PRODUCT STOCK
                // =========================================

                $update_stock_sql = "
                    UPDATE products
                    SET stock = stock - ?
                    WHERE product_id = ?
                    AND stock >= ?
                ";

                $stock_stmt =
                    $conn->prepare($update_stock_sql);

                $stock_stmt->bind_param(
                    "iii",
                    $item["quantity"],
                    $item["product_id"],
                    $item["quantity"]
                );

                $stock_stmt->execute();


                // Make sure stock was updated
                if ($stock_stmt->affected_rows === 0) {

                    throw new Exception(
                        "Unable to update stock for " .
                        $item["product_name"] . "."
                    );
                }

                $stock_stmt->close();
            }


            // =============================================
            // CLEAR CART
            // =============================================

            $clear_cart_sql = "
                DELETE FROM cart_items
                WHERE user_id = ?
            ";

            $clear_cart_stmt =
                $conn->prepare($clear_cart_sql);

            $clear_cart_stmt->bind_param(
                "i",
                $user_id
            );

            $clear_cart_stmt->execute();


            // =============================================
            // COMPLETE TRANSACTION
            // =============================================

            $conn->commit();

            $success =
                "Your order has been placed successfully!";

            $total = $final_total;


        } catch (Exception $e) {

            // Undo all database changes
            $conn->rollback();

            $error = $e->getMessage();
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Checkout - DURAN'S Apparel</title>


    <style>

        * {
            box-sizing: border-box;
        }


        body {
            margin: 0;
            padding: 0;

            font-family:
                Arial,
                Helvetica,
                sans-serif;

            background: #f5f5f5;

            color: #111;
        }


        /* =========================================
           CHECKOUT CONTAINER
        ========================================= */

        .checkout-container {

            width: 90%;

            max-width: 1100px;

            margin: 50px auto;
        }


        /* =========================================
           BACK TO CART BUTTON
        ========================================= */

        .back-cart-btn {

            display: inline-block;

            margin-bottom: 20px;

            padding: 12px 18px;

            background: #111;

            color: white;

            text-decoration: none;

            border-radius: 4px;

            font-size: 14px;

            font-weight: bold;

            transition: 0.3s;
        }


        .back-cart-btn:hover {

            background: #d00000;
        }


        /* =========================================
           TITLE
        ========================================= */

        .checkout-title {

            text-align: center;

            margin-bottom: 35px;
        }


        .checkout-title h1 {

            margin: 0;

            font-size: 32px;
        }


        .checkout-title p {

            color: #777;

            margin-top: 8px;
        }


        /* =========================================
           CHECKOUT LAYOUT
        ========================================= */

        .checkout-layout {

            display: grid;

            grid-template-columns:
                1fr 380px;

            gap: 30px;
        }


        /* =========================================
           BOX
        ========================================= */

        .checkout-box {

            background: white;

            padding: 30px;

            border-radius: 8px;

            box-shadow:
                0 3px 15px
                rgba(0, 0, 0, 0.08);
        }


        .checkout-box h2 {

            margin-top: 0;

            margin-bottom: 25px;

            font-size: 22px;
        }


        /* =========================================
           FORM FIELDS
        ========================================= */

        .checkout-field {

            margin-bottom: 20px;
        }


        .checkout-field label {

            display: block;

            margin-bottom: 8px;

            font-weight: bold;

            font-size: 14px;
        }


        .checkout-field input,
        .checkout-field textarea {

            width: 100%;

            padding: 14px;

            border: 1px solid #ddd;

            outline: none;

            font-size: 14px;

            font-family:
                Arial,
                Helvetica,
                sans-serif;

            background: #fff;

            color: #000;

            border-radius: 4px;
        }


        .checkout-field input:focus,
        .checkout-field textarea:focus {

            border-color: #000;
        }


        .checkout-field textarea {

            min-height: 110px;

            resize: vertical;
        }


        /* =========================================
           PAYMENT METHOD
        ========================================= */

        .payment-box {

            border: 1px solid #ddd;

            padding: 15px;

            background: #fafafa;

            border-radius: 5px;
        }


        .payment-box strong {

            display: block;

            margin-bottom: 5px;
        }


        .payment-box span {

            color: #666;

            font-size: 13px;
        }


        /* =========================================
           ERROR
        ========================================= */

        .error {

            background: #ffe5e5;

            color: #c00000;

            padding: 14px;

            margin-bottom: 20px;

            border-radius: 5px;

            border:
                1px solid #ffb5b5;
        }


        /* =========================================
           SUMMARY ITEM
        ========================================= */

        .summary-item {

            display: flex;

            justify-content:
                space-between;

            gap: 15px;

            padding: 15px 0;

            border-bottom:
                1px solid #eee;
        }


        .summary-item-info {

            flex: 1;
        }


        .summary-item-name {

            font-weight: bold;

            margin-bottom: 5px;
        }


        .summary-item-details {

            color: #777;

            font-size: 13px;

            line-height: 1.5;
        }


        .summary-item-price {

            font-weight: bold;

            white-space: nowrap;
        }


        /* =========================================
           TOTAL
        ========================================= */

        .summary-total {

            display: flex;

            justify-content:
                space-between;

            margin-top: 20px;

            padding-top: 20px;

            border-top:
                2px solid #111;

            font-size: 20px;

            font-weight: bold;
        }


        /* =========================================
           PLACE ORDER BUTTON
        ========================================= */

        .place-order-btn {

            width: 100%;

            border: none;

            background: #111;

            color: white;

            padding: 16px;

            margin-top: 25px;

            font-size: 15px;

            font-weight: bold;

            cursor: pointer;

            border-radius: 4px;

            transition: 0.3s;
        }


        .place-order-btn:hover {

            background: #d00000;
        }


        /* =========================================
           SUCCESS PAGE
        ========================================= */

        .success-page {

            width: 90%;

            max-width: 650px;

            margin: 80px auto;

            background: white;

            padding: 45px;

            text-align: center;

            border-radius: 8px;

            box-shadow:
                0 3px 20px
                rgba(0, 0, 0, 0.1);
        }


        .success-icon {

            width: 70px;

            height: 70px;

            background: #111;

            color: white;

            border-radius: 50%;

            display: flex;

            align-items: center;

            justify-content: center;

            margin: 0 auto 20px;

            font-size: 30px;
        }


        .success-page h1 {

            margin-bottom: 10px;
        }


        .success-page p {

            color: #555;

            line-height: 1.6;
        }


        .success-details {

            background: #f7f7f7;

            padding: 20px;

            margin-top: 25px;

            text-align: left;

            border-radius: 5px;
        }


        .success-details p {

            margin: 10px 0;
        }


        /* =========================================
           SUCCESS BUTTONS
        ========================================= */

        .success-buttons {

            display: flex;

            gap: 10px;

            margin-top: 25px;
        }


        .success-buttons a {

            flex: 1;

            padding: 14px;

            text-decoration: none;

            text-align: center;

            font-weight: bold;

            border-radius: 4px;
        }


        .home-btn {

            background: #111;

            color: white;
        }


        .orders-btn {

            background: #eee;

            color: #111;
        }


        /* =========================================
           MOBILE
        ========================================= */

        @media (max-width: 800px) {

            .checkout-layout {

                grid-template-columns: 1fr;
            }


            .checkout-container {

                width: 95%;

                margin: 30px auto;
            }


            .checkout-box {

                padding: 20px;
            }


            .success-page {

                width: 95%;

                padding: 30px 20px;
            }


            .success-buttons {

                flex-direction: column;
            }
        }

    </style>

</head>


<body>


<?php if ($success && $order_id): ?>


    <!-- =========================================
         SUCCESS PAGE
    ========================================== -->

    <div class="success-page">


        <div class="success-icon">

            ✓

        </div>


        <h1>

            Order Placed!

        </h1>


        <p>

            Thank you for your order
            from DURAN'S Apparel.

        </p>


        <div class="success-details">


            <p>

                <strong>
                    Order Number:
                </strong>

                #<?php
                echo htmlspecialchars($order_id);
                ?>

            </p>


            <p>

                <strong>
                    Total Amount:
                </strong>

                ₱<?php
                echo number_format($total, 2);
                ?>

            </p>


            <p>

                <strong>
                    Payment Method:
                </strong>

                Cash on Delivery

            </p>


            <p>

                <strong>
                    Delivery Address:
                </strong>

                <?php
                echo htmlspecialchars($address);
                ?>

            </p>


            <p>

                <strong>
                    Status:
                </strong>

                Pending

            </p>


        </div>


        <p>

            Please prepare the payment
            when your order arrives.

        </p>


        <div class="success-buttons">


            <a
                href="index.php"
                class="home-btn"
            >

                Continue Shopping

            </a>


            <a
                href="orders.php"
                class="orders-btn"
            >

                View My Orders

            </a>


        </div>


    </div>


<?php else: ?>


    <!-- =========================================
         CHECKOUT PAGE
    ========================================== -->

    <div class="checkout-container">


        <!-- BACK TO CART -->

        <a
            href="cart.php"
            class="back-cart-btn"
        >

            ← Back to Cart

        </a>


        <!-- TITLE -->

        <div class="checkout-title">


            <h1>

                Checkout

            </h1>


            <p>

                Complete your order details

            </p>


        </div>


        <!-- ERROR -->

        <?php if ($error): ?>

            <div class="error">

                <?php
                echo htmlspecialchars($error);
                ?>

            </div>

        <?php endif; ?>


        <!-- CHECKOUT CONTENT -->

        <div class="checkout-layout">


            <!-- =====================================
                 CUSTOMER INFORMATION
            ====================================== -->

            <div class="checkout-box">


                <h2>

                    Delivery Information

                </h2>


                <form
                    method="POST"
                    action=""
                >


                    <!-- FULL NAME -->

                    <div class="checkout-field">


                        <label
                            for="customer_name"
                        >

                            Full Name

                        </label>


                        <input
                            type="text"
                            id="customer_name"
                            name="customer_name"
                            value="<?php
                            echo htmlspecialchars(
                                $customer_name
                            );
                            ?>"
                            required
                        >


                    </div>


                    <!-- EMAIL -->

                    <div class="checkout-field">


                        <label
                            for="email"
                        >

                            Email

                        </label>


                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="<?php
                            echo htmlspecialchars(
                                $email
                            );
                            ?>"
                            required
                        >


                    </div>


                    <!-- MOBILE NUMBER -->

                    <div class="checkout-field">


                        <label
                            for="phone"
                        >

                            Mobile Number

                        </label>


                        <input
                            type="text"
                            id="phone"
                            name="phone"
                            value="<?php
                            echo htmlspecialchars(
                                $phone
                            );
                            ?>"
                            placeholder="09XXXXXXXXX"
                            required
                        >


                    </div>


                    <!-- PAYMENT METHOD -->

                    <div class="checkout-field">


                        <label>

                            Payment Method

                        </label>


                        <div class="payment-box">


                            <strong>

                                Cash on Delivery

                            </strong>


                            <span>

                                Pay the total amount
                                when your order
                                is delivered.

                            </span>


                        </div>


                    </div>


                    <!-- DELIVERY ADDRESS -->

                    <div class="checkout-field">


                        <label
                            for="address"
                        >

                            Delivery Address

                        </label>


                        <textarea
                            id="address"
                            name="address"
                            placeholder="Enter your complete delivery address"
                            required
                        ><?php
                        echo htmlspecialchars($address);
                        ?></textarea>


                    </div>


                    <!-- PLACE ORDER -->

                    <button
                        type="submit"
                        class="place-order-btn"
                    >

                        Place Order

                    </button>


                </form>


            </div>


            <!-- =====================================
                 ORDER SUMMARY
            ====================================== -->

            <div class="checkout-box">


                <h2>

                    Order Summary

                </h2>


                <?php foreach ($cart_items as $item): ?>


                    <div class="summary-item">


                        <div
                            class="summary-item-info"
                        >


                            <div
                                class="summary-item-name"
                            >

                                <?php
                                echo htmlspecialchars(
                                    $item["product_name"]
                                );
                                ?>

                            </div>


                            <div
                                class="summary-item-details"
                            >

                                Size:
                                <?php
                                echo htmlspecialchars(
                                    $item["size"]
                                );
                                ?>

                                <br>

                                Quantity:
                                <?php
                                echo $item["quantity"];
                                ?>

                            </div>


                        </div>


                        <div
                            class="summary-item-price"
                        >

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


    </div>


<?php endif; ?>


</body>

</html>