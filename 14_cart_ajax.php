<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include("db_connect.php");

header("Content-Type: application/json");

if(!isset($_SESSION['user_id'])){
echo json_encode(["status"=>"login"]);
exit();
}

$user_id = $_SESSION['user_id'];
$action = $_POST['action'] ?? "";


/* UPDATE QUANTITY */

if($action=="update"){

$cart_id = intval($_POST['cart_id']);
$quantity = intval($_POST['quantity']);

$stmt = $conn->prepare("UPDATE cart SET quantity=? WHERE cart_id=? AND user_id=?");

if(!$stmt){
echo json_encode(["status"=>"error","msg"=>$conn->error]);
exit();
}

$stmt->bind_param("iii",$quantity,$cart_id,$user_id);
$stmt->execute();

$get = $conn->prepare("
SELECT p.price,(p.price*c.quantity) AS subtotal
FROM cart c
JOIN products p ON c.product_id=p.product_id
WHERE c.cart_id=? AND c.user_id=?
");

$get->bind_param("ii",$cart_id,$user_id);
$get->execute();

$res = $get->get_result()->fetch_assoc();

echo json_encode([
"status"=>"updated",
"subtotal"=>$res['subtotal']
]);

exit();
}


/* REMOVE PRODUCT */

if($action=="remove"){

$cart_id = intval($_POST['cart_id']);

$stmt = $conn->prepare("DELETE FROM cart WHERE cart_id=? AND user_id=?");

if(!$stmt){
echo json_encode(["status"=>"error","msg"=>$conn->error]);
exit();
}

$stmt->bind_param("ii",$cart_id,$user_id);
$stmt->execute();

echo json_encode(["status"=>"removed"]);
exit();
}


/* SAVE FOR LATER */
/* SAVE FOR LATER */

if($action=="saveLater"){

$cart_id = intval($_POST['cart_id']);
$product_id = intval($_POST['product_id']);

/* check if already in wishlist */

$check = $conn->prepare("SELECT id FROM wishlist WHERE user_id=? AND product_id=?");
$check->bind_param("ii",$user_id,$product_id);
$check->execute();
$res = $check->get_result();

if($res->num_rows == 0){

$insert = $conn->prepare("INSERT INTO wishlist(user_id,product_id) VALUES(?,?)");
$insert->bind_param("ii",$user_id,$product_id);
$insert->execute();

}

/* remove from cart */

$delete = $conn->prepare("DELETE FROM cart WHERE cart_id=? AND user_id=?");
$delete->bind_param("ii",$cart_id,$user_id);
$delete->execute();

echo json_encode(["status"=>"saved"]);
exit();
}

/* CLEAR CART */

if($action=="clear"){

$stmt = $conn->prepare("DELETE FROM cart WHERE user_id=?");

if(!$stmt){
echo json_encode(["status"=>"error","msg"=>$conn->error]);
exit();
}

$stmt->bind_param("i",$user_id);
$stmt->execute();

echo json_encode(["status"=>"cleared"]);
exit();
}
?>