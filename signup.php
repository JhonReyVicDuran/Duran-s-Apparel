<?php

/* =====================================================
   START SESSION
===================================================== */

session_start();


/* =====================================================
   DATABASE CONNECTION
===================================================== */

require_once "db.php";


/* =====================================================
   VARIABLES
===================================================== */

$error = "";
$success = "";

$fullname = "";
$email = "";


/* =====================================================
   SIGN UP PROCESS
===================================================== */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $fullname = trim($_POST["fullname"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";
    $confirmPassword = $_POST["confirmPassword"] ?? "";


    /* =================================================
       CHECK EMPTY FIELDS
    ================================================= */

    if (
        empty($fullname) ||
        empty($email) ||
        empty($password) ||
        empty($confirmPassword)
    ) {

        $error = "Please fill in all required fields.";

    }


    /* =================================================
       CHECK EMAIL
    ================================================= */

    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $error = "Please enter a valid email address.";

    }


    /* =================================================
       CHECK PASSWORD LENGTH
    ================================================= */

    elseif (strlen($password) < 6) {

        $error = "Password must be at least 6 characters.";

    }


    /* =================================================
       CHECK PASSWORD MATCH
    ================================================= */

    elseif ($password !== $confirmPassword) {

        $error = "Passwords do not match.";

    }


    else {

        /* =================================================
           CHECK IF EMAIL ALREADY EXISTS
        ================================================= */

        $check = $conn->prepare(
            "SELECT user_id
             FROM users
             WHERE email = ?"
        );

        $check->bind_param(
            "s",
            $email
        );

        $check->execute();

        $result = $check->get_result();


        if ($result->num_rows > 0) {

            $error =
                "An account with this email already exists.";

        }

        else {

            /* =================================================
               HASH PASSWORD
            ================================================= */

            $hashedPassword = password_hash(
                $password,
                PASSWORD_DEFAULT
            );


            /* =================================================
               SAVE ACCOUNT TO DATABASE
            ================================================= */

            $stmt = $conn->prepare(
                "INSERT INTO users
                (full_name, email, password)
                VALUES (?, ?, ?)"
            );

            $stmt->bind_param(
                "sss",
                $fullname,
                $email,
                $hashedPassword
            );


            if ($stmt->execute()) {

                $success =
                    "Account created successfully! You can now log in.";

                $fullname = "";
                $email = "";

            }

            else {

                $error =
                    "Something went wrong. Please try again.";

            }


            $stmt->close();

        }


        $check->close();

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

    <title>Sign Up | DURAN'S Apparel</title>


    <link
        rel="stylesheet"
        href="style.css"
    >

    <link
        rel="stylesheet"
        href="signup.css"
    >

</head>


<body>


    <!-- =====================================================
         NAVBAR
    ===================================================== -->

    <header class="navbar">


        <!-- LOGO -->

        <div class="logo">

            <a href="index.php">

                <img
                    src="images/logo.png"
                    alt="DURAN'S Apparel"
                >

            </a>

        </div>



        <!-- MENU -->

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



        <!-- ACCOUNT -->

        <div class="account">


            <?php if (isset($_SESSION["user_id"])): ?>


                <!-- USER NAME -->

                <span class="user-name">

                    Hello,
                    <?php

                    echo htmlspecialchars(
                        $_SESSION["full_name"]
                    );

                    ?>

                </span>


                <!-- LOG OUT -->

                <a
                    href="logout.php"
                    class="logout"
                >
                    Log Out
                </a>


            <?php else: ?>


                <!-- LOG IN -->

                <a
                    href="login.php"
                    class="login"
                >
                    Log in
                </a>


            <?php endif; ?>


            <!-- CART -->

            <a
                href="cart.php"
                class="cart"
            >
                🛒
            </a>


        </div>

    </header>



    <!-- =====================================================
         SIGN UP PAGE
    ===================================================== -->

    <main class="signup-page">


        <div class="signup-container">


            <!-- HEADER -->

            <div class="signup-header">

                <h1>

                    Create
                    <span>Account</span>

                </h1>


                <p>

                    Join DURAN'S Apparel and
                    discover your style.

                </p>

            </div>



            <!-- =================================================
                 SUCCESS MESSAGE
            ================================================= -->

            <?php if (!empty($success)): ?>

                <div class="success-message">

                    <?php

                    echo htmlspecialchars(
                        $success
                    );

                    ?>

                </div>

            <?php endif; ?>



            <!-- =================================================
                 ERROR MESSAGE
            ================================================= -->

            <?php if (!empty($error)): ?>

                <div class="error-message">

                    <?php

                    echo htmlspecialchars(
                        $error
                    );

                    ?>

                </div>

            <?php endif; ?>



            <!-- =================================================
                 SIGN UP FORM
            ================================================= -->

            <form
                class="signup-form"
                id="signupForm"
                action="signup.php"
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
                        value="<?php

                            echo htmlspecialchars(
                                $fullname
                            );

                        ?>"
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
                        value="<?php

                            echo htmlspecialchars(
                                $email
                            );

                        ?>"
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

                        Password must be at least
                        6 characters.

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
                        name="terms"
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


        signupForm.addEventListener(
            "submit",
            function(event) {

                let valid = true;


                /* PASSWORD LENGTH */

                if (password.value.length < 6) {

                    passwordMessage.style.display =
                        "block";

                    valid = false;

                }

                else {

                    passwordMessage.style.display =
                        "none";

                }


                /* PASSWORD MATCH */

                if (
                    password.value !==
                    confirmPassword.value
                ) {

                    confirmMessage.style.display =
                        "block";

                    valid = false;

                }

                else {

                    confirmMessage.style.display =
                        "none";

                }


                if (!valid) {

                    event.preventDefault();

                }

            }
        );

    </script>


</body>

</html>