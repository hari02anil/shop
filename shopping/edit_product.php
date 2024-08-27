<?php
include 'db.php';
session_start();

if ($_SESSION['role'] !== 'admin') {
    die("Access denied.");
}

$productId = intval($_GET['id']);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $image = '';

    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $image = 'uploads/' . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $image);
    
   
    $stmt = $pdo->prepare("UPDATE products SET name = ?, description = ?, price = ?, stock = ?, image = ? WHERE id = ?");
    $stmt->execute([$name, $description, $price, $stock, $image, $productId]);

    echo "<script>
        alert('Product updated successfully!');
        window.location.href = 'edit_products.php'; 
    </script>";
    }else{
        $stmt = $pdo->prepare("UPDATE products SET name = ?, description = ?, price = ?, stock = ? WHERE id = ?");
    $stmt->execute([$name, $description, $price, $stock, $productId]);

    echo "<script>
        alert('Product updated successfully!');
        window.location.href = 'edit_products.php'; 
    </script>";

    }
}

// Fetch product details for editing
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$productId]);
$product = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
    <link rel="stylesheet" href="edit_product.css"> 
     
</head>
<body>

</script>
    <h1>Edit Product</h1>

    <form method="POST" enctype="multipart/form-data">
        <input type="text" name="name" placeholder="Product Name" value="<?= htmlspecialchars($product['name']) ?>" required>
        <textarea name="description" placeholder="Description"><?= htmlspecialchars($product['description']) ?></textarea>
        <input type="number" name="price" placeholder="Price" step="0.01" value="<?= htmlspecialchars($product['price']) ?>" required>
        <input type="number" name="stock" placeholder="Stock" value="<?= htmlspecialchars($product['stock']) ?>" required>
        <input type="file" name="image" accept="image/*">
        <?php if ($product['image']): ?>
            <p>Current image: <img src="<?= htmlspecialchars($product['image']) ?>" alt="Product Image" width="100"></p>
        <?php endif; ?>
        <input type="submit" value="Update Product">
        
        
    </form>

    <a href="edit_products.php"><button>Back to Product List</button></a>
</body>
</html>

