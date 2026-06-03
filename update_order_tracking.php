<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

$order_id = (int)($_POST['order_id'] ?? 0);
$tracking = mysqli_real_escape_string($conn, $_POST['tracking_number'] ?? '');
$sref     = mysqli_real_escape_string($conn, $_POST['supplier_order_ref'] ?? '');

if ($order_id > 0) {
    mysqli_query($conn,"
        UPDATE orders
        SET tracking_number = '$tracking',
            supplier_order_ref = '$sref'
        WHERE id = $order_id
    ");
}

header("Location: admin_orders.php");
exit();
