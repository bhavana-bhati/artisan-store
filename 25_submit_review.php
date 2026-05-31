<?php
session_start();
include("db_connect.php");

// 🔴 SHOW ERRORS (for debugging)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 🔴 CHECK LOGIN
if(!isset($_SESSION['user_id'])){
    exit("Login required");
}

$user_id = $_SESSION['user_id'];

// 🔴 CHECK POST DATA
if(
    !isset($_POST['product_id']) ||
    !isset($_POST['order_id']) ||
    !isset($_POST['rating']) ||
    !isset($_POST['review'])
){
    exit("Missing data");
}

$product_id = $_POST['product_id'];
$order_id   = $_POST['order_id'];
$rating     = $_POST['rating'];
$comment    = trim($_POST['review']);

// 🔴 VALIDATION
if($rating < 1 || $rating > 5){
    exit("Invalid rating");
}

if($comment === ""){
    $comment = NULL;
}

// 🔴 CHECK IF REVIEW ALREADY EXISTS (IMPORTANT)
$check = $conn->prepare("
SELECT id FROM reviews 
WHERE user_id=? AND product_id=?
");
$check->bind_param("ii", $user_id, $product_id);
$check->execute();
$check->store_result();

if($check->num_rows > 0){
    exit("already_reviewed");
}

// 🔴 INSERT REVIEW
$stmt = $conn->prepare("
INSERT INTO reviews (product_id, user_id, order_id, rating, review)
VALUES (?, ?, ?, ?, ?)
");

if(!$stmt){
    die("Prepare Error: " . $conn->error);
}

$stmt->bind_param("iiiis", $product_id, $user_id, $order_id, $rating, $comment);

if(!$stmt->execute()){
    die("Execute Error: " . $stmt->error);
}

// 🔴 UPDATE ORDER (OPTIONAL)
$update = $conn->prepare("
UPDATE orders SET review_given=1 WHERE order_id=?
");
$update->bind_param("i", $order_id);
$update->execute();

// ✅ FINAL OUTPUT (ONLY ONCE)
echo "success";
?>