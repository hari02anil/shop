<?php
session_start();
include 'db.php'; 
include 'user_log.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Initialize total amount
    $total_amount = 0;
    
    // Start a transaction
    $pdo->beginTransaction();
    
    try {
        // Fetch cart items
        $stmt = $pdo->prepare("
            SELECT cart.id as cart_id, products.id as product_id, products.price, cart.quantity, products.stock
            FROM cart
            JOIN products ON cart.product_id = products.id
            WHERE cart.user_id = :user_id
        ");
        $stmt->execute(['user_id' => $user_id]);
        $cart_items = $stmt->fetchAll();
        
        if (!$cart_items) {
            throw new Exception("Your cart is empty.");
        }
        
        // Create an order
        $stmt = $pdo->prepare("
            INSERT INTO orders (user_id, total_amount)
            VALUES (:user_id, :total_amount)
        ");
        $stmt->execute(['user_id' => $user_id, 'total_amount' => $total_amount]);
        $order_id = $pdo->lastInsertId();
        
        // Prepare statement for inserting order items
        $stmt_order_item = $pdo->prepare("
            INSERT INTO order_items (order_id, product_id, quantity, price)
            VALUES (:order_id, :product_id, :quantity, :price)
        ");
        
        // Prepare statement for updating product stock
        $stmt_update_stock = $pdo->prepare("
            UPDATE products
            SET stock = stock - :quantity
            WHERE id = :product_id
        ");
        
        // Loop through cart items and process them
        foreach ($cart_items as $item) {
            $product_total = $item['price'] * $item['quantity'];
            $total_amount += $product_total;
            
            // Insert order item
            $stmt_order_item->execute([
                'order_id' => $order_id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price' => $item['price']
            ]);
            
            // Update product stock
            if ($item['stock'] < $item['quantity']) {
                throw new Exception("Insufficient stock for product ID " . $item['product_id']);
            }
            $stmt_update_stock->execute([
                'quantity' => $item['quantity'],
                'product_id' => $item['product_id']
            ]);
        }
        
        // Update the order total
        $stmt = $pdo->prepare("
            UPDATE orders
            SET total_amount = :total_amount
            WHERE id = :order_id
        ");
        $stmt->execute(['total_amount' => $total_amount, 'order_id' => $order_id]);
        
        // Clear the cart
        $stmt = $pdo->prepare("
            DELETE FROM cart
            WHERE user_id = :user_id
        ");
        $stmt->execute(['user_id' => $user_id]);
        
        // Commit the transaction
        $pdo->commit();
        log_user($pdo, $user_id, 'Order placed from cart');
        
        
        header("Location: order_confirmation.php");
        
    } catch (Exception $e) {
        // Rollback the transaction if something goes wrong
        $pdo->rollBack();
        echo "<p>Error: " . $e->getMessage() . "</p>";
    }
    
} else {
    // Redirect if not a POST request
    header("Location: cart.php");
    exit();
}
?>
