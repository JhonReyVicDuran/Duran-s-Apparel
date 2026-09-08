<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Size Guide - DURAN'S Apparel</title>

    <link rel="stylesheet" href="style.css">

    <style>

        /* =========================================
           SIZE GUIDE PAGE
        ========================================= */

        body {
            background: #f5f5f5;
            color: #000;
        }

        .size-guide-container {
            width: 90%;
            max-width: 1200px;
            margin: 130px auto 70px;
        }

        /* HEADER */

        .size-guide-header {
            text-align: center;
            margin-bottom: 50px;
        }

        .size-guide-header h1 {
            font-size: 42px;
            font-weight: 900;
            color: red;
            text-transform: uppercase;
            margin-bottom: 12px;
        }

        .size-guide-header p {
            color: #666;
            font-size: 15px;
            max-width: 650px;
            margin: auto;
            line-height: 1.7;
        }

        /* CATEGORY */

        .size-section {
            background: #fff;
            margin-bottom: 35px;
            padding: 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.07);
        }

        .size-section h2 {
            font-size: 24px;
            font-weight: 900;
            text-transform: uppercase;
            margin-bottom: 8px;
        }

        .size-section-description {
            color: #777;
            font-size: 13px;
            margin-bottom: 22px;
        }

        /* TABLE */

        .size-table-wrapper {
            overflow-x: auto;
        }

        .size-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 650px;
        }

        .size-table th {
            background: #000;
            color: #fff;
            padding: 15px 12px;
            font-size: 13px;
            text-transform: uppercase;
            font-weight: 800;
            text-align: center;
        }

        .size-table td {
            padding: 15px 12px;
            border-bottom: 1px solid #ddd;
            text-align: center;
            font-size: 14px;
        }

        .size-table tr:last-child td {
            border-bottom: none;
        }

        .size-table tr:hover td {
            background: #f7f7f7;
        }

        .size-name {
            font-weight: 900;
        }

        /* HOW TO MEASURE */

        .measure-section {
            background: #000;
            color: #fff;
            padding: 40px;
            margin-bottom: 35px;
        }

        .measure-section h2 {
            text-align: center;
            font-size: 27px;
            font-weight: 900;
            text-transform: uppercase;
            margin-bottom: 30px;
        }

        .measure-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 25px;
        }

        .measure-card {
            border: 1px solid #444;
            padding: 25px;
            text-align: center;
        }

        .measure-number {
            width: 42px;
            height: 42px;
            background: #ff0505;
            color: #fff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            font-weight: 900;
        }

        .measure-card h3 {
            font-size: 16px;
            text-transform: uppercase;
            margin-bottom: 10px;
        }

        .measure-card p {
            color: #ccc;
            font-size: 13px;
            line-height: 1.6;
        }

        /* FIT NOTE */

        .size-note {
            background: #fff;
            border-left: 5px solid #ff0505;
            padding: 25px;
            margin-bottom: 35px;
        }

        .size-note h3 {
            font-size: 17px;
            text-transform: uppercase;
            margin-bottom: 8px;
        }

        .size-note p {
            color: #666;
            font-size: 13px;
            line-height: 1.7;
        }

        /* BUTTON */

        .size-button-container {
            text-align: center;
            margin-top: 40px;
        }

        .shop-button {
            display: inline-block;
            background: #000;
            color: #fff;
            padding: 15px 35px;
            font-size: 13px;
            font-weight: 900;
            text-transform: uppercase;
            transition: .3s ease;
        }

        .shop-button:hover {
            background: #ff0505;
        }

        /* MOBILE */

        @media (max-width: 768px) {

            .size-guide-container {
                width: 94%;
                margin-top: 110px;
            }

            .size-guide-header h1 {
                font-size: 30px;
            }

            .size-section {
                padding: 20px;
            }

            .measure-section {
                padding: 25px 20px;
            }

            .measure-grid {
                grid-template-columns: 1fr;
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
     SIZE GUIDE
========================================= -->

<main class="size-guide-container">


    <div class="size-guide-header">

        <h1>Size Guide</h1>

        <p>
            Find your perfect fit.
            Use the measurements below to choose the size
            that works best for you.
        </p>

    </div>


    <!-- =====================================
         T-SHIRTS
    ====================================== -->

    <section class="size-section">

        <h2>T-Shirts</h2>

        <p class="size-section-description">
            Recommended measurements for our regular-fit T-shirts.
        </p>


        <div class="size-table-wrapper">

            <table class="size-table">

                <thead>

                    <tr>
                        <th>Size</th>
                        <th>Chest (in)</th>
                        <th>Length (in)</th>
                        <th>Shoulder (in)</th>
                        <th>Suggested Height</th>
                    </tr>

                </thead>

                <tbody>

                    <tr>
                        <td class="size-name">XS</td>
                        <td>32–34</td>
                        <td>26</td>
                        <td>16</td>
                        <td>5'0"–5'3"</td>
                    </tr>

                    <tr>
                        <td class="size-name">S</td>
                        <td>34–36</td>
                        <td>27</td>
                        <td>17</td>
                        <td>5'2"–5'5"</td>
                    </tr>

                    <tr>
                        <td class="size-name">M</td>
                        <td>36–38</td>
                        <td>28</td>
                        <td>18</td>
                        <td>5'4"–5'8"</td>
                    </tr>

                    <tr>
                        <td class="size-name">L</td>
                        <td>38–41</td>
                        <td>29</td>
                        <td>19</td>
                        <td>5'7"–5'11"</td>
                    </tr>

                    <tr>
                        <td class="size-name">XL</td>
                        <td>41–44</td>
                        <td>30</td>
                        <td>20</td>
                        <td>5'10"–6'2"</td>
                    </tr>

                    <tr>
                        <td class="size-name">XXL</td>
                        <td>44–48</td>
                        <td>31</td>
                        <td>21</td>
                        <td>6'0"+</td>
                    </tr>

                </tbody>

            </table>

        </div>

    </section>


    <!-- =====================================
         COMPRESSION SHIRT
    ====================================== -->

    <section class="size-section">

        <h2>Compression Shirts</h2>

        <p class="size-section-description">
            Compression fit. For a tighter athletic fit,
            consider choosing your usual size.
        </p>


        <div class="size-table-wrapper">

            <table class="size-table">

                <thead>

                    <tr>
                        <th>Size</th>
                        <th>Chest (in)</th>
                        <th>Waist (in)</th>
                        <th>Length (in)</th>
                    </tr>

                </thead>

                <tbody>

                    <tr>
                        <td class="size-name">XS</td>
                        <td>30–32</td>
                        <td>24–26</td>
                        <td>25</td>
                    </tr>

                    <tr>
                        <td class="size-name">S</td>
                        <td>32–34</td>
                        <td>26–28</td>
                        <td>26</td>
                    </tr>

                    <tr>
                        <td class="size-name">M</td>
                        <td>34–37</td>
                        <td>28–31</td>
                        <td>27</td>
                    </tr>

                    <tr>
                        <td class="size-name">L</td>
                        <td>37–40</td>
                        <td>31–34</td>
                        <td>28</td>
                    </tr>

                    <tr>
                        <td class="size-name">XL</td>
                        <td>40–43</td>
                        <td>34–37</td>
                        <td>29</td>
                    </tr>

                    <tr>
                        <td class="size-name">XXL</td>
                        <td>43–46</td>
                        <td>37–40</td>
                        <td>30</td>
                    </tr>

                </tbody>

            </table>

        </div>

    </section>


    <!-- =====================================
         HOODIES
    ====================================== -->

    <section class="size-section">

        <h2>Hoodies</h2>

        <p class="size-section-description">
            Designed for a comfortable and relaxed fit.
        </p>


        <div class="size-table-wrapper">

            <table class="size-table">

                <thead>

                    <tr>
                        <th>Size</th>
                        <th>Chest (in)</th>
                        <th>Length (in)</th>
                        <th>Shoulder (in)</th>
                        <th>Sleeve (in)</th>
                    </tr>

                </thead>

                <tbody>

                    <tr>
                        <td class="size-name">XS</td>
                        <td>36–38</td>
                        <td>25</td>
                        <td>17</td>
                        <td>23</td>
                    </tr>

                    <tr>
                        <td class="size-name">S</td>
                        <td>38–40</td>
                        <td>26</td>
                        <td>18</td>
                        <td>24</td>
                    </tr>

                    <tr>
                        <td class="size-name">M</td>
                        <td>40–42</td>
                        <td>27</td>
                        <td>19</td>
                        <td>25</td>
                    </tr>

                    <tr>
                        <td class="size-name">L</td>
                        <td>42–45</td>
                        <td>28</td>
                        <td>20</td>
                        <td>26</td>
                    </tr>

                    <tr>
                        <td class="size-name">XL</td>
                        <td>45–48</td>
                        <td>29</td>
                        <td>21</td>
                        <td>27</td>
                    </tr>

                    <tr>
                        <td class="size-name">XXL</td>
                        <td>48–52</td>
                        <td>30</td>
                        <td>22</td>
                        <td>28</td>
                    </tr>

                </tbody>

            </table>

        </div>

    </section>


    <!-- =====================================
         SHORTS
    ====================================== -->

    <section class="size-section">

        <h2>Shorts</h2>

        <p class="size-section-description">
            Measure around your natural waist for the best fit.
        </p>


        <div class="size-table-wrapper">

            <table class="size-table">

                <thead>

                    <tr>
                        <th>Size</th>
                        <th>Waist (in)</th>
                        <th>Hip (in)</th>
                        <th>Outseam (in)</th>
                    </tr>

                </thead>

                <tbody>

                    <tr>
                        <td class="size-name">XS</td>
                        <td>26–28</td>
                        <td>34–36</td>
                        <td>17</td>
                    </tr>

                    <tr>
                        <td class="size-name">S</td>
                        <td>28–30</td>
                        <td>36–38</td>
                        <td>18</td>
                    </tr>

                    <tr>
                        <td class="size-name">M</td>
                        <td>30–32</td>
                        <td>38–40</td>
                        <td>19</td>
                    </tr>

                    <tr>
                        <td class="size-name">L</td>
                        <td>32–35</td>
                        <td>40–43</td>
                        <td>20</td>
                    </tr>

                    <tr>
                        <td class="size-name">XL</td>
                        <td>35–38</td>
                        <td>43–46</td>
                        <td>21</td>
                    </tr>

                    <tr>
                        <td class="size-name">XXL</td>
                        <td>38–42</td>
                        <td>46–50</td>
                        <td>22</td>
                    </tr>

                </tbody>

            </table>

        </div>

    </section>


    <!-- =====================================
         PANTS
    ====================================== -->

    <section class="size-section">

        <h2>Pants</h2>

        <p class="size-section-description">
            Measure your waist and inseam to find your best size.
        </p>


        <div class="size-table-wrapper">

            <table class="size-table">

                <thead>

                    <tr>
                        <th>Size</th>
                        <th>Waist (in)</th>
                        <th>Hip (in)</th>
                        <th>Inseam (in)</th>
                    </tr>

                </thead>

                <tbody>

                    <tr>
                        <td class="size-name">XS</td>
                        <td>26–28</td>
                        <td>34–36</td>
                        <td>29</td>
                    </tr>

                    <tr>
                        <td class="size-name">S</td>
                        <td>28–30</td>
                        <td>36–38</td>
                        <td>30</td>
                    </tr>

                    <tr>
                        <td class="size-name">M</td>
                        <td>30–32</td>
                        <td>38–40</td>
                        <td>31</td>
                    </tr>

                    <tr>
                        <td class="size-name">L</td>
                        <td>32–35</td>
                        <td>40–43</td>
                        <td>32</td>
                    </tr>

                    <tr>
                        <td class="size-name">XL</td>
                        <td>35–38</td>
                        <td>43–46</td>
                        <td>32</td>
                    </tr>

                    <tr>
                        <td class="size-name">XXL</td>
                        <td>38–42</td>
                        <td>46–50</td>
                        <td>33</td>
                    </tr>

                </tbody>

            </table>

        </div>

    </section>


    <!-- =====================================
         HOW TO MEASURE
    ====================================== -->

    <section class="measure-section">

        <h2>How To Measure</h2>


        <div class="measure-grid">


            <div class="measure-card">

                <div class="measure-number">
                    1
                </div>

                <h3>Chest</h3>

                <p>
                    Wrap the measuring tape around the
                    fullest part of your chest. Keep the tape
                    level and comfortable.
                </p>

            </div>


            <div class="measure-card">

                <div class="measure-number">
                    2
                </div>

                <h3>Waist</h3>

                <p>
                    Measure around your natural waist.
                    Keep the tape comfortably snug without
                    pulling it too tight.
                </p>

            </div>


            <div class="measure-card">

                <div class="measure-number">
                    3
                </div>

                <h3>Inseam</h3>

                <p>
                    Measure from the crotch down to the
                    bottom of your ankle to determine your
                    ideal pants length.
                </p>

            </div>


        </div>

    </section>


    <!-- =====================================
         SIZE NOTE
    ====================================== -->

    <div class="size-note">

        <h3>Important Note</h3>

        <p>
            These measurements are a general guide and may
            vary slightly depending on the product style and
            fabric. If you are between two sizes, we recommend
            choosing the larger size for a more comfortable fit.
        </p>

    </div>


    <!-- BUTTON -->

    <div class="size-button-container">

        <a href="collection.php" class="shop-button">
            Shop Collection
        </a>

    </div>


</main>


</body>

</html>