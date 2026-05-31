<?php
session_start();
include("db_connect.php");

// 🔒 Protect admin
if(!isset($_SESSION['admin'])){
    header("Location: admin_login.php");
    exit();
}

// ❌ No ID
if(!isset($_GET['id'])){
    echo "Product ID missing!";
    exit();
}

$product_id = intval($_GET['id']);

/* FETCH PRODUCT DATA */
$stmt = $conn->prepare("SELECT * FROM products WHERE product_id=?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

if(!$product){
    echo "Product not found!";
    exit();
}

/* UPDATE PRODUCT */
if(isset($_POST['update'])){

    $name = $_POST['name'];
    $description = $_POST['description'];
    $long_description = $_POST['long_description'];
    $price = $_POST['price'];
    $category = $_POST['category'];

    // IMAGE HANDLING
    if(!empty($_FILES['image']['name'])){
        $image = $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], "image/".$image);
    } else {
        $image = $product['image']; // keep old image
    }

    // UPDATE QUERY
    $update = $conn->prepare("
        UPDATE products 
        SET name=?, description=?, long_description=?, price=?, image=?, category=? 
        WHERE product_id=?
    ");

    $update->bind_param("sssdssi", $name, $description, $long_description, $price, $image, $category, $product_id);
    $update->execute();

    // redirect back
    header("Location: manage_products.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Edit Product</title>

<style>
body{
    font-family:Arial;
    background:#ebebc6;
    margin:0;
}

.nav{
    background:#3e2723;
    color:white;
    padding:20px;
    text-align:center;
    font-size:22px;
}

.container{
    width:500px;
    margin:40px auto;
    background:#f5f5dc;
    padding:30px;
    border-radius:10px;
    box-shadow:0 5px 15px rgba(0,0,0,0.2);
}

input, textarea{
    width:100%;
    padding:10px;
    margin:10px 0;
    border-radius:6px;
    border:1px solid #ccc;
}

button{
    width:100%;
    padding:12px;
    background:#3e2723;
    color:white;
    border:none;
    border-radius:6px;
    cursor:pointer;
}

button:hover{
    background:#5d4037;
}

.back-btn{
    display:block;
    margin-bottom:10px;
    text-decoration:none;
    color:white;
    background:#b37d5c;
    padding:8px;
    text-align:center;
    border-radius:6px;
}
</style>

</head>

<body>

<div class="nav">Edit Product</div>

<div class="container">

<a href="manage_products.php" class="back-btn">⬅ Back</a>

<form method="POST" enctype="multipart/form-data">

<input type="text" name="name" value="<?php echo $product['name']; ?>" required>

<textarea name="description"><?php echo $product['description']; ?></textarea>

<textarea name="long_description"><?php echo $product['long_description']; ?></textarea>

<input type="number" name="price" value="<?php echo $product['price']; ?>" required>

<input type="text" name="category" value="<?php echo $product['category']; ?>" required>

<p>Current Image:</p>
<img src="image/<?php echo $product['image']; ?>" width="100">

<input type="file" name="image">

<button type="submit" name="update">Update Product</button>

</form>

</div>

</body>
</html>