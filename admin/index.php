<?php

session_start();

require_once "../db.php";


/* =====================================================
   LOGOUT
===================================================== */

if (isset($_GET["logout"])) {

    $_SESSION = [];

    session_destroy();

    header("Location: ../index.php");

    exit;
}


/* =====================================================
   ADMIN LOGIN
===================================================== */

$login_error = "";

if (!isset($_SESSION["admin_id"])) {

    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["admin_login"])) {

        $username = trim($_POST["username"] ?? "");
        $password = $_POST["password"] ?? "";

        if ($username === "" || $password === "") {

            $login_error = "Please enter username and password.";

        } else {

            $stmt = $conn->prepare("
                SELECT admin_id, username, password
                FROM admin_users
                WHERE username = ?
                LIMIT 1
            ");

            if (!$stmt) {

                $login_error = "Database error: " . $conn->error;

            } else {

                $stmt->bind_param("s", $username);

                $stmt->execute();

                $result = $stmt->get_result();

                $admin = $result->fetch_assoc();

                if ($admin) {

                    /*
                     * Your current database contains:
                     *
                     * username = admin
                     * password = admin123
                     *
                     * This comparison works with your
                     * current plain-text password.
                     */

                    if ($password === $admin["password"]) {

                        $_SESSION["admin_id"] = $admin["admin_id"];

                        $_SESSION["admin_username"] = $admin["username"];

                        header("Location: index.php");

                        exit;

                    } else {

                        $login_error = "Incorrect password.";
                    }

                } else {

                    $login_error = "Username not found.";
                }

                $stmt->close();
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

    <title>
        Admin Login - DURAN'S Apparel
    </title>

    <link
        rel="stylesheet" href="index.css"
    >

</head>

<body class="login-body">

    <div class="login-box">

        <div class="login-logo">

            DURAN'S

            <span>
                APPAREL ADMIN
            </span>

        </div>


        <h1>
            ADMIN LOGIN
        </h1>


        <p class="login-subtitle">
            Sign in to access the admin dashboard
        </p>


        <?php if ($login_error !== ""): ?>

            <div class="login-error">

                <?= htmlspecialchars($login_error); ?>

            </div>

        <?php endif; ?>


        <form
            method="POST"
            class="login-form"
        >

            <input
                type="hidden"
                name="admin_login"
                value="1"
            >


            <div class="login-form-group">

                <label for="username">
                    USERNAME
                </label>

                <input
                    type="text"
                    id="username"
                    name="username"
                    placeholder="Enter username"
                    autocomplete="username"
                    required
                >

            </div>


            <div class="login-form-group">

                <label for="password">
                    PASSWORD
                </label>

                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Enter password"
                    autocomplete="current-password"
                    required
                >

            </div>


            <button
                type="submit"
                class="login-btn"
            >
                LOGIN
            </button>

        </form>


        <a
            href="../index.php"
            class="back-link"
        >
            ← Back to Website
        </a>

    </div>

</body>

</html>

<?php

    exit;
}


/* =====================================================
   ADMIN IS LOGGED IN
===================================================== */


/* =====================================================
   HELPER REDIRECT
===================================================== */

function adminRedirect($message, $type = "success")
{
    header(
        "Location: index.php?message=" .
        urlencode($message) .
        "&type=" .
        urlencode($type)
    );

    exit;
}


/* =====================================================
   MESSAGE
===================================================== */

$message = $_GET["message"] ?? "";

$message_type = $_GET["type"] ?? "success";


/* =====================================================
   PRODUCT ACTIONS
===================================================== */

if ($_SERVER["REQUEST_METHOD"] === "POST") {


    /* =================================================
       ADD PRODUCT
    ================================================= */

    if (isset($_POST["add_product"])) {

        $product_name = trim(
            $_POST["product_name"] ?? ""
        );

        $description = trim(
            $_POST["description"] ?? ""
        );

        $price = floatval(
            $_POST["price"] ?? 0
        );

        $category = trim(
            $_POST["category"] ?? ""
        );

        $stock = intval(
            $_POST["stock"] ?? 0
        );

        $available_sizes = trim(
            $_POST["available_sizes"] ?? ""
        );


        if ($product_name === "") {

            adminRedirect(
                "Product name is required.",
                "error"
            );
        }


        if ($price < 0) {

            adminRedirect(
                "Price cannot be negative.",
                "error"
            );
        }


        if ($stock < 0) {

            adminRedirect(
                "Stock cannot be negative.",
                "error"
            );
        }


        if ($available_sizes === "") {

            $available_sizes =
                "XS,S,M,L,XL,XXL";
        }


        $image_name = "";


        /* ---------------------------------------------
           IMAGE UPLOAD
        --------------------------------------------- */

        if (
            isset($_FILES["image"]) &&
            $_FILES["image"]["error"] === UPLOAD_ERR_OK
        ) {

            $upload_dir = "../images/";


            if (!is_dir($upload_dir)) {

                mkdir(
                    $upload_dir,
                    0777,
                    true
                );
            }


            $original_name =
                $_FILES["image"]["name"];

            $tmp_name =
                $_FILES["image"]["tmp_name"];


            $extension = strtolower(
                pathinfo(
                    $original_name,
                    PATHINFO_EXTENSION
                )
            );


            $allowed_extensions = [
                "jpg",
                "jpeg",
                "png",
                "webp"
            ];


            if (
                !in_array(
                    $extension,
                    $allowed_extensions
                )
            ) {

                adminRedirect(
                    "Invalid image format.",
                    "error"
                );
            }


            $clean_name = preg_replace(
                "/[^a-zA-Z0-9_-]/",
                "_",
                pathinfo(
                    $original_name,
                    PATHINFO_FILENAME
                )
            );


            $image_name =
                time() .
                "_" .
                $clean_name .
                "." .
                $extension;


            if (
                !move_uploaded_file(
                    $tmp_name,
                    $upload_dir . $image_name
                )
            ) {

                adminRedirect(
                    "Failed to upload image.",
                    "error"
                );
            }
        }


        /* ---------------------------------------------
           INSERT PRODUCT
        --------------------------------------------- */

        $stmt = $conn->prepare("
            INSERT INTO products
            (
                product_name,
                description,
                price,
                image,
                category,
                stock,
                available_sizes
            )
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");


        if (!$stmt) {

            adminRedirect(
                "Database error: " . $conn->error,
                "error"
            );
        }


        $stmt->bind_param(
            "ssdssis",
            $product_name,
            $description,
            $price,
            $image_name,
            $category,
            $stock,
            $available_sizes
        );


        if ($stmt->execute()) {

            $stmt->close();

            adminRedirect(
                "Product added successfully."
            );

        } else {

            $error = $stmt->error;

            $stmt->close();

            adminRedirect(
                "Failed to add product: " . $error,
                "error"
            );
        }
    }


    /* =================================================
       UPDATE PRODUCT
    ================================================= */

    if (isset($_POST["update_product"])) {

        $product_id = intval(
            $_POST["product_id"] ?? 0
        );

        $product_name = trim(
            $_POST["product_name"] ?? ""
        );

        $description = trim(
            $_POST["description"] ?? ""
        );

        $price = floatval(
            $_POST["price"] ?? 0
        );

        $category = trim(
            $_POST["category"] ?? ""
        );

        $stock = intval(
            $_POST["stock"] ?? 0
        );

        $available_sizes = trim(
            $_POST["available_sizes"] ?? ""
        );


        if ($product_id <= 0) {

            adminRedirect(
                "Invalid product.",
                "error"
            );
        }


        if ($product_name === "") {

            adminRedirect(
                "Product name is required.",
                "error"
            );
        }


        if ($price < 0 || $stock < 0) {

            adminRedirect(
                "Price and stock cannot be negative.",
                "error"
            );
        }


        if ($available_sizes === "") {

            $available_sizes =
                "XS,S,M,L,XL,XXL";
        }


        $new_image = "";


        /* ---------------------------------------------
           CHECK NEW IMAGE
        --------------------------------------------- */

        if (
            isset($_FILES["image"]) &&
            $_FILES["image"]["error"] === UPLOAD_ERR_OK
        ) {

            $upload_dir = "../images/";


            if (!is_dir($upload_dir)) {

                mkdir(
                    $upload_dir,
                    0777,
                    true
                );
            }


            $original_name =
                $_FILES["image"]["name"];

            $tmp_name =
                $_FILES["image"]["tmp_name"];


            $extension = strtolower(
                pathinfo(
                    $original_name,
                    PATHINFO_EXTENSION
                )
            );


            $allowed_extensions = [
                "jpg",
                "jpeg",
                "png",
                "webp"
            ];


            if (
                !in_array(
                    $extension,
                    $allowed_extensions
                )
            ) {

                adminRedirect(
                    "Invalid image format.",
                    "error"
                );
            }


            $clean_name = preg_replace(
                "/[^a-zA-Z0-9_-]/",
                "_",
                pathinfo(
                    $original_name,
                    PATHINFO_FILENAME
                )
            );


            $new_image =
                time() .
                "_" .
                $clean_name .
                "." .
                $extension;


            if (
                !move_uploaded_file(
                    $tmp_name,
                    $upload_dir . $new_image
                )
            ) {

                adminRedirect(
                    "Failed to upload image.",
                    "error"
                );
            }


            /* -----------------------------------------
               UPDATE WITH IMAGE
            ----------------------------------------- */

            $stmt = $conn->prepare("
                UPDATE products
                SET
                    product_name = ?,
                    description = ?,
                    price = ?,
                    image = ?,
                    category = ?,
                    stock = ?,
                    available_sizes = ?
                WHERE product_id = ?
            ");


            if (!$stmt) {

                adminRedirect(
                    "Database error: " . $conn->error,
                    "error"
                );
            }


            $stmt->bind_param(
                "ssdssisi",
                $product_name,
                $description,
                $price,
                $new_image,
                $category,
                $stock,
                $available_sizes,
                $product_id
            );

        } else {


            /* -----------------------------------------
               UPDATE WITHOUT IMAGE
            ----------------------------------------- */

            $stmt = $conn->prepare("
                UPDATE products
                SET
                    product_name = ?,
                    description = ?,
                    price = ?,
                    category = ?,
                    stock = ?,
                    available_sizes = ?
                WHERE product_id = ?
            ");


            if (!$stmt) {

                adminRedirect(
                    "Database error: " . $conn->error,
                    "error"
                );
            }


            $stmt->bind_param(
                "ssdssis",
                $product_name,
                $description,
                $price,
                $category,
                $stock,
                $available_sizes,
                $product_id
            );
        }


        if ($stmt->execute()) {

            $stmt->close();

            adminRedirect(
                "Product updated successfully."
            );

        } else {

            $error = $stmt->error;

            $stmt->close();

            adminRedirect(
                "Failed to update product: " . $error,
                "error"
            );
        }
    }


    /* =================================================
       ADD STOCK
    ================================================= */

    if (isset($_POST["add_stock"])) {

        $product_id = intval(
            $_POST["product_id"] ?? 0
        );

        $stock_to_add = intval(
            $_POST["stock_to_add"] ?? 0
        );


        if (
            $product_id <= 0 ||
            $stock_to_add <= 0
        ) {

            adminRedirect(
                "Please enter a valid stock amount.",
                "error"
            );
        }


        $stmt = $conn->prepare("
            UPDATE products
            SET stock = stock + ?
            WHERE product_id = ?
        ");


        if (!$stmt) {

            adminRedirect(
                "Database error: " . $conn->error,
                "error"
            );
        }


        $stmt->bind_param(
            "ii",
            $stock_to_add,
            $product_id
        );


        if ($stmt->execute()) {

            $stmt->close();

            adminRedirect(
                "Stock added successfully."
            );

        } else {

            $error = $stmt->error;

            $stmt->close();

            adminRedirect(
                "Failed to add stock: " . $error,
                "error"
            );
        }
    }


    /* =================================================
       DELETE PRODUCT
    ================================================= */

    if (isset($_POST["delete_product"])) {

        $product_id = intval(
            $_POST["product_id"] ?? 0
        );


        if ($product_id <= 0) {

            adminRedirect(
                "Invalid product.",
                "error"
            );
        }


        /*
         * Check if product is already used
         * in an order.
         */

        $check = $conn->prepare("
            SELECT COUNT(*) AS total
            FROM order_items
            WHERE product_id = ?
        ");


        if (!$check) {

            adminRedirect(
                "Database error: " . $conn->error,
                "error"
            );
        }


        $check->bind_param(
            "i",
            $product_id
        );

        $check->execute();


        $check_result =
            $check->get_result();

        $check_data =
            $check_result->fetch_assoc();


        $check->close();


        if ($check_data["total"] > 0) {

            adminRedirect(
                "This product cannot be deleted because it already has an order history.",
                "error"
            );
        }


        $stmt = $conn->prepare("
            DELETE FROM products
            WHERE product_id = ?
        ");


        if (!$stmt) {

            adminRedirect(
                "Database error: " . $conn->error,
                "error"
            );
        }


        $stmt->bind_param(
            "i",
            $product_id
        );


        if ($stmt->execute()) {

            $stmt->close();

            adminRedirect(
                "Product deleted successfully."
            );

        } else {

            $error = $stmt->error;

            $stmt->close();

            adminRedirect(
                "Failed to delete product: " . $error,
                "error"
            );
        }
    }


    /* =================================================
       UPDATE ORDER STATUS
    ================================================= */

    if (isset($_POST["update_order_status"])) {

        $order_id = intval(
            $_POST["order_id"] ?? 0
        );

        $status = $_POST["status"] ?? "";


        $allowed_statuses = [
            "Pending",
            "Processing",
            "Shipped",
            "Completed",
            "Cancelled"
        ];


        if ($order_id <= 0) {

            adminRedirect(
                "Invalid order.",
                "error"
            );
        }


        if (
            !in_array(
                $status,
                $allowed_statuses
            )
        ) {

            adminRedirect(
                "Invalid order status.",
                "error"
            );
        }


        $stmt = $conn->prepare("
            UPDATE orders
            SET status = ?
            WHERE order_id = ?
        ");


        if (!$stmt) {

            adminRedirect(
                "Database error: " . $conn->error,
                "error"
            );
        }


        $stmt->bind_param(
            "si",
            $status,
            $order_id
        );


        if ($stmt->execute()) {

            $stmt->close();

            adminRedirect(
                "Order status updated successfully."
            );

        } else {

            $error = $stmt->error;

            $stmt->close();

            adminRedirect(
                "Failed to update order: " . $error,
                "error"
            );
        }
    }
}


/* =====================================================
   DASHBOARD STATISTICS
===================================================== */

$total_products = 0;

$low_stock = 0;

$total_orders = 0;

$pending_orders = 0;

$total_sales = 0;


/* TOTAL PRODUCTS */

$result = $conn->query("
    SELECT COUNT(*) AS total
    FROM products
");

if ($result) {

    $data = $result->fetch_assoc();

    $total_products = $data["total"];
}


/* LOW STOCK */

$result = $conn->query("
    SELECT COUNT(*) AS total
    FROM products
    WHERE stock <= 5
");

if ($result) {

    $data = $result->fetch_assoc();

    $low_stock = $data["total"];
}


/* TOTAL ORDERS */

$result = $conn->query("
    SELECT COUNT(*) AS total
    FROM orders
");

if ($result) {

    $data = $result->fetch_assoc();

    $total_orders = $data["total"];
}


/* PENDING ORDERS */

$result = $conn->query("
    SELECT COUNT(*) AS total
    FROM orders
    WHERE status = 'Pending'
");

if ($result) {

    $data = $result->fetch_assoc();

    $pending_orders = $data["total"];
}


/* COMPLETED SALES */

$result = $conn->query("
    SELECT COALESCE(SUM(total_amount), 0) AS total
    FROM orders
    WHERE status = 'Completed'
");

if ($result) {

    $data = $result->fetch_assoc();

    $total_sales = $data["total"];
}


/* =====================================================
   GET PRODUCTS
===================================================== */

$products = [];


$result = $conn->query("
    SELECT
        product_id,
        product_name,
        description,
        price,
        image,
        category,
        stock,
        available_sizes
    FROM products
    ORDER BY product_id DESC
");


if ($result) {

    while ($row = $result->fetch_assoc()) {

        $products[] = $row;
    }
}


/* =====================================================
   GET ORDERS
===================================================== */

$orders = [];


$result = $conn->query("
    SELECT
        order_id,
        user_id,
        customer_name,
        email,
        address,
        phone,
        total_amount,
        status,
        order_date
    FROM orders
    ORDER BY order_id DESC
");


if ($result) {

    while ($row = $result->fetch_assoc()) {

        $orders[] = $row;
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

    <title>
        Admin Dashboard - DURAN'S Apparel
    </title>

    <link
        rel="stylesheet"
        href="index.css"
    >

</head>


<body>


<div class="admin-layout">


    <!-- =================================================
         SIDEBAR
    ================================================== -->

    <aside class="sidebar">


        <div class="sidebar-logo">

            <h1>
                DURAN'S
            </h1>

            <span>
                APPAREL ADMIN
            </span>

        </div>


        <nav class="sidebar-menu">


            <a href="#dashboard">

                <span>
                    ▣
                </span>

                Dashboard

            </a>


            <a href="#products">

                <span>
                    ◈
                </span>

                Products

            </a>


            <a href="#orders">

                <span>
                    ▤
                </span>

                Orders

            </a>


            <a
                href="../../midterm/index.php"
                target="_blank"
            >

                <span>
                    ↗
                </span>

                View Website

            </a>


        </nav>


        <a
            href="index.php?logout=1"
            class="sidebar-logout"
        >

            <span>
                ↪
            </span>

            Logout

        </a>


    </aside>



    <!-- =================================================
         MAIN CONTENT
    ================================================== -->

    <main class="main-content">


        <!-- TOPBAR -->

        <header class="topbar">


            <div>

                <h2>
                    DASHBOARD
                </h2>

            </div>


            <div class="admin-user">


                <div class="admin-user-icon">
                    A
                </div>


                <div>

                    <strong>
                        <?= htmlspecialchars(
                            $_SESSION["admin_username"]
                        ); ?>
                    </strong>

                    <small>
                        Administrator
                    </small>

                </div>


            </div>


        </header>



        <!-- MESSAGE -->

        <?php if ($message !== ""): ?>

            <div
                class="
                    admin-message
                    <?= $message_type === "error"
                        ? "message-error"
                        : "message-success"; ?>
                "
            >

                <?= htmlspecialchars($message); ?>

            </div>

        <?php endif; ?>



        <!-- =================================================
             DASHBOARD
        ================================================== -->

        <section
            id="dashboard"
            class="dashboard-section"
        >

            <div class="stats-grid">


                <div class="stat-card">


                    <div class="stat-icon">
                        P
                    </div>


                    <div>

                        <span>
                            TOTAL PRODUCTS
                        </span>

                        <strong>
                            <?= $total_products; ?>
                        </strong>

                    </div>


                </div>



                <div class="stat-card">


                    <div class="stat-icon">
                        !
                    </div>


                    <div>

                        <span>
                            LOW STOCK
                        </span>

                        <strong>
                            <?= $low_stock; ?>
                        </strong>

                    </div>


                </div>



                <div class="stat-card">


                    <div class="stat-icon">
                        O
                    </div>


                    <div>

                        <span>
                            TOTAL ORDERS
                        </span>

                        <strong>
                            <?= $total_orders; ?>
                        </strong>

                    </div>


                </div>



                <div class="stat-card">


                    <div class="stat-icon">
                        $
                    </div>


                    <div>

                        <span>
                            COMPLETED SALES
                        </span>

                        <strong>
                            ₱<?= number_format(
                                $total_sales,
                                2
                            ); ?>
                        </strong>

                    </div>


                </div>


            </div>


        </section>



        <!-- =================================================
             PRODUCTS
        ================================================== -->

        <section
            id="products"
            class="admin-section"
        >


            <div class="section-heading">


                <div>

                    <h1>
                        Products
                    </h1>

                    <p>
                        Manage products
                    </p>

                </div>


            </div>



            <!-- ADD PRODUCT -->

            <div class="admin-card">


                <h2>
                    Add New Product
                </h2>


                <form
                    method="POST"
                    enctype="multipart/form-data"
                    class="product-form"
                >


                    <input
                        type="hidden"
                        name="add_product"
                        value="1"
                    >


                    <div class="form-group">

                        <label>
                            Product Name
                        </label>

                        <input
                            type="text"
                            name="product_name"
                            placeholder="Example: Classic Hoodie"
                            required
                        >

                    </div>



                    <div class="form-group">

                        <label>
                            Category
                        </label>

                        <input
                            type="text"
                            name="category"
                            placeholder="Example: Hoodies"
                        >

                    </div>



                    <div class="form-group">

                        <label>
                            Price
                        </label>

                        <input
                            type="number"
                            name="price"
                            step="0.01"
                            min="0"
                            placeholder="0.00"
                            required
                        >

                    </div>



                    <div class="form-group">

                        <label>
                            Stock
                        </label>

                        <input
                            type="number"
                            name="stock"
                            min="0"
                            value="0"
                            required
                        >

                    </div>



                    <div class="form-group form-full">

                        <label>
                            Available Sizes
                        </label>

                        <input
                            type="text"
                            name="available_sizes"
                            value="XS,S,M,L,XL,XXL"
                            placeholder="XS,S,M,L,XL,XXL"
                        >

                        <small>
                            Separate sizes using commas.
                        </small>

                    </div>



                    <div class="form-group form-full">

                        <label>
                            Description
                        </label>

                        <textarea
                            name="description"
                            rows="4"
                            placeholder="Product description..."
                        ></textarea>

                    </div>



                    <div class="form-group form-full">

                        <label>
                            Product Image
                        </label>

                        <input
                            type="file"
                            name="image"
                            accept=".jpg,.jpeg,.png,.webp"
                        >

                    </div>



                    <div class="form-full">

                        <button
                            type="submit"
                            class="btn btn-red"
                        >
                            + ADD PRODUCT
                        </button>

                    </div>


                </form>


            </div>



            <!-- PRODUCT LIST -->

            <div class="admin-card">


                <div class="card-header">


                    <div>

                        <h2>
                            Product List
                        </h2>

                        <p>
                            <?= count($products); ?> products
                        </p>

                    </div>


                </div>



                <div class="table-wrapper">


                    <table class="admin-table">


                        <thead>

                            <tr>

                                <th>
                                    IMAGE
                                </th>

                                <th>
                                    PRODUCT
                                </th>

                                <th>
                                    CATEGORY
                                </th>

                                <th>
                                    PRICE
                                </th>

                                <th>
                                    STOCK
                                </th>

                                <th>
                                    SIZES
                                </th>

                                <th>
                                    ACTIONS
                                </th>

                            </tr>

                        </thead>



                        <tbody>


                        <?php if (empty($products)): ?>


                            <tr>

                                <td
                                    colspan="7"
                                    class="empty-table"
                                >
                                    No products found.
                                </td>

                            </tr>


                        <?php else: ?>


                            <?php foreach ($products as $product): ?>


                                <tr>


                                    <td>


                                        <?php if (!empty($product["image"])): ?>


                                            <img
                                                src="images/<?= htmlspecialchars(
                                                    $product["image"]
                                                ); ?>"
                                                class="product-image"
                                                alt="<?= htmlspecialchars(
                                                    $product["product_name"]
                                                ); ?>"
                                            >


                                        <?php else: ?>


                                            <div class="no-image">
                                                NO IMAGE
                                            </div>


                                        <?php endif; ?>


                                    </td>



                                    <td>


                                        <strong class="product-name">

                                            <?= htmlspecialchars(
                                                $product["product_name"]
                                            ); ?>

                                        </strong>


                                        <?php if (!empty($product["description"])): ?>

                                            <small class="product-description">

                                                <?= htmlspecialchars(
                                                    mb_strimwidth(
                                                        $product["description"],
                                                        0,
                                                        80,
                                                        "..."
                                                    )
                                                ); ?>

                                            </small>

                                        <?php endif; ?>


                                    </td>



                                    <td>

                                        <?= htmlspecialchars(
                                            $product["category"]
                                            ?: "Uncategorized"
                                        ); ?>

                                    </td>



                                    <td>

                                        <strong>

                                            ₱<?= number_format(
                                                $product["price"],
                                                2
                                            ); ?>

                                        </strong>

                                    </td>



                                    <td>


                                        <span
                                            class="
                                                stock-number
                                                <?= $product["stock"] <= 5
                                                    ? "stock-low"
                                                    : "stock-good"; ?>
                                            "
                                        >

                                            <?= $product["stock"]; ?>

                                        </span>


                                    </td>



                                    <td>

                                        <span class="size-list">

                                            <?= htmlspecialchars(
                                                $product["available_sizes"]
                                                ?: "XS,S,M,L,XL,XXL"
                                            ); ?>

                                        </span>

                                    </td>



                                    <td>


                                        <div class="action-buttons">


                                            <a
                                                href="#edit-<?= $product["product_id"]; ?>"
                                                class="btn btn-small btn-dark"
                                            >
                                                EDIT
                                            </a>


                                            <form
                                                method="POST"
                                                onsubmit="return confirm('Are you sure you want to delete this product?');"
                                            >

                                                <input
                                                    type="hidden"
                                                    name="product_id"
                                                    value="<?= $product["product_id"]; ?>"
                                                >


                                                <button
                                                    type="submit"
                                                    name="delete_product"
                                                    class="btn btn-small btn-delete"
                                                >
                                                    DELETE
                                                </button>

                                            </form>


                                        </div>


                                    </td>


                                </tr>



                                <!-- EDIT PRODUCT -->

                                <tr
                                    id="edit-<?= $product["product_id"]; ?>"
                                    class="edit-row"
                                >

                                    <td colspan="7">


                                        <div class="edit-product-box">


                                            <h3>

                                                Edit Product:

                                                <?= htmlspecialchars(
                                                    $product["product_name"]
                                                ); ?>

                                            </h3>


                                            <form
                                                method="POST"
                                                enctype="multipart/form-data"
                                                class="product-form"
                                            >


                                                <input
                                                    type="hidden"
                                                    name="update_product"
                                                    value="1"
                                                >


                                                <input
                                                    type="hidden"
                                                    name="product_id"
                                                    value="<?= $product["product_id"]; ?>"
                                                >



                                                <div class="form-group">

                                                    <label>
                                                        Product Name
                                                    </label>

                                                    <input
                                                        type="text"
                                                        name="product_name"
                                                        value="<?= htmlspecialchars(
                                                            $product["product_name"]
                                                        ); ?>"
                                                        required
                                                    >

                                                </div>



                                                <div class="form-group">

                                                    <label>
                                                        Category
                                                    </label>

                                                    <input
                                                        type="text"
                                                        name="category"
                                                        value="<?= htmlspecialchars(
                                                            $product["category"]
                                                        ); ?>"
                                                    >

                                                </div>



                                                <div class="form-group">

                                                    <label>
                                                        Price
                                                    </label>

                                                    <input
                                                        type="number"
                                                        name="price"
                                                        step="0.01"
                                                        min="0"
                                                        value="<?= htmlspecialchars(
                                                            $product["price"]
                                                        ); ?>"
                                                        required
                                                    >

                                                </div>



                                                <div class="form-group">

                                                    <label>
                                                        Stock
                                                    </label>

                                                    <input
                                                        type="number"
                                                        name="stock"
                                                        min="0"
                                                        value="<?= htmlspecialchars(
                                                            $product["stock"]
                                                        ); ?>"
                                                        required
                                                    >

                                                </div>



                                                <div class="form-group form-full">

                                                    <label>
                                                        Available Sizes
                                                    </label>

                                                    <input
                                                        type="text"
                                                        name="available_sizes"
                                                        value="<?= htmlspecialchars(
                                                            $product["available_sizes"]
                                                        ); ?>"
                                                    >

                                                </div>



                                                <div class="form-group form-full">

                                                    <label>
                                                        Description
                                                    </label>

                                                    <textarea
                                                        name="description"
                                                        rows="4"
                                                    ><?= htmlspecialchars(
                                                        $product["description"]
                                                    ); ?></textarea>

                                                </div>



                                                <div class="form-group form-full">

                                                    <label>
                                                        Change Image
                                                    </label>

                                                    <input
                                                        type="file"
                                                        name="image"
                                                        accept=".jpg,.jpeg,.png,.webp"
                                                    >

                                                </div>



                                                <div class="form-full">

                                                    <button
                                                        type="submit"
                                                        class="btn btn-red"
                                                    >
                                                        SAVE CHANGES
                                                    </button>

                                                </div>


                                            </form>


                                        </div>


                                    </td>

                                </tr>



                                <!-- ADD STOCK -->

                                <tr class="stock-row">

                                    <td colspan="7">


                                        <form
                                            method="POST"
                                            class="stock-form"
                                        >


                                            <input
                                                type="hidden"
                                                name="product_id"
                                                value="<?= $product["product_id"]; ?>"
                                            >


                                            <strong>
                                                Add Stock:
                                            </strong>


                                            <input
                                                type="number"
                                                name="stock_to_add"
                                                min="1"
                                                placeholder="Quantity"
                                                required
                                            >


                                            <button
                                                type="submit"
                                                name="add_stock"
                                                class="btn btn-small btn-red"
                                            >
                                                + ADD STOCK
                                            </button>


                                        </form>


                                    </td>

                                </tr>


                            <?php endforeach; ?>


                        <?php endif; ?>


                        </tbody>


                    </table>


                </div>


            </div>


        </section>



        <!-- =================================================
             ORDERS
        ================================================== -->

        <section
            id="orders"
            class="admin-section"
        >


            <div class="section-heading">


                <div>

                    <h1>
                        Orders
                    </h1>

                    <p>
                        Monitor and update customer orders
                    </p>

                </div>


            </div>



            <div class="admin-card">


                <div class="table-wrapper">


                    <table class="admin-table">


                        <thead>

                            <tr>

                                <th>
                                    ORDER
                                </th>

                                <th>
                                    CUSTOMER
                                </th>

                                <th>
                                    CONTACT
                                </th>

                                <th>
                                    ADDRESS
                                </th>

                                <th>
                                    TOTAL
                                </th>

                                <th>
                                    STATUS
                                </th>

                                <th>
                                    DATE
                                </th>

                            </tr>

                        </thead>



                        <tbody>


                        <?php if (empty($orders)): ?>


                            <tr>

                                <td
                                    colspan="7"
                                    class="empty-table"
                                >
                                    No orders found.
                                </td>

                            </tr>


                        <?php else: ?>


                            <?php foreach ($orders as $order): ?>


                                <tr>


                                    <td>

                                        <strong>
                                            #<?= $order["order_id"]; ?>
                                        </strong>

                                    </td>



                                    <td>

                                        <strong>

                                            <?= htmlspecialchars(
                                                $order["customer_name"]
                                            ); ?>

                                        </strong>


                                        <small class="product-description">

                                            <?= htmlspecialchars(
                                                $order["email"]
                                            ); ?>

                                        </small>

                                    </td>



                                    <td>

                                        <?= htmlspecialchars(
                                            $order["phone"]
                                            ?: "N/A"
                                        ); ?>

                                    </td>



                                    <td>

                                        <span class="address-text">

                                            <?= htmlspecialchars(
                                                $order["address"]
                                            ); ?>

                                        </span>

                                    </td>



                                    <td>

                                        <strong>

                                            ₱<?= number_format(
                                                $order["total_amount"],
                                                2
                                            ); ?>

                                        </strong>

                                    </td>



                                    <td>


                                        <form
                                            method="POST"
                                            class="status-form"
                                        >


                                            <input
                                                type="hidden"
                                                name="order_id"
                                                value="<?= $order["order_id"]; ?>"
                                            >


                                            <input
                                                type="hidden"
                                                name="update_order_status"
                                                value="1"
                                            >


                                            <select
                                                name="status"
                                                class="
                                                    status-select
                                                    status-<?= strtolower(
                                                        $order["status"]
                                                    ); ?>
                                                "
                                                onchange="this.form.submit()"
                                            >


                                                <option
                                                    value="Pending"
                                                    <?= $order["status"] === "Pending"
                                                        ? "selected"
                                                        : ""; ?>
                                                >
                                                    Pending
                                                </option>


                                                <option
                                                    value="Processing"
                                                    <?= $order["status"] === "Processing"
                                                        ? "selected"
                                                        : ""; ?>
                                                >
                                                    Processing
                                                </option>


                                                <option
                                                    value="Shipped"
                                                    <?= $order["status"] === "Shipped"
                                                        ? "selected"
                                                        : ""; ?>
                                                >
                                                    Shipped
                                                </option>


                                                <option
                                                    value="Completed"
                                                    <?= $order["status"] === "Completed"
                                                        ? "selected"
                                                        : ""; ?>
                                                >
                                                    Completed
                                                </option>


                                                <option
                                                    value="Cancelled"
                                                    <?= $order["status"] === "Cancelled"
                                                        ? "selected"
                                                        : ""; ?>
                                                >
                                                    Cancelled
                                                </option>


                                            </select>


                                        </form>


                                    </td>



                                    <td>

                                        <?= htmlspecialchars(
                                            $order["order_date"]
                                        ); ?>

                                    </td>


                                </tr>


                            <?php endforeach; ?>


                        <?php endif; ?>


                        </tbody>


                    </table>


                </div>


            </div>


        </section>


    </main>


</div>


</body>

</html>