<?php
session_start();
include("db_connect.php");

if(!isset($_SESSION['admin'])){
    header("Location: admin_login.php");
    exit();
}

if(!isset($_POST['order_id'])){
    echo "Invalid request!";
    exit();
}

$order_id = intval($_POST['order_id']);

/* UPDATE STATUS */
$stmt = $conn->prepare("UPDATE orders SET order_status='Delivered' WHERE order_id=?");
$stmt->bind_param("i", $order_id);

if($stmt->execute()){
    header("Location: manage_orders.php");
} else {
    echo "Error updating status!";
}
?>
