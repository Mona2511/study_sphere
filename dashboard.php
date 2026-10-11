<?php
// dashboard.php - main page after login
require_once 'config.php';
require_once 'includes/auth_check.php';

// Use Indian time for the greeting and for "today" (change this if you live elsewhere)
date_default_timezone_set('Asia/Kolkata');
$today = date("Y-m-d");

// ---------- 1. Counts ----------
function count_rows($conn, $sql, $user_id) {
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    return $row['total'];
}

$total_subjects = count_rows($conn, "SELECT COUNT(*) AS total FROM subjects WHERE user_id = ?", $user_id);
$total_notes    = count_rows($conn, "SELECT COUNT(*) AS total FROM notes WHERE user_id = ?", $user_id);
$pending_tasks  = count_rows($conn, "SELECT COUNT(*) AS total FROM tasks WHERE user_id = ? AND status = 'Pending'", $user_id);
$done_tasks     = count_rows($conn, "SELECT COUNT(*) AS total FROM tasks WHERE user_id = ? AND status = 'Completed'", $user_id);
// overdue = pending tasks whose due date is before today
$stmt = mysqli_prepare($conn, "SELECT COUNT(*) AS total FROM tasks WHERE user_id = ? AND status = 'Pending' AND due_date < ?");
mysqli_stmt_bind_param($stmt, "is", $user_id, $today);
mysqli_stmt_execute($stmt);
$overdue_tasks = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))['total'];

$all_tasks = $pending_tasks + $done_tasks;
$percent   = ($all_tasks > 0) ? round(($done_tasks / $all_tasks) * 100) : 0;

