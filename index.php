<?php
session_start();

if (!isset($_SESSION['user_id']) && isset($_COOKIE['user_id']) && isset($_COOKIE['username'])) {
    $_SESSION['user_id'] = $_COOKIE['user_id'];
    $_SESSION['username'] = $_COOKIE['username'];
}

if (!isset($_SESSION['user_id'])) {
    header("Location: feature/loginpage.php");
    exit;
}

include 'dashboard/maincontent.php';
?>
</body>
</html>
