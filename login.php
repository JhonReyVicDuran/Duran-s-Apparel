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
   LOGIN VARIABLES
===================================================== */

$error = "";


/* =====================================================
   LOGIN PROCESS
===================================================== */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $login = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";
    $remember = isset($_POST["remember"]);


    /* =================================================
       CHECK EMPTY FIELDS
    ================================================= */

    if (empty($login) || empty($password)) {

        $error = "Please enter your email/username and password.";

    } else {

        /* =================================================
           CHECK ADMIN ACCOUNT FIRST
        ================================================= */

        $admin_stmt = $conn->prepare("
            SELECT admin_id, username, password
            FROM admin_users
            WHERE username = ?
            LIMIT 1
        ");

        $admin_stmt->bind_param("s", $login);
        $admin_stmt->execute();

        $admin_result = $admin_stmt->get_result();


        /* =================================================
           ADMIN FOUND
        ================================================= */

        if ($admin_result->num_rows === 1) {

            $admin = $admin_result->fetch_assoc();


            /*
                Your current admin password in the database
                is plain text:

                admin123
            */

            if ($password === $admin["password"]) {

                /* -----------------------------------------
                   CREATE ADMIN SESSION
                ----------------------------------------- */

                session_regenerate_id(true);

                $_SESSION["admin_id"] =
                    $admin["admin_id"];

                $_SESSION["admin_username"] =
                    $admin["username"];


                /* -----------------------------------------
                   REMEMBER ME
                ----------------------------------------- */

                if ($remember) {

                    setcookie(
                        "remember_me",
                        "1",
                        [
                            "expires" => time() + (30 * 24 * 60 * 60),
                            "path" => "/",
                            "secure" => isset($_SERVER["HTTPS"]),
                            "httponly" => false,
                            "samesite" => "Lax"
                        ]
                    );

                } else {

                    setcookie(
                        "remember_me",
                        "",
                        [
                            "expires" => time() - 3600,
                            "path" => "/"
                        ]
                    );

                }


                /* -----------------------------------------
                   ADMIN → ADMIN DASHBOARD
                ----------------------------------------- */

                header("Location: admin/index.php");
                exit;

            } else {

                $error = "Incorrect username or password.";

            }


        } else {

            /* =================================================
               ADMIN NOT FOUND
               NOW CHECK NORMAL USER
            ================================================= */

            $user_stmt = $conn->prepare("
                SELECT user_id, full_name, email, password
                FROM users
                WHERE email = ?
                LIMIT 1
            ");

            $user_stmt->bind_param("s", $login);
            $user_stmt->execute();

            $user_result = $user_stmt->get_result();


            /* =================================================
               USER FOUND
            ================================================= */

            if ($user_result->num_rows === 1) {

                $user = $user_result->fetch_assoc();


                /* -----------------------------------------
                   CHECK USER PASSWORD
                ----------------------------------------- */

                if (password_verify(
                    $password,
                    $user["password"]
                )) {

                    /* -------------------------------------
                       CREATE USER SESSION
                    ------------------------------------- */

                    session_regenerate_id(true);

                    $_SESSION["user_id"] =
                        $user["user_id"];

                    $_SESSION["full_name"] =
                        $user["full_name"];

                    $_SESSION["email"] =
                        $user["email"];


                    /* -------------------------------------
                       REMEMBER ME
                    ------------------------------------- */

                    if ($remember) {

                        setcookie(
                            "remember_me",
                            "1",
                            [
                                "expires" => time() + (30 * 24 * 60 * 60),
                                "path" => "/",
                                "secure" => isset($_SERVER["HTTPS"]),
                                "httponly" => false,
                                "samesite" => "Lax"
                            ]
                        );

                    } else {

                        setcookie(
                            "remember_me",
                            "",
                            [
                                "expires" => time() - 3600,
                                "path" => "/"
                            ]
                        );

                    }


                    /* -------------------------------------
                       USER → HOMEPAGE
                    ------------------------------------- */

                    header("Location: index.php");
                    exit;

                } else {

                    $error =
                        "Incorrect email/username or password.";

                }

            } else {

                $error =
                    "Incorrect email/username or password.";

            }

            $user_stmt->close();

        }

        $admin_stmt->close();

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

    <title>Log In | DURAN'S Apparel</title>

    <link
        rel="stylesheet"
        href="css/style.css"
    >

    <link
        rel="stylesheet"
        href="css/login.css"
    >

</head>


<body>


<!-- =====================================================
     NAVBAR
===================================================== -->

<header class="navbar">

    <div class="logo">

        <a href="index.php">

            <img
                src="admin/images/logo.png"
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

            <span class="user-name">

                Hello,

                <?php
                echo htmlspecialchars(
                    $_SESSION["full_name"]
                );
                ?>

            </span>

            <a
                href="logout.php"
                class="logout"
            >
                Log Out
            </a>

        <?php else: ?>

            <a
                href="signup.php"
                class="signup"
            >
                Sign up
            </a>

        <?php endif; ?>


        <a
            href="cart.php"
            class="cart"
        >
            🛒
        </a>

    </div>

</header>


<!-- =====================================================
     LOGIN
===================================================== -->

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


        <!-- =================================================
             ERROR MESSAGE
        ================================================= -->

        <?php if (!empty($error)): ?>

            <div class="error-message">

                <?php
                echo htmlspecialchars($error);
                ?>

            </div>

        <?php endif; ?>


        <!-- =================================================
             LOGIN FORM
        ================================================= -->

        <form
            class="login-form"
            id="loginForm"
            action="login.php"
            method="POST"
        >


            <!-- LOGIN FIELD -->

            <div class="form-group">

                <label for="email">
                    Email or Username
                </label>

                <input
                    type="text"
                    id="email"
                    name="email"
                    placeholder="Enter your email or username"
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
     REMEMBER ME SCRIPT
===================================================== -->

<script>

const loginForm =
    document.getElementById("loginForm");

const emailInput =
    document.getElementById("email");

const passwordInput =
    document.getElementById("password");

const rememberCheckbox =
    document.getElementById("remember");


/* =====================================================
   LOAD REMEMBERED LOGIN INFORMATION
===================================================== */

window.addEventListener(
    "DOMContentLoaded",
    function () {

        const savedEmail =
            localStorage.getItem(
                "duranRememberEmail"
            );

        const savedPassword =
            localStorage.getItem(
                "duranRememberPassword"
            );

        const rememberMe =
            getCookie("remember_me");


        if (
            rememberMe === "1" &&
            savedEmail &&
            savedPassword
        ) {

            emailInput.value =
                savedEmail;

            passwordInput.value =
                savedPassword;

            rememberCheckbox.checked =
                true;

        }

    }
);


/* =====================================================
   SAVE LOGIN INFORMATION
===================================================== */

loginForm.addEventListener(
    "submit",
    function () {

        if (rememberCheckbox.checked) {

            localStorage.setItem(
                "duranRememberEmail",
                emailInput.value
            );

            localStorage.setItem(
                "duranRememberPassword",
                passwordInput.value
            );

        } else {

            localStorage.removeItem(
                "duranRememberEmail"
            );

            localStorage.removeItem(
                "duranRememberPassword"
            );

        }

    }
);


/* =====================================================
   GET COOKIE
===================================================== */

function getCookie(name) {

    const cookies =
        document.cookie.split(";");


    for (
        let i = 0;
        i < cookies.length;
        i++
    ) {

        const cookie =
            cookies[i].trim();


        if (
            cookie.indexOf(
                name + "="
            ) === 0
        ) {

            return cookie.substring(
                name.length + 1
            );

        }

    }


    return null;

}

</script>


</body>

</html>