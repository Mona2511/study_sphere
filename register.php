<?php
require_once 'config.php';

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    if ($name == "" || $email == "" || $password == "") {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    } elseif ($password != $confirm) {
        $error = "Passwords do not match.";
    } else {
        // check whether the email is already registered
        $stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ?");
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        if (mysqli_stmt_num_rows($stmt) > 0) {
            $error = "This email is already registered.";
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = mysqli_prepare($conn, "INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "sss", $name, $email, $hashed);
            mysqli_stmt_execute($stmt);

            set_msg("Registration successful. Please login.");
            header("Location: login.php");
            exit();
        }
    }
}

$page_title = "Register";
require_once 'includes/auth_top.php';
?>

<h1>Create your account</h1>
<p class="auth-sub">It takes a minute. Then add your subjects and start planning.</p>

<?php if ($error != "") { echo '<div class="alert error">' . e($error) . '</div>'; } ?>

<form method="post" id="registerForm">
    <div class="field">
        <label for="name">Full name</label>
        <input type="text" name="name" id="name" placeholder="Your name"
               value="<?php echo isset($name) ? e($name) : ''; ?>">
    </div>

    <div class="field">
        <label for="email">Email</label>
        <input type="text" name="email" id="email" placeholder="you@example.com"
               value="<?php echo isset($email) ? e($email) : ''; ?>">
    </div>

    <div class="field">
        <label for="password">Password</label>
        <input type="password" name="password" id="password" class="pass-field" placeholder="At least 6 characters">
    </div>

    <div class="field">
        <label for="confirm_password">Confirm password</label>
        <input type="password" name="confirm_password" id="confirm_password" class="pass-field" placeholder="Type it again">
    </div>

    <label class="show-pass"><input type="checkbox" id="showPass"> Show passwords</label>

    <button type="submit" class="btn">Create account</button>
</form>

<p class="auth-switch">Already have an account? <a href="login.php">Login here</a></p>

<?php require_once 'includes/auth_bottom.php'; ?>
