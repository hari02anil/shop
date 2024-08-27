<?php
include 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$n = $_SESSION['user_id'];
// Fetch user details
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$n]);
$user = $stmt->fetch();

?>
<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <style>
        body {
            background-image: url('shop img.jpg');
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            
            width: 50%;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
        }
        .profile-section {
            display: flex;
            flex-direction: column;
            align-items: flex-start; /* Align items to the right */
            margin-bottom: 20px;
        }
        .profile-pic {
            border-radius: 50%;
            width: 100px;
            height: 100px;
            object-fit: cover;
            margin-bottom: 10px; /* Space between the picture and details */
        }
        .profile-details {
            text-align: left; /* Align text to the right */
        }
        ul {
            list-style-type: none;
            padding: 0;
        }
        ul li {
            margin: 10px 0;
        }
        a {
            text-decoration: none;
        }
        button {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1><?= htmlspecialchars($user['name']) ?></h1>

        <div class="profile-section">
           
                <img src="<?= htmlspecialchars($user['profile_photo']) ?>" alt="Profile Picture" class="profile-pic">
            
                <?php if ($user['role'] === 'user'): ?>
            <div class="profile-details">
                
                    <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
                
                
                    <p><strong>Phone:</strong> <?= htmlspecialchars($user['phone']) ?></p>
                
                
                    <p><strong>Date of Birth:</strong> <?= htmlspecialchars($user['dob']) ?></p>
                
               
                    <p><strong>Gender:</strong> <?= htmlspecialchars($user['gender']) ?></p>
                
            </div>
            <?php endif; ?>
        </div>

        <?php if ($user['role'] === 'admin'): ?>
            
            <ul><center>
                <li><a href="product_adding.php"><button>Add Product</button></a></li>
                <li><a href="edit_products.php"><button>Edit Products</button></a></li>
                <li><a href="report.php"><button>Download Report</button></a></li>
                <li><a href="newad.php"><button>Advertisement Mail</button></a></li>
                
            </ul></center>
        <?php else: ?>
            
            <ul><center>
                <li><a href="products.php"><button>Browse Products</button></a></li>
                <li><a href="cart.php"><button>View Cart / Checkout</button></a></li>
                <li><a href="orders.php"><button>View my orders</button></a></li>
            </ul></center>
        <?php endif; ?>

        <br><br>
        <a href="logout.php"><button>Logout</button></a>
    </div>
    
</body>
</html>


