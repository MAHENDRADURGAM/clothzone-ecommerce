<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

$result = mysqli_query($conn, "SELECT id, name, email, created_at FROM users ORDER BY id DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Users - CLOTHZONE Admin</title>
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
        .wrapper { max-width:1100px; margin:26px auto 40px; padding:0 20px; }
        h1 { margin:0 0 16px; font-size:22px; }
        .btn-secondary {
            display:inline-block;
            padding:8px 16px;
            border-radius:999px;
            background:transparent;
            color:#fff;
            border:1px solid #4b5563;
            font-size:13px;
            font-weight:600;
            text-decoration:none;
        }
        table {
            width:100%;
            border-collapse:collapse;
            background:#090909;
            border-radius:14px;
            overflow:hidden;
            margin-top:14px;
        }
        th, td { padding:10px 12px; font-size:13px; text-align:left; }
        th { background:#111; }
        tr:nth-child(even) td { background:#0f0f0f; }
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
    <h1>Users</h1>

    <a href="admin_dashboard.php" class="btn-secondary">← Back to Dashboard</a>

    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Joined</th>
        </tr>
        <?php while($row = mysqli_fetch_assoc($result)) { ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo htmlspecialchars($row['name']); ?></td>
                <td><?php echo htmlspecialchars($row['email']); ?></td>
                <td><?php echo $row['created_at']; ?></td>
            </tr>
        <?php } ?>
    </table>
</div>

</body>
</html>
