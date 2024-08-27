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
    $cart_id = $_POST['cart_id'];

  

    try {
        // Delete the item from the cart
        $stmt = $pdo->prepare("
            DELETE FROM cart
            WHERE id = :cart_id AND user_id = :user_id
        ");
        $stmt->execute(['cart_id' => $cart_id, 'user_id' => $user_id]);
        log_user($pdo, $user_id, 'Product removed from cart');

        echo json_encode(['status' => 'success', 'message' => 'Item removed from cart']);
        
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'An error occurred: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>
