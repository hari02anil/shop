<?php
include 'db.php';
session_start();

// Check if the user is an admin
if ($_SESSION['role'] !== 'admin') {
    die("Access denied.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'])) {
    $user_id = intval($_POST['user_id']);
    $action = $_POST['action'];

    // Fetch user information
    $stmt = $pdo->prepare("SELECT username FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();

    if (!$user) {
        echo "User not found.";
        exit();
    }

    if($action=='report'){

    header('Content-Type: text/csv');
    header('Content-Disposition: attachment;filename="' . $user['username'] . ' report.csv"');


    $output = fopen('php://output', 'w');

    // Write headings
    fputcsv($output, ['Order ID', 'Product Name', 'Quantity', 'Price', 'Total Amount', 'Order Date']);

    // Fetch orders for the specified user
    $stmt = $pdo->prepare("
        SELECT orders.id as order_id, products.name as product_name, order_items.quantity, 
               order_items.price, orders.total_amount, orders.order_date
        FROM orders
        JOIN order_items ON orders.id = order_items.order_id
        JOIN products ON order_items.product_id = products.id
        WHERE orders.user_id = ?
        ORDER BY orders.order_date DESC
    ");
    $stmt->execute([$user_id]);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Loop and write the data
    foreach ($orders as $order) {
        fputcsv($output, [
            $order['order_id'],
            $order['product_name'],
            $order['quantity'],
            $order['price'],
            $order['total_amount'],
            $order['order_date']
        ]);
    }

    fclose($output);
    exit();
}else if($action=='log'){
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment;filename="' . $user['username'] . ' log.csv"');


    $output = fopen('php://output', 'w');

    // Write headings
    fputcsv($output, ['ID', 'Action', 'Action time', 'Details']);

    //fetch the log for the user
    $stmt = $pdo->prepare("
        SELECT id,action,action_time,details
        FROM log
        WHERE user_id = ?
        ORDER BY action_time DESC
    ");
    $stmt->execute([$user_id]);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Loop and write the data
    foreach ($orders as $order) {
        fputcsv($output, [
            $order['id'],
            $order['action'],
            $order['action_time'],
            $order['details']
            
        ]);
    }

    fclose($output);
    exit();

}
} else {
    echo "Invalid request.";
}
?>
