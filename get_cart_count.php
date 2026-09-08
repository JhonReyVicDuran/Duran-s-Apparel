<?php

session_start();

require_once "db.php";

header("Content-Type: application/json");


if (!isset($_SESSION["user_id"])) {

    echo json_encode([
        "count" => 0
    ]);

    exit;
}


$user_id =
    intval($_SESSION["user_id"]);


$stmt = $conn->prepare(
    "SELECT COALESCE(SUM(quantity), 0) AS total
     FROM cart_items
     WHERE user_id = ?"
);

$stmt->bind_param(
    "i",
    $user_id
);

$stmt->execute();

$result =
    $stmt->get_result();

$row =
    $result->fetch_assoc();


$stmt->close();


echo json_encode([
    "count" => intval($row["total"])
]);

?>