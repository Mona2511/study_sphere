<?php
// includes/layout_top.php
// Shared top bar + sidebar for the redesigned pages (dashboard, subjects, notes, tasks).

// ICON SIZE in pixels - change this one number to make the logo bigger or smaller
$logo_size = 24;
//
// Before including this file, a page must set:
//   $page_title  -> text for the browser tab
//   $active      -> "dashboard", "subjects", "notes" or "tasks" (highlights the sidebar link)

$full_name  = $_SESSION['user_name'];
$first_name = explode(" ", trim($full_name))[0];
$initial    = strtoupper(substr($full_name, 0, 1));

// Returns "active" for the link of the current page
function nav_class($name, $active) {
    return ($name == $active) ? "active" : "";
}

// Flash message from set_msg() in config.php - show once, then remove
$flash = "";
$flash_type = "success";
if (isset($_SESSION['msg'])) {
    $flash = $_SESSION['msg'];
    $flash_type = $_SESSION['msg_type'];
    unset($_SESSION['msg'], $_SESSION['msg_type']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo e($page_title); ?> | StudySphere</title>

    <!-- Apply the saved theme before the page is drawn, so it does not flash white -->
    <script>
        try {
            if (localStorage.getItem("theme") === "dark") {
                document.documentElement.setAttribute("data-theme", "dark");
            }
        } catch (err) {}
    </script>

    <link rel="icon" type="image/png" href="images/favicon.png">
    <link rel="stylesheet" href="css/app.css?v=<?php echo filemtime(__DIR__ . '/../css/app.css'); ?>">
</head>
<body>

<!-- ======== TOP BAR ======== -->
<header class="topbar">
    <button class="menu-btn" id="menuBtn" aria-label="Open menu">&#9776;</button>

    <a href="dashboard.php" class="brand">
        <img src="images/logo.png?v=2" class="logo-img" alt="StudySphere logo"
             width="<?php echo $logo_size; ?>" height="<?php echo $logo_size; ?>"
             style="width: <?php echo $logo_size; ?>px; height: <?php echo $logo_size; ?>px;">
        <span class="brand-name">StudySphere</span>
    </a>

    <div class="top-right">
        <button class="theme-btn" id="themeBtn" aria-label="Switch between light and dark theme" title="Switch theme">&#9790;</button>
        <span class="profile-name"><?php echo e($full_name); ?></span>
        <span class="avatar"><?php echo e($initial); ?></span>
    </div>
</header>

<div class="layout">

    <!-- ======== SIDEBAR ======== -->
    <aside class="sidebar" id="sidebar">
        <nav>
            <a href="dashboard.php" class="<?php echo nav_class('dashboard', $active); ?>"><span class="ico">&#8962;</span> Dashboard</a>
            <a href="subjects.php" class="<?php echo nav_class('subjects', $active); ?>"><span class="ico">&#9636;</span> My Subjects</a>
            <a href="notes.php" class="<?php echo nav_class('notes', $active); ?>"><span class="ico">&#9998;</span> Study Materials</a>
            <a href="tasks.php" class="<?php echo nav_class('tasks', $active); ?>"><span class="ico">&#9745;</span> To-Do List</a>
            <a href="dashboard.php#progress"><span class="ico">&#9684;</span> Progress</a>
        </nav>
        <a href="logout.php" class="logout"><span class="ico">&#9211;</span> Logout</a>
    </aside>
    <div class="overlay" id="overlay"></div>

    <!-- ======== MAIN CONTENT (closed in layout_bottom.php) ======== -->
    <main class="content">

        <?php if ($flash != "") { ?>
            <div class="alert <?php echo ($flash_type == 'error') ? 'error' : 'success'; ?>">
                <?php echo e($flash); ?>
            </div>
        <?php } ?>
