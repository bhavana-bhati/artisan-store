<?php
session_start();
include("db_connect.php");

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status"=>"error","message"=>"Please login first."]);
    exit;
}

$user_id = $_SESSION['user_id'];

if (!isset($_POST['product_id'])) {
    echo json_encode(["status"=>"error","message"=>"Product ID missing."]);
    exit;
}

$product_id = intval($_POST['product_id']);

/* CHECK IF PRODUCT ALREADY IN WISHLIST */

$stmt = $conn->prepare("SELECT 1 FROM wishlist WHERE user_id=? AND product_id=?");
$stmt->bind_param("ii", $user_id, $product_id);
$stmt->execute();
$exists = $stmt->get_result()->num_rows > 0;


/* IF EXISTS → REMOVE FROM WISHLIST */

if ($exists) {

$del = $conn->prepare("DELETE FROM wishlist WHERE user_id=? AND product_id=?");
$del->bind_param("ii",$user_id,$product_id);

if($del->execute()){

echo json_encode([
"status"=>"removed",
"message"=>"Removed from wishlist"
]);

}else{

echo json_encode([
"status"=>"error",
"message"=>"Could not remove"
]);

}

}

/* IF NOT EXISTS → ADD TO WISHLIST */

else {

$ins = $conn->prepare("INSERT INTO wishlist(user_id,product_id) VALUES(?,?)");
$ins->bind_param("ii",$user_id,$product_id);

if($ins->execute()){

/* REMOVE FROM CART IF EXISTS */

$removeCart = $conn->prepare("DELETE FROM cart WHERE user_id=? AND product_id=?");
$removeCart->bind_param("ii",$user_id,$product_id);
$removeCart->execute();

echo json_encode([
"status"=>"added",
"message"=>"Added to wishlist"
]);

}else{

echo json_encode([
"status"=>"error",
"message"=>"Could not add"
]);

}

}

?>