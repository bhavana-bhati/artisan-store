
<?php
session_start();
include("db_connect.php");

if (!isset($_GET['id'])) {
    echo "❌ Product not found! (missing ID)";
    exit;
}

$product_id = intval($_GET['id']);

// Fetch main product
$stmt = $conn->prepare("SELECT * FROM products WHERE product_id = ?");

if(!$stmt){
    die("SQL Error (Product Fetch): " . $conn->error);
}

$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

if (!$product) {
    echo "❌ Product not found in database!";
    exit;
}

// Wishlist check for main product
$in_wishlist = false;
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $wishStmt = $conn->prepare("SELECT 1 FROM wishlist WHERE user_id = ? AND product_id = ?");

if(!$wishStmt){
    die("SQL Error (Wishlist): " . $conn->error);
}
    $wishStmt->bind_param("ii", $user_id, $product_id);
    $wishStmt->execute();
    $in_wishlist = $wishStmt->get_result()->num_rows > 0;
}

// Fetch related products
$relatedStmt = $conn->prepare("SELECT * FROM products WHERE category = ? AND product_id != ? LIMIT 4");

if(!$relatedStmt){
    die("SQL Error (Related): " . $conn->error);
}
$relatedStmt->bind_param("si", $product['category'], $product_id);
$relatedStmt->execute();
$related_result = $relatedStmt->get_result();
$related_products = [];
while ($rel = $related_result->fetch_assoc()) $related_products[] = $rel;

// Wishlist check for related products
$related_wishlist = [];
if (isset($_SESSION['user_id']) && count($related_products) > 0) {
    foreach ($related_products as $rel) {
        $stmt = $conn->prepare("SELECT 1 FROM wishlist WHERE user_id=? AND product_id=?");
        $stmt->bind_param("ii", $_SESSION['user_id'], $rel['product_id']);
        $stmt->execute();
        if ($stmt->get_result()->num_rows) $related_wishlist[$rel['product_id']] = true;
    }
}

// Fetch reviews if table exists
$reviews = [];

