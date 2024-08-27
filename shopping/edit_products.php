<?php
include 'db.php';
session_start();

if ($_SESSION['role'] !== 'admin') {
    die("Access denied.");
}

// Handle delete request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete'])) {
    $productId = intval($_POST['id']);
    
    // Prepare the DELETE statement
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    
    // Execute the DELETE statement
    if ($stmt->execute([$productId])) {
        echo "<script>
            alert('Product deleted successfully!');
            window.location.href = 'edit_products.php';
        </script>";
        exit();
    } else {
        echo "Error deleting product: " . $stmt->errorInfo()[2];
    }
}

// Fetch all products
$stmt = $pdo->query("SELECT * FROM products");
$products = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Products</title>
    
    
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

    <style>
        body {
            background-image: url('shop img.jpg');
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        h1 {
            margin-top: 20px;
            color: #333;
            text-align: center; /* Center the title */
        }
        .back-button {
            background-color: #556d7a;
            margin: 20px 0;
        }
        .back-button:hover {
            background-color: #5a6268;
        }
        .back-button-container {
            padding: 0 20px; 
        }
        .back-button-container {
            display: flex;
            justify-content: flex-start; 
        }
        table {
            width: 90%;
            margin: 20px auto;
            border-collapse: collapse;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f4f4f4;
        }
        .actions {
            display: flex;
            gap: 10px;
        }
        button {
            background-color: #28a745;
            color: #fff;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }
        button:hover {
            background-color: #218838;
        }
        .delete-button {
            background-color: #dc3545;
            color: #fff;
            border: none;
            border-radius: 5px;
            padding: 10px 15px;
            font-size: 0.9em;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .delete-button:hover {
            background-color: #c82333;
        }
        .dataTables_wrapper {
    background: floralwhite;
    position: relative;
    clear: both;
}
    </style>

<style>
    /* Table Wrapper */
    .dataTables_wrapper {
        width: 90%;
        margin: 20px auto;
        font-family: Arial, sans-serif;
    }

    /* Table Header */
    table.dataTable thead th {
        background-color: #556d7a;
        color: white;
        font-weight: bold;
        padding: 10px;
        text-align: left;
        border-bottom: 2px solid #ddd;
    }

    /* Table Body */
    table.dataTable tbody tr {
        background-color: #f9f9f9;
    }

    table.dataTable tbody tr:nth-child(even) {
        background-color: #f4f4f4;
    }

    table.dataTable tbody td {
        padding: 10px;
        border-bottom: 1px solid #ddd;
    }

    /* Table Row Hover Effect */
    table.dataTable tbody tr:hover {
        background-color: #e2e6ea;
    }

    /* Pagination Controls */
    .dataTables_paginate .paginate_button {
        background-color: #556d7a;
        color: white;
        border: none;
        border-radius: 4px;
        padding: 5px 10px;
        margin: 0 2px;
        cursor: pointer;
    }

    .dataTables_paginate .paginate_button:hover {
        background-color: #5a6268;
    }

    .dataTables_paginate .paginate_button.current {
        background-color: #343a40;
    }

    /* Search Box */
    .dataTables_filter input {
        border: 1px solid #ddd;
        padding: 5px;
        border-radius: 4px;
        margin-left: 5px;
    }

    /* Length Menu */
    .dataTables_length select {
        border: 1px solid #ddd;
        padding: 5px;
        border-radius: 4px;
        margin-left: 5px;
    }

    /* Information Text */
    .dataTables_info {
        margin-top: 10px;
        color: #666;
    }

    /* Loading Indicator */
    .dataTables_processing {
        background-color: #556d7a;
        color: white;
        padding: 10px;
        border-radius: 4px;
    }
</style>

</head>
<body>
    <h1>Edit Products</h1>

    <table id="productsTable" class="display">
        <thead>
            <tr>
                <th>Product Name</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $product): ?>
                <tr>
                    <td><?= htmlspecialchars($product['name']) ?></td>
                    <td>
                        <div class="actions">
                            <a href="edit_product.php?id=<?= $product['id'] ?>">
                                <button>Edit</button>
                            </a>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="id" value="<?= $product['id'] ?>">
                                <input type="submit" name="delete" value="Delete" class="delete-button" onclick="return confirm('Are you sure you want to delete this product?');">
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="back-button-container">
        <a href="dashboard.php"><button class="back-button">Back to Dashboard</button></a>
    </div>

    <script>
    $(document).ready(function() {
        $('#productsTable').DataTable({
            "paging": true,
            "searching": true,
            "ordering": true,
            "pageLength": 5       // Show  entries per page by default
        });
    });
    </script>
</body>
</html>
