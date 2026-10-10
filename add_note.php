<?php
require_once 'config.php';
require_once 'includes/auth_check.php';

$subjects = get_subjects($conn, $user_id);

if (count($subjects) == 0) {
    set_msg("Please add a subject before adding notes.", "error");
    header("Location: subjects.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $subject_id = (int)$_POST['subject_id'];
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);

    if ($title == "" || $content == "" || $subject_id == 0) {
        set_msg("All fields are required.", "error");
    } else {
        $stmt = mysqli_prepare($conn, "INSERT INTO notes (user_id, subject_id, title, content) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "iiss", $user_id, $subject_id, $title, $content);
        mysqli_stmt_execute($stmt);
        set_msg("Note added successfully.");
        header("Location: notes.php");
        exit();
    }
}

$page_title = "Add Note";
$active = "notes";
require_once 'includes/layout_top.php';
?>

<div class="page-head">
    <div>
        <h1>Add a note</h1>
        <p>Write it down now, find it easily later.</p>
    </div>
</div>

<section class="card form-card">
    <form method="post">
        <div class="field">
            <label for="subject_id">Subject</label>
            <select name="subject_id" id="subject_id">
                <option value="0">-- Select subject --</option>
                <?php foreach ($subjects as $s) { ?>
                    <option value="<?php echo $s['id']; ?>"><?php echo e($s['subject_name']); ?></option>
                <?php } ?>
            </select>
        </div>

        <div class="field">
            <label for="title">Title</label>
            <input type="text" name="title" id="title" placeholder="e.g. Unit 2 summary">
        </div>

        <div class="field">
            <label for="content">Content</label>
            <textarea name="content" id="content" rows="9" placeholder="Write your note here..."></textarea>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn">Save note</button>
            <a href="notes.php" class="btn btn-light">Cancel</a>
        </div>
    </form>
</section>

<?php require_once 'includes/layout_bottom.php'; ?>
