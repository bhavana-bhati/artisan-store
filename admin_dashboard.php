<?php
session_start();

// 🔒 Protect page
if(!isset($_SESSION['admin'])){
    header("Location: admin_login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Admin Dashboard</title>

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
    width:80%;
    margin:100px auto;
    display:flex;
    justify-content:center;
    gap:30px;
    flex-wrap:wrap;
}

/* CARDS */
.card{
    background:#f5f5dc;
    padding:30px;
    border-radius:12px;
    width:220px;
    text-align:center;
    cursor:pointer;
    box-shadow:0 5px 15px rgba(0,0,0,0.2);
    transition:0.3s;
}

.card:hover{
    transform:scale(1.05);
    background:#fffaf0;
}
</style>

</head>

<body>

<div class="nav">
    Admin Dashboard
</div>

<div class="container">

<div class="card" onclick="location.href='manage_users.php'">
    👤 Manage Users
</div>

<div class="card" onclick="location.href='manage_products.php'">
    📦 Manage Products
</div>

<div class="card" onclick="location.href='manage_orders.php'">
    🧾 Manage Orders
</div>

<div class="card" onclick="location.href='admin_logout.php'">
    🚪 Logout
</div>

</div>

</body>
</html>