<?php
session_start();
include 'protect_user.php';
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = (int)$_SESSION['user_id'];

$sql = "
    SELECT 
        o.id,
        o.total_amount,
        o.status,
        o.order_date,
        o.fullname,
        o.address,
        o.city,
        o.state,
        o.pincode,
        o.phone
    FROM orders o
    WHERE o.user_id = $user_id
    ORDER BY o.id DESC
";
$result = mysqli_query($conn, $sql);

include 'nav.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>My Orders - CLOTHZONE</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            background: #f7f7f7;
            min-height:100vh;
            display:flex;
            flex-direction:column;
        }
        .page-wrap { flex:1; padding:30px 20px 50px; }
        .page-inner { max-width:1100px; margin:0 auto; }

        .page-title {
            font-size:26px;
            font-weight:700;
            margin:0 0 6px;
        }
        .page-sub {
            font-size:14px;
            color:#777;
            margin:0 0 20px;
        }

        .orders-grid {
            display:grid;
            grid-template-columns:repeat(auto-fill,minmax(280px,1fr));
            gap:18px;
        }
        .order-card {
            background:#fff;
            border-radius:14px;
            padding:16px 18px 14px;
            box-shadow:0 6px 18px rgba(0,0,0,0.06);
            display:flex;
            flex-direction:column;
            justify-content:space-between;
        }
        .order-top {
            display:flex;
            justify-content:space-between;
            align-items:center;
            margin-bottom:8px;
        }
        .order-id {
            font-weight:600;
            font-size:14px;
        }
        .order-date {
            font-size:12px;
            color:#888;
        }
        .status-badge {
            display:inline-block;
            padding:4px 10px;
            border-radius:999px;
            font-size:11px;
            font-weight:600;
            text-transform:uppercase;
            letter-spacing:0.5px;
        }
        .status-pending { background:#fff7e6; color:#b36b00; }
        .status-processing { background:#e6f2ff; color:#0052b3; }
        .status-completed { background:#e6ffe9; color:#1b7a34; }
        .status-cancelled { background:#ffe6e6; color:#c0392b; }

        .order-main {
            font-size:13px;
            color:#555;
            margin:6px 0 10px;
        }
        .order-total {
            font-weight:700;
            font-size:15px;
            margin-top:4px;
        }

        .order-actions {
            display:flex;
            justify-content:flex-end;
            margin-top:8px;
        }
        .btn-small {
            display:inline-block;
            padding:7px 16px;
            border-radius:999px;
            background:#000;
            color:#fff;
            font-size:13px;
            font-weight:600;
            text-decoration:none;
        }
        .btn-small:hover { background:#333; }

        .empty {
            text-align:center;
            font-size:18px;
            color:#555;
            margin-top:40px;
        }
        .empty a { color:#000; font-weight:600; text-decoration:none; }
        /* desktop styles here ... */

/* mobile overrides for this page */
@media (max-width: 768px) {
    body {
        font-size: 14px;
        margin: 0;
    }

    .nav-bar {
        padding: 4px 8px;
        flex-wrap: wrap;
        row-gap: 4px;
    }

    .container,
    .page-wrap {
        padding: 12px 10px;
        max-width: 100%;
    }

    /* example grid/cards */
    .product-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px;
    }

    .checkout-btn {
        width: 100%;
        max-width: 320px;
        display: block;
        margin: 10px auto 0;
    }
}

    </style>
</head>
<body>

<div class="page-wrap">
    <div class="page-inner">
        <h1 class="page-title">My Orders</h1>
        <p class="page-sub">Track your recent purchases and view order details.</p>

        <?php if (mysqli_num_rows($result) == 0) { ?>
            <div class="empty">
                You have no orders yet.<br>
                <a href="products.php">Start shopping →</a>
            </div>
        <?php } else { ?>
            <div class="orders-grid">
                <?php while($row = mysqli_fetch_assoc($result)) {
                    $status = strtolower($row['status']);
                    $badgeClass = 'status-pending';
                    if ($status === 'processing') $badgeClass = 'status-processing';
                    elseif ($status === 'completed') $badgeClass = 'status-completed';
                    elseif ($status === 'cancelled') $badgeClass = 'status-cancelled';

                    $addressLine = trim(($row['address'] ?? '').' '.($row['city'] ?? '').' '.($row['state'] ?? ''));
                ?>
                    <div class="order-card">
                        <div class="order-top">
                            <div>
                                <div class="order-id">Order #<?php echo $row['id']; ?></div>
                                <?php if (!empty($row['order_date'])) { ?>
                                    <div class="order-date"><?php echo $row['order_date']; ?></div>
                                <?php } ?>
                            </div>
                            <span class="status-badge <?php echo $badgeClass; ?>">
                                <?php echo strtoupper($row['status']); ?>
                            </span>
                        </div>

                        <div class="order-main">
                            <div><strong><?php echo htmlspecialchars($row['fullname']); ?></strong></div>
                            <?php if ($addressLine !== '') { ?>
                                <div><?php echo htmlspecialchars($addressLine); ?></div>
                            <?php } ?>
                            <?php if (!empty($row['pincode'])) { ?>
                                <div>Pincode: <?php echo htmlspecialchars($row['pincode']); ?></div>
                            <?php } ?>
                            <?php if (!empty($row['phone'])) { ?>
                                <div>Phone: <?php echo htmlspecialchars($row['phone']); ?></div>
                            <?php } ?>
                            <div class="order-total">Total: ₹<?php echo number_format($row['total_amount'],2); ?></div>
                        </div>

                        <div class="order-actions">
                            <a class="btn-small" href="order_details.php?id=<?php echo $row['id']; ?>">View details</a>
                        </div>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>
    </div>
</div>

<?php include 'footer.php'; ?>
</body>
</html>
