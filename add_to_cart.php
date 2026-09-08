<?php

session_start();

require_once "db.php";

header("Content-Type: application/json");


/* =====================================================
   CHECK LOGIN
===================================================== */

if (!isset($_SESSION["user_id"])) {

    echo json_encode([
        "success" => false,
        "login_required" => true,
        "message" => "Please log in first."
    ]);

    exit;
}


$user_id = intval($_SESSION["user_id"]);


/* =====================================================
   GET DATA
===================================================== */

$product_id = isset($_POST["product_id"])
    ? intval($_POST["product_id"])
    : 0;

$quantity = isset($_POST["quantity"])
    ? intval($_POST["quantity"])
    : 1;

$size = isset($_POST["size"])
    ? trim($_POST["size"])
    : "";


/* =====================================================
   VALIDATE DATA
===================================================== */

if ($product_id <= 0) {

    echo json_encode([
        "success" => false,
        "message" => "Invalid product."
    ]);

    exit;
}


if ($quantity < 1) {

    $quantity = 1;
}


/*
   IMPORTANT:
   Do NOT automatically use M here if a size
   was not sent. This helps us detect problems.
*/

if ($size === "") {

    echo json_encode([
        "success" => false,
        "message" => "Please select a size."
    ]);

    exit;
}


/* =====================================================
   GET PRODUCT
===================================================== */

$stmt = $conn->prepare("
    SELECT
        product_id,
        product_name,
        price,
        stock,
        available_sizes
    FROM products
    WHERE product_id = ?
");

$stmt->bind_param("i", $product_id);

$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 0) {

    $stmt->close();

    echo json_encode([
        "success" => false,
        "message" => "Product not found."
    ]);

    exit;
}

$product = $result->fetch_assoc();

$stmt->close();


/* =====================================================
   CHECK SIZE
===================================================== */

$available_sizes = [];

if (!empty($product["available_sizes"])) {

    $available_sizes = array_map(
        "trim",
        explode(",", $product["available_sizes"])
    );
}


/*
   Make sure the selected size actually exists
   for this product.
*/

if (!in_array($size, $available_sizes, true)) {

    echo json_encode([
        "success" => false,
        "message" => "Selected size is not available for this product."
    ]);

    exit;
}


/* =====================================================
   CHECK STOCK
===================================================== */

if ($product["stock"] <= 0) {

    echo json_encode([
        "success" => false,
        "message" => "This product is out of stock."
    ]);

    exit;
}


/* =====================================================
   CHECK EXISTING CART ITEM
===================================================== */

$check_stmt = $conn->prepare("
    SELECT
        cart_item_id,
        quantity
    FROM cart_items
    WHERE user_id = ?
    AND product_id = ?
    AND size = ?
");

$check_stmt->bind_param(
    "iis",
    $user_id,
    $product_id,
    $size
);

$check_stmt->execute();

$check_result = $check_stmt->get_result();


/* =====================================================
   UPDATE EXISTING ITEM
===================================================== */

if ($check_result->num_rows > 0) {

    $existing = $check_result->fetch_assoc();

    $cart_item_id = intval(
        $existing["cart_item_id"]
    );

    $existing_quantity = intval(
        $existing["quantity"]
    );

    $new_quantity =
        $existing_quantity + $quantity;


    if ($new_quantity > $product["stock"]) {

        $check_stmt->close();

        echo json_encode([
            "success" => false,
            "message" =>
                "You cannot add more than the available stock."
        ]);

        exit;
    }


    $update_stmt = $conn->prepare("
        UPDATE cart_items
        SET quantity = ?
        WHERE cart_item_id = ?
        AND user_id = ?
    ");

    $update_stmt->bind_param(
        "iii",
        $new_quantity,
        $cart_item_id,
        $user_id
    );

    $update_stmt->execute();

    $update_stmt->close();

    $check_stmt->close();


    echo json_encode([
        "success" => true,
        "message" =>
            $product["product_name"] .
            " (" . $size . ") updated in your cart.",
        "size" => $size,
        "quantity" => $new_quantity
    ]);

    exit;
}


/* =====================================================
   NEW CART ITEM
===================================================== */

if ($quantity > $product["stock"]) {

    $check_stmt->close();

    echo json_encode([
        "success" => false,
        "message" =>
            "You cannot add more than the available stock."
    ]);

    exit;
}


$insert_stmt = $conn->prepare("
    INSERT INTO cart_items
    (
        user_id,
        product_id,
        quantity,
        size
    )
    VALUES (?, ?, ?, ?)
");

$insert_stmt->bind_param(
    "iiis",
    $user_id,
    $product_id,
    $quantity,
    $size
);

$insert_stmt->execute();

$insert_stmt->close();

$check_stmt->close();


/* =====================================================
   SUCCESS
===================================================== */

echo json_encode([
    "success" => true,
    "message" =>
        $product["product_name"] .
        " (" . $size . ") added to your cart.",
    "size" => $size,
    "quantity" => $quantity
]);

exit;

?>