// ---------- 2. Next 6 pending tasks ----------
$stmt = mysqli_prepare($conn, "SELECT t.id, t.title, t.due_date, s.subject_name
                               FROM tasks t LEFT JOIN subjects s ON t.subject_id = s.id
                               WHERE t.user_id = ? AND t.status = 'Pending'
                               ORDER BY t.due_date ASC LIMIT 6");
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$upcoming = mysqli_stmt_get_result($stmt);

// ---------- 3. Recent activity (latest notes + latest tasks) ----------
$stmt = mysqli_prepare($conn, "(SELECT 'note' AS kind, title, created_at FROM notes WHERE user_id = ?)
                               UNION ALL
                               (SELECT 'task' AS kind, title, created_at FROM tasks WHERE user_id = ?)
                               ORDER BY created_at DESC LIMIT 6");
mysqli_stmt_bind_param($stmt, "ii", $user_id, $user_id);
mysqli_stmt_execute($stmt);
$activity = mysqli_stmt_get_result($stmt);

// ---------- 4. Task progress for each subject ----------
$stmt = mysqli_prepare($conn, "SELECT s.subject_name, COUNT(t.id) AS total, SUM(t.status = 'Completed') AS done
                               FROM subjects s JOIN tasks t ON t.subject_id = s.id
                               WHERE s.user_id = ?
                               GROUP BY s.id
                               ORDER BY s.subject_name LIMIT 5");
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$subject_progress = mysqli_stmt_get_result($stmt);

// ---------- 5. Greeting details ----------
$hour = (int)date("G");   // current hour, 0 to 23

if ($hour >= 5 && $hour < 12)       { $greeting = "Good morning"; }     // 5 am to 11:59 am
elseif ($hour >= 12 && $hour < 17)  { $greeting = "Good afternoon"; }   // 12 pm to 4:59 pm
elseif ($hour >= 17 && $hour < 21)  { $greeting = "Good evening"; }     // 5 pm to 8:59 pm
else                                { $greeting = "Good night"; }       // 9 pm to 4:59 am

$date_text = date("l, d F");

if ($all_tasks == 0) {
    $motivation = "A fresh start! Add your first task and plan your week.";
} elseif ($pending_tasks == 0) {
    $motivation = "Everything is done. Nice work, take a short break!";
} elseif ($overdue_tasks > 0) {
    $motivation = "Some tasks are running late. Pick the smallest one and finish it first.";
} elseif ($pending_tasks == 1) {
    $motivation = "Just one task left. Your next task is waiting.";
} else {
    $motivation = "You have " . $pending_tasks . " tasks to go. One small step at a time.";
}

// ---------- Shared top bar + sidebar ----------
$page_title = "Dashboard";
$active = "dashboard";
require_once 'includes/layout_top.php';   // also gives us $first_name
?>

<!-- Welcome banner -->
<section class="welcome">
    <div class="welcome-text">
        <p class="date"><?php echo $date_text; ?></p>
        <h1><?php echo $greeting; ?>, <?php echo e($first_name); ?>!</h1>
        <p class="motivation"><?php echo $motivation; ?></p>
        <div class="welcome-buttons">
            <a href="tasks.php" class="btn btn-white">Add a task</a>
            <a href="add_note.php" class="btn btn-outline">Write a note</a>
        </div>
    </div>
</section>

<!-- Quick access cards (real counts from the database) -->
<section class="quick">
    <a href="subjects.php" class="quick-card">
        <span class="quick-icon">&#9636;</span>
        <span class="quick-number"><?php echo $total_subjects; ?></span>
        <span class="quick-title">My Subjects</span>
        <span class="quick-text">Manage your subjects</span>
    </a>
    <a href="notes.php" class="quick-card">
        <span class="quick-icon">&#9998;</span>
        <span class="quick-number"><?php echo $total_notes; ?></span>
        <span class="quick-title">Notes &amp; Resources</span>
        <span class="quick-text">Read or write notes</span>
    </a>
    <a href="tasks.php?status=Pending" class="quick-card">
        <span class="quick-icon">&#9745;</span>
        <span class="quick-number"><?php echo $pending_tasks; ?></span>
        <span class="quick-title">Upcoming Tasks</span>
        <span class="quick-text">
            <?php echo ($overdue_tasks > 0) ? $overdue_tasks . " overdue" : "Nothing overdue"; ?>
        </span>
    </a>
    <a href="tasks.php?status=Completed" class="quick-card">
        <span class="quick-icon">&#10003;</span>
        <span class="quick-number"><?php echo $done_tasks; ?></span>
        <span class="quick-title">Completed Tasks</span>
        <span class="quick-text">See what you finished</span>
    </a>
</section>

<div class="two-col">

    <!-- Tasks checklist -->
    <section class="card">
        <h2>Your tasks</h2>
        <p class="card-sub">Tick a task when you finish it.</p>

        <?php if (mysqli_num_rows($upcoming) == 0) { ?>
            <p class="empty">No tasks added yet. Add one when you're ready.</p>
        <?php } else { ?>
            <ul class="checklist">
            <?php while ($t = mysqli_fetch_assoc($upcoming)) {
                if ($t['due_date'] < $today)      { $label = "Overdue";   $label_class = "tag-red"; }
                elseif ($t['due_date'] == $today) { $label = "Due today"; $label_class = "tag-yellow"; }
                else { $label = date("d M", strtotime($t['due_date'])); $label_class = "tag-plain"; }
            ?>
                <li>
                    <form method="post" action="update_task.php">
                        <input type="hidden" name="id" value="<?php echo $t['id']; ?>">
                        <input type="checkbox" class="task-check" title="Mark as done">
                    </form>
                    <div class="task-text">
                        <span class="task-title"><?php echo e($t['title']); ?></span>
                        <span class="task-subject"><?php echo $t['subject_name'] ? e($t['subject_name']) : 'No subject'; ?></span>
                    </div>
                    <span class="tag <?php echo $label_class; ?>"><?php echo $label; ?></span>
                </li>
            <?php } ?>
            </ul>
        <?php } ?>

        <a href="tasks.php" class="link">View all tasks</a>
    </section>

    <!-- Recent activity -->
    <section class="card">
        <h2>Recent activity</h2>
        <p class="card-sub">What you added lately.</p>

        <?php if (mysqli_num_rows($activity) == 0) { ?>
            <p class="empty">Nothing here yet. Your notes and tasks will show up as you add them.</p>
        <?php } else { ?>
            <ul class="activity">
            <?php while ($a = mysqli_fetch_assoc($activity)) { ?>
                <li>
                    <span class="act-icon"><?php echo ($a['kind'] == 'note') ? '&#9998;' : '&#9745;'; ?></span>
                    <div>
                        <span class="act-title">
                            <?php echo ($a['kind'] == 'note') ? 'New note: ' : 'New task: '; ?><?php echo e($a['title']); ?>
                        </span>
                        <span class="act-date"><?php echo date("d M Y", strtotime($a['created_at'])); ?></span>
                    </div>
                </li>
            <?php } ?>
            </ul>
        <?php } ?>

        <a href="notes.php" class="link">View all notes</a>
    </section>
</div>

<!-- Progress -->
<section class="card" id="progress">
    <h2>Your progress</h2>

    <?php if ($all_tasks == 0) { ?>
        <p class="empty">Progress appears here once you add some tasks.</p>
    <?php } else { ?>
        <div class="progress-top">
            <div class="ring" style="--p: <?php echo $percent; ?>"><span><?php echo $percent; ?>%</span></div>
            <p class="progress-text">
                You've finished <strong><?php echo $done_tasks; ?></strong> of
                <strong><?php echo $all_tasks; ?></strong> tasks.<br>
                <?php echo ($percent >= 50) ? "You're making progress, keep going." : "Every task you finish counts."; ?>
            </p>
        </div>

        <?php if (mysqli_num_rows($subject_progress) > 0) { ?>
            <h3>By subject</h3>
            <?php while ($p = mysqli_fetch_assoc($subject_progress)) {
                $sub_percent = round(($p['done'] / $p['total']) * 100);
            ?>
                <div class="subject-row">
                    <div class="subject-info">
                        <span><?php echo e($p['subject_name']); ?></span>
                        <span class="muted"><?php echo $p['done']; ?>/<?php echo $p['total']; ?> tasks</span>
                    </div>
                    <div class="bar"><div class="bar-fill" style="width: <?php echo $sub_percent; ?>%"></div></div>
                </div>
            <?php } ?>
        <?php } ?>
    <?php } ?>
</section>

<?php require_once 'includes/layout_bottom.php'; ?>
