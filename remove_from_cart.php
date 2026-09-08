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
   GET CART ITEM
===================================================== */

$cart_item_id =
    isset($_POST["cart_item_id"])
    ? intval($_POST["cart_item_id"])
    : 0;


if ($cart_item_id <= 0) {

    echo json_encode([
        "success" => false,
        "message" => "Invalid cart item."
    ]);

    exit;
}


/* =====================================================
   DELETE ITEM
===================================================== */

$stmt = $conn->prepare("
    DELETE FROM cart_items
    WHERE
        cart_item_id = ?
        AND user_id = ?
");

$stmt->bind_param(
    "ii",
    $cart_item_id,
    $user_id
);


if ($stmt->execute()) {

    if ($stmt->affected_rows > 0) {

        echo json_encode([
            "success" => true,
            "message" => "Item removed from cart."
        ]);

    } else {

        echo json_encode([
            "success" => false,
            "message" => "Cart item was not found."
        ]);

    }

} else {

    echo json_encode([
        "success" => false,
        "message" => "Failed to remove item."
    ]);

}


$stmt->close();

?>