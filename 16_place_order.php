<?php
session_start();
include("db_connect.php");

if(!isset($_SESSION['user_id'])){
    header("Location:03_login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

/* GET DATA FROM CHECKOUT */
$product_id = $_POST['product_id'];
$qty = $_POST['qty'];

$name = $_POST['name'];
$phone = $_POST['phone'];
$address = $_POST['address'];
$city = $_POST['city'];
$pincode = $_POST['pincode'];
$payment = $_POST['payment'];

/* FETCH PRODUCT DETAILS */
$productQuery = $conn->prepare("SELECT price, name, image FROM products WHERE product_id=?");

if(!$productQuery){
    die("Product Query Error: " . $conn->error);
}

$productQuery->bind_param("i", $product_id);
$productQuery->execute();
$res = $productQuery->get_result();
$product = $res->fetch_assoc();

if(!$product){
    die("Product not found!");
}

$price = $product['price'];
$product_name = $product['name'];
$product_image = $product['image'];

$total = $price * $qty;

    
if($payment == "upi"){

    $_SESSION['temp_order'] = [
        "product_id" => $product_id,
        "qty" => $qty,
        "name" => $name,
        "phone" => $phone,
        "address" => $address,
        "city" => $city,
        "pincode" => $pincode,
        "product_name" => $product_name,
        "product_image" => $product_image,
        "price" => $price,
        "total" => $total
    ];

    header("Location: 23_upi_payment.php");
    exit();
}

/* =========================
   STEP 1: INSERT INTO ORDERS
   ========================= */
$orderStmt = $conn->prepare("INSERT INTO orders 
(user_id, total_price, name, phone, address, city, pincode, payment_method) 
VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

if(!$orderStmt){
    die("Order Insert Error: " . $conn->error);
}

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

/* GET ORDER ID */
$order_id = $conn->insert_id;

/* =========================
   STEP 2: INSERT INTO ORDER ITEMS
   ========================= */
$itemStmt = $conn->prepare("INSERT INTO order_items 
(order_id, product_id, quantity, price) 
VALUES (?, ?, ?, ?)");

if(!$itemStmt){
    die("Order Item Error: " . $conn->error);
}

$itemStmt->bind_param("iiid",
    $order_id,
    $product_id,
    $qty,
    $price
);

$itemStmt->execute();
?>

<!DOCTYPE html>
<html>
<head>
<title>Order Success</title>

<style>
:root{
--brand:#3e2723;
--card:#f5f5dc;
--bg:#ebebc6;
--btn:#b37d5c;
--btn-hover:#a06a4a;
}

body{
margin:0;
font-family:Arial;
background:var(--bg);
display:flex;
justify-content:center;
align-items:center;
min-height:100vh;
padding:15px;
}

/* BOX */
.success-box{
background:var(--card);
padding:30px;
border-radius:12px;
box-shadow:0 5px 15px rgba(0,0,0,0.2);
text-align:center;
width:100%;
max-width:420px;
animation:fadeIn 0.5s ease;
}

@keyframes fadeIn{
from{opacity:0; transform:translateY(20px);}
to{opacity:1; transform:translateY(0);}
}

.success-box h2{
color:green;
margin-bottom:15px;
font-size:22px;
}

/* PRODUCT */
.product{
display:flex;
align-items:center;
gap:15px;
margin:20px 0;
text-align:left;
flex-wrap:wrap;
}

.product img{
width:70px;
height:70px;
object-fit:cover;
border-radius:6px;
flex-shrink:0;
}

.product-details{
font-size:14px;
line-height:1.5;
}

/* BUTTONS */
.buttons{
display:flex;
gap:10px;
margin-top:20px;
flex-wrap:wrap;
}

.btn{
flex:1;
min-width:120px;
padding:10px;
border:none;
border-radius:6px;
cursor:pointer;
font-size:14px;
background:var(--btn);
color:white;
transition:0.3s;
text-decoration:none;
text-align:center;
}

.btn:hover{
background:var(--btn-hover);
transform:scale(1.05);
}

/* RESPONSIVE */
@media (max-width:480px){

.product{
flex-direction:column;
align-items:flex-start;
}

.product img{
width:100%;
height:150px;
}

.buttons{
flex-direction:column;
}

.btn{
width:100%;
}
}
</style>
</head>

<body>

<div class="success-box">

<h2>🎉 Order Confirmed Successfully!</h2>

<div class="product">
    <img src="image/<?php echo $product_image; ?>">
    <div class="product-details">
        <strong><?php echo $product_name; ?></strong><br>
        Qty: <?php echo $qty; ?><br>
        Total: ₹<?php echo $total; ?>
    </div>
</div>

<div class="buttons">
    <a href="04_product_section.html" class="btn">Continue Shopping</a>
    <a href="17_my_orders.php" class="btn">My Orders</a>
</div>

</div>

</body>
</html>