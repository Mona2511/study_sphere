<?php
if (!isset($page_title)) { $page_title = "StudySphere"; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($page_title); ?> | StudySphere</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<header class="navbar">
    <div class="container nav-inner">
        <a href="index.php" class="logo">StudySphere</a>
        <nav>
            <?php if (isset($_SESSION['user_id'])) { ?>
                <a href="dashboard.php">Dashboard</a>
                <a href="subjects.php">Subjects</a>
                <a href="notes.php">Notes</a>
                <a href="tasks.php">Tasks</a>
                <a href="logout.php" class="logout">Logout (<?php echo e($_SESSION['user_name']); ?>)</a>
            <?php } else { ?>
                <a href="login.php">Login</a>
                <a href="register.php">Register</a>
            <?php } ?>
        </nav>
    </div>
</header>

<main class="container">
<?php
if (isset($_SESSION['msg'])) {
    echo '<div class="alert ' . $_SESSION['msg_type'] . '">' . e($_SESSION['msg']) . '</div>';
    unset($_SESSION['msg']);
    unset($_SESSION['msg_type']);
}
?>
