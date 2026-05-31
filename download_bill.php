<?php
session_start();
include("db_connect.php");

// Admin check
if(!isset($_SESSION['admin'])){
    die("Access denied!");
}

if(!isset($_GET['order_id'])){
    die("Order ID missing!");
}

require('fpdf.php');

$order_id = intval($_GET['order_id']);

// Fetch order
$stmt = $conn->prepare("SELECT * FROM orders WHERE order_id=?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();
if(!$order) die("Order not found!");

// Fetch items
$stmtItems = $conn->prepare("
    SELECT oi.*, p.name, p.price
    FROM order_items oi
    JOIN products p ON oi.product_id = p.product_id
    WHERE oi.order_id=?
");
$stmtItems->bind_param("i", $order_id);
$stmtItems->execute();
$items = $stmtItems->get_result();

// Create PDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial','B',16);

// Header
$pdf->Cell(0,10,"Order #{$order['order_id']}",0,1,'C');
$pdf->Ln(5);

// Customer Info
$pdf->SetFont('Arial','',12);
$pdf->Cell(0,8,"Name: ".$order['name'],0,1);
$pdf->Cell(0,8,"Phone: ".$order['phone'],0,1);
$pdf->Cell(0,8,"Address: ".$order['address'].", ".$order['city']." - ".$order['pincode'],0,1);
$pdf->Cell(0,8,"Status: ".$order['order_status'],0,1);
$pdf->Ln(5);

// Table Header
$pdf->SetFont('Arial','B',12);
$pdf->Cell(90,8,"Product",1);
$pdf->Cell(30,8,"Qty",1,0,'C');
$pdf->Cell(40,8,"Price",1,0,'C');
$pdf->Cell(30,8,"Total",1,1,'C');

$pdf->SetFont('Arial','',12);

// Table Rows
while($item = $items->fetch_assoc()){
    $pdf->Cell(90,8,$item['name'],1);
    $pdf->Cell(30,8,$item['quantity'],1,0,'C');
    $pdf->Cell(40,8,"₹".$item['price'],1,0,'C');
    $pdf->Cell(30,8,"₹".($item['price']*$item['quantity']),1,1,'C');
}

// Total
$pdf->SetFont('Arial','B',12);
$pdf->Cell(160,8,"Total Amount",1);
$pdf->Cell(30,8,"₹".$order['total_price'],1,1,'C');

// Output
$pdf->Output('D', "order_{$order_id}.pdf"); // Force download
exit();
?>