<?php
// config.php - database connection + small helper functions
// Included at the top of every page.

session_start();

$conn = mysqli_connect("localhost", "root", "", "studysphere");

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Escapes output so that user input cannot run as HTML/JS (prevents XSS)
function e($text) {
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

// Stores a message that is shown once on the next page
function set_msg($text, $type = "success") {
    $_SESSION['msg'] = $text;
    $_SESSION['msg_type'] = $type;
}

// Returns all subjects of a user (used in dropdowns)
function get_subjects($conn, $user_id) {
    $stmt = mysqli_prepare($conn, "SELECT id, subject_name FROM subjects WHERE user_id = ? ORDER BY subject_name");
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $list = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $list[] = $row;
    }
    return $list;
}
?>
