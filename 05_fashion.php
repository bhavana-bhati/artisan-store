<?php
session_start();
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
include("db_connect.php");

$isLoggedIn = isset($_SESSION['user_id']);
$user_id = $isLoggedIn ? $_SESSION['user_id'] : 0;

// Fetch all fashion products
$query = "SELECT * FROM products WHERE category='fashion'";
$result = mysqli_query($conn, $query);

// Fetch wishlist for logged-in user
$wishlistProducts = [];
if ($isLoggedIn) {
    $res = mysqli_query($conn, "SELECT product_id FROM wishlist WHERE user_id=$user_id");
    while($w = mysqli_fetch_assoc($res)) {
        $wishlistProducts[] = $w['product_id'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Fashion Products</title>
<link rel="stylesheet" href="style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
/* Your existing CSS goes here – unchanged */
:root {
    --brand: #3e2723;
    --brand-light: #5d4037;
    --page-bg: #ebebc6;
    --card-radius: 14px;
    --gap: 22px;
    --card-bg: #f5f5dc;
    --btn-color: #b37d5c;
    --btn-hover: #a06a4a;
}
body { margin:0;      padding-top: 70px; font-family:Arial, sans-serif; background:var(--page-bg); }
footer {
        background: var(--brand);
        color: #f5f5dc;
        padding: 40px 5% 20px;
        margin-top: 40px;
      }
      .footer-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 30px;
        max-width: 1200px;
        margin: 0 auto;
      }
      .footer-section h3 {
        color: #b37d5c;
        margin-bottom: 15px;
        font-size: 18px;
        border-bottom: 2px solid var(--btn-color);
        display: inline-block;
        padding-bottom: 5px;
      }
      .footer-section ul {
        list-style: none;
        padding: 0;
      }
      .footer-section ul li {
        margin-bottom: 10px;
      }
      .footer-section a {
        color: #f5f5dc;
        text-decoration: none;
        transition:
          color 0.3s,
          transform 0.2s;
        display: inline-block;
      }
      .footer-section a:hover {
        color: var(--btn-color);
        transform: translateX(5px);
      }
      .social-icons a {
        margin-right: 15px;
        font-size: 20px;
      }
      .copyright {
        text-align: center;
        margin-top: 30px;
        padding-top: 20px;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        font-size: 14px;
        color: #ccc;
      }
      /* --- BACK TO TOP BUTTON --- */
      #backToTopBtn {
        display: none;
        /* Hidden by default */
        position: fixed;
        bottom: 30px;
        right: 30px;
        z-index: 99;
        border: none;
        outline: none;
        background-color: var(--btn-color);
        color: white;
        cursor: pointer;
        padding: 15px;
        border-radius: 50%;
        font-size: 18px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
        transition:
          background-color 0.3s,
          transform 0.3s;
      }
      #backToTopBtn:hover {
        background-color: var(--btn-hover);
        transform: translateY(-5px);
      }
      /* --- EXISTING STYLES --- */ /* FIRST NAVBAR (INDEX PAGE STYLE) */
      .top-nav {
     position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 1000;
    height: 70px;

    display: flex;
    align-items: center;
    justify-content: space-between;

    height: 70px; /* FIXED HEIGHT */
    padding: 0 5%;

    background: var(--brand);
    color: #fff;
}
      .top-nav .logo img {
        height: 50px;
        width: auto;
        display: block;
      }
      .top-nav ul {
        list-style: none;
        display: flex;
        gap: 2rem;
        margin: 0;
        padding: 0;
      }
      .top-nav a {
        color: #f5f5dc;
        text-decoration: none;
        font-weight: bold;
        transition: color 0.3s;
      }
      .top-nav a:hover {
        color: #b37d5c;
      }
     

   .main-title {
    font-size: 22px;
    color: #f5f5dc;
    margin: 0;

}
      /* SECOND NAVBAR (PRODUCT PAGE STYLE) */
     header {
    position: fixed;
    top: 70px;   /* directly below first nav */
    left: 0;
    right: 0;
    z-index: 999;

    display: flex;
    align-items: center;
    justify-content: space-between;

     padding: 6px 18px;
    background: var(--brand-light);
}
      header h1 {
        margin: 0;
        font-size: 20px;
        font-weight: 700;
         color: white;
      }
      header input {
        width: 280px;
        max-width: 45vw;
        padding: 8px 10px;
        border-radius: 8px;
        border: none;
        background: #f5f5dc;
        color: #3e2723;
      }
      .hamburger {
        display: none;
        font-size: 24px;
        background: none;
        border: 0;
        color: #fff;
      }
      .nav-links {
        list-style: none;
        display: flex;
        gap: 22px;
        margin: 0;
        padding: 0;
      }
      .nav-links a {
        color: #f5f5dc;
        text-decoration: none;
        padding: 6px 10px;
        border-radius: 4px;
        transition:
          background 0.2s,
          color 0.2s;
      }
      .nav-links a:hover {
        background: rgba(255, 255, 255, 0.2);
        color: #b37d5c;
      }
      .filters {
        position: fixed;
        top: 120px;
        left: -210px;
        z-index: 10;
        width: 200px;
        height: 100vh;
        padding: 18px;
        background: #f5f5dc;
        transition: left 0.35s ease-in-out;
        box-shadow: 2px 0 8px rgba(0, 0, 0, 0.08);
      }
      .filters:hover {
        left: 0;
      }
      .filters h3 {
        margin: 0 0 12px;
        font-size: 18px;
        color: #3e2723;
      }
      .filter-list {
        list-style: none;
        margin: 0;
        padding: 0;
      }
      .filter-list li {
        display: flex;
        align-items: center;
        gap: 10px;
        margin: 50px 0;
        font-size: 16px;
      }
      .filter-list input[type="radio"] {
        accent-color: var(--brand);
      }
      .main-content {
      margin-top: 60px;  
    padding-left: 24px;
    padding-right: 24px;
    padding-bottom: 60px;
}
      .main-content h2 {
        margin-top: 10px;   /* reduce this */
    margin-bottom: 10px;
        text-align: center;
        font-size: 36px;
        font-weight: 700;
     
        color: #3e2723;
      }
      #related-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: 8px;
        padding: 20px;
        justify-content: center;
        align-items: start;
      }
      .product-card {
        width: 340px;
        background: var(--card-bg);
        border-radius: var(--card-radius);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.18);
        overflow: hidden;
        position: relative;
        transition:
          transform 0.3s ease,
          box-shadow 0.3s ease; /* Hover Effect */
      }
      /* Enhanced Hover Effect for Card */
      .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.25);
      }
      .image-container {
        position: relative;
        height: 400px;
        overflow: hidden;
      }
      .image-container img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 6px;
        transition: transform 0.4s ease;
      }
      .image-container::after {
        content: "";
        position: absolute;
        inset: 0;
        background: rgba(0, 0, 0, 0.5);
        opacity: 0;
        transition: opacity 0.3s;
      }
      .image-container:hover::after {
        opacity: 1;
      }
      .image-container:hover img {
        transform: scale(1.05);
      }
      .quickview-btn {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        font-size: 26px;
        color: white;
        background: none;
        border: none;
        padding: 0;
        opacity: 0;
        transition:
          opacity 0.3s,
          transform 0.3s;
        cursor: pointer;
        z-index: 2;
      }
      .image-container:hover .quickview-btn {
        opacity: 1;
        transform: translate(-50%, -50%) scale(1.1);
      }
      .wishlist-btn {
        position: absolute;
        top: 12px;
        right: 12px;
        z-index: 3;
        background: white;
        border: none;
        border-radius: 6px;
        padding: 4px 6px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
        cursor: pointer;
        transition: transform 0.2s;
      }
      .wishlist-btn i {
        font-size: 18px;
        color: gray;
        transition: color 0.2s;
      }
      .wishlist-btn.active i {
        color: red;
      }
      .wishlist-btn:hover {
        transform: scale(1.1);
      }
      .card-body {
        padding: 14px 16px 16px;
      }
      .card-body h3 {
        font-size: 22px;
        margin: 6px 0 6px;
        color: #3e2723;
      }
      .card-body p {
        margin: 0;
        color: #3e2723;
        font-size: 16px;
        line-height: 1.35;
      }
      .price {
        margin-top: 14px;
        font-size: 18px;
        font-weight: 700;
        color: #3e2723;
      }
      .buy-btn {
        display: inline-block;
        margin-top: 10px;
        padding: 10px 16px;
        background: var(--btn-color);
        color: #fff;
        text-decoration: none;
        border-radius: 8px;
        font-size: 16px;
        transition:
          background 0.2s,
          transform 0.2s; /* Hover Effect */
        border: none;
        cursor: pointer;
      }
      .buy-btn:hover {
        background: var(--btn-hover);
        transform: scale(1.05);
      }
      footer {
        background-color: #3e2723;
        color: white;
        text-align: center;
        padding: 1.5rem;
      }
      #loginPopup{
