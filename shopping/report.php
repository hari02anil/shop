<?php
include 'db.php';
session_start();

if ($_SESSION['role'] !== 'admin') {
    die("Access denied.");
}
// Fetch all users
$stmt = $pdo->prepare("SELECT id, name FROM users WHERE role = 'user'");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Download Reports</title>
    <style>
        body {
            background-image: url('shop img.jpg');
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background-color: #fff;
            padding: 20px 40px;
            border-radius: 10px;
            box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            width: 100%;
            text-align: center;
        }

        h1 {
            color: #333;
            font-size: 24px;
            margin-bottom: 20px;
        }

        h2 {
            color: #555;
            font-size: 18px;
            margin-bottom: 15px;
        }

        label {
            font-size: 16px;
            color: #555;
            margin-bottom: 10px;
            display: block;
        }

        select {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }

        button {
            background-color: #007bff;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 10px;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #0056b3;
        }

        .report-section {
            margin-bottom: 30px;
        }

        .report-section:last-child {
            margin-bottom: 0;
        }

        .back-button {
            background-color: #6c757d;
            
        }

        .back-button:hover {
            background-color: #5a6268;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Download Reports</h1>

        <div class="report-section">
            <h2>All Orders Report</h2>
            <a href="download_report.php"><button>Download All Orders Report</button></a>
        </div>

        <div class="report-section">
            <h2>User-wise Report</h2>
            <form action="user_report.php" method="POST">
                <label for="user_id">Select User:</label>
                <select name="user_id" id="user_id" required>
                    <option value="">Select User</option>
                    <?php foreach ($users as $user): ?>
                        <option value="<?php echo htmlspecialchars($user['id']); ?>">
                            <?php echo htmlspecialchars($user['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" name = "action" value ="report">Download User Order Report</button>
                <button type ="submit"name = "action" value ="log">Download User Log</button>
            </form>
           
        </div>

        <div>
            <a href="dashboard.php"><button class="back-button">Back to Dashboard</button></a>
        </div>
    </div>
</body>
</html>
