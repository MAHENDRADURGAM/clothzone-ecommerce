<?php
session_start();
include 'db.php';

// must be logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if (!isset($_GET['id'])) {
    die('Product ID missing');
}

$user_id    = (int)$_SESSION['user_id'];
$product_id = (int)$_GET['id'];

// check product exists
$check = mysqli_query($conn, "SELECT id FROM products WHERE id = $product_id");
if (mysqli_num_rows($check) === 0) {
    die('Invalid product ID');
}

// check if already in wishlist
$wq = mysqli_query(
    $conn,
    "SELECT id FROM wishlist WHERE user_id = $user_id AND product_id = $product_id"
);

if (mysqli_num_rows($wq) === 0) {
    mysqli_query(
        $conn,
        "INSERT INTO wishlist (user_id, product_id) VALUES ($user_id, $product_id)"
    );
}

// redirect back to product page or wishlist
header("Location: wishlist.php");
exit();
