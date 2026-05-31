<?php
session_start();
include("db_connect.php");

// 🔒 Protect page
if(!isset($_SESSION['admin'])){
    header("Location: admin_login.php");
    exit();
}

// Fetch all users
$result = mysqli_query($conn, "SELECT id, username, phone FROM users");
?>

<!DOCTYPE html>
<html>
<head>
<title>Manage Users</title>

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

/* TABLE */
.container{
    width:80%;
    margin:50px auto;
}

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

.back-btn{
    display:inline-block;
    margin:20px;
    padding:10px 15px;
    background:#b37d5c;
    color:white;
    text-decoration:none;
    border-radius:6px;
}
</style>

</head>

<body>

<div class="nav">
    Manage Users
</div>

<a href="admin_dashboard.php" class="back-btn">⬅ Back</a>

<div class="container">

<table>
<tr>
    <th>ID</th>
    <th>Name</th>
    <th>Phone</th>
</tr>

<?php while($row = mysqli_fetch_assoc($result)): ?>
<tr>
    <td><?php echo $row['id']; ?></td>
    <td><?php echo $row['username']; ?></td>
    <td><?php echo $row['phone']; ?></td>
</tr>
<?php endwhile; ?>

</table>

</div>

</body>
</html>