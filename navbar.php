<?php
if(session_status() === PHP_SESSION_NONE){
    session_start();
}

include("db_connect.php");

$cartCount = 0;

if(isset($_SESSION['user_id'])){
    $user_id = $_SESSION['user_id'];

    $countQuery = $conn->prepare("SELECT COUNT(*) as total FROM cart WHERE user_id=?");
    $countQuery->bind_param("i",$user_id);
    $countQuery->execute();
    $countResult = $countQuery->get_result()->fetch_assoc();
    $cartCount = $countResult['total'];
}
?>

<style>

/* RESET */
*{margin:0;padding:0;box-sizing:border-box;font-family:Arial;}

/* NAVBAR */
nav{
position:fixed;
top:0;
width:100%;
background:#3e2723;
display:flex;
align-items:center;
padding:25px 5%;
z-index:1000;
}

/* LOGO */
.logo{
color:#f5f5dc;
font-size:30px;
font-weight:bold;
}

/* RIGHT SIDE */
.nav-right{
margin-left:auto;
display:flex;
align-items:center;
gap:15px;
}

/* MENU */
#navMenu{
list-style:none;
display:flex;
font-size:18px;
gap:25px;
align-items:center;

}

/* LINKS */
nav a{
color:white;
text-decoration:none;
font-weight:bold;
padding:6px 10px;
border-radius:5px;
transition:0.3s;
}

nav a:hover{
background:#b37d5c;
}

/* DROPDOWN */
.dropdown{
position:relative;
}

.dropdown-content{
position:absolute;
top:100%;
left:0;
background:#3e2723;
min-width:170px;
border-radius:6px;
overflow:hidden;
box-shadow:0 5px 10px rgba(0,0,0,0.3);

opacity:0;
transform:translateY(10px);
pointer-events:none;
transition:0.3s;
}

.dropdown-content a{
display:flex;
justify-content:space-between;
align-items:center;
padding:10px;
}

.dropdown-content a:hover{
background:#b37d5c;
}

.dropdown:hover .dropdown-content{
opacity:1;
transform:translateY(0);
pointer-events:auto;
}

/* CART BADGE */
.cart-badge{
background:red;
color:white;
font-size:11px;
padding:2px 6px;
border-radius:50%;
}

/* HAMBURGER */
.menu-toggle{
display:none;
font-size:28px;
color:white;
cursor:pointer;
z-index:1100;
}

/* MOBILE */
@media(max-width:768px){

/* SHOW HAMBURGER */
.menu-toggle{
display:block !important;
}

/* HIDE MENU */
#navMenu{
display:none !important;
flex-direction:column;
position:absolute;
top:60px;
right:5%;
background:#3e2723;
padding:15px;
border-radius:8px;
width:200px;
}

/* SHOW MENU */
#navMenu.active{
display:flex !important;
}

/* DROPDOWN MOBILE */
.dropdown-content{
position:static;
opacity:1;
transform:none;
pointer-events:auto;
display:none;
}

.dropdown.open .dropdown-content{
display:block;
}

}

</style>

<nav>

<div class="logo">Artisan & Handmade</div>

<div class="nav-right">

<!-- MENU -->
<ul id="navMenu">

<li><a href="01_index.php">Home</a></li>
<li><a href="04_product_section.html">Shop</a></li>

<li class="dropdown">
<a href="#" onclick="toggleDropdown(event)">
<i class="fa fa-user"></i> Account
</a>

<div class="dropdown-content">

<?php if(isset($_SESSION['user_id'])){ ?>

<a href="21_myaccount.php">My Account</a>
<a href="17_my_orders.php">My Orders</a>
<a href="11_wishlist.php">Wishlist</a>

<a href="13_cart.php">
<span><i class="fa fa-shopping-cart"></i> Cart</span>
<span class="cart-badge"><?php echo $cartCount; ?></span>
</a>

<a href="22_logout.php">Logout</a>

<?php } else { ?>

<a href="03_login.php">Login</a>
<a href="02_register.php">Register</a>
<a href="admin_login.php">Admin</a>

<?php } ?>

</div>

</li>

<li><a href="19_about_us.php">About</a></li>

</ul>

<!-- HAMBURGER (IMPORTANT: OUTSIDE UL) -->
<i class="fa fa-bars menu-toggle" onclick="toggleMenu()"></i>

</div>

</nav>

<script>
function toggleMenu(){
document.getElementById("navMenu").classList.toggle("active");
}

function toggleDropdown(e){
if(window.innerWidth <= 768){
e.preventDefault();
let dropdown = e.target.closest(".dropdown");
dropdown.classList.toggle("open");
}
}
</script>