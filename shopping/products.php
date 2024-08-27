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

// Fetch all products from the database
$stmt = $pdo->query("SELECT * FROM products");
$products = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products</title>
    
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

        .product-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .product {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .product img {
            max-width: 100%;
            height: auto;
            border-radius: 5px;
        }

        .product h3 {
            font-size: 1.25rem;
            margin-bottom: 10px;
            color: #333;
        }

        .product p {
            font-size: 1rem;
            margin-bottom: 10px;
            color: #666;
        }

        .product button {
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 10px 15px;
            font-size: 1rem;
            cursor: pointer;
            margin: 5px 0;
        }

        .product button.buy-now {
            background-color: #007bff;
        }

        .product button:hover {
            opacity: 0.8;
        }

        a {
            display: inline-block;
            background-color: #28a745;
            margin-bottom: 20px;
            color: #007bff;
            text-decoration: none;
            font-size: 1rem;
            margin-left: 20px;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <h2>Products</h2>
    <a href="dashboard.php"><button>Back to Dashboard</button></a>

    <div class="product-list">
        <?php if ($products): ?>
            <?php foreach ($products as $product): ?>
                <div class="product" data-id="<?php echo htmlspecialchars($product['id']); ?>">
                    <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                    <img src="<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" width="100"><br>
                    <p><?php echo htmlspecialchars($product['description']); ?></p>
                    <p>Price: ₹ <?php echo htmlspecialchars($product['price']); ?></p>
                    <button class="add-to-cart">Add to Cart</button>
                    <button class="buy-now">Buy Now</button>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No products available.</p>
        <?php endif; ?>
    </div>
</body>
</html>
