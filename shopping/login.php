<?php
include 'db.php';
include 'user_log.php';
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];

        //log
        if($_SESSION['role']=='user'){
            $user_id = $_SESSION['user_id'];

    log_user($pdo, $user_id, 'User logged in');
        }
        header("Location: dashboard.php");
    } else {
        echo"<script>
        alert('Inavlid Username Or Password');
         
    </script>";
    }
    
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        /* General Body Styling */
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
            overflow: hidden; /* Hide any overflow to avoid scrollbars */
        }

        /* Form Container Styling */
        .form-container {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            width: 100%;
            max-width: 400px; /* Adjust to fit within viewport */
            box-sizing: border-box; /* Include padding in width calculation */
        }

        /* Form Element Styling */
        form {
            display: flex;
            flex-direction: column;
            gap: 15px; /* Space between elements */
        }

        input[type="text"],
        input[type="password"] {
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 10px;
            font-size: 1em;
            box-sizing: border-box; /* Include padding in width calculation */
        }

        input[type="submit"] {
            background-color: #007bff;
            border: none;
            color: #fff;
            padding: 10px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1em;
        }

        input[type="submit"]:hover {
            background-color: #0056b3;
        }

        input[type="reset"] {
            background-color: #007bff;
            border: none;
            color: #fff;
            padding: 10px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1em;
        }

        input[type="reset"]:hover {
            background-color: #0056b3;
        }

        /* Error Message Styling */
        .error-message {
            color: #d9534f; /* Bootstrap danger color */
            font-size: 0.9em;
            margin-top: 10px;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h1><center>Login and shop</h1>
    <form method="POST">
        
        <input type="text" name="username" placeholder="Username" required><br>
        <input type="password" name="password" placeholder="Password" required><br>
        <input type="submit" value="Login">
        <input type="reset" onclick="window.location.href='home.php';" value ="Cancel">
        
    
        </form>
</div>

</body>
</html>