<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

require '../db/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (!isset($_SESSION['user_id'])) {
        die("User not logged in: Session user_id not set");
    }

    $title = $conn->real_escape_string($_POST['title']);
    $city = $conn->real_escape_string($_POST['city']);
    $description = $conn->real_escape_string($_POST['description']);
    $user_id = $_SESSION['user_id'];
    $attendee_id = isset($_POST['attendee_id']) ? $conn->real_escape_string($_POST['attendee_id']) : NULL;

  
    if ($attendee_id) {
        $sql = "INSERT INTO post (user_id, post_title, post_city, post_description, attendee_id, created_at) VALUES ('$user_id', '$title', '$city', '$description', '$attendee_id', NOW())";
    } else {
        $sql = "INSERT INTO post (user_id, post_title, post_city, post_description, created_at) VALUES ('$user_id', '$title', '$city', '$description', NOW())";
    }

    if ($conn->query($sql) === TRUE) {
        header("Location: ../index.php");
        exit;
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close(); 
}
?>
