<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>FAQs - DURAN'S Apparel</title>

    <link rel="stylesheet" href="style.css">

    <style>

        /* =========================================
           FAQ PAGE
        ========================================= */

        body {
            background: #f5f5f5;
            color: #000;
        }

        .faq-container {
            width: 90%;
            max-width: 1000px;
            margin: 130px auto 70px;
        }

        /* HEADER */

        .faq-header {
            text-align: center;
            margin-bottom: 50px;
        }

        .faq-header h1 {
            font-size: 42px;
            font-weight: 900;
            color: red;
            text-transform: uppercase;
            margin-bottom: 12px;
        }

        .faq-header p {
            max-width: 650px;
            margin: auto;
            color: #666;
            font-size: 15px;
            line-height: 1.7;
        }

        /* FAQ SECTION */

        .faq-section {
            margin-bottom: 35px;
        }

        .faq-category {
            font-size: 22px;
            font-weight: 900;
            text-transform: uppercase;
            margin-bottom: 15px;
            padding-left: 12px;
            border-left: 5px solid #ff0505;
        }

        /* FAQ ITEM */

        .faq-item {
            background: #fff;
            margin-bottom: 10px;
            box-shadow: 0 3px 12px rgba(0, 0, 0, 0.05);
        }

        .faq-question {
            width: 100%;
            background: #fff;
            border: none;
            padding: 20px 22px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            text-align: left;
            cursor: pointer;
            font-size: 15px;
            font-weight: 800;
            color: #000;
        }

        .faq-question:hover {
            color: #ff0505;
        }

        .faq-icon {
            font-size: 22px;
            font-weight: 400;
            transition: transform 0.3s ease;
            margin-left: 15px;
            flex-shrink: 0;
        }

        .faq-item.active .faq-icon {
            transform: rotate(45deg);
            color: #ff0505;
        }

        /* ANSWER */

        .faq-answer {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.35s ease;
        }

        .faq-answer-content {
            padding: 0 22px 20px;
            color: #666;
            font-size: 14px;
            line-height: 1.8;
        }

        /* CONTACT BOX */

        .faq-contact {
            background: #000;
            color: #fff;
            text-align: center;
            padding: 45px 30px;
            margin-top: 50px;
        }

        .faq-contact h2 {
            font-size: 25px;
            font-weight: 900;
            text-transform: uppercase;
            margin-bottom: 10px;
        }

        .faq-contact p {
            color: #ccc;
            font-size: 14px;
            margin-bottom: 25px;
        }

        .faq-contact a {
            display: inline-block;
            background: #ff0505;
            color: #fff;
            padding: 14px 30px;
            font-size: 13px;
            font-weight: 900;
            text-transform: uppercase;
            transition: 0.3s ease;
        }

        .faq-contact a:hover {
            background: #fff;
            color: #000;
        }

        /* MOBILE */

        @media (max-width: 768px) {

            .faq-container {
                width: 94%;
                margin-top: 110px;
            }

            .faq-header h1 {
                font-size: 30px;
            }

            .faq-question {
                padding: 17px;
                font-size: 14px;
            }

            .faq-answer-content {
                padding: 0 17px 18px;
                font-size: 13px;
            }

            .faq-category {
                font-size: 19px;
            }

            .faq-contact {
                padding: 35px 20px;
            }

        }

    </style>

</head>


<body>


<!-- =========================================
     NAVBAR
========================================= -->

<header class="navbar">

    <div class="logo">

        <a href="index.php">

            <img
                src="images/logo.png"
                alt="DURAN'S Apparel"
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

        <?php if (isset($_SESSION["user_id"])): ?>

            <span>
                Hello, <?= htmlspecialchars($_SESSION["full_name"]) ?>
            </span>

            <a href="track_order.php">
                MY ORDERS
            </a>

            <a href="cart.php" class="cart">
                🛒
            </a>

            <a href="logout.php" class="login">
                Log Out
            </a>

        <?php else: ?>

            <a href="login.php" class="login">
                Log In
            </a>

            <a href="signup.php" class="signup">
                Sign Up
            </a>

            <a href="cart.php" class="cart">
                🛒
            </a>

        <?php endif; ?>

    </div>

</header>


<!-- =========================================
     FAQ CONTENT
========================================= -->

