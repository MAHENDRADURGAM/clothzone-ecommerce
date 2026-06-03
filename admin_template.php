<?php
if (session_status() == PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Panel</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            display: flex;
            background: #f4f4f4;
        }

        .sidebar {
            width: 250px;
            background: #000;
            color: white;
            height: 100vh;
            padding: 20px;
            position: fixed;
        }

        .sidebar h2 {
            margin: 0 0 25px 0;
            font-size: 24px;
            text-align: center;
            letter-spacing: 1px;
        }

        .sidebar a {
            display: block;
            color: white;
            padding: 12px;
            margin: 8px 0;
            text-decoration: none;
            border-radius: 6px;
            transition: 0.3s;
            font-size: 16px;
        }

        .sidebar a:hover {
            background: #444;
        }

        .main-content {
            margin-left: 270px;
            padding: 30px;
            width: 100%;
        }

        .header {
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 3px 15px rgba(0,0,0,0.1);
            margin-bottom: 25px;
        }
    </style>
</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <h2>Admin Panel</h2>

    <a href="admin_dashboard.php">📊 Dashboard</a>
    <a href="admin_users.php">👤 Users</a>
    <a href="admin_products.php">🛍 Products</a>
    <a href="admin_orders.php">📦 Orders</a>
    <a href="admin_logout.php" style="background:#c00;margin-top:30px;">Logout</a>
</div>

<!-- MAIN BODY -->
<div class="main-content">
</div>
</body>
</html>
