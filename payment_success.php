<?php
session_start();
include 'db.php';
include 'protect_user.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id   = (int)$_SESSION['user_id'];
$paymentId = isset($_GET['payment_id']) ? $_GET['payment_id'] : '';
$orderId   = isset($_GET['order_id'])   ? $_GET['order_id']   : '';

if ($paymentId === '' || $orderId === '') {
    die("Invalid payment callback.");
}

if (empty($_SESSION['pending_order']) ||
    $_SESSION['pending_order']['razorpay_order_id'] !== $orderId) {
    die("Order session expired or not found.");
}

$amount      = (float)$_SESSION['pending_order']['amount'];
$addressData = $_SESSION['pending_order']['address_data'];

// Build address string from saved form data
$fullName = mysqli_real_escape_string($conn, $addressData['fullname'] ?? '');
$addr     = mysqli_real_escape_string($conn, $addressData['address']  ?? '');
$city     = mysqli_real_escape_string($conn, $addressData['city']     ?? '');
$state    = mysqli_real_escape_string($conn, $addressData['state']    ?? '');
$pincode  = mysqli_real_escape_string($conn, $addressData['pincode']  ?? '');
$phone    = mysqli_real_escape_string($conn, $addressData['phone']    ?? '');
$notes    = mysqli_real_escape_string($conn, $addressData['notes']    ?? '');

$fullAddress = trim("$addr, $city, $state - $pincode");

// 1) Insert into orders table (adapt columns/table name as per your DB)
$sqlOrder = "INSERT INTO orders (user_id, total_amount, payment_method, payment_status,
                                 payment_id, razorpay_order_id, fullname, address,
                                 phone, notes, created_at)
             VALUES ($user_id, $amount, 'Razorpay', 'Paid',
                     '$paymentId', '$orderId', '$fullName', '$fullAddress',
                     '$phone', '$notes', NOW())";
mysqli_query($conn, $sqlOrder);
$newOrderId = mysqli_insert_id($conn);

// 2) Move cart items to order_items table (if you have one)
$cartSql = "SELECT c.quantity, c.size, p.id AS product_id, p.price
            FROM cart c
            JOIN products p ON c.product_id = p.id
            WHERE c.user_id = $user_id";
$cartRes = mysqli_query($conn, $cartSql);
while ($row = mysqli_fetch_assoc($cartRes)) {
    $pid  = (int)$row['product_id'];
    $quantity  = (int)$row['quantity'];
    $size = mysqli_real_escape_string($conn, $row['size']);
    $price = (float)$row['price'];

    $sqlItem = "INSERT INTO order_items (order_id, product_id, quantity, size, price)
                VALUES ($newOrderId, $pid, $quantity, '$size', $price)";
    mysqli_query($conn, $sqlItem);
}

// 3) Clear cart
mysqli_query($conn, "DELETE FROM cart WHERE user_id = $user_id");

// 4) Clear pending order session
unset($_SESSION['pending_order']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Success - CLOTHZONE</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family:'Poppins',sans-serif;
            margin:0;
            background:#f4f4f4;
            display:flex;
            align-items:center;
            justify-content:center;
            height:100vh;
        }
        .box {
            background:#fff;
            padding:30px 32px;
            border-radius:14px;
            box-shadow:0 10px 30px rgba(0,0,0,0.08);
            max-width:420px;
            text-align:center;
        }
        h1 {
            margin:0 0 10px;
            font-size:24px;
        }
        p {
            margin:4px 0;
            color:#555;
            font-size:14px;
        }
        .btn {
            margin-top:18px;
            display:inline-block;
            padding:10px 20px;
            border-radius:999px;
            background:#000;
            color:#fff;
            text-decoration:none;
            font-weight:600;
            font-size:14px;
        }
        .btn:hover { background:#333; }
    </style>
</head>
<body>
<div class="box">
    <h1>Payment successful</h1>
    <p>Thank you, your order has been placed.</p>
    <p><strong>Order ID:</strong> #<?php echo htmlspecialchars($newOrderId); ?></p>
    <p><strong>Payment ID:</strong> <?php echo htmlspecialchars($paymentId); ?></p>
    <a class="btn" href="my_orders.php">View my orders</a>
</div>
</body>
</html>
