<?php
session_start();

$error = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){

    $username = $_POST['username'];
    $password = $_POST['password'];

    // 🔒 Simple hardcoded admin (for now)
    if($username === "admin" && $password === "admin123"){
        $_SESSION['admin'] = true;
        header("Location: admin_dashboard.php");
        exit();
    } else {
        $error = "Invalid admin credentials!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Admin Login</title>

<style>
body{
    font-family:Arial;
    background:#ebebc6;
    display:flex;
    justify-content:center;
    align-items:center;
    height:100vh;
}

.box{
    background:#f5f5dc;
    padding:30px;
    border-radius:10px;
    width:300px;
    box-shadow:0 5px 15px rgba(0,0,0,0.2);
}

input{
    width:100%;
    padding:10px;
    margin:10px 0;
}

button{
    width:100%;
    padding:10px;
    background:#3e2723;
    color:white;
    border:none;
}
.error{
    color:red;
    text-align:center;
}
</style>

</head>
<body>

<div class="box">
<h2>Admin Login</h2>

<div class="error"><?php echo $error; ?></div>

<form method="POST">
<input type="text" name="username" placeholder="Username" required>
<input type="password" name="password" placeholder="Password" required>

<button type="submit">Login</button>
</form>

</div>

</body>
</html>