<?php
require_once 'config.php';
require_once 'includes/auth_check.php';

// Add new subject (same logic as before)
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $subject_name = trim($_POST['subject_name']);
    $teacher = trim($_POST['teacher']);

    if ($subject_name == "") {
        set_msg("Subject name is required.", "error");
    } else {
        $stmt = mysqli_prepare($conn, "INSERT INTO subjects (user_id, subject_name, teacher) VALUES (?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "iss", $user_id, $subject_name, $teacher);
        mysqli_stmt_execute($stmt);
        set_msg("Subject added successfully.");
    }
    header("Location: subjects.php");
    exit();
}

// Get subjects with number of notes in each (same query as before)
$stmt = mysqli_prepare($conn, "SELECT s.id, s.subject_name, s.teacher, COUNT(n.id) AS note_count
                               FROM subjects s LEFT JOIN notes n ON n.subject_id = s.id
                               WHERE s.user_id = ?
                               GROUP BY s.id
                               ORDER BY s.subject_name");
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$subjects = mysqli_stmt_get_result($stmt);

$page_title = "My Subjects";
$active = "subjects";
require_once 'includes/layout_top.php';
?>

<div class="page-head">
    <div>
        <h1>My Subjects</h1>
        <p>All the subjects you're studying, with the notes you've written for each.</p>
    </div>
</div>

<section class="card">
    <h2>Add a subject</h2>
    <form method="post" class="inline-form" id="subjectForm">
        <input type="text" name="subject_name" placeholder="Subject name (e.g. Web Technology)">
        <input type="text" name="teacher" placeholder="Teacher name (optional)">
        <button type="submit" class="btn">Add subject</button>
    </form>
</section>

<?php if (mysqli_num_rows($subjects) == 0) { ?>
    <p class="empty">No subjects added yet. Add your first one above when you're ready.</p>
<?php } else { ?>
    <div class="subject-grid">
    <?php while ($s = mysqli_fetch_assoc($subjects)) { ?>
        <div class="subject-card">
            <div class="subject-top">
                <span class="s-badge"><?php echo e(strtoupper(substr($s['subject_name'], 0, 1))); ?></span>
                <div>
                    <h3><?php echo e($s['subject_name']); ?></h3>
                    <p class="muted"><?php echo $s['teacher'] ? e($s['teacher']) : 'No teacher added'; ?></p>
                </div>
            </div>

            <!-- Opens the notes page already filtered to this subject -->
            <a href="notes.php?subject=<?php echo $s['id']; ?>" class="note-count">
                <?php echo $s['note_count']; ?> <?php echo ($s['note_count'] == 1) ? 'note' : 'notes'; ?>
            </a>

            <div class="card-actions">
                <a href="edit_subject.php?id=<?php echo $s['id']; ?>" class="btn-small">Edit</a>
                <form method="post" action="delete_subject.php" class="delete-form"
                      onsubmit="return confirm('Delete this subject? Its notes will also be deleted.');">
                    <input type="hidden" name="id" value="<?php echo $s['id']; ?>">
                    <button type="submit" class="btn-small btn-danger">Delete</button>
                </form>
            </div>
        </div>
    <?php } ?>
    </div>
<?php } ?>

<?php require_once 'includes/layout_bottom.php'; ?>
