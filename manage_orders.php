<?php
session_start();
include("db_connect.php");

// 🔒 Admin protection
if(!isset($_SESSION['admin'])){
    header("Location: admin_login.php");
    exit();
}

/* FETCH ORDERS WITH USER NAME */
$query = "
SELECT o.*, u.username 
FROM orders o
JOIN users u ON o.user_id = u.id
ORDER BY o.order_id DESC
";

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html>
<head>
<title>Manage Orders</title>
<style>
body{ font-family:Arial; background:#ebebc6; margin:0; }
.nav{ background:#3e2723; color:white; padding:20px; text-align:center; font-size:22px; }
.container{ width:95%; margin:40px auto; }
table{ width:100%; border-collapse:collapse; background:#f5f5dc; box-shadow:0 5px 15px rgba(0,0,0,0.2); }
th, td{ padding:12px; border:1px solid #ccc; text-align:center; }
th{ background:#3e2723; color:white; }
tr:hover{ background:#fffaf0; }
/* STATUS COLORS */
.pending{ color:orange; font-weight:bold; }
.shipped{ color:blue; font-weight:bold; }
.delivered{ color:green; font-weight:bold; }
.cancelled{ color:red; font-weight:bold; }
/* BUTTONS */
.btn{ padding:6px 10px; border:none; border-radius:6px; cursor:pointer; text-decoration:none; color:white; font-size:14px; }
.view-btn{ background:#2196f3; }
.bill-btn{ background:#4caf50; }
.back-btn{ background:#b37d5c; margin-bottom:15px; display:inline-block; }
</style>
</head>
<body>

<div class="nav">All Orders</div>
<div class="container">
<a href="admin_dashboard.php" class="btn back-btn">⬅ Back</a>

<table>
<tr>
    <th>Order ID</th>
    <th>User</th>
    <th>Address</th>
    <th>City</th>
    <th>Pincode</th>
    <th>Total</th>
    <th>Status</th>
    <th>Date</th>
    <th>Action</th>
</tr>

<?php while($row = mysqli_fetch_assoc($result)): ?>
<tr>
    <td><?php echo $row['order_id']; ?></td>
    <td><?php echo $row['username']; ?></td>
    <td><?php echo $row['address']; ?></td>
    <td><?php echo $row['city']; ?></td>
    <td><?php echo $row['pincode']; ?></td>
    <td>₹<?php echo $row['total_price']; ?></td>
    <td class="<?php echo strtolower($row['order_status']); ?>">
        <?php echo $row['order_status']; ?>
    </td>
    <td><?php echo $row['order_date']; ?></td>
    <td>
        <a href="order_details.php?id=<?php echo $row['order_id']; ?>" class="btn view-btn">View</a>
        <a href="download_bill.php?order_id=<?php echo $row['order_id']; ?>" class="btn bill-btn">Download Bill</a>
    </td>
</tr>
<?php endwhile; ?>

</table>
</div>
</body>
</html>