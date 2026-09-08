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
        "message" => "Please log in first."
    ]);

    exit;
}


$user_id = intval($_SESSION["user_id"]);


/* =====================================================
   GET DATA
===================================================== */

$cart_item_id =
    isset($_POST["cart_item_id"])
    ? intval($_POST["cart_item_id"])
    : 0;

$quantity =
    isset($_POST["quantity"])
    ? intval($_POST["quantity"])
    : 0;


/* =====================================================
   VALIDATE
===================================================== */

if ($cart_item_id <= 0) {

    echo json_encode([
        "success" => false,
        "message" => "Invalid cart item."
    ]);

    exit;
}


if ($quantity < 1) {

    echo json_encode([
        "success" => false,
        "message" => "Quantity must be at least 1."
    ]);

    exit;
}


/* =====================================================
   FIND CART ITEM
===================================================== */

$stmt = $conn->prepare("
    SELECT
        c.product_id,
        p.stock
    FROM cart_items c
    INNER JOIN products p
        ON c.product_id = p.product_id
    WHERE
        c.cart_item_id = ?
        AND c.user_id = ?
");

$stmt->bind_param(
    "ii",
    $cart_item_id,
    $user_id
);

$stmt->execute();

$result = $stmt->get_result();

$item = $result->fetch_assoc();

$stmt->close();


/* =====================================================
   CART ITEM NOT FOUND
===================================================== */

if (!$item) {

    echo json_encode([
        "success" => false,
        "message" => "Cart item not found."
    ]);

    exit;
}


$stock = intval($item["stock"]);


/* =====================================================
   CHECK STOCK
===================================================== */

if ($stock <= 0) {

    echo json_encode([
        "success" => false,
        "message" => "This product is currently out of stock."
    ]);

    exit;
}


if ($quantity > $stock) {

    echo json_encode([
        "success" => false,
        "message" =>
            "Only " . $stock . " item(s) are available."
    ]);

    exit;
}


/* =====================================================
   UPDATE CART
===================================================== */

$stmt = $conn->prepare("
    UPDATE cart_items
    SET quantity = ?
    WHERE
        cart_item_id = ?
        AND user_id = ?
");

$stmt->bind_param(
    "iii",
    $quantity,
    $cart_item_id,
    $user_id
);


if ($stmt->execute()) {

    echo json_encode([
        "success" => true,
        "message" => "Cart updated successfully."
    ]);

} else {

    echo json_encode([
        "success" => false,
        "message" => "Failed to update cart."
    ]);

}


$stmt->close();

?>