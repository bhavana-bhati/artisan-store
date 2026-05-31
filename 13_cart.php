<?php
session_start();
include("db_connect.php");

/* LOGIN CHECK */

if (!isset($_SESSION['user_id'])) {
    header("Location: 02_register.php?redirect=13_cart.php");
    exit();
}

$user_id = $_SESSION['user_id'];

/* ADD PRODUCT TO CART */

if(isset($_POST['product_id'])){

$product_id = intval($_POST['product_id']);
$quantity = intval($_POST['quantity']);

if($quantity <= 0){
$quantity = 1;
}

$check = $conn->prepare("SELECT cart_id, quantity FROM cart WHERE user_id=? AND product_id=?");
$check->bind_param("ii",$user_id,$product_id);
$check->execute();
$res = $check->get_result();

if($res->num_rows > 0){

$row = $res->fetch_assoc();
$newQty = $row['quantity'] + $quantity;

$update = $conn->prepare("UPDATE cart SET quantity=? WHERE cart_id=?");
$update->bind_param("ii",$newQty,$row['cart_id']);
$update->execute();

}else{

$insert = $conn->prepare("INSERT INTO cart (user_id,product_id,quantity) VALUES (?,?,?)");
$insert->bind_param("iii",$user_id,$product_id,$quantity);
$insert->execute();

}

header("Location: 13_cart.php");
exit();
}

/* FETCH CART PRODUCTS */

$stmt = $conn->prepare("
SELECT c.cart_id, c.product_id, p.name, p.image, p.price, c.quantity,
(p.price * c.quantity) AS subtotal
FROM cart c
JOIN products p ON c.product_id = p.product_id
WHERE c.user_id = ?
");

$stmt->bind_param("i",$user_id);
$stmt->execute();
$cartItems = $stmt->get_result();
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Your Cart</title>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<style>

body{
font-family:Arial;
background:#f5f5dc;
margin:0;
margin-top:80px; /* IMPORTANT for navbar */
}

/* ===== KEEP YOUR ORIGINAL DESIGN ===== */

.container{
max-width:900px;
margin:40px auto;
background:#fffdf7;
padding:20px;
border-radius:12px;
box-shadow:0 4px 10px rgba(0,0,0,0.1);
}

.cart-grid{
display:grid;
grid-template-columns:repeat(auto-fit,minmax(350px,1fr));
gap:20px;
}

.cart-item{
position:relative;
background:#fff;
border:1px solid #ddd;
border-radius:12px;
padding:15px;
display:flex;
flex-direction:column;
align-items:center;
}

.cart-item img{
width:200px;
height:200px;
object-fit:cover;
border-radius:8px;
margin-bottom:15px;
}

.qty{
display:flex;
gap:10px;
align-items:center;
margin:10px 0;
}

.qty button{
padding:6px 12px;
background:#6b8e23;
color:#fff;
border:none;
border-radius:6px;
cursor:pointer;
}

.remove-btn{
position:absolute;
top:10px;
right:10px;
background:red;
color:#fff;
border:none;
border-radius:50%;
width:28px;
height:28px;
cursor:pointer;
}

.total{
text-align:right;
font-size:18px;
margin-top:20px;
font-weight:bold;
}

.action-btn{
margin-top:8px;
padding:6px 12px;
border:none;
border-radius:6px;
cursor:pointer;
}

.view-btn{background:#444;color:white;text-decoration:none}
.save-btn{background:#999;color:white}
.clear-btn{background:#b22222;color:white}
.checkout-btn{background:#2e8b57;color:white;text-decoration:none}

</style>
</head>

<body>

<!-- ✅ COMMON NAVBAR -->
<?php include("navbar.php"); ?>

<div class="container">

<h2>🛒 Your Cart</h2>

<?php if($cartItems->num_rows == 0): ?>
<p>Your cart is empty</p>
<?php else: ?>

<div class="cart-grid">

<?php 
$total = 0;
while($row = $cartItems->fetch_assoc()):
$total += $row['subtotal'];
?>

<div class="cart-item" data-id="<?php echo $row['cart_id']; ?>" data-product="<?php echo $row['product_id']; ?>">

<button class="remove-btn">×</button>

<img src="image/<?php echo $row['image']; ?>">

<h3><?php echo $row['name']; ?></h3>

<p>Price: ₹<?php echo $row['price']; ?></p>

<div class="qty">
<button class="updateQty" data-change="-1">-</button>
<input type="number" value="<?php echo $row['quantity']; ?>" min="1">
<button class="updateQty" data-change="1">+</button>
</div>

<p class="subtotal">Subtotal: ₹<?php echo $row['subtotal']; ?></p>

<a class="action-btn view-btn"
href="10_product_detail.php?id=<?php echo $row['product_id']; ?>">
View Product
</a>

<button type="button" class="action-btn save-btn saveLater">
Save for later
</button>

</div>

<?php endwhile; ?>

</div>

<div class="total">
Total: ₹<span id="cartTotal"><?php echo $total; ?></span>
</div>

<div style="margin-top:20px;text-align:right">

<button id="clearCart" class="action-btn clear-btn">Clear Cart</button>

<a href="15_checkout.php" class="action-btn checkout-btn">
Proceed to Checkout
</a>

</div>

<?php endif; ?>

</div>

<script>

/* ===== YOUR ORIGINAL JS (UNCHANGED) ===== */

function updateTotal(){
let total = 0;
$(".subtotal").each(function(){
let val = parseFloat($(this).text().replace("Subtotal: ₹",""));
if(!isNaN(val)) total += val;
});
$("#cartTotal").text(total);
}

$(document).on("click",".updateQty",function(){
let parent = $(this).closest(".cart-item");
let input = parent.find("input");
let qty = parseInt(input.val()) + parseInt($(this).data("change"));
if(qty < 1) qty = 1;
input.val(qty);
let cart_id = parent.data("id");

$.ajax({
url:"14_cart_ajax.php",
type:"POST",
dataType:"json",
data:{action:"update",cart_id:cart_id,quantity:qty},
success:function(res){
if(res.status=="updated"){
parent.find(".subtotal").text("Subtotal: ₹"+res.subtotal);
updateTotal();
}
}
});
});

$(document).on("click",".remove-btn",function(){
let parent = $(this).closest(".cart-item");
let cart_id = parent.data("id");

$.ajax({
url:"14_cart_ajax.php",
type:"POST",
dataType:"json",
data:{action:"remove",cart_id:cart_id},
success:function(res){
if(res.status=="removed"){
parent.remove();
updateTotal();
}
}
});
});

$(".saveLater").click(function(){
let parent = $(this).closest(".cart-item");
let cart_id = parent.data("id");
let product_id = parent.data("product");

$.ajax({
url:"14_cart_ajax.php",
type:"POST",
dataType:"json",
data:{action:"saveLater",cart_id:cart_id,product_id:product_id},
success:function(res){
if(res.status=="saved"){
parent.remove();
updateTotal();
alert("Product moved to wishlist");
}
}
});
});

$("#clearCart").click(function(){
if(confirm("Clear entire cart?")){
$.ajax({
url:"14_cart_ajax.php",
type:"POST",
dataType:"json",
data:{action:"clear"},
success:function(res){
if(res.status=="cleared"){
$(".cart-item").remove();
$("#cartTotal").text("0");
alert("Cart cleared");
}
}
});
}
});

</script>

</body>
</html>