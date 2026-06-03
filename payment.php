<?php
session_start();
include 'db.php';
include 'protect_user.php';

header('Content-Type: application/json');

// Only JSON POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    echo json_encode(['success' => false, 'error' => 'Invalid JSON body']);
    exit();
}

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Not logged in']);
    exit();
}

$user_id = (int)$_SESSION['user_id'];

// 1) Calculate order amount from cart (server‑side, not from client)
$cartSql = "SELECT c.quantity, p.price
            FROM cart c
            JOIN products p ON c.product_id = p.id
            WHERE c.user_id = $user_id";
$cartRes = mysqli_query($conn, $cartSql);

if (!$cartRes || mysqli_num_rows($cartRes) === 0) {
    echo json_encode(['success' => false, 'error' => 'Cart is empty']);
    exit();
}

$subtotal = 0;
while ($row = mysqli_fetch_assoc($cartRes)) {
    $subtotal += ((float)$row['price']) * ((int)$row['quantity']);
}

$shipping = 0;
$discount = 0;
$amount   = $subtotal - $discount + $shipping;
if ($amount <= 0) {
    echo json_encode(['success' => false, 'error' => 'Invalid amount']);
    exit();
}

// 2) Razorpay API keys (TEST mode)
$key_id     = 'rzp_test_RnW80gSg2dWQom';      // your test Key ID
$key_secret = 'fPSXzGmWyrf1tltOBWyMTEvg';
 // your test Key Secret

// 3) Create Razorpay order
$postData = [
    'amount'         => (int)round($amount * 100), // in paise
    'currency'       => 'INR',
    'receipt'        => 'rcpt_' . time(),
    'payment_capture'=> 1
];

$ch = curl_init('https://api.razorpay.com/v1/orders');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_USERPWD, $key_id . ':' . $key_secret);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 200 || !$response) {
    echo json_encode([
        'success' => false,
        'error'   => 'Failed to create Razorpay order (HTTP ' . $httpCode . ')'
    ]);
    exit();
}

$order = json_decode($response, true);
if (!is_array($order) || empty($order['id'])) {
    echo json_encode([
        'success' => false,
        'error'   => 'Invalid Razorpay response'
    ]);
    exit();
}

$razorpay_order_id = $order['id'];

// 4) Save pending order data in session for payment_success.php
$_SESSION['pending_order'] = [
    'razorpay_order_id' => $razorpay_order_id,
    'amount'            => $amount,
    'address_data'      => [
        'fullname' => $input['fullname'] ?? '',
        'address'  => $input['address']  ?? '',
        'city'     => $input['city']     ?? '',
        'state'    => $input['state']    ?? '',
        'pincode'  => $input['pincode']  ?? '',
        'phone'    => $input['phone']    ?? '',
        'notes'    => $input['notes']    ?? ''
    ]
];

// 5) Return data to JS
echo json_encode([
    'success'   => true,
    'order_id'  => $razorpay_order_id,
    'amount'    => $amount
]);
