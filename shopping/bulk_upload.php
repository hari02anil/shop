


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bulk Upload</title>
    <style>
        body {
            background-image: url('shop img.jpg');
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }
        .container {
            text-align: center;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 80%;
            max-width: 600px;
        }
        h1 {
            color: #333;
            margin-bottom: 20px;
        }
        a {
            text-decoration: none;
            align-self: baseline;
        }
        button {
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            margin: 10px;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: #0056b3;
        }
        button:focus {
            outline: none;
        }
        form {
            margin: 20px 0;
        }
        label {
            display: block;
            margin-bottom: 10px;
            font-size: 16px;
            color: #333;
        }
        input[type="file"] {
            margin: 10px 0;
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


    </style>
</head>
<body>

<?php
include 'db.php';
session_start();


if ($_SESSION['role'] !== 'admin') {
    die("Access denied.");
}
?>
    <div class="container">
        <h1>Bulk Upload</h1>
        <a href="sample_products.xlsx" download>
            <button>Download Sample Excel Sheet</button>
        </a>
        <form action="bulk_up.php" method="post" enctype="multipart/form-data">
            <label for="file">Upload Excel File:</label>
            <input type="file" name="file" id="file" accept=".xlsx, .xls" required>
            <button type="submit">Upload</button>
        </form>
        
        </a>
    </div>
    <a href="dashboard.php"><button class="back-button">Back to Dashboard</button></a>
</body>
</html>


