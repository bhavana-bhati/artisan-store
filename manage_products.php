<?php
session_start();
include("db_connect.php");

// 🔒 Protect page
if(!isset($_SESSION['admin'])){
    header("Location: admin_login.php");
    exit();
}

/* DELETE PRODUCT */
if(isset($_GET['delete'])){
    $id = intval($_GET['delete']);

    $delete = $conn->prepare("DELETE FROM products WHERE product_id=?");
    $delete->bind_param("i", $id);
    $delete->execute();

    header("Location: manage_products.php");
    exit();
}

// Fetch products
$result = mysqli_query($conn, "SELECT * FROM products ORDER BY product_id DESC");
?>

<!DOCTYPE html>
<html>
<head>
<title>Manage Products</title>

<style>
body{
    font-family:Arial;
    background:#ebebc6;
    margin:0;
}

/* NAV */
.nav{
    background:#3e2723;
    color:white;
    padding:20px;
    text-align:center;
    font-size:22px;
}

/* CONTAINER */
.container{
    width:90%;
    margin:40px auto;
}

/* TABLE */
table{
    width:100%;
    border-collapse:collapse;
    background:#f5f5dc;
    box-shadow:0 5px 15px rgba(0,0,0,0.2);
}

th, td{
    padding:12px;
    border:1px solid #ccc;
    text-align:center;
}

th{
    background:#3e2723;
    color:white;
}

tr:hover{
    background:#fffaf0;
}

/* IMAGE */
img{
    border-radius:6px;
}

/* BUTTONS */
.btn{
    padding:6px 10px;
    border:none;
    border-radius:6px;
    cursor:pointer;
    text-decoration:none;
    color:white;
    font-size:14px;
}

.add-btn{ background:green; }
.back-btn{ background:#b37d5c; margin-bottom:15px; display:inline-block; }
.edit-btn{ background:#2196f3; }
.delete-btn{ background:red; }

.action-btns{
    display:flex;
    gap:8px;
    justify-content:center;
}
</style>

</head>

<body>

<div class="nav">Manage Products</div>

<div class="container">

<a href="admin_dashboard.php" class="btn back-btn">⬅ Back</a>
<a href="add_product.php" class="btn add-btn">+ Add Product</a>

<table>
<tr>
    <th>ID</th>
    <th>Name</th>
    <th>Price</th>
    <th>Image</th>
    <th>Actions</th>
</tr>

<?php while($row = mysqli_fetch_assoc($result)): ?>
<tr>
    <td><?php echo $row['product_id']; ?></td>
    <td><?php echo $row['name']; ?></td>
    <td>₹<?php echo $row['price']; ?></td>
    <td>
        <img src="image/<?php echo $row['image']; ?>" width="60">
    </td>

    <td>
        <div class="action-btns">

            <!-- EDIT -->
            <a href="edit_product.php?id=<?php echo $row['product_id']; ?>" 
               class="btn edit-btn">Edit</a>

            <!-- DELETE -->
            <a href="manage_products.php?delete=<?php echo $row['product_id']; ?>" 
               class="btn delete-btn"
               onclick="return confirm('Are you sure to delete this product?')">
               Delete
            </a>

        </div>
    </td>
</tr>
<?php endwhile; ?>

</table>

</div>

</body>
</html>