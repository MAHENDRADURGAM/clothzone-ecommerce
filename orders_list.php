<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['status'])) {
    $oid    = (int)$_POST['order_id'];
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    mysqli_query($conn, "UPDATE orders SET status = '$status' WHERE id = $oid");
}

// fetch orders
$sql = "
    SELECT o.*, u.name AS customer
    FROM orders o
    JOIN users u ON o.user_id = u.id
    ORDER BY o.id DESC
";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Orders - CLOTHZONE Admin</title>
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        body { margin:0; font-family:'Poppins',sans-serif; background:#050505; color:#fff; }
        .topbar {
            padding:14px 30px;
            background:#000;
            display:flex;
            justify-content:space-between;
            align-items:center;
        }
        .topbar a { color:#fff; text-decoration:none; font-size:13px; margin-left:14px; }
        .wrapper { max-width:1200px; margin:26px auto 40px; padding:0 20px; }
        h1 { margin:0 0 16px; font-size:22px; }
        .btn {
            display:inline-block;
            padding:8px 16px;
            border-radius:999px;
            border:none;
            background:#fff;
            color:#000;
            font-size:13px;
            font-weight:600;
            text-decoration:none;
        }
        .btn-secondary {
            background:transparent;
            color:#fff;
            border:1px solid #4b5563;
        }
        table {
            width:100%;
            border-collapse:collapse;
            background:#090909;
            border-radius:14px;
            overflow:hidden;
        }
        th, td { padding:10px 12px; font-size:13px; text-align:left; }
        th { background:#111; }
        tr:nth-child(even) td { background:#0f0f0f; }
        select {
            background:#111;
            color:#fff;
            border-radius:999px;
            border:1px solid #4b5563;
            padding:4px 8px;
            font-size:12px;
        }
        .small-link {
            font-size:12px;
            color:#60a5fa;
            text-decoration:none;
        }
    </style>
</head>
<body>

<div class="topbar">
    <div>CLOTHZONE ADMIN</div>
    <div>
        <a href="admin_dashboard.php">Dashboard</a>
        <a href="logout.php">Logout</a>
    </div>
</div>

<div class="wrapper">
    <h1>Orders</h1>

    <a href="admin_dashboard.php" class="btn btn-secondary">← Back to Dashboard</a>

    <table style="margin-top:14px;">
        <tr>
            <th>Order ID</th>
            <th>Customer</th>
            <th>Total (₹)</th>
            <th>Status</th>
            <th>Date</th>
            <th>Details</th>
        </tr>
        <?php while($row = mysqli_fetch_assoc($result)) { ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo htmlspecialchars($row['customer']); ?></td>
                <td><?php echo $row['total_amount']; ?></td>
                <td>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="order_id" value="<?php echo $row['id']; ?>">
                        <select name="status" onchange="this.form.submit()">
                            <option value="Pending"   <?php if($row['status']=='Pending')   echo 'selected'; ?>>Pending</option>
                            <option value="Confirmed" <?php if($row['status']=='Confirmed') echo 'selected'; ?>>Confirmed</option>
                            <option value="Cancelled" <?php if($row['status']=='Cancelled') echo 'selected'; ?>>Cancelled</option>
                        </select>
                    </form>
                </td>
                <td><?php echo $row['order_date']; ?></td>
                <td>
                    <a class="small-link" href="order_details.php?id=<?php echo $row['id']; ?>">View</a>
                </td>
            </tr>
        <?php } ?>
    </table>
</div>

</body>
</html>
