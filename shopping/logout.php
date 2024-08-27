<?php
include 'user_log.php';
session_start();
$user_id = $_SESSION['user_id'];
   if($_SESSION['role']==='user'){
    log_user($pdo, $user_id, 'User logged out');
   }
session_unset();
session_destroy();
header("Location: home.php");
exit();
?>
