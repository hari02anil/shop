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

// Fetch items from the cart
$stmt = $pdo->prepare("
    SELECT cart.id as cart_id, products.id as product_id, products.name, products.price, cart.quantity
    FROM cart
    JOIN products ON cart.product_id = products.id
    WHERE cart.user_id = :user_id
");
$stmt->execute(['user_id' => $user_id]);
$cart_items = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="script.js"></script> 

    <style>
        body {
            background-image: url('shop img.jpg');
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
            margin: 0 auto;
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

        .quantity {
            width: 60px;
            padding: 5px;
            text-align: center;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .update-cart, .remove-cart {
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 5px 10px;
            cursor: pointer;
            margin: 5px;
        }

        .remove-cart {
            background-color: #dc3545;
        }

        .update-cart:hover, .remove-cart:hover {
            opacity: 0.8;
        }

        button[type="submit"] {
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 10px 20px;
            font-size: 1rem;
            cursor: pointer;
            display: block;
            margin: 20px auto;
        }

        button[type="submit"]:hover {
            opacity: 0.9;
        }

        p {
            text-align: center;
            font-size: 1.2rem;
            color: #555;
        }
    </style>
</head>
<body>
    <h2>Your Cart</h2>
    <a href="dashboard.php"><button>Back to Dashboard</button></a>

    <?php if ($cart_items): ?>
        <form id="cart-form" action="buy_now.php" method="post">
            <table>
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Total</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cart_items as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['name']); ?></td>
                            <td>₹<?php echo htmlspecialchars($item['price']); ?></td>
                            <td>
                                <input type="number" class="quantity" value="<?php echo htmlspecialchars($item['quantity']); ?>" data-cart-id="<?php echo htmlspecialchars($item['cart_id']); ?>" min="1">
                            </td>
                            <td>₹<?php echo htmlspecialchars($item['price'] * $item['quantity']); ?></td>
                            <td>
                                <button type="button" class="update-cart" data-cart-id="<?php echo htmlspecialchars($item['cart_id']); ?>">Update</button>
                                <button type="button" class="remove-cart" data-cart-id="<?php echo htmlspecialchars($item['cart_id']); ?>">Remove</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <button type="submit">Proceed to Checkout</button>
        </form>
    <?php else: ?>
        <p>Your cart is empty.</p>
    <?php endif; ?>

    <script>
        $(document).ready(function() {
            $(".update-cart").click(function() {
                var cartId = $(this).data("cart-id");
                var quantity = $(this).closest("tr").find(".quantity").val();
                $.ajax({
                    url: 'update_cart.php',
                    method: 'POST',
                    data: { cart_id: cartId, quantity: quantity },
                    success: function(response) {
                        alert((response));
                        location.reload(); // Refresh the page to reflect changes
                    },
                    error: function() {
                        alert("An error occurred while updating the cart.");
                    }
                });
            });

            $(".remove-cart").click(function() {
                var cartId = $(this).data("cart-id");
                $.ajax({
                    url: 'remove_from_cart.php',
                    method: 'POST',
                    data: { cart_id: cartId },
                    success: function(response) {
                        alert("Item removed from cart");
                        location.reload(); // Refresh the page to reflect changes
                    },
                    error: function() {
                        alert("An error occurred while removing the item from the cart.");
                    }
                });
            });
        });
    </script>
</body>
</html>
