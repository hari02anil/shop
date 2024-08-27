<!DOCTYPE html>
<html lang="en">
<head>
    
    
</head>
<body>
    <style>
        /* Reset some default styles */
body, form, input, textarea, button {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

/* Set global styles */
body {
    background-image: url('shop img.jpg');
    font-family: Arial, sans-serif;
    background-color: #f9f9f9;
    color: #333;
    padding: 20px;
}

form {
    max-width: 600px;
    margin: auto;
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

h1 {
    text-align: center;
    color: #007bff;
    margin-bottom: 20px;
}

input[type="text"], 
input[type="number"], 
textarea, 
input[type="file"] {
    width: 100%;
    padding: 10px;
    margin-bottom: 15px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

input[type="file"] {
    border: none;
}

textarea {
    height: 100px;
    resize: vertical;
}

input[type="submit"], 
button {
    background-color: #007bff;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 4px;
    cursor: pointer;
    font-size: 16px;
    text-align: center;
}

input[type="submit"]:hover, 
button:hover {
    background-color: #0056b3;
}

/* Style the back to dashboard button */
a {
    display: inline-block;
    margin-top: 20px;
    text-decoration: none;
}

a button {
    background-color: #556d7a;
}

a button:hover {
    background-color: #5a6268;
}

    </style>
    
</body>
</html>

<?php
include 'db.php';
session_start();


if ($_SESSION['role'] !== 'admin') {
    die("Access denied.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $image = '';

    if (isset($_FILES['image'])) {
        $image = 'uploads/' . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $image);
    }

    $stmt = $pdo->prepare("INSERT INTO products (name, description, price, stock, image) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$name, $description, $price, $stock, $image]);

    echo "<script>
        alert('Product updated successfully!');
        </script>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>
    
</head>
<body>
    <h1>Add New Product</h1>
    <form method="POST" enctype="multipart/form-data">
        <input type="text" name="name" placeholder="Product Name" required>
        <textarea name="description" placeholder="Description" maxlength="100"></textarea>
        <input type="number" name="price" placeholder="Price" step="0.01" required>
        <input type="number" name="stock" placeholder="Stock" required>
        Product Pic
        <input type="file" name="image" accept="image/*" required>
        <input type="submit" value="Add Product">
    </form>
    <a href="dashboard.php"><button>Back to Dashboard</button></a>
</body>
</html>