if ($conn->query("SHOW TABLES LIKE 'reviews'")->num_rows) {
$revStmt = $conn->prepare("
SELECT r.*, u.username 
FROM reviews r
JOIN users u ON r.user_id = u.id
WHERE r.product_id = ?
ORDER BY r.created_at DESC
LIMIT 5
");

    if(!$revStmt){
        die("SQL Error (Reviews): " . $conn->error);
    }

    $revStmt->bind_param("i", $product_id);
    $revStmt->execute();
    $reviews = $revStmt->get_result();
}
// Prepare images
$images = [];
for ($i = 0; $i <= 5; $i++) {
    $field = $i === 0 ? 'image' : 'image'.$i;
    if (!empty($product[$field])) $images[] = $product[$field];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title><?php echo $product['name']; ?> - Product Details</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<style>
body { font-family: Arial,sans-serif; margin:0; background:#ebebc6; color:#3e2723;}
.container { max-width:1100px; margin:80px auto 30px; padding:20px; background:#fff; border-radius:12px; box-shadow:0 4px 10px rgba(0,0,0,0.1);}
.product-detail { display:flex; gap:30px; flex-wrap:wrap;}
.image-box { position:relative; width:420px;}
.image-box img { width:100%; border-radius:12px; object-fit:cover;}
.wishlist-btn-main { position:absolute; top:15px; right:15px; font-size:26px; color:gray; cursor:pointer;}
.wishlist-btn-main.active { color:red; }
.image-arrow { position:absolute; top:50%; transform:translateY(-50%); font-size:40px; color:olive; cursor:pointer; user-select:none; background:rgba(255,255,255,0.7); border-radius:50%; width:50px; height:50px; display:flex; align-items:center; justify-content:center;}
.arrow-left { left:-25px; }
.arrow-right { right:-25px; }
.info { flex:1; display:flex; flex-direction:column; gap:12px;}
.info h2 { margin:0; font-size:28px;}
.short-desc { font-size:15px; color:#555; line-height:1.5;}
.price { font-size:22px; color:green; font-weight:bold;}
.qty { display:flex; align-items:center; gap:10px;}
.qty button { padding:6px 12px; font-size:18px; background:#eee; border:none; border-radius:6px; cursor:pointer; color:olive; font-weight:bold;}
.qty button:hover { background:#ddd;}
.qty input { width:60px; text-align:center; padding:5px; font-size:16px;}
.subtotal { margin:10px 0; font-weight:bold;}
button { padding:10px 20px; background:olive; color:#fff; border:none; border-radius:6px; cursor:pointer;}
button:hover { background:darkolivegreen;}
.btn-row { display:flex; gap:15px; flex-wrap:wrap;}
.separator { border-top:1px solid #ddd; margin:25px 0;}
.long-desc, .reviews, .related { margin-top:20px;}
.description { font-size:16px; color:#444; max-height:70px; overflow:hidden; transition:max-height 0.3s ease; line-height:1.6;}
.description.expanded { max-height:500px; }
.toggle-btn { color:olive; cursor:pointer; font-weight:bold; }
.related-products { display:grid; grid-template-columns:repeat(auto-fit,minmax(260px,1fr)); gap:20px; }
.card { background:#fff; border-radius:12px; box-shadow:0 2px 6px rgba(0,0,0,0.1); padding:15px; text-align:center; transition:transform 0.2s; min-height:500px; position:relative; overflow:hidden;}
.card .wishlist-btn { position:absolute; top:18px; right:20px; font-size:20px; color:white; background-color:white; background:25px; border-radius:25px; cursor:pointer; z-index:2;}
.card .wishlist-btn.active { color:red; }
.card .quickview { position:absolute; top:25%; left:50%; transform:translate(-50%,-50%); font-size:28px; color:white; border-radius:50%; padding:10px; opacity:0; transition:opacity 0.3s; text-decoration:none; z-index:2;}
.card:hover .quickview { opacity:1;}
.card img { width:100%; height:350px; object-fit:cover; border-radius:8px;}
.card p { margin:10px 0 5px; font-weight:bold;}
.card span { color:green; font-size:16px; }
.thumbs{margin-top:10px; display:flex; gap:10px; flex-wrap:wrap;}
.thumbs img{width:80px; height:80px; object-fit:cover; border-radius:6px; cursor:pointer; border:2px solid #ddd;}
.thumbs img:hover, .thumbs img.selected{border:2px solid olive;}
#toast { position:fixed; bottom:30px; right:30px; background:pink; color:#333; padding:12px 20px; border-radius:8px; box-shadow:0 2px 8px rgba(0,0,0,0.2); display:none; z-index:9999;}
#loginPopup { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); display: none; justify-content: center; align-items: center; z-index: 9999; padding: 15px;}
.popup-box { background: #f5f5dc; width: 320px; max-width: 100%; padding: 25px; border-radius: 10px; text-align:
 center; box-shadow: 0 8px 20px rgba(0,0,0,0.3); position: relative; animation: popupFade 0.3s ease;}
 .review-box p{
margin:5px 0;
line-height:1.4;
}
.review-box{
border-bottom:1px solid #ddd;
padding:10px 0;
}
@keyframes popupFade { from { transform: scale(0.8); opacity: 0; } to { transform: scale(1); opacity: 1; }}
.popup-box h3 { margin-bottom: 10px; color: #3e2723; }
.popup-box p { margin-bottom: 20px; color: #5d4037; }
.popup-buttons { display: flex; justify-content: space-between; }
.popup-btn { padding: 10px 18px; border: none; border-radius: 6px; cursor: pointer; font-size: 14px; }
.ok-btn { background: #b37d5c; color: white; }
.ok-btn:hover { background: #a06a4a; }
.popup-cancel { background: #ccc; }
.popup-cancel:hover { background: #aaa; }
</style>
</head>

<body>

<div class="container">
<div class="product-detail">

<div class="image-box">

<img id="mainImage" src="image/<?php echo $images[0]; ?>">

<i class="fa fa-heart wishlist-btn-main <?php echo $in_wishlist ? 'active' : ''; ?>" data-id="<?php echo $product['product_id']; ?>"></i>

<div class="thumbs">
<?php foreach($images as $img): ?>
<img src="image/<?php echo $img; ?>" onclick="setImage(this)">
<?php endforeach; ?>
</div>

</div>

<div class="info">

<h2><?php echo $product['name']; ?></h2>

<p class="short-desc"><?php echo $product['description']; ?></p>

<div class="price">₹<?php echo $product['price']; ?></div>

<div class="qty">
<label>Qty:</label>
<button onclick="changeQty(-1)">−</button>
<input type="number" id="qty" value="1" min="1" readonly>
<button onclick="changeQty(1)">+</button>
</div>

<div class="subtotal">Subtotal: ₹<span id="subtotal"><?php echo $product['price']; ?></span></div>

<div class="btn-row">

<form method="POST" action="13_cart.php">
<input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
<input type="hidden" name="quantity" id="hiddenQty" value="1">
<button type="submit">Add to Cart</button>
</form>
<!-- BUY NOW / CHECKOUT -->
<form action="15_checkout.php" method="POST">
<input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
<input type="hidden" name="price" value="<?php echo $product['price']; ?>">
<input type="hidden" name="quantity" id="checkoutQty" value="1">
<button type="submit">Buy Now</button>
</form>

</div>

</div>
</div>

<div class="separator"></div>

<div class="long-desc">
<h3>Product Details</h3>
<p class="description" id="desc"><?php echo nl2br($product['long_description']); ?></p>
<span class="toggle-btn" onclick="toggleDesc()">Read More</span>
</div>

<div class="separator"></div>

<div class="separator"></div>

<div class="reviews">
<h3>Customer Reviews</h3>

<?php if ($reviews && $reviews->num_rows > 0): ?>

<?php while ($rev = $reviews->fetch_assoc()): ?>

<div class="review-box">

    <strong><?php echo htmlspecialchars($rev['username'] ?? 'User'); ?></strong><br>

    <?php if(!empty($rev['review'])): ?>
        <p><?php echo htmlspecialchars($rev['review']); ?></p>
    <?php else: ?>
        <p style="color:gray;">No written review</p>
    <?php endif; ?>

    <div>⭐ <?php echo $rev['rating']; ?>/5</div>

</div>

<?php endwhile; ?>

<?php else: ?>
<p>No reviews yet.</p>
<?php endif; ?>

</div>
<div class="separator"></div>

<div class="related">
<h3>Related Products</h3>
<div class="related-products">
<?php foreach ($related_products as $rel): ?>
<div class="card">
<img src="image/<?php echo basename($rel['image']); ?>">
<i class="fa fa-heart wishlist-btn <?php echo isset($related_wishlist[$rel['product_id']]) ? 'active' : ''; ?>" data-id="<?php echo $rel['product_id']; ?>"></i>
<a href="10_product_detail.php?id=<?php echo $rel['product_id']; ?>" class="quickview"><i class="fa fa-eye"></i></a>
<p><?php echo $rel['name']; ?></p>
<span>₹<?php echo $rel['price']; ?></span>
</div>
<?php endforeach; ?>
</div>
</div>
</div>

<div id="toast"></div>

<div id="loginPopup">
<div class="popup-box">
<h3>Login Required</h3>
<p>You need to register to use this feature.</p>
<div class="popup-buttons">
<button class="popup-btn ok-btn">Login</button>
<button class="popup-btn popup-cancel" onclick="document.getElementById('loginPopup').style.display='none'">Cancel</button>
</div>
</div>
</div>

<script>

let images = <?php echo json_encode($images); ?>;
let currentIndex = 0;

function setImage(el){
document.getElementById('mainImage').src = el.src;
}

function changeQty(val){
    let qtyInput = document.getElementById("qty");
    let qty = parseInt(qtyInput.value) + val;

    if (qty < 1) qty = 1;

    qtyInput.value = qty;

    let price = <?php echo $product['price']; ?>;
    document.getElementById("subtotal").innerText = qty * price;

    // ✅ UPDATE BOTH INPUTS
    document.getElementById("hiddenQty").value = qty;
    document.getElementById("checkoutQty").value = qty;
}

function toggleDesc(){
let desc=document.getElementById("desc");
let btn=document.querySelector(".toggle-btn");
desc.classList.toggle("expanded");
btn.innerText = desc.classList.contains("expanded") ? "Read Less" : "Read More";
}

function showToast(msg){
let toast=document.getElementById("toast");
toast.innerText=msg;
toast.style.display="block";
setTimeout(()=>{toast.style.display="none"},2000);
}

/* WISHLIST AJAX */

$(".wishlist-btn-main, .wishlist-btn").click(function(){

let btn=$(this)
let productId=btn.data("id")

$.post("12_wishlist_ajax.php",{product_id:productId},function(res){

if(res.status==="added"){
btn.addClass("active")
showToast("Added to wishlist ❤️")
}

if(res.status==="removed"){
btn.removeClass("active")
showToast("Removed from wishlist")
}

},"json")

})

</script>

</body>
</html>