<main class="faq-container">


    <div class="faq-header">

        <h1>FAQs</h1>

        <p>
            Have questions about DURAN'S Apparel?
            Find answers to some of the most common questions
            about our products, orders, cart & items, and delivery.
        </p>

    </div>


    <!-- =====================================
         PRODUCTS
    ====================================== -->

    <section class="faq-section">

        <h2 class="faq-category">
            Products
        </h2>


        <div class="faq-item">

            <button class="faq-question">

                <span>
                    What sizes are available?
                </span>

                <span class="faq-icon">
                    +
                </span>

            </button>

            <div class="faq-answer">

                <div class="faq-answer-content">

                    Our clothing is generally available in
                    XS, S, M, L, XL, and XXL. However, available
                    sizes may vary depending on the product.

                    <br><br>

                    Please check our
                    <strong>Size Guide</strong>
                    before placing your order.

                </div>

            </div>

        </div>


        <div class="faq-item">

            <button class="faq-question">

                <span>
                    How do I choose the right size?
                </span>

                <span class="faq-icon">
                    +
                </span>

            </button>

            <div class="faq-answer">

                <div class="faq-answer-content">

                    We recommend measuring your chest, waist,
                    hips, and inseam and comparing your measurements
                    with our Size Guide.

                    <br><br>

                    If you are between two sizes, choosing the
                    larger size may provide a more comfortable fit.

                </div>

            </div>

        </div>


        <div class="faq-item">

            <button class="faq-question">

                <span>
                    Are the compression shirts supposed to fit tightly?
                </span>

                <span class="faq-icon">
                    +
                </span>

            </button>

            <div class="faq-answer">

                <div class="faq-answer-content">

                    Yes. Compression shirts are designed to have
                    a closer and more fitted feel compared with
                    regular T-shirts.

                </div>

            </div>

        </div>

    </section>


    <!-- =====================================
         ORDERS
    ====================================== -->

    <section class="faq-section">

        <h2 class="faq-category">
            Orders
        </h2>


        <div class="faq-item">

            <button class="faq-question">

                <span>
                    Do I need an account to place an order?
                </span>

                <span class="faq-icon">
                    +
                </span>

            </button>

            <div class="faq-answer">

                <div class="faq-answer-content">

                    Yes. You need to log in to your DURAN'S Apparel
                    account before adding products to your cart and
                    placing an order.

                </div>

            </div>

        </div>


        <div class="faq-item">

            <button class="faq-question">

                <span>
                    How can I view my orders?
                </span>

                <span class="faq-icon">
                    +
                </span>

            </button>

            <div class="faq-answer">

                <div class="faq-answer-content">

                    After logging in, click
                    <strong>Track Order</strong>
                    in the bottom footer bar.

                    <br><br>

                    You will be able to see your orders,
                    the items you purchased, your total amount,
                    and the current order status.

                </div>

            </div>

        </div>


        <div class="faq-item">

            <button class="faq-question">

                <span>
                    Can I change my order after placing it?
                </span>

                <span class="faq-icon">
                    +
                </span>

            </button>

            <div class="faq-answer">

                <div class="faq-answer-content">

                    Orders are processed after they are placed.
                    If you need to make a change, contact us as soon
                    as possible through our Contact Us page.

                    <br><br>

                    Changes may not be possible once the order has
                    already been processed or shipped.

                </div>

            </div>

        </div>


        <div class="faq-item">

            <button class="faq-question">

                <span>
                    Where can I track my order?
                </span>

                <span class="faq-icon">
                    +
                </span>

            </button>

            <div class="faq-answer">

                <div class="faq-answer-content">

                    Log in to your account and open
                    <strong>Track Order</strong>
                    at the bottom part of the home page.

                    <br><br>

                    Your order status will show whether your order
                    is Pending, Processing, Shipped, or Completed.

                </div>

            </div>

        </div>

    </section>


    <!-- =====================================
         CART & PAYMENT
    ====================================== -->

    <section class="faq-section">

        <h2 class="faq-category">
            Cart & Payment
        </h2>


        <div class="faq-item">

            <button class="faq-question">

                <span>
                    How do I add an item to my cart?
                </span>

                <span class="faq-icon">
                    +
                </span>

            </button>

            <div class="faq-answer">

                <div class="faq-answer-content">

                    Go to the Collections page, choose the product
                    you want, select the quantity, size, and click
                    <strong>ADD TO CART</strong>.

                </div>

            </div>

        </div>


        <div class="faq-item">

            <button class="faq-question">

                <span>
                    Can I change the quantity of an item in my cart?
                </span>

                <span class="faq-icon">
                    +
                </span>

            </button>

            <div class="faq-answer">

                <div class="faq-answer-content">

                    Yes. Open your cart and use the plus or minus
                    buttons beside the product to change the quantity.

                    <br><br>

                    The quantity cannot exceed the available stock.

                </div>

            </div>

        </div>


        <div class="faq-item">

            <button class="faq-question">

                <span>
                    What happens when a product is out of stock?
                </span>

                <span class="faq-icon">
                    +
                </span>

            </button>

            <div class="faq-answer">

                <div class="faq-answer-content">

                    Products with no available stock cannot be added
                    to the cart. Please check again later for
                    availability.

                </div>

            </div>

        </div>

    </section>


    <!-- =====================================
         DELIVERY
    ====================================== -->

    <section class="faq-section">

        <h2 class="faq-category">
            Delivery
        </h2>


        <div class="faq-item">

            <button class="faq-question">

                <span>
                    How long does delivery take?
                </span>

                <span class="faq-icon">
                    +
                </span>

            </button>

            <div class="faq-answer">

                <div class="faq-answer-content">

                    Delivery time may vary depending on your
                    location and the current delivery schedule.
                    Please check your order status for updates.

                </div>

            </div>

        </div>


        <div class="faq-item">

            <button class="faq-question">

                <span>
                    How do I know if my order has been shipped?
                </span>

                <span class="faq-icon">
                    +
                </span>

            </button>

            <div class="faq-answer">

                <div class="faq-answer-content">

                    Your order status will change to
                    <strong>Shipped</strong> once your order has
                    been prepared and sent for delivery.

                    <br><br>

                    You can check this through the
                    <strong>Track Order</strong> 
                    at the bottom part of the page.

                </div>

            </div>

        </div>


        <div class="faq-item">

            <button class="faq-question">

                <span>
                    What if my order arrives damaged or incomplete?
                </span>

                <span class="faq-icon">
                    +
                </span>

            </button>

            <div class="faq-answer">

                <div class="faq-answer-content">

                    Please contact DURAN'S Apparel as soon as
                    possible and provide your order number and
                    details about the problem so we can assist you.

                </div>

            </div>

        </div>

    </section>


    <!-- =====================================
         ACCOUNT
    ====================================== -->

    <section class="faq-section">

        <h2 class="faq-category">
            Account
        </h2>


        <div class="faq-item">

            <button class="faq-question">

                <span>
                    How do I create an account?
                </span>

                <span class="faq-icon">
                    +
                </span>

            </button>

            <div class="faq-answer">

                <div class="faq-answer-content">

                    Click <strong>SIGN UP</strong> in the navigation
                    bar and enter the required information to create
                    your DURAN'S Apparel account.

                </div>

            </div>

        </div>


        <div class="faq-item">

            <button class="faq-question">

                <span>
                    I forgot my password. What should I do?
                </span>

                <span class="faq-icon">
                    +
                </span>

            </button>

            <div class="faq-answer">

                <div class="faq-answer-content">

                    If you forget your password, contact the
                    DURAN'S Apparel for assistance
                    with your account.

                </div>

            </div>

        </div>


        <div class="faq-item">

            <button class="faq-question">

                <span>
                    Can I log out and still keep my account?
                </span>

                <span class="faq-icon">
                    +
                </span>

            </button>

            <div class="faq-answer">

                <div class="faq-answer-content">

                    Yes. Logging out only ends your current session.
                    Your account and order history will remain saved.

                </div>

            </div>

        </div>

    </section>


    <!-- =====================================
         CONTACT
    ====================================== -->

    <section class="faq-contact">

        <h2>
            Still Have Questions?
        </h2>

        <p>
            If you couldn't find the answer you're looking for,
            feel free to contact DURAN'S Apparel.
        </p>

        <a href="contact.php">
            Contact Us
        </a>

    </section>


</main>


<!-- =========================================
     FAQ JAVASCRIPT
========================================= -->

<script>

    const faqQuestions = document.querySelectorAll(".faq-question");

    faqQuestions.forEach(function(question) {

        question.addEventListener("click", function() {

            const item = this.parentElement;
            const answer = item.querySelector(".faq-answer");

            /* Close other FAQ items */

            document.querySelectorAll(".faq-item").forEach(function(otherItem) {

                if (otherItem !== item) {

                    otherItem.classList.remove("active");

                    const otherAnswer =
                        otherItem.querySelector(".faq-answer");

                    otherAnswer.style.maxHeight = null;

                }

            });


            /* Open / close selected FAQ */

            item.classList.toggle("active");

            if (item.classList.contains("active")) {

                answer.style.maxHeight =
                    answer.scrollHeight + "px";

            } else {

                answer.style.maxHeight = null;

            }

        });

    });

</script>


</body>

</html>