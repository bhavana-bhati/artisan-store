<?php
session_start();
session_unset();
session_destroy();
?>

<!DOCTYPE html>
<html>
<head>
<title>Logout</title>

<style>
body{
    margin:0;
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
    text-align:center;
    box-shadow:0 5px 15px rgba(0,0,0,0.2);
}

a{
    display:inline-block;
    margin-top:15px;
    padding:10px 20px;
    background:#3e2723;
    color:white;
    text-decoration:none;
    border-radius:6px;
}
</style>

<!-- AUTO REDIRECT AFTER 2 SEC -->
<meta http-equiv="refresh" content="2;url=01_index.php">

</head>

<body>

<div class="box">
    <h2>Logged Out Successfully</h2>
    <p>Redirecting to login...</p>
    <a href="01_index.php">Go to Login</a>
</div>

</body>
</html>