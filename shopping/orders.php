<?php
session_start();
include 'db.php'; 

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
if ($_SESSION['role'] !== 'user') {
    header("Location: dashboard.php");
    exit();
}


$user_id = $_SESSION['user_id'];

// Fetch orders and their associated products for the logged-in user
$stmt = $pdo->prepare("
    SELECT orders.id as order_id, orders.total_amount, orders.order_date, 
           products.name as product_name, order_items.quantity as product_quantity
    FROM orders
    JOIN order_items ON orders.id = order_items.order_id
    JOIN products ON order_items.product_id = products.id
    WHERE orders.user_id = :user_id
    ORDER BY orders.order_date DESC
");
$stmt->execute(['user_id' => $user_id]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

$groupedOrders = [];
foreach ($orders as $order) {
    $groupedOrders[$order['order_id']]['total_amount'] = $order['total_amount'];
    $groupedOrders[$order['order_id']]['order_date'] = $order['order_date'];
    $groupedOrders[$order['order_id']]['items'][] = [
        'product_name' => $order['product_name'],
        'product_quantity' => $order['product_quantity']
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Orders</title>
    
    <style>
        body {
            background-image: url('shop img.jpg');
            font-family: 'Arial', sans-serif;
            background-color: #f9f9f9;
            color: #333;
            margin: 0;
            padding: 20px;
        }

        h2 {
            text-align: center;
            color: #007bff;
            margin-bottom: 20px;
        }

        a {
            display: inline-block;
            margin-bottom: 20px;
            color: #007bff;
            text-decoration: none;
            font-size: 1rem;
            margin-left: 20px;
        }

        a:hover {
            text-decoration: underline;
        }

        table {
            width: 100%;
            max-width: 1000px;
            margin: 20px auto;
            border-collapse: collapse;
            background-color: #fff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        th, td {
            padding: 12px 15px;
            border: 1px solid #ddd;
            text-align: center;
        }

        th {
            background-color: #007bff;
            color: white;
        }

        td {
            font-size: 1rem;
            color: #555;
        }

        .cancel-order {
            background-color: #dc3545;
            color: white;
            padding: 8px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
            transition: background-color 0.3s;
        }

        .cancel-order:hover {
            background-color: #c82333;
        }

        .no-orders {
            text-align: center;
            font-size: 1.2rem;
            color: #666;
            margin-top: 30px;
        }
    </style>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <h2>Your Orders</h2>
    <a href="dashboard.php"><button>Back to Dashboard</button></a>

    <?php if ($groupedOrders): ?>
        <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Total Amount</th>
                    <th>Order Date</th>
                    <th>Product Name</th>
                    <th>Quantity</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($groupedOrders as $order_id => $order): ?>
                    <?php foreach ($order['items'] as $index => $item): ?>
                        <tr>
                            <?php if ($index === 0): ?>
                                <td rowspan="<?php echo count($order['items']); ?>"><?php echo htmlspecialchars($order_id); ?></td>
                                <td rowspan="<?php echo count($order['items']); ?>">₹<?php echo htmlspecialchars(number_format($order['total_amount'], 2)); ?></td>
                                <td rowspan="<?php echo count($order['items']); ?>"><?php echo htmlspecialchars($order['order_date']); ?></td>
                            <?php endif; ?>
                            <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                            <td><?php echo htmlspecialchars($item['product_quantity']); ?></td>
                            <?php if ($index === 0): ?>
                                <td rowspan="<?php echo count($order['items']); ?>">
                                    <button type="button" class="cancel-order" data-order-id="<?php echo htmlspecialchars($order_id); ?>">Cancel Order</button>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="no-orders">You have no orders.</p>
    <?php endif; ?>

    <script>
        $(document).ready(function() {
            $(".cancel-order").click(function() {
                var orderId = $(this).data("order-id");
                if (confirm("Are you sure you want to cancel this order?")) {
                    $.ajax({
                        url: 'cancel_order.php',
                        method: 'POST',
                        data: { order_id: orderId },
                        success: function(response) {
                            alert("Order canceled successfully");
                            location.reload(); // Refresh the page to reflect changes
                        },
                        error: function() {
                            alert("An error occurred while canceling the order.");
                        }
                    });
                }
            });
        });
    </script>
</body>
</html>


