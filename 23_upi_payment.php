<?php
session_start();

if(!isset($_SESSION['temp_order'])){
    die("Invalid access!");
}

$data = $_SESSION['temp_order'];
?>

<!DOCTYPE html>
<html>
<head>
<title>UPI Scanner</title>

<style>
body{
    font-family: Arial;
    background:#ebebc6;
    display:flex;
    justify-content:center;
    align-items:center;
    height:100vh;
}

.box{
    background:#f5f5dc;
    padding:30px;
    text-align:center;
    border-radius:10px;
}

img{
    width:200px;
}

</style>
</head>

<body>

<div class="box">

<h2>Scan & Pay</h2>

<p>Amount: ₹<?php echo $data['total']; ?></p>

<img src="image/sacnner.jpeg">

<p>Processing Payment...</p>

</div>

<script>
// AUTO REDIRECT AFTER 5 SECONDS
setTimeout(function(){
    window.location.href = "24_upi_success.php";
}, 5000);
</script>

</body>
</html>