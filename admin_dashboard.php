<?php
session_start();
include 'db.php';

// simple admin auth check (adjust if you have different logic)
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// quick stats
$products_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS c FROM products"))['c'];
$users_count    = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS c FROM users"))['c'];
$orders_count   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS c FROM orders"))['c'];
$revenue_row    = mysqli_fetch_assoc(mysqli_query($conn, "SELECT IFNULL(SUM(total_amount),0) AS s FROM orders"));
$total_revenue  = $revenue_row['s'];
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Admin Dashboard - CLOTHZONE</title>
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            margin:0;
            font-family:'Poppins',sans-serif;
            background:#0f0f0f;
            color:#fff;
        }
        .topbar {
            padding:14px 36px;
            display:flex;
            justify-content:space-between;
            align-items:center;
            background:#000;
        }
        .topbar-title {
            font-size:20px;
            font-weight:600;
            letter-spacing:2px;
        }
        .topbar-right a {
            color:#fff;
            text-decoration:none;
            font-size:14px;
            margin-left:18px;
        }
        .wrapper {
            max-width:1200px;
            margin:30px auto 40px;
            padding:0 20px;
        }
        .page-title {
            font-size:24px;
            font-weight:600;
            margin-bottom:18px;
        }
        .cards {
            display:grid;
            grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
            gap:18px;
            margin-bottom:26px;
        }
        .card {
            background:#171717;
            border-radius:14px;
            padding:18px 18px 16px;
            box-shadow:0 10px 30px rgba(0,0,0,0.55);
        }
        .card label {
            font-size:13px;
            text-transform:uppercase;
            letter-spacing:1px;
            color:#9ca3af;
        }
        .card h2 {
            margin:8px 0 0;
            font-size:26px;
        }
        .card span {
            font-size:13px;
            color:#6b7280;
        }
        .grid-links {
            display:grid;
            grid-template-columns:repeat(auto-fit,minmax(260px,1fr));
            gap:18px;
        }
        .link-card {
            background:#171717;
            border-radius:14px;
            padding:18px;
            box-shadow:0 10px 30px rgba(0,0,0,0.45);
            display:flex;
            flex-direction:column;
            justify-content:space-between;
        }
        .link-card h3 {
            margin:0 0 6px;
            font-size:18px;
        }
        .link-card p {
            margin:0 0 12px;
            font-size:13px;
            color:#9ca3af;
        }
        .btn {
            display:inline-block;
            padding:9px 18px;
            border-radius:999px;
            background:#fff;
            color:#000;
            text-decoration:none;
            font-size:13px;
            font-weight:600;
        }
        .btn-outline {
            background:transparent;
            color:#fff;
            border:1px solid #4b5563;
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

<div class="topbar">
    <div class="topbar-title">CLOTHZONE ADMIN</div>
    <div class="topbar-right">
        <span style="font-size:13px;color:#9ca3af;">Logged in as Admin</span>
        <a href="index.php" class="btn-outline">View Site</a>
        <a href="logout.php" class="btn-outline">Logout</a>
    </div>
</div>

<div class="wrapper">
    <div class="page-title">Dashboard Overview</div>

    <div class="cards">
        <div class="card">
            <label>Products</label>
            <h2><?php echo $products_count; ?></h2>
            <span>Total active products</span>
        </div>
        <div class="card">
            <label>Users</label>
            <h2><?php echo $users_count; ?></h2>
            <span>Registered customers</span>
        </div>
        <div class="card">
            <label>Orders</label>
            <h2><?php echo $orders_count; ?></h2>
            <span>All time orders</span>
        </div>
        <div class="card">
            <label>Revenue</label>
            <h2>₹<?php echo number_format((float)$total_revenue,2); ?></h2>
            <span>Total order value</span>
        </div>
    </div>

    <div class="grid-links">
        <div class="link-card">
            <h3>Products</h3>
            <p>View, add, and edit products in your catalog.</p>
            <div>
                <a href="products_list.php" class="btn">Manage Products</a>
                <a href="add_product.php" class="btn btn-outline" style="margin-left:10px;">Add New</a>
            </div>
        </div>
        <div class="link-card">
            <h3>Orders</h3>
            <p>Track customer orders and update their status.</p>
            <a href="orders_list.php" class="btn">View Orders</a>
        </div>
        <div class="link-card">
            <h3>Users</h3>
            <p>See who is shopping on CLOTHZONE.</p>
            <a href="admin_users.php" class="btn">View Users</a>
        </div>
    </div>
</div>

</body>
</html>
