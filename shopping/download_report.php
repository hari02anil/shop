<?php
session_start();
include 'db.php'; 

// Check if the user is an admin
if ($_SESSION['role'] !== 'admin') {
    die("Access denied.");
}

header('Content-Type: text/csv');
header('Content-Disposition: attachment;filename="full_orderreport.csv"');

$output = fopen('php://output', 'w');

// Write headings
fputcsv($output, ['Order ID', 'User ID', 'User Name', 'Product Name', 'Quantity', 'Price', 'Total Amount', 'Order Date']);

// Fetch  details
$stmt = $pdo->prepare("
    SELECT orders.id as order_id, orders.user_id, users.name as user_name, products.name as product_name, 
           order_items.quantity, order_items.price, orders.total_amount, orders.order_date
    FROM orders
    JOIN order_items ON orders.id = order_items.order_id
    JOIN products ON order_items.product_id = products.id
    JOIN users ON orders.user_id = users.id
    ORDER BY orders.order_date DESC
");
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Loop and write
foreach ($orders as $order) {
    fputcsv($output, [
        $order['order_id'],
        $order['user_id'],
        $order['user_name'],
        $order['product_name'],
        $order['quantity'],
        $order['price'],
        $order['total_amount'], 
        $order['order_date']
    ]);
}


fclose($output);
exit();
