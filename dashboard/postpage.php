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

$sql = "SELECT p.post_id, p.post_title, p.post_description, p.post_city, p.created_at, u.username, p.user_id, p.likes
        FROM post p
        JOIN users u ON p.user_id = u.user_id
        WHERE p.post_id = $post_id";
$result = $conn->query($sql);

if ($result === false) {
    die("Error executing query: " . $conn->error);
}

$post = $result->fetch_assoc();

if (!$post) {
    die("Post not found.");
}


$check_like_sql = "SELECT * FROM post WHERE post_id = $post_id AND user_id = $current_user_id";
$check_like_result = $conn->query($check_like_sql);
$liked = $check_like_result->num_rows > 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($post['post_title']) ?></title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/styles.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
</head>
<body>
    <div class="container mt-4">
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title"><?= htmlspecialchars($post['post_title']) ?></h5>
                <p class="card-text"><strong>Posted by:</strong> <?= htmlspecialchars($post['username']) ?></p>
                <p class="card-text"><strong>Created at:</strong> <?= htmlspecialchars($post['created_at']) ?></p>
                <p class="card-text"><strong>City:</strong> <?= htmlspecialchars($post['post_city']) ?></p>
                <label><strong>Description: </strong></label>
                <p class="card-text"><?= nl2br(htmlspecialchars($post['post_description'])) ?></p>
                

                <div class="mb-3">
                    <button id="like-button" class="btn btn-primary"><?= $liked ? 'Liked' : 'Like' ?></button>
                    <span id="like-count"><?= htmlspecialchars($post['likes']) ?> Likes</span>
                </div>

                <h6><strong>Attendees:</strong></h6>
                <ul class="list-group list-group-flush" id="attendees-list">
                    <?php
                    $attendees_sql = "SELECT u.username 
                                      FROM attendee a
                                      JOIN users u ON a.user_id = u.user_id
                                      WHERE a.post_id = " . $post['post_id'];
                    $attendees_result = $conn->query($attendees_sql);
                    while ($attendee = $attendees_result->fetch_assoc()):
                    ?>
                        <li class="list-group-item"><?= htmlspecialchars($attendee['username']) ?></li>
                    <?php endwhile; ?>
                </ul>

                <?php if ($current_user_id == $post['user_id']): ?>
                    <a href="../feature/edit_post.php?post_id=<?= $post_id ?>" class="btn btn-primary">Edit</a>
                <?php else: ?>
                    <form id="attend-form" method="post">
                        <input type="hidden" name="post_id" value="<?= $post_id ?>">
                        <button type="submit" class="btn btn-success">Attend</button>
                    </form>
                <?php endif; ?>


                <h6 class="mt-4"><strong>Comments:</strong></h6>
                <ul class="list-group list-group-flush" id="comments-list">
                    <?php
                    $comments_sql = "SELECT c.comment, u.username, c.created_at 
                                     FROM post_comment c
                                     JOIN users u ON c.user_id = u.user_id
                                     WHERE c.post_id = " . $post['post_id'] . " ORDER BY c.created_at ASC";
                    $comments_result = $conn->query($comments_sql);
                    while ($comment = $comments_result->fetch_assoc()):
                    ?>
                        <li class="list-group-item">
                            <strong><?= htmlspecialchars($comment['username']) ?>:</strong>
                            <span><?= htmlspecialchars($comment['comment']) ?></span>
                            <br><small><?= htmlspecialchars($comment['created_at']) ?></small>
                        </li>
                    <?php endwhile; ?>
                </ul>


                <form id="comment-form" method="post" class="mt-4">
                    <input type="hidden" name="post_id" value="<?= $post_id ?>">
                    <div class="form-group">
                        <textarea name="comment" class="form-control" rows="3" placeholder="Write a comment..." required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Submit Comment</button>
                </form>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            $('#attend-form').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    type: 'POST',
                    url: '../feature/attend_post.php',
                    data: $(this).serialize(),
                    success: function(response) {
                        $('#attendees-list').append('<li class="list-group-item">' + response + '</li>');
                    },
                    error: function() {
                        alert('An error occurred while processing your request.');
                    }
                });
            });

            $('#comment-form').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    type: 'POST',
                    url: '../feature/comment_post.php',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(response) {
                        $('#comments-list').append('<li class="list-group-item"><strong>' + response.username + ':</strong> <span>' + response.comment + '</span><br><small>' + response.created_at + '</small></li>');
                        $('#comment-form')[0].reset();
                    },
                    error: function() {
                        alert('An error occurred while submitting your comment.');
                    }
                });
            });

            $('#like-button').on('click', function() {
                var button = $(this);
                $.ajax({
                    type: 'POST',
                    url: '../feature/like_post.php',
                    data: { post_id: '<?= $post_id ?>' },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'liked') {
                            $('#like-count').text(response.likes + ' Likes');
                            button.text('Liked');
                        } else if (response.status === 'already_liked') {
                            alert('You have already liked this post.');
                        }
                    },
                    error: function() {
                       
                        alert('An error occurred while processing your request.');
                    }
                });
            });
        });
    </script>
</body>
</html>

<?php
$conn->close();
?>
