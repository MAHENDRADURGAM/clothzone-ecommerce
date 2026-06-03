<?php
session_start();
include 'db.php';

// validate id
if (!isset($_GET['id']) || !ctype_digit($_GET['id'])) {
    header("Location: wishlist.php");
    exit();
}

$id = (int)$_GET['id'];

// secure delete
$stmt = mysqli_prepare($conn, "DELETE FROM wishlist WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

// redirect back
header("Location: wishlist.php");
exit();
?>
