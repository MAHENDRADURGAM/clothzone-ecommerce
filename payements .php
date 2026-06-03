<?php
session_start();
include 'db.php';
include 'protect_user.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit();
}

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);
if (!is_array($data)) {
    echo json_encode(['success' => false, 'error' => 'Invalid JSON body']);
    exit();
}

if (empty($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Not logged in']);
    exit();
}
$user_id = (int)$_SESSION['user_id'];

$sql = "SELECT SUM(c.quantity * p.price) AS total
        FROM cart c
        JOIN products p ON c.product_id = p.id
        WHERE c.user_id = $user_id";
$res = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($res);
$cart_total = (float)($row['total'] ?? 0);

if ($cart_total <= 0) {
    echo json_encode(['success' => false, 'error' => 'Empty cart']);
    exit();
}

$razorpay_order = [
    'receipt'  => 'order_' . time(),
    'amount'   => (int)round($cart_total * 100),
    'currency' => 'INR'
];

$key_id     = 'rzp_test_Rn6PemBdCkmhja';
$key_secret = 'KChBb1YYNtjom8dXFw1yOj1w';

$ch = curl_init('https://api.razorpay.com/v1/orders');
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($razorpay_order));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERPWD, "$key_id:$key_secret");
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 200 || !$response) {
    echo json_encode(['success' => false, 'error' => 'Payment setup failed']);
    exit();
}

$order = json_decode($response, true);
if (!is_array($order) || empty($order['id'])) {
    echo json_encode(['success' => false, 'error' => 'Payment setup failed']);
    exit();
}

$_SESSION['pending_order'] = [
    'razorpay_order_id' => $order['id'],
    'amount'            => $cart_total,
    'address_data'      => $data
];

echo json_encode([
    'success'  => true,
    'order_id' => $order['id'],
    'amount'   => $cart_total
]);
exit();
?>
