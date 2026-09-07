<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Log In | DURAN'S Apparel</title>

    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="login.css">
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

            <a href="signup.php" class="signup">
                Sign up
            </a>

            <a href="cart.php" class="cart">
                🛒 
            </a>

        </div>

    </header>


    <!-- ================= LOGIN ================= -->

    <main class="login-page">

        <div class="login-container">

            <div class="login-header">

                <h1>
                    Log <span>In</span>
                </h1>

                <p>
                    Log in to your DURAN'S Apparel account.
                </p>

            </div>


            <form
                class="login-form"
                id="loginForm"
                action="#"
                method="POST"
            >

                <!-- EMAIL -->

                <div class="form-group">

                    <label for="email">
                        Email Address
                    </label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        placeholder="Enter your email"
                        required
                    >

                </div>


                <!-- PASSWORD -->

                <div class="form-group">

                    <div class="password-label">

                        <label for="password">
                            Password
                        </label>

                        <a href="#">
                            Forgot Password?
                        </a>

                    </div>

                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Enter your password"
                        required
                    >

                </div>


                <!-- REMEMBER ME -->

                <div class="remember">

                    <input
                        type="checkbox"
                        id="remember"
                        name="remember"
                    >

                    <label for="remember">
                        Remember me
                    </label>

                </div>


                <!-- LOGIN BUTTON -->

                <button
                    type="submit"
                    class="login-button"
                >
                    LOG IN
                </button>

            </form>


            <!-- SIGN UP -->

            <div class="signup-link">

                Don't have an account?

                <a href="signup.php">
                    Sign up
                </a>

            </div>

        </div>

    </main>


    <!-- ================= FOOTER ================= -->

    <footer>

        <p>
            © 2026 DURAN'S Apparel.
            All Rights Reserved.
        </p>

    </footer>


    <!-- ================= JAVASCRIPT ================= -->

    <script>

        function updateCartCount() {

            const cart =
                JSON.parse(localStorage.getItem("duranCart")) || [];

            const count =
                cart.reduce(
                    (total, item) => total + item.quantity,
                    0
                );

            const cartCount =
                document.getElementById("cartCount");

            if (cartCount) {
                cartCount.textContent = count;
            }

        }

        updateCartCount();

    </script>

</body>

</html>