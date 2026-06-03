<?php
session_start();
include 'protect_user.php';
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id  = (int)$_SESSION['user_id'];

$fullname = mysqli_real_escape_string($conn, $_POST['fullname'] ?? '');
$address  = mysqli_real_escape_string($conn, $_POST['address'] ?? '');
$city     = mysqli_real_escape_string($conn, $_POST['city'] ?? '');
$state    = mysqli_real_escape_string($conn, $_POST['state'] ?? '');
$pincode  = mysqli_real_escape_string($conn, $_POST['pincode'] ?? '');
$phone    = mysqli_real_escape_string($conn, $_POST['phone'] ?? '');
$notes    = mysqli_real_escape_string($conn, $_POST['notes'] ?? '');

// 1) Get all items from this user's cart with product info
$sql = "SELECT c.id AS cart_id, c.product_id, c.quantity, p.price
        FROM cart c
        JOIN products p ON c.product_id = p.id
        WHERE c.user_id = $user_id";
$res = mysqli_query($conn, $sql);

if (!$res || mysqli_num_rows($res) == 0) {
    header("Location: cart.php");
    exit();
}

// 2) Calculate total_amount
$total = 0;
$items = [];
while ($row = mysqli_fetch_assoc($res)) {
    $items[] = $row;
    $total += $row['price'] * $row['quantity'];
}

// 3) Insert into orders (with address fields)
$status = 'Pending';
$insertOrder = "
    INSERT INTO orders 
        (user_id, total_amount, status, fullname, address, city, state, pincode, phone, notes)
    VALUES 
        ($user_id, $total, '$status', '$fullname', '$address', '$city', '$state', '$pincode', '$phone', '$notes')
";
mysqli_query($conn, $insertOrder);
$order_id = mysqli_insert_id($conn);
// GA4: track purchase event
$_SESSION['ga_purchase'] = [
    'order_id' => $order_id,
    'value' => (float)$total,
    'currency' => 'INR',
    'items' => $items  // already have this array
];

// 4) Insert each item into order_items
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

// 5) Clear cart for this user
mysqli_query($conn, "DELETE FROM cart WHERE user_id = $user_id");

// GA4: send purchase event on next page
?>
<script>
<?php if (isset($_SESSION['ga_purchase'])): 
    $purchase = $_SESSION['ga_purchase'];
    unset($_SESSION['ga_purchase']);
?>
  if (typeof gtag === 'function') {
    gtag('event', 'purchase', {
      transaction_id: <?php echo json_encode($purchase['order_id']); ?>,
      value: <?php echo json_encode($purchase['value']); ?>,
      currency: <?php echo json_encode($purchase['currency']); ?>,
      items: <?php echo json_encode($purchase['items']); ?>
    });
  }
<?php endif; ?>
</script>
<?php
// Redirect to My Orders
header("Location: my_orders.php");
exit();
?>



