<?php
require __DIR__ . '/../db/db.php';

if (isset($_SESSION['user_id'])) {
    $username = $_SESSION['username'];
} elseif (isset($_COOKIE['user_id']) && isset($_COOKIE['username'])) {
    $_SESSION['user_id'] = $_COOKIE['user_id'];
    $_SESSION['username'] = $_COOKIE['username'];
    $username = $_COOKIE['username'];
} else {
    header("Location: ../feature/loginpage.php");
    exit;
}
?>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">ShowEvents</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="/finalproj/dashboard/maincontent.php">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="/finalproj/feature/logout.php">Logout</a>
        </li>
      </ul>
      <span class="navbar-text">
        Hello, <?php echo htmlspecialchars($username); ?>!
      </span>
    </div>
  </div>
</nav>
