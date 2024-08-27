<?php
session_start();
include 'db.php';
include 'user_log.php'; 

if (!isset($_SESSION['user_id'])) {
    echo json_encode('User not logged in');
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = $_POST['order_id'];

    

    try {
        // Fetch the order to verify ownership
        $stmt = $pdo->prepare("
            SELECT id FROM orders
            WHERE id = :order_id AND user_id = :user_id
        ");
        $stmt->execute(['order_id' => $order_id, 'user_id' => $user_id]);
        $order = $stmt->fetch();

        if (!$order) {
            echo json_encode(['status' => 'error', 'message' => 'Order not found or you do not have permission to cancel this order']);
            exit();
        }

        // Fetch order items for restocking before deleting them
        $stmt = $pdo->prepare("
            SELECT product_id, quantity FROM order_items WHERE order_id = :order_id
        ");
        $stmt->execute(['order_id' => $order_id]);
        $order_items = $stmt->fetchAll();
        // log
        log_user($pdo, $user_id, 'Order canceled');
        // Restock the items
        foreach ($order_items as $item) {
            $stmt = $pdo->prepare("
                UPDATE products
                SET stock = stock + :quantity
                WHERE id = :product_id
            ");
            $stmt->execute(['quantity' => $item['quantity'], 'product_id' => $item['product_id']]);
        }

        // Now delete the order items
        $stmt = $pdo->prepare("
            DELETE FROM order_items
            WHERE order_id = :order_id
        ");
        $stmt->execute(['order_id' => $order_id]);

        // Delete the order
        $stmt = $pdo->prepare("
            DELETE FROM orders
            WHERE id = :order_id
        ");
        $stmt->execute(['order_id' => $order_id]);

        echo json_encode(['status' => 'success', 'message' => 'Order canceled successfully and stock updated']);
        
        
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'An error occurred: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}


