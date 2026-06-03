<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

$orders = mysqli_query($conn, "SELECT * FROM orders ORDER BY order_date DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Orders - CLOTHZONE Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        body{margin:0;font-family:'Poppins',sans-serif;background:#050505;color:#fff;}
        .wrapper{max-width:1150px;margin:30px auto;padding:0 20px;}
        h1{font-size:24px;margin-bottom:16px;}
        table{width:100%;border-collapse:collapse;background:#101010;border-radius:14px;overflow:hidden;}
        th,td{padding:9px 11px;font-size:13px;border-bottom:1px solid #222;vertical-align:top;}
        th{background:#000;text-align:left;}
        tr:last-child td{border-bottom:none;}
        a.btn-link{display:inline-block;padding:4px 9px;border-radius:999px;background:#fff;color:#000;
            font-size:11px;font-weight:600;text-decoration:none;margin-top:3px;}
        .track-form{margin-top:6px;}
        .track-form input{width:130px;padding:4px 6px;border-radius:6px;border:1px solid #444;
            background:#181818;color:#fff;font-size:11px;margin-right:4px;}
        .track-form button{padding:4px 9px;border-radius:999px;border:none;background:#fff;color:#000;
            font-size:11px;font-weight:600;cursor:pointer;}
    </style>
</head>
<body>
<div class="wrapper">
    <h1>Orders</h1>
    <table>
        <tr>
            <th>ID</th>
            <th>Date</th>
            <th>Customer</th>
            <th>Total</th>
            <th>Items / Supplier / Tracking</th>
        </tr>
        <?php while($o = mysqli_fetch_assoc($orders)){ ?>
            <tr>
                <td>#<?php echo $o['id']; ?></td>
                <td><?php echo $o['order_date']; ?></td>
                <td>
                    <?php echo htmlspecialchars($o['fullname']); ?><br>
                    <?php echo htmlspecialchars($o['phone']); ?><br>
                    <?php echo htmlspecialchars($o['city'] . ', ' . $o['state']); ?>
                </td>
                <td>₹<?php echo number_format($o['total_amount'], 2); ?></td>
                <td>
                    Status: <?php echo htmlspecialchars($o['status']); ?><br>
<?php
$tracking = $o['tracking_number'] ?? '';
$sref     = $o['supplier_order_ref'] ?? '';
$city     = $o['city'] ?? '';
$state    = $o['state'] ?? '';
?>
Tracking: <?php echo htmlspecialchars($tracking); ?><br>
Supplier ref: <?php echo htmlspecialchars($sref); ?><br>
<?php echo htmlspecialchars(trim($city . ', ' . $state, ' ,')); ?>


                    <?php
                    $oid = (int)$o['id'];
                    $items = mysqli_query($conn,"
                        SELECT oi.*, p.name, p.supplier_product_url
                        FROM order_items oi
                        JOIN products p ON oi.product_id = p.id
                        WHERE oi.order_id = $oid
                    ");
                    while($it = mysqli_fetch_assoc($items)){ ?>
                        <div style="margin-top:6px;">
                            <?php echo htmlspecialchars($it['name']); ?> × <?php echo (int)$it['quantity']; ?>
                            <?php if(!empty($it['supplier_product_url'])){ ?>
                                <a class="btn-link"
                                   href="<?php echo htmlspecialchars($it['supplier_product_url']); ?>"
                                   target="_blank">
                                    Open on supplier
                                </a>
                            <?php } ?>
                        </div>
                    <?php } ?>

                    <form class="track-form" method="post" action="update_order_tracking.php">
                        <input type="hidden" name="order_id" value="<?php echo $o['id']; ?>">
                        <input type="text" name="tracking_number" placeholder="Tracking no."
                               value="<?php echo htmlspecialchars($o['tracking_number'] ?? ''); ?>">
                        <input type="text" name="supplier_order_ref" placeholder="Supplier order"
                               value="<?php echo htmlspecialchars($o['supplier_order_ref'] ?? ''); ?>">
                        <button type="submit">Save</button>
                    </form>
                </td>
            </tr>
        <?php } ?>
    </table>
</div>
</body>
</html>
