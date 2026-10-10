<?php
require_once 'config.php';
require_once 'includes/auth_check.php';

// Toggles a task between Pending and Completed
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $id = (int)$_POST['id'];

    $stmt = mysqli_prepare($conn, "UPDATE tasks
                                   SET status = IF(status = 'Pending', 'Completed', 'Pending')
                                   WHERE id = ? AND user_id = ?");
    mysqli_stmt_bind_param($stmt, "ii", $id, $user_id);
    mysqli_stmt_execute($stmt);
}
header("Location: tasks.php");
exit();
?>
