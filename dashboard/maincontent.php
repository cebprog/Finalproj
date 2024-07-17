<?php
require __DIR__ . '/../db/db.php';
include 'header.php';

$sql = "SELECT p.post_id, p.post_title, p.post_description, p.post_city, p.created_at, u.username, p.user_id 
        FROM post p
        JOIN users u ON p.user_id = u.user_id
        ORDER BY p.created_at DESC";
$result = $conn->query($sql);

if ($result === false) {
    die("Error executing query: " . $conn->error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['post_id'])) {
    $post_id = $conn->real_escape_string($_POST['post_id']);
    $user_id = 1;

    $check_sql = "SELECT * FROM attendee WHERE post_id = $post_id AND user_id = $user_id";
    $check_result = $conn->query($check_sql);

    if ($check_result->num_rows == 0) {
        $insert_sql = "INSERT INTO attendee (user_id, post_id) VALUES ($user_id, $post_id)";
        $conn->query($insert_sql);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Main Content</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/styles.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <div class="row">
        <div class="col-lg-8">
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="card mb-4">
                        <div class="card-body">
                            <h2 class="card-title"><a class="nav-link" href="/finalproj/dashboard/postpage.php?post_id=<?= $row['post_id'] ?>"><?= htmlspecialchars($row['post_title']) ?></a></h2>
                            <p class="card-text"><strong>Created by: </strong><?= htmlspecialchars($row['username']) ?></p>
                            <p class="card-text"><strong>Created at: </strong><?= htmlspecialchars($row['created_at']) ?></p>
                            <p class="card-text"><strong>Venue: </strong><?= htmlspecialchars($row['post_city']) ?></p>
                            <label><strong>Description: </strong></label>
                            <p class="card-text"><?= nl2br(htmlspecialchars($row['post_description'])) ?></p>

                            <p><strong>Attendees:</strong></p>
                            <ul>
                                <?php
                                $attendees_sql = "SELECT u.username 
                                                  FROM attendee a
                                                  JOIN users u ON a.user_id = u.user_id
                                                  WHERE a.post_id = " . $row['post_id'];
                                $attendees_result = $conn->query($attendees_sql);
                                while ($attendee = $attendees_result->fetch_assoc()):
                                ?>
                                    <li><?= htmlspecialchars($attendee['username']) ?></li>
                                <?php endwhile; ?>
                            </ul>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No posts found.</p>
            <?php endif; ?>
        </div>
        <div class="col-lg-4">
            <?php include 'sidebar.php'; ?>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
</body>
</html>

<?php
$conn->close();
?>
