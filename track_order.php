<?php
session_start();
require_once "db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION["user_id"];

/* Get all orders of the logged-in user */
$stmt = $conn->prepare("
    SELECT 
        order_id,
        customer_name,
        email,
        address,
        phone,
        total_amount,
        status,
        order_date
    FROM orders
    WHERE user_id = ?
    ORDER BY order_id DESC
");

$stmt->bind_param("i", $user_id);
$stmt->execute();

$orders = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>My Orders - DURAN'S Apparel</title>

    <link rel="stylesheet" href="style.css">

    <link rel="stylesheet" href="logout.css">

    <style>

        body {
            background: #f5f5f5;
            color: #000;
        }

        .track-container {
            width: 90%;
            max-width: 1000px;
            margin: 120px auto 60px;
        }

        .track-title {
            text-align: center;
            margin-bottom: 40px;
        }

        .track-title h1 {
            font-size: 36px;
            font-weight: 900;
            color: red;
            text-transform: uppercase;
            margin-bottom: 10px;
        }

        .track-title p {
            color: #666;
            font-size: 15px;
        }

        /* ORDER CARD */

        .order-card {
            background: #fff;
            margin-bottom: 30px;
            padding: 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        }

        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #ddd;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .order-number h2 {
            font-size: 22px;
            margin-bottom: 5px;
        }

        .order-number p {
            color: #777;
            font-size: 13px;
        }

        /* STATUS */

        .status {
            padding: 9px 18px;
            font-size: 12px;
            font-weight: 800;
            text-transform: uppercase;
        }

        .status.pending {
            background: #fff3cd;
            color: #856404;
        }

        .status.processing {
            background: #cfe2ff;
            color: #084298;
        }

        .status.shipped {
            background: #d1ecf1;
            color: #0c5460;
        }

        .status.completed {
            background: #d4edda;
            color: #155724;
        }

        .status.cancelled {
            background: #f8d7da;
            color: #721c24;
        }

        /* TIMELINE */

        .timeline {
            display: flex;
            justify-content: space-between;
            position: relative;
            margin: 40px 20px;
        }

        .timeline::before {
            content: "";
            position: absolute;
            top: 14px;
            left: 0;
            right: 0;
            height: 3px;
            background: #ddd;
            z-index: 1;
        }

        .timeline-step {
            position: relative;
            z-index: 2;
            text-align: center;
            width: 25%;
        }

        .timeline-circle {
            width: 30px;
            height: 30px;
            background: #ddd;
            border-radius: 50%;
            margin: 0 auto 10px;
            border: 4px solid #f5f5f5;
        }

        .timeline-step.completed .timeline-circle {
            background: #000;
        }

        .timeline-step.active .timeline-circle {
            background: #ff0505;
        }

        .timeline-step p {
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
        }

        /* ITEMS */

        .items-title {
            font-size: 17px;
            font-weight: 900;
            text-transform: uppercase;
            margin-bottom: 15px;
        }

        .order-items {
            border-top: 1px solid #ddd;
            border-bottom: 1px solid #ddd;
        }

        .order-item {
            display: flex;
            align-items: center;
            gap: 18px;
            padding: 18px 0;
            border-bottom: 1px solid #eee;
        }

        .order-item:last-child {
            border-bottom: none;
        }

        .item-image {
            width: 90px;
            height: 90px;
            background: #f5f5f5;
            object-fit: contain;
            flex-shrink: 0;
        }

        .item-info {
            flex: 1;
        }

        .item-info h3 {
            font-size: 16px;
            font-weight: 800;
            margin-bottom: 6px;
        }

        .item-info p {
            font-size: 13px;
            color: #777;
            margin: 3px 0;
        }

        .item-price {
            text-align: right;
            min-width: 130px;
        }

        .item-price .price {
            font-size: 14px;
            color: #555;
        }

        .item-price .subtotal {
            font-size: 17px;
            font-weight: 900;
            margin-top: 5px;
        }

        /* ORDER DETAILS */

        .order-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
            margin-top: 30px;
        }

        .details-box {
            background: #f7f7f7;
            padding: 20px;
        }

        .details-box h3 {
            font-size: 15px;
            text-transform: uppercase;
            margin-bottom: 12px;
        }

        .details-box p {
            font-size: 14px;
            color: #555;
            line-height: 1.7;
        }

        /* TOTAL */

        .order-total {
            text-align: right;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
        }

        .order-total span {
            font-size: 14px;
            color: #777;
        }

        .order-total strong {
            font-size: 25px;
            margin-left: 10px;
        }

        /* NO ORDERS */

        .no-orders {
            background: #fff;
            text-align: center;
            padding: 60px 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        }

        .no-orders h2 {
            font-size: 25px;
            margin-bottom: 10px;
        }

        .no-orders p {
            color: #777;
            margin-bottom: 25px;
        }

        .shop-btn {
            display: inline-block;
            background: #000;
            color: #fff;
            padding: 13px 25px;
            font-size: 13px;
            font-weight: 800;
            text-transform: uppercase;
        }

        .shop-btn:hover {
            background: #ff0505;
        }

        /* MOBILE */

        @media (max-width: 700px) {

            .track-container {
                margin-top: 100px;
            }

            .track-title h1 {
                font-size: 28px;
            }

            .order-card {
                padding: 20px;
            }

            .order-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }

            .order-details {
                grid-template-columns: 1fr;
            }

            .timeline {
                margin-left: 0;
                margin-right: 0;
            }

            .timeline-step p {
                font-size: 9px;
            }

            .order-item {
                align-items: flex-start;
            }

            .item-image {
                width: 70px;
                height: 70px;
            }

            .item-info h3 {
                font-size: 14px;
            }

            .item-price {
                min-width: auto;
            }

            .item-price .subtotal {
                font-size: 14px;
            }
        }

    </style>

