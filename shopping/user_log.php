<?php
include 'db.php';
function log_user($pdo, $user_id, $action, $details = null) {
    $stmt = $pdo->prepare("
        INSERT INTO log (user_id, action, details)
        VALUES (:user_id, :action, :details)
    ");
    $stmt->execute([
        ':user_id' => $user_id,
        ':action' => $action,
        ':details' => $details
    ]);
}
?>