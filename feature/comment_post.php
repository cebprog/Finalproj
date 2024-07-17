<?php
require __DIR__ . '/../db/db.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    die("User not logged in.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $post_id = $conn->real_escape_string($_POST['post_id']);
    $user_id = $_SESSION['user_id'];
    $comment = $conn->real_escape_string($_POST['comment']);

    $sql = "INSERT INTO post_comment (post_id, user_id, comment) VALUES ('$post_id', '$user_id', '$comment')";
    
    if ($conn->query($sql) === TRUE) {
        $response = [
            'username' => $_SESSION['username'],
            'comment' => $comment,
            'created_at' => date('Y-m-d H:i:s')
        ];
        echo json_encode($response);
    } else {
        http_response_code(500);
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>
