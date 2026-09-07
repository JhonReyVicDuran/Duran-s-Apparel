<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Sign Up | DURAN'S Apparel</title>

    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="signup.css">
</head>

<body>

    <!-- =====================================================
         NAVBAR
    ===================================================== -->

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

            <a href="login.php" class="login">Log in</a>

         

            <a href="cart.php" class="cart">
                🛒
            </a>

        </div>

    </header>


    <!-- =====================================================
         SIGN UP PAGE
    ===================================================== -->

    <main class="signup-page">

        <div class="signup-container">

            <div class="signup-header">

                <h1>
                    Create <span>Account</span>
                </h1>

                <p>
                    Join DURAN'S Apparel and discover your style.
                </p>

            </div>


            <form
                class="signup-form"
                id="signupForm"
                action="#"
                method="POST"
            >

                <!-- FULL NAME -->

                <div class="form-group">

                    <label for="fullname">
                        Full Name
                    </label>

                    <input
                        type="text"
                        id="fullname"
                        name="fullname"
                        placeholder="Enter your full name"
                        required
                    >

                </div>


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

                    <label for="password">
                        Password
                    </label>

                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Create a password"
                        minlength="6"
                        required
                    >

                    <div
                        class="password-message"
                        id="passwordMessage"
                    >
                        Password must be at least 6 characters.
                    </div>

                </div>


                <!-- CONFIRM PASSWORD -->

                <div class="form-group">

                    <label for="confirmPassword">
                        Confirm Password
                    </label>

                    <input
                        type="password"
                        id="confirmPassword"
                        name="confirmPassword"
                        placeholder="Confirm your password"
                        required
                    >

                    <div
                        class="password-message"
                        id="confirmMessage"
                    >
                        Passwords do not match.
                    </div>

                </div>


                <!-- TERMS -->

                <div class="terms">

                    <input
                        type="checkbox"
                        id="terms"
                        required
                    >

                    <label for="terms">

                        I agree to the
                        <a href="#">
                            Terms & Conditions
                        </a>

                        and

                        <a href="#">
                            Privacy Policy
                        </a>.

                    </label>

                </div>


                <!-- BUTTON -->

                <button
                    type="submit"
                    class="signup-button"
                >
                    CREATE ACCOUNT
                </button>

            </form>


            <!-- LOGIN LINK -->

            <div class="login-link">

                Already have an account?

                <a href="login.php">
                    Log in
                </a>

            </div>

        </div>

    </main>


    <!-- =====================================================
         FOOTER
    ===================================================== -->

    <footer>

        <p>
            © 2026 DURAN'S Apparel.
            All Rights Reserved.
        </p>

    </footer>


    <!-- =====================================================
         JAVASCRIPT
    ===================================================== -->

    <script>

        const signupForm =
            document.getElementById("signupForm");

        const password =
            document.getElementById("password");

        const confirmPassword =
            document.getElementById("confirmPassword");

        const passwordMessage =
            document.getElementById("passwordMessage");

        const confirmMessage =
            document.getElementById("confirmMessage");


        signupForm.addEventListener("submit", function(event) {

            let valid = true;


            /* PASSWORD LENGTH */

            if (password.value.length < 6) {

                passwordMessage.style.display = "block";

                valid = false;

            } else {

                passwordMessage.style.display = "none";

            }


            /* PASSWORD MATCH */

            if (password.value !== confirmPassword.value) {

                confirmMessage.style.display = "block";

                valid = false;

            } else {

                confirmMessage.style.display = "none";

            }


            if (!valid) {

                event.preventDefault();

            }

        });


        /* =====================================================
           CART COUNT
        ===================================================== */

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