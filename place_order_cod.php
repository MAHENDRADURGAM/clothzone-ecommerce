<?php
session_start();
include 'protect_user.php';
include 'db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Not logged in']);
    exit();
}

$user_id  = (int)$_SESSION['user_id'];

$fullname = mysqli_real_escape_string($conn, $_POST['fullname'] ?? '');
$address  = mysqli_real_escape_string($conn, $_POST['address']  ?? '');
$city     = mysqli_real_escape_string($conn, $_POST['city']     ?? '');
$state    = mysqli_real_escape_string($conn, $_POST['state']    ?? '');
$pincode  = mysqli_real_escape_string($conn, $_POST['pincode']  ?? '');
$phone    = mysqli_real_escape_string($conn, $_POST['phone']    ?? '');
$notes    = mysqli_real_escape_string($conn, $_POST['notes']    ?? '');

if ($fullname === '' || $address === '' || $phone === '') {
    echo json_encode(['success' => false, 'error' => 'Missing required fields']);
    exit();
}

// get cart items
$sql = "SELECT c.id AS cart_id, c.product_id, c.quantity, p.price
        FROM cart c
        JOIN products p ON c.product_id = p.id
        WHERE c.user_id = $user_id";
$res = mysqli_query($conn, $sql);

if (!$res || mysqli_num_rows($res) == 0) {
    echo json_encode(['success' => false, 'error' => 'Cart is empty']);
    exit();
}

// calculate total
$total = 0;
$items = [];
while ($row = mysqli_fetch_assoc($res)) {
    $items[] = $row;
    $total += $row['price'] * $row['quantity'];
}

// insert order (same columns as place_order.php)
$status = 'Pending';
$insertOrder = "
    INSERT INTO orders 
        (user_id, total_amount, status, fullname, address, city, state, pincode, phone, notes)
    VALUES 
        ($user_id, $total, '$status', '$fullname', '$address', '$city', '$state', '$pincode', '$phone', '$notes')
";

mysqli_query($conn, $insertOrder);
$order_id = mysqli_insert_id($conn);

// insert order items
foreach ($items as $it) {
    $pid   = (int)$it['product_id'];
    $quantity   = (int)$it['quantity'];
    $price = $it['price'];

    $insertItem = "
        INSERT INTO order_items (order_id, product_id, quantity, price)
        VALUES ($order_id, $pid, $quantity, $price)
    ";
    mysqli_query($conn, $insertItem);
}

// clear cart
mysqli_query($conn, "DELETE FROM cart WHERE user_id = $user_id");

// success JSON
echo json_encode(['success' => true, 'order_id' => $order_id]);