position: fixed;
top:0;
left:0;
width:100%;
height:100%;
background: rgba(0,0,0,0.6);
display:none;
justify-content:center;
align-items:center;
z-index:999;
}

.popup-box{
background:#f5f5dc;
width:320px;
padding:25px;
border-radius:10px;
text-align:center;
box-shadow:0 8px 20px rgba(0,0,0,0.3);
}

.popup-box h3{
margin-bottom:10px;
color:#3e2723;
}

.popup-box p{
margin-bottom:20px;
color:#5d4037;
}

.popup-buttons{
display:flex;
justify-content:space-between;
}

.popup-btn{
padding:10px 18px;
border:none;
border-radius:6px;
cursor:pointer;
font-size:14px;
}

.ok-btn{
background:#b37d5c;
color:white;
}

.ok-btn:hover{
background:#a06a4a;
}

.popup-cancel{
background:#ccc;
}

.popup-cancel:hover{
background:#aaa;
}
header h1,
.main-content h2 {
    margin: 0;
}
      @media (max-width: 768px) {
        .nav-links {
          display: none;
          position: absolute;
          top: 120px;
          left: 0;
          right: 0;
          background: var(--brand-light);
          padding: 10px 16px;
          flex-direction: column;
          gap: 10px;
        }
        .nav-links.active {
          display: flex;
        }
        .hamburger {
          display: block;
        }
        header input {
          display: none;
        }
      }
    




