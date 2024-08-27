<?php
session_start();
include 'db.php'; 

require 'vendor/autoload.php';

if ($_SESSION['role'] !== 'admin') {
    die("Access denied.");
}


use PhpOffice\PhpSpreadsheet\IOFactory;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $file = $_FILES['file']['tmp_name'];

    try {
        $spreadsheet = IOFactory::load($file);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, true);

        // Validate column headers
        $requiredHeaders = ['A'=> 'name','B'=> 'description', 'C'=>'price','D'=> 'stock', 'E'=>'image'];
        $headers = array_map('trim', $rows[1]);

        if ($headers != $requiredHeaders) {
            echo "<script>
                 alert('Unsuccessful, Please check the file!');
                 window.location.href = 'bulk_upload.php';
                 </script>";
            exit();
        }

       

        // Prepare to insert into the database
        $pdo->beginTransaction();
        $insertQuery = $pdo->prepare("
            INSERT INTO products (name, description, price, stock, image)
            VALUES (:name, :description, :price, :stock, :image)
        ");

       // Prepare to update the products
        $updateQuery = $pdo->prepare("
        UPDATE products
        SET description = :description, price = :price, stock = :stock, image = :image
        WHERE name = :name
    ");

        // Iterate over rows (starting from the second row)
        for ($i = 2; $i <= count($rows); $i++) {
            $row = $rows[$i];

            $name = trim($row['A']);
            $description = trim($row['B']);
            $price = trim($row['C']);
            $stock = trim($row['D']);
            $image = trim($row['E']);

            // Validate each cell 
            if (empty($name) || !is_numeric($price) || !is_numeric($stock) || empty($image)) {
                echo "<script>
                 alert('Unsuccessful, Please check the file!');
                 window.location.href = 'bulk_upload.php';
                 </script>";
                $pdo->rollBack();
                exit();
            }//select the products 
            $stmt = $pdo->prepare("SELECT * FROM products WHERE name = :name");
            $stmt->execute(['name' => $name]);
            $exists = $stmt->fetchColumn();

            if ($exists) {
                // Update existing product
                $updateQuery->execute([
                    'name' => $name,
                    'description' => $description,
                    'price' => $price,
                    'stock' => $stock,
                    'image' => $image,
                ]);
            }else{
            

            // Insert product into the database
            $insertQuery->execute([
                'name' => $name,
                'description' => $description,
                'price' => $price,
                'stock' => $stock,
                'image' => $image,
            ]);
        }
    }

        $pdo->commit();
        

        echo "<script>
        alert('Product added successfully!');
        window.location.href = 'bulk_upload.php'; 
        </script>";
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['status' => 'error', 'message' => 'An error occurred: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
