<?php
session_start();
include 'protect_user.php';
include 'db.php';

if (!isset($_GET['id'])) {
    header("Location: my_orders.php");
    exit();
}
$order_id = (int)$_GET['id'];
$user_id  = (int)$_SESSION['user_id'];

// get order (ensure belongs to user)
$orderRes = mysqli_query($conn, "SELECT * FROM orders WHERE id = $order_id AND user_id = $user_id LIMIT 1");
if (mysqli_num_rows($orderRes) == 0) {
    header("Location: my_orders.php");
    exit();
}
$order = mysqli_fetch_assoc($orderRes);

// get items
$itemSql = "
    SELECT oi.*, p.name, p.image
    FROM order_items oi
    JOIN products p ON oi.product_id = p.id
    WHERE oi.order_id = $order_id
";
$items = mysqli_query($conn, $itemSql);

include 'nav.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order #<?php echo $order_id; ?> - CLOTHZONE</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        body {
            margin:0;
            font-family:'Poppins',sans-serif;
            background:#f5f5f5;
            min-height:100vh;
            display:flex;
            flex-direction:column;
        }
        .page-wrap {
            flex:1;
            max-width:1100px;
            margin:30px auto 50px;
            padding:0 20px;
        }
        .top-row {
            display:flex;
            justify-content:space-between;
            align-items:center;
            margin-bottom:14px;
        }
        .top-row h1 {
            margin:0;
            font-size:24px;
        }
        .back-link {
            font-size:13px;
            text-decoration:none;
            color:#000;
            font-weight:500;
        }

        .summary-layout {
            display:grid;
            grid-template-columns:2fr 1.5fr;
            gap:14px;
            margin-bottom:18px;
        }
        @media(max-width:900px){
            .summary-layout { grid-template-columns:1fr; }
        }
        .summary-box,
        .address-box {
            background:#fff;
            padding:14px 16px;
            border-radius:12px;
            box-shadow:0 4px 14px rgba(0,0,0,0.06);
            font-size:14px;
        }
        .summary-line {
            margin:3px 0;
        }
        .summary-label {
            font-weight:600;
        }

        table {
            width:100%;
            border-collapse:collapse;
            background:#fff;
            border-radius:14px;
            overflow:hidden;
            box-shadow:0 6px 18px rgba(0,0,0,0.08);
        }
        th, td {
            padding:12px 14px;
            text-align:left;
            font-size:14px;
        }
        th {
            background:#222;
            color:#fff;
        }
        tr:nth-child(even) td {
            background:#fafafa;
        }
        .prod-img {
            width:60px;
            height:60px;
            object-fit:cover;
            border-radius:8px;
        }
    </style>
</head>
<body>

<div class="page-wrap">
    <div class="top-row">
        <h1>Order #<?php echo $order_id; ?></h1>
        <a href="my_orders.php" class="back-link">← Back to My Orders</a>
    </div>

    <div class="summary-layout">
        <div class="summary-box">
            <div class="summary-line">
                <span class="summary-label">Status:</span>
                <span><?php echo htmlspecialchars($order['status']); ?></span>
            </div>
            <?php if (!empty($order['order_date'])) { ?>
                <div class="summary-line">
                    <span class="summary-label">Date:</span>
                    <span><?php echo htmlspecialchars($order['order_date']); ?></span>
                </div>
            <?php } ?>
            <div class="summary-line">
                <span class="summary-label">Total:</span>
                <span>₹<?php echo number_format($order['total_amount'],2); ?></span>
            </div>
        </div>

        <div class="address-box">
            <div class="summary-line">
                <span class="summary-label">Name:</span>
                <span><?php echo htmlspecialchars($order['fullname'] ?? ''); ?></span>
            </div>
            <?php if (!empty($order['address'])) { ?>
                <div class="summary-line">
                    <span class="summary-label">Address:</span>
                    <span><?php echo nl2br(htmlspecialchars($order['address'])); ?></span>
                </div>
            <?php } ?>
            <?php
                $cityState = trim(($order['city'] ?? '').' '.($order['state'] ?? ''));
                if ($cityState !== '') {
            ?>
                <div class="summary-line">
                    <span class="summary-label">City/State:</span>
                    <span><?php echo htmlspecialchars($cityState); ?></span>
                </div>
            <?php } ?>
            <?php if (!empty($order['pincode'])) { ?>
                <div class="summary-line">
                    <span class="summary-label">Pincode:</span>
                    <span><?php echo htmlspecialchars($order['pincode']); ?></span>
                </div>
            <?php } ?>
            <?php if (!empty($order['phone'])) { ?>
                <div class="summary-line">
                    <span class="summary-label">Phone:</span>
                    <span><?php echo htmlspecialchars($order['phone']); ?></span>
                </div>
            <?php } ?>
            <?php if (!empty($order['notes'])) { ?>
                <div class="summary-line">
                    <span class="summary-label">Notes:</span>
                    <span><?php echo nl2br(htmlspecialchars($order['notes'])); ?></span>
                </div>
            <?php } ?>
        </div>
    </div>

    <table>
        <tr>
            <th>Product</th>
            <th>Image</th>
            <th>Quantity</th>
            <th>Price</th>
            <th>Subtotal</th>
        </tr>
        <?php while($item = mysqli_fetch_assoc($items)) {
            $sub = $item['price'] * $item['quantity'];
        ?>
            <tr>
                <td><?php echo htmlspecialchars($item['name']); ?></td>
                <td><img class="prod-img" src="<?php echo htmlspecialchars($item['image']); ?>" alt=""></td>
                <td><?php echo (int)$item['quantity']; ?></td>
                <td>₹<?php echo number_format($item['price'],2); ?></td>
                <td>₹<?php echo number_format($sub,2); ?></td>
            </tr>
        <?php } ?>
    </table>
</div>

<?php include 'footer.php'; ?>
</body>
</html>
