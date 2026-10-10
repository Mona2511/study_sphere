<?php
// Put this at the top of every page that needs a logged-in user.
if (!isset($_SESSION['user_id'])) {
    set_msg("Please login first.", "error");
    header("Location: login.php");
    exit();
}
$user_id = $_SESSION['user_id'];
?>
