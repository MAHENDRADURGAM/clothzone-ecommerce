<?php
if (session_status() === PHP_SESSION_NONE) session_start();
include 'db.php';

// Check user login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Product id must come from GET (your form uses GET)
if (!isset($_GET['id'])) {
    die("Product ID missing");
}

$product_id = (int) $_GET['id'];
$user_id    = (int) $_SESSION['user_id'];

// Quantity also comes from GET hidden field "quantity"
$quantity = isset($_GET['quantity']) ? (int) $_GET['quantity'] : 1;
if ($quantity < 1) {
    $quantity = 1;
}

// Check product exists
$check = mysqli_query(
    $conn,
    "SELECT id, name, price, category 
     FROM products 
     WHERE id = $product_id"
);
if (mysqli_num_rows($check) === 0) {
    die("Invalid product ID");
}
$product = mysqli_fetch_assoc($check);

// Check if product already in cart
$check_cart = mysqli_query(
    $conn,
    "SELECT id, quantity 
     FROM cart 
     WHERE product_id = $product_id AND user_id = $user_id"
);

if (mysqli_num_rows($check_cart) > 0) {
    // Item exists → increase quantity by selected amount
    mysqli_query(
        $conn,
        "UPDATE cart
         SET quantity = quantity + $quantity
         WHERE product_id = $product_id AND user_id = $user_id"
    );
} else {
    // Insert new cart row with selected quantity
    mysqli_query(
        $conn,
        "INSERT INTO cart (user_id, product_id, quantity)
         VALUES ($user_id, $product_id, $quantity)"
    );
}

// Optional tracking for cart.php
$_SESSION['ga_add_to_cart'] = [
    'id'       => $product['id'],
    'name'     => $product['name'],
    'category' => $product['category'],
    'price'    => (float) $product['price']
];

// Redirect to cart
header("Location: cart.php");
exit();
?>
