<?php
session_start();
include("db_connect.php");

if (!isset($_SESSION['user_id'])) {
    header("Location:03_login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

/* HANDLE UPDATE */
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['field'])) {

    $field = $_POST['field'];
    $value = trim($_POST['value']);

    if ($value != "") {

        // USERS TABLE
        if (in_array($field, ['username', 'phone', 'password'])) {

            if ($field == 'password') {
                $value = password_hash($value, PASSWORD_BCRYPT);
            }

            $stmt = $conn->prepare("UPDATE users SET $field=? WHERE id=?");
            $stmt->bind_param("si", $value, $user_id);
            $stmt->execute();
        }

        // ORDERS TABLE (latest order only)
        else if (in_array($field, ['address', 'city', 'pincode'])) {

            $getOrder = $conn->prepare("SELECT order_id FROM orders WHERE user_id=? ORDER BY order_id DESC LIMIT 1");
            $getOrder->bind_param("i", $user_id);
            $getOrder->execute();
            $res = $getOrder->get_result()->fetch_assoc();

            if ($res) {
                $order_id = $res['order_id'];

                $stmt = $conn->prepare("UPDATE orders SET $field=? WHERE order_id=?");
                $stmt->bind_param("si", $value, $order_id);
                $stmt->execute();
            }
        }
    }
}

/* FETCH USER */
$user_sql = $conn->prepare("SELECT username, phone FROM users WHERE id=?");
$user_sql->bind_param("i", $user_id);
$user_sql->execute();
$user = $user_sql->get_result()->fetch_assoc();

/* FETCH LATEST ORDER */
$order_sql = $conn->prepare("
    SELECT address, city, pincode 
    FROM orders 
    WHERE user_id=? 
    ORDER BY order_id DESC LIMIT 1
");
$order_sql->bind_param("i", $user_id);
$order_sql->execute();
$order = $order_sql->get_result()->fetch_assoc();

$address = $order['address'] ?? "Not Provided";
$city = $order['city'] ?? "Not Provided";
$pincode = $order['pincode'] ?? "Not Provided";
?>

<!DOCTYPE html>
<html>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<head>
<title>My Account</title>

<style>
:root{
--brand:#3e2723;
--bg:#ebebc6;
--card:#f5f5dc;
--btn:#b37d5c;
--btn-hover:#a06a4a;
}

body { 
    font-family: Arial; 
    background: var(--bg); 
    margin:0; 
    padding:0;
}

/* push content below navbar */
.container {
    max-width: 850px;
    margin: 100px auto;
    background: var(--card);
    padding: 40px 50px;
    border-radius: 20px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

h2 { 
    text-align:center; 
    color: var(--brand); 
    margin-bottom: 40px;
}

.section {
    margin: 20px 0;
    padding: 20px;
    border-radius: 12px;
    background: #fffaf0;
    box-shadow: 0 3px 10px rgba(0,0,0,0.08);
}

.section label {
    font-weight: bold;
    font-size: 20px;
    color: black;
}

.section span { 
    font-size: 16px; 
    color:#333; 
    display:block; 
    margin-top:8px; 
}

.manage-btn {
    background: var(--brand);
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 8px;
    cursor: pointer;
    margin-top:10px;
}

.manage-btn:hover { background: var(--btn); }

.edit-form { display: none; margin-top: 10px; }

input {
    padding: 8px;
    border: 1px solid #aaa;
    border-radius: 8px;
    width: 100%;
    max-width: 400px;
    margin-bottom:8px;
}

.update-btn {
    background: var(--btn);
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 8px;
    cursor: pointer;
}

.update-btn:hover { background: var(--btn-hover); }
</style>

<script>
function toggleEdit(field) {
    const form = document.getElementById(field + '-form');
    form.style.display = (form.style.display === 'block') ? 'none' : 'block';
}
</script>

</head>

<body>

<?php include("navbar.php"); ?>

<div class="container">

<h2>My Account</h2>

<?php
$fields = [
    'username'=>'Name',
    'phone'=>'Phone',
    'password'=>'Password',
    'address'=>'Address',
    'city'=>'City',
    'pincode'=>'Pincode'
];

$values = [
    'username'=>$user['username'],
    'phone'=>$user['phone'],
    'password'=>'********',
    'address'=>$address,
    'city'=>$city,
    'pincode'=>$pincode
];

foreach($fields as $key => $label):
?>

<div class="section">

<label><?php echo $label; ?></label>

<span><?php echo htmlspecialchars($values[$key]); ?></span>

<button class="manage-btn" onclick="toggleEdit('<?php echo $key; ?>')">Manage</button>

<form method="post" id="<?php echo $key; ?>-form" class="edit-form">
    <input type="<?php echo $key=='password'?'password':'text'; ?>" 
           name="value" 
           placeholder="Enter new <?php echo strtolower($label); ?>">
    
    <input type="hidden" name="field" value="<?php echo $key; ?>">
    
    <button type="submit" class="update-btn">Update</button>
</form>

</div>

<?php endforeach; ?>

</div>

</body>
</html>