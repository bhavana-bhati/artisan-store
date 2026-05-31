<?php
session_start();
include("db_connect.php");

if(!isset($_SESSION['temp_order'])){
    die("Session expired");
}

$data = $_SESSION['temp_order'];

/* ✅ EXTRACT EVERYTHING PROPERLY */
$name = $data['name'];
$product_name = $data['product_name'];
$product_image = $data['product_image'];
$total = $data['total'];

$product_id = $data['product_id'];
$qty = $data['qty'];
$phone = $data['phone'];
$address = $data['address'];
$city = $data['city'];
$pincode = $data['pincode'];

$user_id = $_SESSION['user_id'];

/* OPTIONAL: INSERT ORDER (if not already inserted) */
// You can keep your DB logic here if needed
$orderStmt = $conn->prepare("INSERT INTO orders 
(user_id, total_price, name, phone, address, city, pincode, payment_method) 
VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

$orderStmt->bind_param("idssssss",
    $user_id,
    $total,
    $name,
    $phone,
    $address,
    $city,
    $pincode,
    $payment
);

$orderStmt->execute();

$order_id = $conn->insert_id;

/* INSERT ORDER ITEMS */
$itemStmt = $conn->prepare("INSERT INTO order_items 
(order_id, product_id, quantity, price) 
VALUES (?, ?, ?, ?)");

$itemStmt->bind_param("iiid",
    $order_id,
    $product_id,
    $qty,
    $data['price']
);

$itemStmt->execute();


unset($_SESSION['temp_order']); // clear session after success
?>

<!DOCTYPE html>
<html>
<head>
<title>Payment Success</title>

<style>
body{
    font-family: Arial;
    background:#ebebc6;
    display:flex;
    justify-content:center;
    align-items:center;
    height:100vh;
}

.box{
    background:#f5f5dc;
    padding:30px;
    border-radius:10px;
    text-align:center;
}

img{
    width:80px;
}
.buttons{
    display:flex;
    gap:10px;
    margin-top:20px;
}

.btn{
    flex:1;
    padding:10px;
    border:none;
    border-radius:6px;
    cursor:pointer;
    font-size:14px;
    background:#b37d5c;
    color:white;
    text-decoration:none;
    text-align:center;
    transition:0.3s;
}

.btn:hover{
    background:#a06a4a;
    transform:scale(1.05);
}
</style>

</head>

<body>

<div class="box">

<h2>✅ Payment Successful</h2>

<p><strong><?php echo $name; ?></strong></p>

<img src="image/<?php echo $product_image; ?>">

<p><?php echo $product_name; ?></p>
<p>Total: ₹<?php echo $total; ?></p>

<p>🎉 Order Confirmed!</p>

<div class="buttons">

    <a href="04_product_section.html" class="btn">
        Continue Shopping
    </a>

    <a href="17_my_orders.php" class="btn">
        View Orders
    </a>

</div>
</div>

</body>
</html>