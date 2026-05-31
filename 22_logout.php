<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $_SESSION = [];
    session_destroy();

    session_start(); // restart to store message
    $_SESSION['logout_msg'] = "You have been logged out successfully.";

    header("Location:01_index.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<title>Logout</title>

<style>
:root{
--brand:#3e2723;
--bg:#ebebc6;
--card:#f5f5dc;
--btn:#b37d5c;
--btn-hover:#a06a4a;
}

body{
margin:0;
font-family:Arial;
background:var(--bg);
}

.container{
max-width:450px;
margin:120px auto;
background:var(--card);
padding:30px;
border-radius:12px;
text-align:center;
box-shadow:0 5px 15px rgba(0,0,0,0.2);
}

h2{
color:var(--brand);
}

.btn{
padding:10px 20px;
margin:10px;
border:none;
border-radius:6px;
cursor:pointer;
font-size:15px;
}

.btn-yes{
background:var(--brand);
color:white;
}

.btn-yes:hover{
background:var(--btn);
}

.btn-no{
background:#ccc;
}

.btn-no:hover{
background:#aaa;
}
</style>

</head>

<body>

<?php include("navbar.php"); ?>

<div class="container">

<h2>Are you sure you want to logout?</h2>

<form method="POST">
    <button type="submit" class="btn btn-yes">Yes, Logout</button>
    <button type="button" class="btn btn-no" onclick="window.location.href='01_index.php'">No, Go Back</button>
</form>

</div>

</body>
</html>