<?php
include 'db.php';
session_start();
use PHPMailer\PHPMailer\PHPMailer;

if ($_SESSION['role'] !== 'admin') {
    die("Access denied.");
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $textbox = $_POST['textbox'];
    $textarea = $_POST['textarea'];



// use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';

// require 'path/to/PHPMailer/src/Exception.php';
// require 'path/to/PHPMailer/src/PHPMailer.php';
// require 'path/to/PHPMailer/src/SMTP.php';

$mail = new PHPMailer(true);

try {
    // Server settings
    $mail->isSMTP();
    $mail->Host       = 'smtp-relay.brevo.com'; // Mailtrap SMTP server
    $mail->SMTPAuth   = true;
    $mail->Username   = '7aea6a001@smtp-brevo.com'; // Replace with Mailtrap username
    $mail->Password   = 'v9VtEmaUFQRk5bYJ'; // Replace with Mailtrap password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Enable TLS encryption; or use `PHPMailer::ENCRYPTION_SMTPS` for SSL
    $mail->Port       = 2525; // Mailtrap's port (can be 2525, 587, or 465)

    $stmt = $pdo->query("SELECT email, name FROM users WHERE NOT role ='admin'");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);


    // Recipients
    $mail->setFrom('teat22745@gmail.com', 'ShoppingCart');

    foreach ($users as $user) {
        $mail->clearAddresses(); // Clear previous recipient to avoid stacking recipients
        $mail->addAddress($user['email'], $user['name']); // Add a new recipient

        // Email subject and body
        $mail->Subject = $textbox;
        $mail->Body    = '<p>Hello ' . htmlspecialchars($user['name']) . ',</p><p>' . $textarea .'</p>';
        // $mail->AltBody = 'Hello ' . $user['name'] . ', Your message here.'; // Plain text for non-HTML mail clients

        // Send the email
        if ($mail->send()) {
            $flag = 1;
        } else {
            $flag = 0;
        }
    }if ($flag == 1){
        echo json_encode("Mail sent to all users");
    }else{
        echo json_encode("Error sending the mail");
    }

} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
}
?>

