<?php
session_start();

if (isset($_SESSION['user_id'])) {
    // User logout
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit();
}

// If admin tries to access this logout page by mistake
header("Location: login.php");
exit();
?>
