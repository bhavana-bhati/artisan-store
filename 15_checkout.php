<?php
session_start();
include("db_connect.php");

if(!isset($_SESSION['user_id'])){
    header("Location:03_login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

/* Fetch user data */
$userQuery = mysqli_query($conn,"SELECT username, phone FROM users WHERE id='$user_id'");
$user = mysqli_fetch_assoc($userQuery);

/* Get product & quantity */
if(!isset($_POST['product_id']) || !isset($_POST['quantity'])){
    echo "Invalid request!";
    exit();
}

$product_id = intval($_POST['product_id']);
$qty = intval($_POST['quantity']);

/* Fetch product info safely */
$productQuery = mysqli_query($conn,"SELECT name,image,price FROM products WHERE product_id='$product_id'");

if(!$productQuery || mysqli_num_rows($productQuery) == 0){
    echo "Product not found!";
    exit();
}

$product = mysqli_fetch_assoc($productQuery);

/* Always calculate from DB */
$price = $product['price'];
$total = $price * $qty;
?>
<!DOCTYPE html>
<html>
<head>

<title>Checkout</title>

<style>
:root{
--brand:#3e2723;
--brand-light:#5d4037;
--bg:#ebebc6;
--card:#f5f5dc;
--btn:#b37d5c;
--btn-hover:#a06a4a;
}

body{
margin:0;
font-family:Arial;
background:var(--bg);
}

.checkout-container{
max-width:900px;
margin:120px auto;
background:var(--card);
padding:30px;
border-radius:12px;
box-shadow:0 5px 15px rgba(0,0,0,0.2);
animation:fadeIn 0.6s ease;
}

@keyframes fadeIn{
from{opacity:0; transform:translateY(20px);}
to{opacity:1; transform:translateY(0);}
}

.order-item{
display:flex;
align-items:center;
gap:20px;
border-bottom:1px solid #ddd;
padding-bottom:20px;
margin-bottom:20px;
}

.order-item img{
width:80px;
height:80px;
object-fit:cover;
border-radius:6px;
}

.total{
text-align:right;
font-size:20px;
font-weight:bold;
margin-bottom:20px;
}

form label{
display:block;
margin-top:15px;
font-weight:bold;
}

form input,textarea{
width:100%;
padding:10px;
border-radius:6px;
border:1px solid #ccc;
margin-top:6px;
}

.place-btn{
margin-top:20px;
background:var(--btn);
color:white;
padding:12px;
border:none;
border-radius:6px;
cursor:pointer;
font-size:16px;
width:100%;
transition:0.3s;
}

.place-btn:hover{
background:var(--btn-hover);
transform:scale(1.03);
}
</style>

</head>

<body>

<div class="checkout-container">

<h2>Order Summary</h2>

<div class="order-item">
<img src="image/<?php echo $product['image']; ?>">
<div>
<h3><?php echo $product['name']; ?></h3>
<p>Qty : <?php echo $qty; ?> × ₹<?php echo $price; ?></p>
</div>
</div>

<div class="total">
Total : ₹<?php echo $total; ?>
</div>

<h2>Shipping Info</h2>

<form action="16_place_order.php" method="POST" onsubmit="return validatePayment()">

<input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
<input type="hidden" name="qty" value="<?php echo $qty; ?>">

<label>Name</label>
<input type="text" name="name" value="<?php echo $user['username']; ?>" required>

<label>Phone</label>
<input type="text" name="phone" value="<?php echo $user['phone']; ?>" required>

<label>Address</label>
<textarea name="address" required></textarea>

<label>City</label>
<input type="text" name="city" required>

<label>Pincode</label>
<input type="text" name="pincode" required>

<label>Payment Method</label>

<select name="payment" id="paymentSelect">
    <option value="cod">Cash On Delivery</option>
    <option value="upi">UPI</option>
</select>




<button class="place-btn">Place Order</button>

</form>

</div>
<script>

const cod = document.querySelector('input[value="cod"]');
const upi = document.getElementById('upiOption');
const upiBox = document.getElementById('upiBox');

upi.addEventListener("change", () => {
    upiBox.style.display = "block";
});

cod.addEventListener("change", () => {
    upiBox.style.display = "none";
});

function validatePayment(){
    const upi = document.getElementById('upiOption');
    const upiInput = document.querySelector('input[name="upi_id"]');

    if(upi.checked && upiInput.value.trim() === ""){
        alert("Enter UPI ID");
        return false;
    }
    return true;
}
</script>
</body>
</html>