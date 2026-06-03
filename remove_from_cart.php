<?php
session_start();
include 'db.php';
include 'protect_user.php';

if (!isset($_GET['id'])) {
    die("Missing item ID");
}

$id = (int)$_GET['id'];

mysqli_query($conn, "DELETE FROM cart WHERE id=$id");

header("Location: cart.php");
exit();
?>
