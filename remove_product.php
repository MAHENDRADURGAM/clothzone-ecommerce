<?php
include 'db.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $sql = "DELETE FROM products WHERE id = $id";
    mysqli_query($conn, $sql);

    header("Location: products_list.php");
    exit();
}
?>
