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
        "message" => "Please log in first before adding items to your cart."
    ]);

    exit;
}


/* =====================================================
   CHECK REQUEST
===================================================== */

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    echo json_encode([
        "success" => false,
        "message" => "Invalid request."
    ]);

    exit;
}


/* =====================================================
   GET DATA
===================================================== */

$user_id = intval($_SESSION["user_id"]);

$product_id = intval($_POST["product_id"] ?? 0);

$quantity = intval($_POST["quantity"] ?? 0);


/* =====================================================
   VALIDATE
===================================================== */

if ($product_id <= 0 || $quantity <= 0) {

    echo json_encode([
        "success" => false,
        "message" => "Invalid product or quantity."
    ]);

    exit;
}


/* =====================================================
   GET PRODUCT
===================================================== */

$stmt = $conn->prepare(
    "SELECT product_id, product_name, price, image, stock
     FROM products
     WHERE product_id = ?"
);

$stmt->bind_param(
    "i",
    $product_id
);

$stmt->execute();

$result = $stmt->get_result();


if ($result->num_rows !== 1) {

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
   CHECK CURRENT CART QUANTITY
===================================================== */

$stmt = $conn->prepare(
    "SELECT quantity
     FROM cart_items
     WHERE user_id = ?
     AND product_id = ?"
);

$stmt->bind_param(
    "ii",
    $user_id,
    $product_id
);

$stmt->execute();

$result = $stmt->get_result();

$currentCartQuantity = 0;


if ($result->num_rows === 1) {

    $cartItem = $result->fetch_assoc();

    $currentCartQuantity =
        intval($cartItem["quantity"]);

}

$stmt->close();


/* =====================================================
   CALCULATE NEW QUANTITY
===================================================== */

$newQuantity =
    $currentCartQuantity + $quantity;


/* =====================================================
   CHECK STOCK
===================================================== */

if ($newQuantity > intval($product["stock"])) {

    echo json_encode([
        "success" => false,
        "message" =>
            "You can only add " .
            $product["stock"] .
            " item(s) of this product."
    ]);

    exit;
}


/* =====================================================
   ADD OR UPDATE CART
===================================================== */

if ($currentCartQuantity > 0) {

    /* =============================================
       UPDATE EXISTING CART ITEM
    ============================================= */

    $stmt = $conn->prepare(
        "UPDATE cart_items
         SET quantity = ?
         WHERE user_id = ?
         AND product_id = ?"
    );

    $stmt->bind_param(
        "iii",
        $newQuantity,
        $user_id,
        $product_id
    );

    $success = $stmt->execute();

    $stmt->close();

} else {

    /* =============================================
       ADD NEW CART ITEM
    ============================================= */

    $stmt = $conn->prepare(
        "INSERT INTO cart_items
        (user_id, product_id, quantity)
        VALUES (?, ?, ?)"
    );

    $stmt->bind_param(
        "iii",
        $user_id,
        $product_id,
        $quantity
    );

    $success = $stmt->execute();

    $stmt->close();
}


/* =====================================================
   RESPONSE
===================================================== */

if ($success) {

    echo json_encode([
        "success" => true,
        "message" =>
            $product["product_name"] .
            " added to your cart.",
        "cart_quantity" => $newQuantity
    ]);

} else {

    echo json_encode([
        "success" => false,
        "message" =>
            "Unable to add product to cart."
    ]);

}

?>