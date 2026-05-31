<?php
include("db_connect.php");

$order_id = $_POST['order_id'];

$stmt = $conn->prepare("UPDATE orders SET review_given=1 WHERE order_id=?");
$stmt->bind_param("i", $order_id);
$stmt->execute();

echo "done";
?>