</head>

<body>


<!-- NAVBAR -->

<header class="navbar">

    <div class="logo">
        <a href="index.php">
            <img src="images/logo.png" alt="DURAN'S Apparel">
        </a>
    </div>

    <nav class="menu">

        <a href="index.php">HOME</a>

        <a href="about.php">ABOUT US</a>

        <a href="collection.php">COLLECTIONS</a>

        <a href="contact.php">CONTACT US</a>

    </nav>

    <div class="account">

        <span class="user-name">
        Hello, <?php echo htmlspecialchars($_SESSION["full_name"]); ?>
        </span>

        <a href="logout.php" class="login">
            Log Out
        </a>

        <a href="cart.php" class="cart">
            🛒
        </a>

    </div>

</header>


<!-- MY ORDERS -->

<main class="track-container">

    <div class="track-title">

        <h1>My Orders</h1>

        <p>
            View and track all of your orders.
        </p>

    </div>


    <?php if ($orders->num_rows === 0): ?>

        <div class="no-orders">

            <h2>No Orders Yet</h2>

            <p>
                You haven't placed any orders yet.
            </p>

            <a href="collection.php" class="shop-btn">
                Shop Now
            </a>

        </div>


    <?php else: ?>


        <?php while ($order = $orders->fetch_assoc()): ?>

            <?php

            $status = $order["status"];

            $steps = [
                "Pending",
                "Processing",
                "Shipped",
                "Completed"
            ];

            $current_step = array_search($status, $steps);

            if ($current_step === false) {
                $current_step = -1;
            }

            ?>


            <div class="order-card">


                <!-- ORDER HEADER -->

                <div class="order-header">

                    <div class="order-number">

                        <h2>
                            Order #<?= htmlspecialchars($order["order_id"]) ?>
                        </h2>

                        <p>
                            Ordered on
                            <?= date(
                                "F d, Y h:i A",
                                strtotime($order["order_date"])
                            ) ?>
                        </p>

                    </div>


                    <div class="status <?= strtolower($status) ?>">

                        <?= htmlspecialchars($status) ?>

                    </div>

                </div>


                <!-- TIMELINE -->

                <?php if ($status !== "Cancelled"): ?>

                    <div class="timeline">

                        <?php foreach ($steps as $index => $step): ?>

                            <div class="timeline-step
                                <?php

                                if ($index < $current_step) {
                                    echo "completed";
                                }

                                elseif ($index == $current_step) {
                                    echo "active";
                                }

                                ?>
                            ">

                                <div class="timeline-circle"></div>

                                <p>
                                    <?= $step ?>
                                </p>

                            </div>

                        <?php endforeach; ?>

                    </div>

                <?php else: ?>

                    <div style="
                        background:#f8d7da;
                        color:#721c24;
                        padding:15px;
                        margin-bottom:25px;
                        text-align:center;
                        font-weight:700;
                    ">
                        This order has been cancelled.
                    </div>

                <?php endif; ?>


                <!-- ITEMS -->

                <h3 class="items-title">
                    Items Ordered
                </h3>


                <div class="order-items">

                    <?php

                    /*
                     * Get the products belonging to this order
                     */
                    $item_stmt = $conn->prepare("
                        SELECT
                            oi.product_id,
                            oi.quantity,
                            oi.price,
                            oi.subtotal,
                            p.product_name,
                            p.image
                        FROM order_items oi
                        INNER JOIN products p
                            ON oi.product_id = p.product_id
                        WHERE oi.order_id = ?
                        ORDER BY oi.order_item_id ASC
                    ");

                    $item_stmt->bind_param(
                        "i",
                        $order["order_id"]
                    );

                    $item_stmt->execute();

                    $items = $item_stmt->get_result();

                    ?>


                    <?php while ($item = $items->fetch_assoc()): ?>

                        <div class="order-item">


                            <!-- PRODUCT IMAGE -->

                            <?php if (!empty($item["image"])): ?>

                                <img
                                    src="<?= htmlspecialchars($item["image"]) ?>"
                                    alt="<?= htmlspecialchars($item["product_name"]) ?>"
                                    class="item-image"
                                >

                            <?php else: ?>

                                <div class="item-image"></div>

                            <?php endif; ?>


                            <!-- PRODUCT INFORMATION -->

                            <div class="item-info">

                                <h3>
                                    <?= htmlspecialchars($item["product_name"]) ?>
                                </h3>

                                <p>
                                    Quantity:
                                    <strong>
                                        <?= $item["quantity"] ?>
                                    </strong>
                                </p>

                                <p>
                                    Price:
                                    ₱<?= number_format($item["price"], 2) ?>
                                </p>

                            </div>


                            <!-- SUBTOTAL -->

                            <div class="item-price">

                                <div class="price">
                                    Subtotal
                                </div>

                                <div class="subtotal">

                                    ₱<?= number_format(
                                        $item["subtotal"],
                                        2
                                    ) ?>

                                </div>

                            </div>


                        </div>

                    <?php endwhile; ?>


                </div>


                <!-- CUSTOMER AND ADDRESS -->

                <div class="order-details">


                    <div class="details-box">

                        <h3>
                            Customer Information
                        </h3>

                        <p>

                            <strong>Name:</strong>
                            <?= htmlspecialchars(
                                $order["customer_name"]
                            ) ?>

                            <br>

                            <strong>Email:</strong>
                            <?= htmlspecialchars(
                                $order["email"]
                            ) ?>

                            <br>

                            <strong>Phone:</strong>
                            <?= htmlspecialchars(
                                $order["phone"] ?: "Not provided"
                            ) ?>

                        </p>

                    </div>


                    <div class="details-box">

                        <h3>
                            Delivery Address
                        </h3>

                        <p>
                            <?= nl2br(
                                htmlspecialchars(
                                    $order["address"]
                                )
                            ) ?>
                        </p>

                    </div>


                </div>


                <!-- TOTAL -->

                <div class="order-total">

                    <span>
                        Total Amount:
                    </span>

                    <strong>
                        ₱<?= number_format(
                            $order["total_amount"],
                            2
                        ) ?>
                    </strong>

                </div>


            </div>


        <?php endwhile; ?>


    <?php endif; ?>


</main>

</body>

</html>