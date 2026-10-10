<?php
// includes/auth_top.php
// Top part of the login and register pages (logged-out pages, so no sidebar).

// ICON SIZE in pixels - change this one number to make the logo bigger or smaller
$logo_size = 32;
// Before including this file, the page sets:  $page_title

// Flash message from set_msg() (for example "Registration successful. Please login.")
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

    <!-- Apply the saved theme before the page is drawn -->
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

<button class="theme-btn auth-theme" id="themeBtn" aria-label="Switch between light and dark theme" title="Switch theme">&#9790;</button>

<div class="auth-page">

    <a href="index.php" class="brand auth-brand">
        <img src="images/logo.png?v=2" class="logo-img" alt="StudySphere logo"
             width="<?php echo $logo_size; ?>" height="<?php echo $logo_size; ?>"
             style="width: <?php echo $logo_size; ?>px; height: <?php echo $logo_size; ?>px;">
        <span class="brand-name">StudySphere</span>
    </a>

    <div class="auth-card">
        <?php if ($flash != "") { ?>
            <div class="alert <?php echo ($flash_type == 'error') ? 'error' : 'success'; ?>">
                <?php echo e($flash); ?>
            </div>
        <?php } ?>
