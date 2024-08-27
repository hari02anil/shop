<?php
session_start();
include 'db.php'; 
include 'user_log.php';



$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cart_id = $_POST['cart_id'];
    $quantity = $_POST['quantity'];

    
    try {
        // Check if the cart item belongs to the logged-in user
        $stmt = $pdo->prepare("
            SELECT * FROM cart
            WHERE id = :cart_id AND user_id = :user_id
        ");
        $stmt->execute(['cart_id' => $cart_id, 'user_id' => $user_id]);
        $cart_item = $stmt->fetch();

       
        if (!$cart_item) {
            echo json_encode(['status' => 'error', 'message' => 'Cart item not found']);
            exit();
        }

        $stmt = $pdo->prepare("
        SELECT stock FROM products
        WHERE id = :product_id
    ");
    $stmt->execute(['product_id' => $cart_item['product_id']]);
    $product = $stmt->fetch();

    if ($quantity > $product['stock']) {
        echo json_encode("Requested quantity exceeds available stock");
        exit();
    }
      
        // Update the quantity of the cart item
         else {
            $stmt = $pdo->prepare("
            UPDATE cart
            SET quantity = :quantity
            WHERE id = :cart_id
        ");
        $stmt->execute(['quantity' => $quantity, 'cart_id' => $cart_id]);
        log_user($pdo, $user_id, 'Cart Updated');

        echo json_encode('Cart updated successfully');
        }   
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'An error occurred: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
