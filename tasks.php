<?php
require_once 'config.php';
require_once 'includes/auth_check.php';

// Same timezone as the dashboard, so "Overdue" is decided by Indian date
date_default_timezone_set('Asia/Kolkata');

// Add new task (same logic as before)
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $title = trim($_POST['title']);
    $due_date = $_POST['due_date'];
    $subject_id = ($_POST['subject_id'] != "") ? (int)$_POST['subject_id'] : null;

    if ($title == "" || $due_date == "") {
        set_msg("Task title and due date are required.", "error");
    } else {
        $stmt = mysqli_prepare($conn, "INSERT INTO tasks (user_id, subject_id, title, due_date) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "iiss", $user_id, $subject_id, $title, $due_date);
        mysqli_stmt_execute($stmt);
        set_msg("Task added successfully.");
    }
    header("Location: tasks.php");
    exit();
}

// Filter by status (empty = show all)
$status = isset($_GET['status']) ? $_GET['status'] : "";
if ($status != "Pending" && $status != "Completed") {
    $status = "";
}

$sql = "SELECT t.id, t.title, t.due_date, t.status, s.subject_name
        FROM tasks t LEFT JOIN subjects s ON t.subject_id = s.id
        WHERE t.user_id = ? AND (? = '' OR t.status = ?)
        ORDER BY t.status ASC, t.due_date ASC";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "iss", $user_id, $status, $status);
mysqli_stmt_execute($stmt);
$tasks = mysqli_stmt_get_result($stmt);

$subjects = get_subjects($conn, $user_id);
$today = date("Y-m-d");

$page_title = "To-Do List";
$active = "tasks";
require_once 'includes/layout_top.php';
?>

<div class="page-head">
    <div>
        <h1>To-Do List</h1>
        <p>Add your assignments and tick them off as you finish.</p>
    </div>
</div>

<section class="card">
    <h2>Add a task</h2>
    <form method="post" class="inline-form" id="taskForm">
        <input type="text" name="title" placeholder="Task title">
        <select name="subject_id">
            <option value="">No subject</option>
            <?php foreach ($subjects as $s) { ?>
                <option value="<?php echo $s['id']; ?>"><?php echo e($s['subject_name']); ?></option>
            <?php } ?>
        </select>
        <input type="date" name="due_date">
        <button type="submit" class="btn">Add task</button>
    </form>
</section>

<section class="card">
    <div class="tabs">
        <a href="tasks.php" class="<?php if ($status == '') echo 'active'; ?>">All</a>
        <a href="tasks.php?status=Pending" class="<?php if ($status == 'Pending') echo 'active'; ?>">Pending</a>
        <a href="tasks.php?status=Completed" class="<?php if ($status == 'Completed') echo 'active'; ?>">Completed</a>
    </div>

    <?php if (mysqli_num_rows($tasks) == 0) { ?>
        <p class="empty">
            <?php if ($status == 'Completed') { ?>
                Nothing completed yet. Finish a task and it will show up here.
            <?php } elseif ($status == 'Pending') { ?>
                No pending tasks. You're all caught up!
            <?php } else { ?>
                No tasks added yet. Add one when you're ready.
            <?php } ?>
        </p>
    <?php } else { ?>
        <ul class="checklist">
        <?php while ($t = mysqli_fetch_assoc($tasks)) {
            $done    = ($t['status'] == 'Completed');
            $overdue = (!$done && $t['due_date'] < $today);
        ?>
            <li class="<?php if ($done) echo 'done'; ?> <?php if ($overdue) echo 'overdue'; ?>">
                <!-- The checkbox submits to update_task.php, which switches Pending <-> Completed -->
                <form method="post" action="update_task.php">
                    <input type="hidden" name="id" value="<?php echo $t['id']; ?>">
                    <input type="checkbox" class="task-check"
                           title="<?php echo $done ? 'Mark as pending' : 'Mark as done'; ?>"
                           <?php if ($done) echo 'checked'; ?>>
                </form>

                <div class="task-text">
                    <span class="task-title"><?php echo e($t['title']); ?></span>
                    <span class="task-subject"><?php echo $t['subject_name'] ? e($t['subject_name']) : 'No subject'; ?></span>
                </div>

                <span class="due"><?php echo date("d M Y", strtotime($t['due_date'])); ?></span>
                <?php if ($overdue) { ?><span class="tag tag-red">Overdue</span><?php } ?>
                <span class="tag <?php echo $done ? 'tag-green' : 'tag-yellow'; ?>"><?php echo $t['status']; ?></span>

                <form method="post" action="delete_task.php" class="delete-form"
                      onsubmit="return confirm('Delete this task?');">
                    <input type="hidden" name="id" value="<?php echo $t['id']; ?>">
                    <button type="submit" class="btn-small btn-danger">Delete</button>
                </form>
            </li>
        <?php } ?>
        </ul>
    <?php } ?>
</section>

<?php require_once 'includes/layout_bottom.php'; ?>
