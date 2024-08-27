<?php
session_start();
include 'db.php';
include 'user_log.php'; 

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $product_id = $_POST['product_id'];

   

    try {
        // Start transaction
        $pdo->beginTransaction();

        // Check the product and retrieve details
        $stmt = $pdo->prepare("
            SELECT id, name, price, stock
            FROM products
            WHERE id = :product_id
            LIMIT 1
        ");
        $stmt->execute(['product_id' => $product_id]);
        $product = $stmt->fetch();

        if (!$product) {
            throw new Exception('Product not found');
        }

        // Check stock availability
        if ($product['stock'] < 1) {
            throw new Exception('Product is out of stock');
        }

        // Create the order
        $stmt = $pdo->prepare("
            INSERT INTO orders (user_id, total_amount)
            VALUES (:user_id, :total_amount)
        ");
        $total_amount = $product['price']; 
        $stmt->execute(['user_id' => $user_id, 'total_amount' => $total_amount]);
        $order_id = $pdo->lastInsertId();

        // Insert into order items
        $stmt = $pdo->prepare("
            INSERT INTO order_items (order_id, product_id, quantity, price)
            VALUES (:order_id, :product_id, :quantity, :price)
        ");
        $stmt->execute([
            'order_id'   => $order_id,
            'product_id' => $product['id'],
            'quantity'   => 1, 
            'price'      => $product['price']
        ]);

        // Reduce the stock by 1
        $stmt = $pdo->prepare("
            UPDATE products
            SET stock = stock - 1
            WHERE id = :product_id
        ");
        $stmt->execute(['product_id' => $product_id]);

        // Commit the transaction
        $pdo->commit();
        log_user($pdo, $user_id, 'Product Bought directly');

        // Redirect to order confirmation
        echo json_encode(['status' => 'success', 'redirect' => 'order_confirmation.php?order_id=' . $order_id]);

    } catch (Exception $e) {
        // Rollback in case of error
        $pdo->rollBack();
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }

} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>
