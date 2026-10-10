<?php
require_once 'config.php';
require_once 'includes/auth_check.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// fetch the subject (only if it belongs to this user)
$stmt = mysqli_prepare($conn, "SELECT * FROM subjects WHERE id = ? AND user_id = ?");
mysqli_stmt_bind_param($stmt, "ii", $id, $user_id);
mysqli_stmt_execute($stmt);
$subject = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$subject) {
    set_msg("Subject not found.", "error");
    header("Location: subjects.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $subject_name = trim($_POST['subject_name']);
    $teacher = trim($_POST['teacher']);

    if ($subject_name == "") {
        set_msg("Subject name is required.", "error");
        header("Location: edit_subject.php?id=" . $id);
        exit();
    }

    $stmt = mysqli_prepare($conn, "UPDATE subjects SET subject_name = ?, teacher = ? WHERE id = ? AND user_id = ?");
    mysqli_stmt_bind_param($stmt, "ssii", $subject_name, $teacher, $id, $user_id);
    mysqli_stmt_execute($stmt);

    set_msg("Subject updated.");
    header("Location: subjects.php");
    exit();
}

$page_title = "Edit Subject";
$active = "subjects";
require_once 'includes/layout_top.php';
?>

<div class="page-head">
    <div>
        <h1>Edit subject</h1>
        <p>Change the subject name or the teacher.</p>
    </div>
</div>

<section class="card form-card">
    <form method="post">
        <div class="field">
            <label for="subject_name">Subject name</label>
            <input type="text" name="subject_name" id="subject_name" value="<?php echo e($subject['subject_name']); ?>">
        </div>

        <div class="field">
            <label for="teacher">Teacher</label>
            <input type="text" name="teacher" id="teacher" placeholder="Optional"
                   value="<?php echo e($subject['teacher']); ?>">
        </div>

        <div class="form-actions">
            <button type="submit" class="btn">Save changes</button>
            <a href="subjects.php" class="btn btn-light">Cancel</a>
        </div>
    </form>
</section>

<?php require_once 'includes/layout_bottom.php'; ?>
