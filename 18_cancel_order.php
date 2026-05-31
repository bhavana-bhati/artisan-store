<?php
session_start();
include("db_connect.php");

if(!isset($_SESSION['user_id'])){
    exit("login_error");
}

if(isset($_POST['order_id'])){

    $order_id = intval($_POST['order_id']);
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("
        UPDATE orders 
        SET order_status='Cancelled' 
        WHERE order_id=? AND user_id=? AND order_status='Pending'
    ");

    $stmt->bind_param("ii", $order_id, $user_id);
    $stmt->execute();

    if($stmt->affected_rows > 0){
        echo "success";
    } else {
        echo "no_update"; // 👈 important for debugging
    }

} else {
    echo "invalid_request";
}
?>