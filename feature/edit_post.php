<?php
require __DIR__ . '/../db/db.php';

session_start();

if (!isset($_GET['post_id'])) {
    die("Post ID not provided.");
}

if (!isset($_SESSION['user_id'])) {
    die("User not logged in.");
}

$post_id = $conn->real_escape_string($_GET['post_id']);
$current_user_id = $_SESSION['user_id'];

$sql = "SELECT post_id, post_title, post_description, post_city, created_at 
        FROM post 
        WHERE post_id = $post_id AND user_id = $current_user_id";
$result = $conn->query($sql);

if ($result === false) {
    die("Error executing query: " . $conn->error);
}

$post = $result->fetch_assoc();

if (!$post) {
    die("Post not found or you do not have permission to edit this post.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete'])) {
        $conn->begin_transaction();
        try {
   
            $delete_attendees_sql = "DELETE FROM attendee WHERE post_id = $post_id";
            if ($conn->query($delete_attendees_sql) !== true) {
                throw new Exception("Error deleting attendees: " . $conn->error);
            }

            $delete_comments_sql = "DELETE FROM post_comment WHERE post_id = $post_id";
            if ($conn->query($delete_comments_sql) !== true) {
                throw new Exception("Error deleting comments: " . $conn->error);
            }

         
            $delete_post_sql = "DELETE FROM post WHERE post_id = $post_id AND user_id = $current_user_id";
            if ($conn->query($delete_post_sql) !== true) {
                throw new Exception("Error deleting post: " . $conn->error);
            }

 
            $conn->commit();
            header("Location: ../dashboard/maincontent.php");
            exit;
        } catch (Exception $e) {
            $conn->rollback();
            echo $e->getMessage();
        }
    } else {
        $post_title = $conn->real_escape_string($_POST['post_title']);
        $post_description = $conn->real_escape_string($_POST['post_description']);
        $post_city = $conn->real_escape_string($_POST['post_city']);

        $update_sql = "UPDATE post 
                       SET post_title = '$post_title', post_description = '$post_description', post_city = '$post_city' 
                       WHERE post_id = $post_id AND user_id = $current_user_id";

        if ($conn->query($update_sql) === true) {
            header("Location: ../dashboard/postpage.php?post_id=$post_id");
            exit;
        } else {
            echo "Error updating post: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Post</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/styles.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
</head>
<body>
    <div class="container mt-4">
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Edit Post</h5>
                <form method="POST">
                    <div class="form-group">
                        <label for="post_title">Title</label>
                        <input type="text" class="form-control" id="post_title" name="post_title" value="<?= htmlspecialchars($post['post_title']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="post_description">Description</label>
                        <textarea class="form-control" id="post_description" name="post_description" rows="5" required><?= htmlspecialchars($post['post_description']) ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="post_city">City</label>
                        <input type="text" class="form-control" id="post_city" name="post_city" value="<?= htmlspecialchars($post['post_city']) ?>" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                    <a href="../dashboard/postpage.php?post_id=<?= $post_id ?>" class="btn btn-secondary">Cancel</a>
                    <button type="submit" name="delete" class="btn btn-danger">Delete Post</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>

<?php
$conn->close();
?>