</style>
</head>
<body>

<!-- FIRST NAVBAR -->
<nav class="top-nav">
  

  <h1 class="main-title">Artisan & Handmade Products</h1>

  <ul>
    <li><a href="01_index.php">Home</a></li>
     <li><a href="19_about_us.php">About Us</a></li>
  </ul>
</nav>

<!-- SECOND NAVBAR -->
<header>
  <h1>Category section</h1>
  <input type="text" placeholder="Search products..." id="searchInput">
  <ul class="nav-links">
    <li><a href="06_art.php">Art & Crafts</a></li>
    <li><a href="07_gifts.php">Gifts & Custom</a></li>
    <li><a href="08_homedecor.php">Home & Decor</a></li>
    <li>
   <?php if($isLoggedIn){ ?>
<li>
<a href="11_wishlist.php">
<i class="fa fa-heart"></i> Wishlist
</a>
</li>
<?php } else { ?>
<li>
<a href="#" class="loginRequired" data-redirect="11_wishlist.php">
<i class="fa fa-heart"></i> Wishlist
</a>
</li>
<?php } ?>
   </li>
    <li><a href="13_cart.php"><i class="fa fa-shopping-cart"></i> Cart</a></li>
  </ul>
</header>
<!-- ADD THE FILTER SIDEBAR HERE -->
<aside class="filters">
    <h3>Filter by Price</h3>
    <ul class="filter-list">
        <li><input type="radio" name="price" value="all" checked> <label>All</label></li>
        <li><input type="radio" name="price" value="under300"> <label>Under ₹300</label></li>
        <li><input type="radio" name="price" value="300to1000"> <label>₹300–₹1000</label></li>
        <li><input type="radio" name="price" value="above1000"> <label>Above ₹1000</label></li>
    </ul>
</aside>


