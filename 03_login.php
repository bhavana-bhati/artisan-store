<?php
session_start();
include("db_connect.php");

$redirect = $_GET['redirect'] ?? '01_index.php';
$error = "";

if(isset($_POST['login'])){
    $username = mysqli_real_escape_string($conn,$_POST['username']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE BINARY username='$username'";
    $result = mysqli_query($conn,$sql);
    if(!$result){
    die("Query Failed: " . mysqli_error($conn));
}

    if(mysqli_num_rows($result) > 0){
        $row = mysqli_fetch_assoc($result);
        if(password_verify($password,$row['password'])){
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            header("Location: $redirect");
            exit();
        } else { $error="Incorrect password"; }
    } else { $error="No user found with this username"; }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Login</title>
<style>
body{font-family:Arial,sans-serif;background-color:#ebebc6;color:#3e2723;display:flex;justify-content:center;align-items:center;height:100vh;}
.login-section{min-height:100vh; display:flex; align-items:center; justify-content:center; padding:100px 5% 50px;}
.login-container{background-color:white; padding:40px; border-radius:20px; width:100%; max-width:500px;}
h2{text-align:center;margin-bottom:30px;font-size:2.5rem;}
.form-group{margin-bottom:20px;}
.form-group label{display:block;margin-bottom:5px;font-weight:bold;}
.form-group input{width:100%;padding:12px;font-size:1rem;background-color:#ebebc6;border:none;border-radius:8px;}
.form-group input:focus{outline:none;background-color:#f5f5dc;}
.btn-login{width:100%; background-color:#b37d5c;color:white;padding:15px;border:none;border-radius:8px;font-size:1.2rem;cursor:pointer;}
.btn-login:hover{background-color:#a06a4a;}
.error{color:red;text-align:center;margin-bottom:15px;}
.register-link{text-align:center;margin-top:12px;}
.register-link a{color:#3e2723;text-decoration:none;font-weight:bold;}
.register-link a:hover{text-decoration:underline;}
.password-box{position:relative;}
.toggle-eye{position:absolute;right:10px;top:50%;transform:translateY(-50%);cursor:pointer;color:#6b8e23;}
</style>
</head>
<body>

<section class="login-section">
<div class="login-container">
<h2>Login</h2>
<?php if($error!="") echo "<div class='error'>$error</div>"; ?>
<form method="POST">
    <div class="form-group">
        <label>Username</label>
        <input type="text" name="username" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required>
    </div>
    <div class="form-group password-box">
        <label>Password</label>
        <input type="password" name="password" id="password" required>
        <i class="fa-solid fa-eye toggle-eye" id="eyeIcon" onclick="togglePassword()"></i>
    </div>
    <button type="submit" name="login" class="btn-login">Login</button>
</form>
<div class="register-link">
Don't have an account? <a href="02_register.php?redirect=<?= urlencode($redirect) ?>">Register here</a>
</div>
</div>
</section>

<script>
function togglePassword(){
    const passField = document.getElementById("password");
    const eyeIcon = document.getElementById("eyeIcon");
    if(passField.type === "password"){
        passField.type="text";
        eyeIcon.classList.remove("fa-eye");
        eyeIcon.classList.add("fa-eye-slash");
    } else {
        passField.type="password";
        eyeIcon.classList.remove("fa-eye-slash");
        eyeIcon.classList.add("fa-eye");
    }
}
</script>
</body>
</html>