<?php
require __DIR__ . '/../db/db.php';

session_start();

if (!isset($_POST['post_id'])) {
    die("Post ID not provided.");
}

if (!isset($_SESSION['user_id'])) {
    die("User not logged in.");
}

$post_id = $conn->real_escape_string($_POST['post_id']);
$current_user_id = $_SESSION['user_id'];


$check_sql = "SELECT * FROM attendee WHERE post_id = $post_id AND user_id = $current_user_id";
$check_result = $conn->query($check_sql);

if ($check_result->num_rows == 0) {

    $attend_sql = "INSERT INTO attendee (post_id, user_id) VALUES ($post_id, $current_user_id)";
    if ($conn->query($attend_sql) === false) {
        die("Error executing query: " . $conn->error);
    }
}


$user_sql = "SELECT username FROM users WHERE user_id = $current_user_id";
$user_result = $conn->query($user_sql);
$user = $user_result->fetch_assoc();

echo htmlspecialchars($user['username']);

$conn->close();
?>
