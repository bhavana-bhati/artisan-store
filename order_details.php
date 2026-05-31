<?php
session_start();
include("db_connect.php");

if(!isset($_SESSION['admin'])){
    header("Location: admin_login.php");
    exit();
}

if(!isset($_GET['id'])){
    echo "Order not found!";
    exit();
}

$order_id = intval($_GET['id']);

/* FETCH ORDER */
$orderQuery = $conn->prepare("SELECT * FROM orders WHERE order_id=?");
$orderQuery->bind_param("i", $order_id);
$orderQuery->execute();
$order = $orderQuery->get_result()->fetch_assoc();

if(!$order){
    echo "Order not found!";
    exit();
}

/* FETCH ORDER ITEMS */
$itemQuery = $conn->prepare("
SELECT oi.*, p.name, p.image 
FROM order_items oi
JOIN products p ON oi.product_id = p.product_id
WHERE oi.order_id=?
");
$itemQuery->bind_param("i", $order_id);
$itemQuery->execute();
$items = $itemQuery->get_result();
?>

<!DOCTYPE html>
<html>
<head>
<title>Order Details</title>

<style>
body{
    font-family:Arial;
    background:#ebebc6;
}

.container{
    width:80%;
    margin:40px auto;
    background:#f5f5dc;
    padding:25px;
    border-radius:10px;
}

h2{
    text-align:center;
}

.product{
    display:flex;
    align-items:center;
    gap:15px;
    margin:15px 0;
}

.product img{
    width:70px;
    height:70px;
    border-radius:6px;
}

.btn{
    padding:10px 15px;
    border:none;
    border-radius:6px;
    cursor:pointer;
    color:white;
}

.deliver{ background:green; }
.back{ background:#b37d5c; }
</style>

</head>

<body>

<div class="container">

<h2>Order #<?php echo $order['order_id']; ?></h2>

<p><strong>Name:</strong> <?php echo $order['name']; ?></p>
<p><strong>Phone:</strong> <?php echo $order['phone']; ?></p>
<p><strong>Address:</strong> <?php echo $order['address']; ?></p>
<p><strong>City:</strong> <?php echo $order['city']; ?></p>
<p><strong>Pincode:</strong> <?php echo $order['pincode']; ?></p>

<p><strong>Status:</strong> <?php echo $order['order_status']; ?></p>
<p><strong>Total:</strong> ₹<?php echo $order['total_price']; ?></p>

<hr>

<h3>Products</h3>

<?php while($item = $items->fetch_assoc()): ?>
<div class="product">
    <img src="image/<?php echo $item['image']; ?>">
    <div>
        <?php echo $item['name']; ?><br>
        Qty: <?php echo $item['quantity']; ?><br>
        Price: ₹<?php echo $item['price']; ?>
    </div>
</div>
<?php endwhile; ?>

<a href="download_bill.php?order_id=<?php echo $order_id; ?>" class="btn" style="background:#2196f3;">
    Download Bill
</a>

<br><br>

<!-- ✅ ONLY FOR PENDING -->
<?php if($order['order_status'] == 'Pending'): ?>
<form method="POST" action="update_order_status.php">
    <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
    <button class="btn deliver">Mark as Delivered</button>
</form>
<?php endif; ?>

<a href="manage_orders.php" class="btn back">⬅ Back</a>

</div>

</body>
</html>