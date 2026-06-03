<?php
session_start();
include 'nav.php';
include 'db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userId = (int)$_SESSION['user_id'];

// Fetch user data
$query = "SELECT * FROM users WHERE id = $userId LIMIT 1";
$result = mysqli_query($conn, $query);

if (!$result || mysqli_num_rows($result) == 0) {
    header("Location: login.php");
    exit();
}

$user = mysqli_fetch_assoc($result); // This makes $user an ARRAY

// Orders query
$ordersQuery = "SELECT * FROM orders WHERE user_id = $userId ORDER BY id DESC";
$ordersResult = mysqli_query($conn, $ordersQuery);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Profile - CLOTHZONE</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background: #fafafa;
            color: #111;
            min-height: 100vh;
        }
        .page-wrap {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
            display: grid;
            grid-template-columns: 350px 1fr;
            gap: 40px;
        }
        .profile-card {
            background: white;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.08);
        }
        .profile-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: #e5e7eb;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            color: #6b7280;
        }
        .profile-name {
            text-align: center;
            font-size: 24px;
            font-weight: 700;
            margin: 0 0 8px;
            color: #111;
        }
        .profile-email {
            text-align: center;
            color: #6b7280;
            font-size: 14px;
            margin: 0 0 25px;
        }
        .info-grid {
            display: grid;
            gap: 20px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #f3f4f6;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .label {
            font-weight: 500;
            color: #374151;
            font-size: 14px;
        }
        .value {
            font-weight: 600;
            color: #111;
            text-align: right;
        }
        .orders-section {
            background: white;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.08);
        }
        .section-title {
            font-size: 22px;
            font-weight: 700;
            margin: 0 0 25px;
            color: #111;
        }
        .orders-grid {
            display: grid;
            gap: 15px;
        }
        .order-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 18px;
            background: #f8fafc;
            border-radius: 12px;
            border-left: 4px solid #22c55e;
        }
        .order-info h4 {
            margin: 0 0 4px;
            font-size: 16px;
            font-weight: 600;
        }
        .order-meta {
            font-size: 13px;
            color: #6b7280;
            margin: 0;
        }
        .order-amount {
            font-weight: 700;
            font-size: 18px;
            color: #111;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: #000;
            color: white;
            text-decoration: none;
            border-radius: 999px;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s;
            text-align: center;
        }
        .btn:hover {
            background: #333;
            transform: translateY(-1px);
        }
        .btn-secondary {
            background: transparent;
            color: #000;
            border: 2px solid #000;
        }
        .btn-secondary:hover {
            background: #000;
            color: white;
        }
        @media (max-width: 768px) {
            .page-wrap {
                grid-template-columns: 1fr;
                padding: 20px;
                gap: 25px;
            }
        }
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
    <!-- Profile Card -->
    <div class="profile-card">
        <div class="profile-avatar">👤</div>
        <h1 class="profile-name"><?php echo htmlspecialchars($user['name']); ?></h1>
        <p class="profile-email"><?php echo htmlspecialchars($user['email']); ?></p>
        
        <div class="info-grid">
            <div class="info-row">
                <div class="label">Member Since</div>
                <div class="value"><?php echo date('M Y', strtotime($user['created_at'])); ?></div>
            </div>
            <div class="info-row">
                <div class="label">Total Orders</div>
                <div class="value"><?php echo mysqli_num_rows($ordersResult); ?></div>
            </div>
        </div>
        
        <div style="margin-top: 30px; text-align: center;">
            <a href="logout.php" class="btn btn-secondary">Logout</a>
        </div>
    </div>

    <!-- Orders Section -->
    <div class="orders-section">
        <h2 class="section-title">Your Orders</h2>
        
        <?php if (mysqli_num_rows($ordersResult) == 0) { ?>
            <div style="text-align: center; padding: 60px 20px; color: #6b7280;">
                <h3>No orders yet</h3>
                <p style="margin: 10px 0 0;">Start shopping to see your orders here.</p>
                <a href="products.php" class="btn" style="margin-top: 20px;">Shop Now</a>
            </div>
        <?php } else { ?>
            <div class="orders-grid">
                <?php while ($order = mysqli_fetch_assoc($ordersResult)) { ?>
                    <div class="order-item">
                        <div class="order-info">
                            <h4>Order #<?php echo $order['id']; ?></h4>
                            <p class="order-meta">
                                <?php echo date('M d, Y', strtotime($order['order_date'])); ?> 
                                • ₹<?php echo number_format($order['total_amount'], 2); ?>
                            </p>
                        </div>
                        <div class="order-amount">₹<?php echo number_format($order['total_amount'], 2); ?></div>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>
    </div>
</div>

</body>
</html>
