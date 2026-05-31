<?php
session_start();
include("db_connect.php");

/* ❌ STOP NORMAL USERS */
if(!isset($_SESSION['admin'])){
    header("Location: admin_login.php");
    exit();
}

/* HANDLE FORM */
if($_SERVER['REQUEST_METHOD'] == 'POST'){

    $name = $_POST['name'];
    $description = $_POST['description'];
    $long_description = $_POST['long_description'];
    $price = $_POST['price'];
    $image = $_POST['image'];
    $image1 = $_POST['image1'];
    $category = $_POST['category'];

    $stmt = $conn->prepare("INSERT INTO products (name, description, long_description, price, image, image1, category) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssdsss", $name, $description, $long_description, $price, $image, $image1, $category);

    if($stmt->execute()){
        $success = "✅ Product added successfully!";
    } else {
        $error = "❌ Failed to add product!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Add Product</title>

<style>
body{
    font-family:Arial;
    background:#ebebc6;
    margin:0;
}

/* CONTAINER */
.container{
    width:600px;
    margin:100px auto;
    background:#f5f5dc;
    padding:30px;
    border-radius:15px;
    box-shadow:0 5px 15px rgba(0,0,0,0.2);
}

h2{
    text-align:center;
    color:#3e2723;
}

/* INPUTS */
input, textarea, select{
    width:100%;
    padding:10px;
    margin:10px 0;
    border-radius:8px;
    border:1px solid #aaa;
}

textarea{
    height:100px;
}

/* BUTTON */
button{
    width:100%;
    padding:12px;
    background:#3e2723;
    color:white;
    border:none;
    border-radius:8px;
    cursor:pointer;
}

button:hover{
    background:#5d4037;
}

/* MSG */
.success{
    background:#d4edda;
    color:#155724;
    padding:10px;
    margin-bottom:10px;
    border-radius:6px;
    text-align:center;
}

.error{
    background:#f8d7da;
    color:#721c24;
    padding:10px;
    margin-bottom:10px;
    border-radius:6px;
    text-align:center;
}
</style>

</head>

<body>

<div class="container">

<h2>Add Product</h2>

<?php if(isset($success)) echo "<div class='success'>$success</div>"; ?>
<?php if(isset($error)) echo "<div class='error'>$error</div>"; ?>

<form method="POST">

<input type="text" name="name" placeholder="Product Name" required>

<input type="text" name="description" placeholder="Short Description" required>

<textarea name="long_description" placeholder="Long Description"></textarea>

<input type="number" step="0.01" name="price" placeholder="Price" required>

<input type="text" name="image" placeholder="Main Image (e.g. p1.jpg)" required>

<input type="text" name="image1" placeholder="Second Image (optional)">

<select name="category" required>
    <option value="">Select Category</option>
    <option value="fashion">Fashion</option>
    <option value="art">Art</option>
    <option value="gifts">Gifts</option>
    <option value="homedecor">Home Decor</option>
</select>

<button type="submit">Add Product</button>

</form>

</div>

</body>
</html>