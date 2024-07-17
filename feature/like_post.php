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


$sql = "UPDATE post SET likes = likes + 1 WHERE post_id = ?";
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die("Error preparing the statement: " . $conn->error);
}

$stmt->bind_param("i", $post_id);
if ($stmt->execute() === false) {
    die("Error executing the query: " . $stmt->error);
}

$stmt->close();

$sql = "SELECT likes FROM post WHERE post_id = ?";
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die("Error preparing the statement: " . $conn->error);
}

$stmt->bind_param("i", $post_id);
if ($stmt->execute() === false) {
    die("Error executing the query: " . $stmt->error);
}

$result = $stmt->get_result();
if ($result === false) {
    die("Error getting the result: " . $stmt->error);
}

$post = $result->fetch_assoc();
$stmt->close();
$conn->close();

echo $post['likes'];
?>
