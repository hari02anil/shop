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

// Fetch the most recent order for the user
$stmt = $pdo->prepare("
    SELECT * FROM orders
    WHERE user_id = :user_id
    ORDER BY order_date DESC
    LIMIT 1
");
$stmt->execute(['user_id' => $user_id]);
$order = $stmt->fetch();



// Fetch order items
$stmt = $pdo->prepare("
    SELECT order_items.product_id, products.name, order_items.quantity, order_items.price
    FROM order_items
    JOIN products ON order_items.product_id = products.id
    WHERE order_items.order_id = :order_id
");
$stmt->execute(['order_id' => $order['id']]);
$order_items = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
    
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            margin: 0;
            padding: 20px;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        h3 {
            margin-top: 30px;
            color: #007bff;
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

        p {
            font-size: 1.1rem;
            color: #555;
        }

        table {
            width: 100%;
            max-width: 800px;
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

        .total-row td {
            font-weight: bold;
        }

        .continue-shopping {
            text-align: center;
            margin-top: 20px;
        }

        .continue-shopping a {
            background-color: #28a745;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 1rem;
            display: inline-block;
            transition: background-color 0.3s;
        }

        .continue-shopping a:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <h2>Order Invoice</h2>
    <a href="dashboard.php"><button>Back to Dashboard</button></a>

    <h3>Order details</h3>
    <p>Date: <?php echo htmlspecialchars($order['order_date']); ?></p>
    <p>Total Amount: ₹<?php echo number_format($order['total_amount'], 2); ?></p>

    <?php if ($order_items): ?>
        <table>
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($order_items as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['name']); ?></td>
                        <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                        <td>₹<?php echo number_format($item['price'], 2); ?></td>
                        <td>₹<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No items in this order.</p>
    <?php endif; ?>

    <div class="continue-shopping">
        <a href="products.php">Continue Shopping</a>
    </div>
</body>
</html>
<?php
use PHPMailer\PHPMailer\PHPMailer;
require 'vendor/autoload.php';
$mail = new PHPMailer(true);

try {
    // Server settings
    $mail->isSMTP();
    $mail->Host       = 'smtp-relay.brevo.com'; // Mailtrap SMTP server
    $mail->SMTPAuth   = true;
    $mail->Username   = '7aea6a001@smtp-brevo.com'; // Replace with Mailtrap username
    $mail->Password   = 'v9VtEmaUFQRk5bYJ'; // Replace with Mailtrap password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Enable TLS encryption; or use `PHPMailer::ENCRYPTION_SMTPS` for SSL
    $mail->Port       = 2525; // Mailtrap's port (can be 2525, 587, or 465)

    $stmt = $pdo->prepare("SELECT email FROM users WHERE id = :user_id");
    $stmt->execute(['user_id' => $user_id]);
    $user = $stmt->fetch();

    $mail->setFrom('teat22745@gmail.com', 'ShoppingCart');

    $mail->addAddress($user['email'], ''); // Add a new recipient

    $mail->Subject = 'Order Invoice';
    $mail->Body    =" <html>
<head>
    <style>
        body { font-family: Arial, sans-serif; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px; border: 1px solid #ddd; }
        th { background-color: #007bff; color: white; }
        .total-row td { font-weight: bold; }
    </style>
</head>
<body>
    <h2>Order Confirmation</h2>
    <p>Thank you for your order! Here are the details:</p>
    <p>Date: {$order['order_date']}</p>
    <p>Total Amount: Rs" . $order['total_amount'] . "</p>
    <table>
        <thead>
            <tr>
                <th>Product Name</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>";
        foreach ($order_items as $item) {
            $mail->Body .= "
                    <tr>
                        <td>{$item['name']}</td>
                        <td>{$item['quantity']}</td>
                        <td>" . $item['price'] . "</td>
                        <td>" . $item['price'] * $item['quantity'] . "</td>
                    </tr>";
        }
        $mail->send();
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
    
    ?>