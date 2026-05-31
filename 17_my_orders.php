<?php 
session_start();
include("db_connect.php");

if(!isset($_SESSION['user_id'])){
    header("Location:03_login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$reviewedProducts = [];

$reviewQuery = $conn->prepare("SELECT product_id FROM reviews WHERE user_id=?");
$reviewQuery->bind_param("i", $user_id);
$reviewQuery->execute();
$reviewResult = $reviewQuery->get_result();

while($row = $reviewResult->fetch_assoc()){
    $reviewedProducts[] = $row['product_id'];
}
$type = isset($_GET['type']) ? $_GET['type'] : 'orders';

if($type == "orders"){
    $statusFilter = "('Pending','Shipped')";
}
elseif($type == "history"){
    $statusFilter = "('Delivered')";
}
else{
    $statusFilter = "('Cancelled')";
}

$orderQuery = $conn->prepare("
SELECT * FROM orders 
WHERE user_id=? 
AND order_status IN $statusFilter 
ORDER BY order_id DESC
");
$orderQuery->bind_param("i", $user_id);
$orderQuery->execute();
$orders = $orderQuery->get_result();
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>My Orders</title>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:Arial;}

body{background:#ebebc6;margin-top:80px;}

.header{text-align:center;font-size:26px;font-weight:bold;}

.tabs{text-align:center;margin:20px 0;}
.tabs a{padding:10px 20px;background:#ccc;margin:5px;border-radius:6px;text-decoration:none;color:black;}
.tabs a.active{background:#3e2723;color:white;}

.container{max-width:900px;margin:auto;padding:20px;}

.order-card{
background:#f5f5dc;
padding:20px;
border-radius:12px;
box-shadow:0 4px 10px rgba(0,0,0,0.1);
margin-bottom:20px;
}

.product{display:flex;gap:15px;align-items:center;margin:10px 0;}
.product img{width:120px;height:120px;border-radius:8px; object-fit:cover;}

.status{font-weight:bold;margin-top:10px;}
.pending{color:orange;}
.shipped{color:blue;}
.delivered{color:green;}
.cancelled{color:red;}
.cancel-btn{
position:absolute;
top:10px;
right:15px;
font-size:18px;
color:red;
cursor:pointer;
font-weight:bold;
}

.cancel-btn:hover{
transform:scale(1.2);
}
.cancel-btn{
    position:absolute;
    top:10px;
    right:15px;
    font-size:18px;
    color:red;
    cursor:pointer;
    font-weight:bold;
}
/* REVIEW BUTTON */
.review-btn{
    margin-top:8px;
    padding:8px 14px;
    background:#3e2723;
    color:#fff;
    border:none;
    border-radius:6px;
    font-size:14px;
    cursor:pointer;
    transition:all 0.3s ease;
}

/* HOVER EFFECT */
.review-btn:hover{
    background:#5d4037;
    transform:scale(1.05);
}

/* CLICK EFFECT */
.review-btn:active{
    transform:scale(0.95);
}

/* REVIEWED TEXT */
.reviewed-text{
    display:inline-block;
    margin-top:8px;
    color:green;
    font-size:14px;
    font-weight:bold;
}
/* Tooltip */
.cancel-btn::after{
    content: attr(data-tooltip);
    position:absolute;
    top: -30px;
    right: 0;
    background:black;
    color:white;
    padding:5px 8px;
    font-size:12px;
    border-radius:4px;
    opacity:0;
    pointer-events:none;
    white-space:nowrap;
    transition:0.3s;
}

.cancel-btn:hover::after{
    opacity:1;
}
.cancel-btn:hover{
    transform: scale(1.2);
}
/* POPUP */
#reviewPopup{
position:fixed;
top:0;left:0;
width:100%;height:100%;
background:rgba(0,0,0,0.6);
display:none;
justify-content:center;
align-items:center;
z-index:9999;
}

.popup-box{
background:#fff;
padding:25px;
width:380px;
border-radius:12px;
text-align:center;
box-shadow:0 10px 25px rgba(0,0,0,0.3);
}

.popup-box img{
width:100px;
height:100px;
border-radius:10px;
margin-bottom:10px;
}

.stars{
margin:10px 0;
font-size:24px;
color:#ccc;
cursor:pointer;
}

.stars i.active{color:gold;}

.popup-box textarea{
width:100%;
height:80px;
padding:10px;
border-radius:6px;
border:1px solid #ccc;
}

.popup-btns{
margin-top:15px;
display:flex;
justify-content:space-between;
}

.popup-btns button, .popup-box button{
padding:10px 15px;
border:none;
border-radius:6px;
cursor:pointer;
background:#7a7f00;
color:white;
}
</style>
</head>

<body>

<?php include("navbar.php"); ?>

<div class="header">📦 My Orders</div>

<div class="tabs">
<a href="?type=orders" class="<?php if($type=='orders') echo 'active'; ?>">Orders</a>
<a href="?type=history" class="<?php if($type=='history') echo 'active'; ?>">History</a>
<a href="?type=cancelled" class="<?php if($type=='cancelled') echo 'active'; ?>">Cancelled</a>
</div>

<div class="container">

<?php 
$popupData = null;

while($order = $orders->fetch_assoc()):
?>

<div class="order-card" style="position:relative;">

<?php if($order['order_status'] == 'Pending'): ?>
 <span class="cancel-btn" 
      onclick="cancelOrder(<?php echo $order['order_id']; ?>)" 
      data-tooltip="Cancel Order">✖</span>  
<?php endif; ?>

<p><strong>Order #<?php echo $order['order_id']; ?></strong></p>

<?php
$itemQuery = $conn->prepare("
SELECT oi.*, p.name, p.image, p.product_id 
FROM order_items oi
JOIN products p ON oi.product_id = p.product_id
WHERE oi.order_id=?");

if(!$itemQuery){
    die("SQL Error: " . $conn->error);
}

$itemQuery->bind_param("i", $order['order_id']);
$itemQuery->execute();
$items = $itemQuery->get_result();

/* STORE DATA */
$itemList = [];
while($row = $items->fetch_assoc()){
    $itemList[] = $row;
}

/* CHECK REVIEW */
foreach($itemList as $item){

    if($order['order_status']=='Delivered' 
       && !in_array($item['product_id'], $reviewedProducts)){

        $popupData = [
            "product"=>$item['name'],
            "product_id"=>$item['product_id'],
            "order_id"=>$order['order_id'],
            "image"=>$item['image']
        ];
        break;
    }
}
?>
<?php foreach($itemList as $item): ?>

<div class="product">
    <img src="image/<?php echo $item['image']; ?>">

    <div>
        <?php echo $item['name']; ?> × <?php echo $item['quantity']; ?>

        <?php if($type == 'history'): ?>

            <?php if(!in_array($item['product_id'], $reviewedProducts)): ?>
                <br>
                <button onclick="openReview(
                    '<?php echo addslashes($item['name']); ?>',
                    <?php echo $item['product_id']; ?>,
                    <?php echo $order['order_id']; ?>,
                    '<?php echo $item['image']; ?>'
                )">
                Write Review
                </button>
            <?php else: ?>
                <br><span style="color:green;">✔ Reviewed</span>
            <?php endif; ?>

        <?php endif; ?>

    </div>
</div>

<?php endforeach; ?>
<div class="status <?php echo strtolower($order['order_status']); ?>">
Status: <?php echo $order['order_status']; ?>
</div>

<?php

?>

</div>

<?php endwhile; ?>

</div>

<!-- POPUP -->
<div id="reviewPopup">
  <div class="popup-box">

    <!-- STEP 1 -->
    <div id="step1">
      <img id="popupImage">
      <h3 id="popupTitle"></h3>
      <p id="productText"></p>
      <button onclick="goToReview()">OK</button>
    </div>

    <!-- STEP 2 -->
    <div id="step2" style="display:none;">
      <h3 id="reviewTitle"></h3>

      <div class="stars">
        <i class="fa fa-star" data-value="1"></i>
        <i class="fa fa-star" data-value="2"></i>
        <i class="fa fa-star" data-value="3"></i>
        <i class="fa fa-star" data-value="4"></i>
        <i class="fa fa-star" data-value="5"></i>
      </div>

      <textarea id="reviewText" placeholder="Write your feedback..."></textarea>

      <div class="popup-btns">
        <button onclick="submitReview()">Submit</button>
        <button onclick="closePopup()">Skip</button>
      </div>
    </div>

  </div>
</div>

<script>
   let popupData = null;
let selectedRating = 0;

/* OPEN REVIEW POPUP */
function openReview(name, product_id, order_id, image){

    popupData = {
        product: name,
        product_id: product_id,
        order_id: order_id,
        image: image
    };

    document.getElementById("reviewPopup").style.display = "flex";

    document.getElementById("popupImage").src = "image/" + image;
    document.getElementById("popupTitle").innerText = name;
    document.getElementById("productText").innerText = "Write review for " + name;

    document.getElementById("step1").style.display = "block";
    document.getElementById("step2").style.display = "none";
}

/* CANCEL ORDER */
function cancelOrder(orderId){

    if(!confirm("Are you sure you want to cancel this order?")){
        return;
    }

    fetch("18_cancel_order.php",{
        method:"POST",
        headers:{"Content-Type":"application/x-www-form-urlencoded"},
        body:`order_id=${orderId}`
    })
    .then(res => res.text())
    .then(res => {
        if(res.trim() === "success"){
            alert("Order cancelled");
            location.reload();
        } else {
            alert("Error: " + res);
        }
    });
}
/* STEP SWITCH */
function goToReview(){
    document.getElementById("step1").style.display = "none";
    document.getElementById("step2").style.display = "block";
}

/* CLOSE POPUP */
function closePopup(){

    if(popupData){
        fetch("mark_review_done.php",{
            method:"POST",
            headers:{"Content-Type":"application/x-www-form-urlencoded"},
            body:`order_id=${popupData.order_id}`
        });
    }

    document.getElementById("reviewPopup").style.display = "none";
}

/* STAR RATING */
document.querySelectorAll(".stars i").forEach(star=>{
    star.addEventListener("click",function(){
        selectedRating = this.getAttribute("data-value");

        document.querySelectorAll(".stars i").forEach(s=>s.classList.remove("active"));

        for(let i=0; i < selectedRating; i++){
            document.querySelectorAll(".stars i")[i].classList.add("active");
        }
    });
});

/* SUBMIT REVIEW */
function submitReview(){

    let review = document.getElementById("reviewText").value.trim();

    if(review === ""){
        alert("Write review first");
        return;
    }

    if(selectedRating == 0){
        alert("Select rating");
        return;
    }

    fetch("25_submit_review.php",{
        method:"POST",
        headers:{"Content-Type":"application/x-www-form-urlencoded"},
        body:`product_id=${popupData.product_id}&order_id=${popupData.order_id}&review=${encodeURIComponent(review)}&rating=${selectedRating}`
    })
    .then(res => res.text())
    .then(res => {

        console.log(res);

        if(res.trim() === "success"){
            alert("Review submitted");
            closePopup();
            location.reload();
        } 
        else if(res.trim() === "already_reviewed"){
            alert("You already reviewed this product");
            closePopup();
        }
        else{
            alert("Error: " + res);
        }
    });
}
</script>

</body>
</html>