<div class="main-content">
  <h2>Fashion & Accessories Products</h2>
  <div id="related-container">
    <?php while($row = mysqli_fetch_assoc($result)){ ?>
      <div class="product-card"
           data-price="<?php echo $row['price']; ?>"
           data-title="<?php echo strtolower($row['name']); ?>"
           data-desc="<?php echo strtolower($row['description']); ?>">

        <div class="image-container">
          <img src="image/<?php echo $row['image']; ?>" alt="<?php echo $row['name']; ?>">
          <a href="10_product_detail.php?id=<?php echo $row['product_id']; ?>" class="quickview-btn">
            <i class="fas fa-eye"></i>
          </a>

          <!-- WISHLIST -->
          <?php if(!$isLoggedIn): ?>
            <button type="button" class="wishlist-btn loginRequired" data-redirect="<?php echo $_SERVER['REQUEST_URI']; ?>">
              <i class="fa fa-heart"></i>
            </button>
          <?php else: ?>
            <button type="button" class="wishlist-btn <?= in_array($row['product_id'], $wishlistProducts) ? 'active' : '' ?>" data-product-id="<?= $row['product_id'] ?>">
              <i class="fa fa-heart"></i>
            </button>
          <?php endif; ?>
        </div>

        <div class="card-body">
          <h3><?php echo $row['name']; ?></h3>
          <p><?php echo $row['description']; ?></p>
          <div class="price">₹<?php echo $row['price']; ?></div>

          <!-- ADD TO CART -->
          <?php if(!$isLoggedIn): ?>
            <button class="buy-btn loginRequired" data-redirect="<?php echo $_SERVER['REQUEST_URI']; ?>">Add to Cart</button>
          <?php else: ?>
            <form method="POST" action="13_cart.php">
              <input type="hidden" name="product_id" value="<?php echo $row['product_id']; ?>">
              <input type="hidden" name="quantity" value="1">
              <button type="submit" class="buy-btn">Add to Cart</button>
            </form>
          <?php endif; ?>
        </div>
      </div>
    <?php } ?>
  </div>

  <p id="noProductMsg" style="display:none;text-align:center;font-size:18px;margin-top:20px;">
    No related products found.
  </p>
</div>

<!-- LOGIN POPUP -->
<div id="loginPopup">
  <div class="popup-box">
    <h3>Login Required</h3>
    <p>Please login to continue.</p>
    <div class="popup-buttons">
      <button class="popup-btn ok-btn">OK</button>
      <button class="popup-btn popup-cancel" onclick="closePopup()">Cancel</button>
    </div>
  </div>
</div>

<footer>
  Artisan & Homemade Products
</footer>

<script>
// SEARCH & FILTER
const searchInput = document.getElementById("searchInput");
const cards = document.querySelectorAll(".product-card");
const priceFilters = document.querySelectorAll("input[name='price']");

function filterProducts(){
  let searchText = searchInput.value.toLowerCase();
  let selectedPrice = document.querySelector("input[name='price']:checked")?.value || "all";
  let visibleCount = 0;
  cards.forEach(card=>{
    let title = card.dataset.title;
    let desc = card.dataset.desc;
    let price = parseInt(card.dataset.price);
    let matchSearch = title.includes(searchText) || desc.includes(searchText);
    let matchPrice = true;
    if(selectedPrice==="under300") matchPrice=price<300;
    else if(selectedPrice==="300to1000") matchPrice=price>=300 && price<=1000;
    else if(selectedPrice==="above1000") matchPrice=price>1000;
    if(matchSearch && matchPrice){card.style.display="block";visibleCount++;}
    else{card.style.display="none";}
  });
  document.getElementById("noProductMsg").style.display = visibleCount===0?"block":"none";
}
searchInput.addEventListener("keyup",filterProducts);
priceFilters.forEach(radio=>{radio.addEventListener("change",filterProducts);});

// LOGIN POPUP & REDIRECT
const loginPopup = document.getElementById("loginPopup");
let redirectUrl = window.location.href;

document.querySelectorAll(".loginRequired").forEach(btn=>{
  btn.addEventListener("click", function(e){
    e.preventDefault();   // stop link redirect
    e.stopPropagation();  // stop double trigger
    redirectUrl = btn.dataset.redirect || window.location.href;
    loginPopup.style.display = "flex";
  });
});
function closePopup(){ loginPopup.style.display = "none"; }

document.querySelector(".popup-btn.ok-btn").addEventListener("click", function(){
  window.location.href = "03_login.php?redirect=" + encodeURIComponent(redirectUrl);
});

// --- WISHLIST TOGGLE ---
document.querySelectorAll(".wishlist-btn").forEach(btn => {

    btn.addEventListener("click", function(e){

        // If button requires login → stop AJAX
        if(btn.classList.contains("loginRequired")){
            return;
        }

        const productId = btn.dataset.productId;

        fetch("12_wishlist_ajax.php", {
            method: "POST",
            headers: {"Content-Type":"application/x-www-form-urlencoded"},
            body: "product_id=" + encodeURIComponent(productId)
        })
        .then(res => res.json())
        .then(data => {

            if(data.status === "added"){
                btn.classList.add("active");
            }

            else if(data.status === "removed"){
                btn.classList.remove("active");
            }

        })
        .catch(err => console.error("Wishlist error:", err));

    });

});
</script>

</body>
</html>