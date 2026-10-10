<?php
require_once 'config.php';

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if ($email == "" || $password == "") {
        $error = "Please enter email and password.";
    } else {
        $stmt = mysqli_prepare($conn, "SELECT id, name, password FROM users WHERE email = ?");
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Invalid email or password.";
        }
    }
}

$page_title = "Login";
require_once 'includes/auth_top.php';
?>

<h1>Welcome back</h1>
<p class="auth-sub">Log in to pick up where you left off.</p>

<?php if ($error != "") { echo '<div class="alert error">' . e($error) . '</div>'; } ?>

<form method="post" id="loginForm">
    <div class="field">
        <label for="email">Email</label>
        <input type="text" name="email" id="email" placeholder="you@example.com"
               value="<?php echo isset($email) ? e($email) : ''; ?>">
    </div>

    <div class="field">
        <label for="password">Password</label>
        <input type="password" name="password" id="password" class="pass-field" placeholder="Your password">
    </div>

    <label class="show-pass"><input type="checkbox" id="showPass"> Show password</label>

    <button type="submit" class="btn">Login</button>
</form>

<p class="auth-switch">New here? <a href="register.php">Create an account</a></p>

<?php require_once 'includes/auth_bottom.php'; ?>
