<?php
include 'db.php'; 

if (isset($_POST['username'])) {
    $username = $_POST['username'];
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $username_exists = $stmt->fetchColumn();

    if ($username_exists) {
        echo json_encode(['status' => 'error', 'message' => 'Username already exists']);
        exit();
    } else {
        echo json_encode(['status' => 'success', 'message' => '']);
        exit();
    }
}

if (isset($_POST['email'])) {
    $email = $_POST['email'];
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $email_exists = $stmt->fetchColumn();

    if ($email_exists) {
        echo json_encode(['status' => 'error', 'message' => 'Email already exists']);
        exit();
    } else {
        echo json_encode(['status' => 'success', 'message' => '']);
        exit();
    }
}
?>
