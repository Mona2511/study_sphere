<?php
require_once 'config.php';
require_once 'includes/auth_check.php';

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $id = (int)$_POST['id'];

    $stmt = mysqli_prepare($conn, "DELETE FROM notes WHERE id = ? AND user_id = ?");
    mysqli_stmt_bind_param($stmt, "ii", $id, $user_id);
    mysqli_stmt_execute($stmt);

    set_msg("Note deleted.");
}
header("Location: notes.php");
exit();
?>
