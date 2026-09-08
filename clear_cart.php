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
   CLEAR USER CART
===================================================== */

$stmt = $conn->prepare("
    DELETE FROM cart_items
    WHERE user_id = ?
");

$stmt->bind_param(
    "i",
    $user_id
);


if ($stmt->execute()) {

    echo json_encode([
        "success" => true,
        "message" => "Cart cleared successfully."
    ]);

} else {

    echo json_encode([
        "success" => false,
        "message" => "Failed to clear cart."
    ]);

}


$stmt->close();

?>