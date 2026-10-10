<?php
require_once 'config.php';
require_once 'includes/auth_check.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = mysqli_prepare($conn, "SELECT * FROM notes WHERE id = ? AND user_id = ?");
mysqli_stmt_bind_param($stmt, "ii", $id, $user_id);
mysqli_stmt_execute($stmt);
$note = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$note) {
    set_msg("Note not found.", "error");
    header("Location: notes.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $subject_id = (int)$_POST['subject_id'];
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);

    if ($title == "" || $content == "" || $subject_id == 0) {
        set_msg("All fields are required.", "error");
        header("Location: edit_note.php?id=" . $id);
        exit();
    }

    $stmt = mysqli_prepare($conn, "UPDATE notes SET subject_id = ?, title = ?, content = ? WHERE id = ? AND user_id = ?");
    mysqli_stmt_bind_param($stmt, "issii", $subject_id, $title, $content, $id, $user_id);
    mysqli_stmt_execute($stmt);

    set_msg("Note updated.");
    header("Location: notes.php");
    exit();
}

$subjects = get_subjects($conn, $user_id);

$page_title = "Edit Note";
$active = "notes";
require_once 'includes/layout_top.php';
?>

<div class="page-head">
    <div>
        <h1>Edit note</h1>
        <p>Update your note and save it.</p>
    </div>
</div>

<section class="card form-card">
    <form method="post">
        <div class="field">
            <label for="subject_id">Subject</label>
            <select name="subject_id" id="subject_id">
                <?php foreach ($subjects as $s) { ?>
                    <option value="<?php echo $s['id']; ?>" <?php if ($s['id'] == $note['subject_id']) echo "selected"; ?>>
                        <?php echo e($s['subject_name']); ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <div class="field">
            <label for="title">Title</label>
            <input type="text" name="title" id="title" value="<?php echo e($note['title']); ?>">
        </div>

        <div class="field">
            <label for="content">Content</label>
            <textarea name="content" id="content" rows="9"><?php echo e($note['content']); ?></textarea>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn">Save changes</button>
            <a href="notes.php" class="btn btn-light">Cancel</a>
        </div>
    </form>
</section>

<?php require_once 'includes/layout_bottom.php'; ?>
