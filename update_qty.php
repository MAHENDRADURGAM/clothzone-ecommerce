<?php
session_start();
include 'db.php';
include 'protect_user.php';

if (!isset($_GET['id'])) {
    die("Cart ID missing");
}

$cart_id = (int)$_GET['id'];
$action  = $_GET['act'] ?? "";
$newquantity  = (int)($_GET['quantity'] ?? 1);

if ($newquantity < 1) $newquantity = 1;

// Get current cart row
$cart = mysqli_query($conn, "SELECT quantity FROM cart WHERE id=$cart_id LIMIT 1");
if (mysqli_num_rows($cart) == 0) {
    die("Cart not found");
}

$row = mysqli_fetch_assoc($cart);
$currentquantity = (int)$row['quantity'];


// Detect + / -
if ($action === "plus") {
    $newquantity = $currentquantity + 1;
} elseif ($action === "minus") {
    // if current is 1 and user clicks minus → delete row
    if ($currentquantity <= 1) {
        mysqli_query($conn, "DELETE FROM cart WHERE id=$cart_id");
        header("Location: cart.php");
        exit();
    } else {
        $newquantity = $currentquantity - 1;
    }
}

// Update final quantity (only if not deleted)
mysqli_query($conn, "UPDATE cart SET quantity=$newquantity WHERE id=$cart_id");

header("Location: cart.php");
exit();
?>
