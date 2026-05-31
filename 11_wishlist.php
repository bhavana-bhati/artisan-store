<?php
session_start();
include("db_connect.php");

if(!isset($_SESSION['user_id'])){
    header("Location: 02_register.php?redirect=11_wishlist.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT w.id, p.product_id, p.name, p.description, p.price, p.image
        FROM wishlist w
        JOIN products p ON w.product_id = p.product_id
        WHERE w.user_id = ?";

$stmt = $conn->prepare($sql);

if(!$stmt){
    die("SQL Error: ".$conn->error);
}

$stmt->bind_param("i",$user_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Wishlist</title>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<style>

/* RESET */
*{margin:0;padding:0;box-sizing:border-box;font-family:Arial;}
body{
background:#ebebc6;
margin-top:80px; /* IMPORTANT for navbar */
}

/* PAGE TITLE */
.page-title{
text-align:center;
margin-bottom:30px;
font-size:28px;
}

/* PRODUCTS */
.product-container{
display:flex;
flex-wrap:wrap;
justify-content:center;
gap:25px;
padding-bottom:50px;
}

.product-card{
position:relative;
width:260px;
background:#fff;
border-radius:10px;
overflow:hidden;
box-shadow:0 4px 10px rgba(0,0,0,0.1);
transition:transform .2s;
}

.product-card:hover{
transform:translateY(-5px);
}

.image-container{
position:relative;
height:200px;
overflow:hidden;
}

.image-container img{
width:100%;
height:100%;
object-fit:cover;
}

.quick-view{
position:absolute;
top:50%;
left:50%;
transform:translate(-50%,-50%);
background:rgba(0,0,0,0.6);
color:#fff;
border:none;
padding:10px 14px;
border-radius:50%;
display:none;
cursor:pointer;
}

.image-container:hover .quick-view{
display:block;
}

.wishlist-btn{
position:absolute;
top:10px;
right:10px;
border:none;
background:none;
font-size:20px;
color:red;
cursor:pointer;
}

.product-info{
padding:15px;
}

.product-info h3{
margin:0 0 8px;
font-size:18px;
}

.description{
font-size:14px;
color:#666;
margin-bottom:8px;
}

.price{
color:#1e7d3c;
font-weight:bold;
}

.empty-msg{
text-align:center;
font-size:18px;
}

</style>
</head>

<body>

<!-- ✅ NAVBAR INCLUDE -->
<?php include("navbar.php"); ?>

<h2 class="page-title">My Wishlist ❤️</h2>

<div class="product-container">

<?php if($result->num_rows > 0): ?>

<?php while($row = $result->fetch_assoc()): ?>

<div class="product-card">

<div class="image-container">
<img src="image/<?php echo $row['image']; ?>">

<button class="quick-view"
onclick="location.href='10_product_detail.php?id=<?php echo $row['product_id']; ?>'">
<i class="fa fa-eye"></i>
</button>
</div>

<button class="wishlist-btn" data-id="<?php echo $row['product_id']; ?>">
<i class="fa-solid fa-heart"></i>
</button>

<div class="product-info">
<h3><?php echo $row['name']; ?></h3>
<p class="description"><?php echo $row['description']; ?></p>
<p class="price">₹<?php echo $row['price']; ?></p>
</div>

</div>

<?php endwhile; ?>

<?php else: ?>

<p class="empty-msg">Your wishlist is empty 💔</p>

<?php endif; ?>

</div>

<script>

/* Wishlist remove */
$(document).on("click",".wishlist-btn",function(){

var btn=$(this)
var productId=btn.data("id")

$.post("12_wishlist_ajax.php",{product_id:productId},function(res){

if(res.status==="removed"){
btn.closest(".product-card").fadeOut(function(){
$(this).remove()
})
}

},"json")

});

</script>

</body>
</html>