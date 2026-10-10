<?php
require_once 'config.php';
require_once 'includes/auth_check.php';

// Search + filter (same logic as before)
$search = isset($_GET['search']) ? trim($_GET['search']) : "";
$subject_filter = isset($_GET['subject']) ? (int)$_GET['subject'] : 0;
$like = "%" . $search . "%";

// subject_filter = 0 means "all subjects"
$sql = "SELECT n.id, n.title, n.content, n.created_at, s.subject_name
        FROM notes n JOIN subjects s ON n.subject_id = s.id
        WHERE n.user_id = ?
        AND (? = 0 OR n.subject_id = ?)
        AND (n.title LIKE ? OR n.content LIKE ?)
        ORDER BY n.created_at DESC";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "iiiss", $user_id, $subject_filter, $subject_filter, $like, $like);
mysqli_stmt_execute($stmt);
$notes = mysqli_stmt_get_result($stmt);

$subjects = get_subjects($conn, $user_id);

// true when the student searched or picked a subject (used for the empty message)
$filtering = ($search != "" || $subject_filter != 0);

$page_title = "Study Materials";
$active = "notes";
require_once 'includes/layout_top.php';
?>

<div class="page-head">
    <div>
        <h1>Study Materials</h1>
        <p>Your notes, organised by subject.</p>
    </div>
    <a href="add_note.php" class="btn">+ Add note</a>
</div>

<section class="card">
    <form method="get" class="inline-form" style="margin-top:0">
        <input type="text" name="search" placeholder="Search your notes..." value="<?php echo e($search); ?>">
        <select name="subject">
            <option value="0">All subjects</option>
            <?php foreach ($subjects as $s) { ?>
                <option value="<?php echo $s['id']; ?>" <?php if ($subject_filter == $s['id']) echo "selected"; ?>>
                    <?php echo e($s['subject_name']); ?>
                </option>
            <?php } ?>
        </select>
        <button type="submit" class="btn">Search</button>
        <a href="notes.php" class="btn btn-light">Reset</a>
    </form>
</section>

<?php if (mysqli_num_rows($notes) == 0) { ?>
    <p class="empty">
        <?php if ($filtering) { ?>
            No notes match your search. Try a different word or subject.
        <?php } else { ?>
            No notes yet. Write your first one when you're ready.
        <?php } ?>
    </p>
<?php } else { ?>
    <div class="note-grid">
    <?php while ($n = mysqli_fetch_assoc($notes)) {
        $is_long = (strlen($n['content']) > 180);   // show "Read more" only for long notes
    ?>
        <div class="note-card">
            <h3><?php echo e($n['title']); ?></h3>
            <div class="note-meta">
                <span class="tag tag-plain"><?php echo e($n['subject_name']); ?></span>
                <span class="note-date"><?php echo date("d M Y", strtotime($n['created_at'])); ?></span>
            </div>

            <p class="note-body <?php if ($is_long) echo 'clamp'; ?>"><?php echo nl2br(e($n['content'])); ?></p>
            <?php if ($is_long) { ?>
                <button type="button" class="read-more">Read more</button>
            <?php } ?>

            <div class="card-actions">
                <a href="edit_note.php?id=<?php echo $n['id']; ?>" class="btn-small">Edit</a>
                <form method="post" action="delete_note.php" class="delete-form"
                      onsubmit="return confirm('Delete this note?');">
                    <input type="hidden" name="id" value="<?php echo $n['id']; ?>">
                    <button type="submit" class="btn-small btn-danger">Delete</button>
                </form>
            </div>
        </div>
    <?php } ?>
    </div>
<?php } ?>

<?php require_once 'includes/layout_bottom.php'; ?>
