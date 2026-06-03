<?php
session_start();
include 'db.php';

// use same key as admin_login.php
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

if (!isset($_GET['id']) || !ctype_digit($_GET['id'])) {
    die("Product ID not found.");
}

$id = (int)$_GET['id'];

// safer delete with prepared statement
$stmt = mysqli_prepare($conn, "DELETE FROM products WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

header("Location: products_list.php");
exit();
?>
