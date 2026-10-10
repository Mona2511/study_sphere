<?php
require_once 'config.php';

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

$page_title = "Home";
require_once 'includes/header.php';
?>

<section class="hero">
    <h1>StudySphere</h1>
    <p>A simple place for students to manage subjects, notes and assignments.</p>
    <a href="register.php" class="btn">Get Started</a>
    <a href="login.php" class="btn btn-light">Login</a>
</section>

<section class="grid three">
    <div class="card">
        <h3>Subjects</h3>
        <p>Add all your subjects along with the teacher's name.</p>
    </div>
    <div class="card">
        <h3>Notes</h3>
        <p>Write notes subject-wise and search them whenever you need.</p>
    </div>
    <div class="card">
        <h3>Tasks</h3>
        <p>Keep track of assignments and deadlines and mark them as done.</p>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
