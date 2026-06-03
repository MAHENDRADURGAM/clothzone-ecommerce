<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

$result = mysqli_query($conn, "SELECT * FROM products ORDER BY id DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Products - CLOTHZONE Admin</title>
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
        .actions { margin-bottom:14px; }
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
        img { width:60px; height:60px; object-fit:cover; border-radius:8px; }
        .badge-actions a {
            display:inline-block;
            padding:4px 9px;
            border-radius:999px;
            font-size:12px;
            text-decoration:none;
            margin-right:6px;
        }
        .edit { background:#22c55e; color:#000; }
        .del { background:#ef4444; color:#fff; }
        @media (max-width: 768px) {
    /* product cards on listing/search pages */
    .product-card img,
    .product-tile img,
    .product-grid img {
        width: 100%;
        height: auto;
        object-fit: cover;
        display: block;
    }

    .product-card,
    .product-tile {
        width: 100%;
        max-width: 100%;
    }

    /* cart page product images */
    .cart-item img {
        width: 90px;          /* smaller for phone */
        height: auto;
        object-fit: cover;
    }

    /* recommended items at bottom of cart */
    .reco-card img {
        width: 100%;
        height: auto;
        object-fit: cover;
    }

    .reco-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px;
    }
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
    <h1>Manage Products</h1>

    <div class="actions">
        <a href="add_product.php" class="btn">➕ Add New Product</a>
        <a href="admin_dashboard.php" class="btn btn-secondary">← Back to Dashboard</a>
    </div>

    <table>
        <tr>
            <th>ID</th>
            <th>Image</th>
            <th>Name</th>
            <th>Price (₹)</th>
            <th>Category</th>
            <th>Action</th>
        </tr>
  <?php while($row = mysqli_fetch_assoc($result)) {
    $img  = $row['image'] ?? '';
    $name = $row['name'] ?? '';
    $cat  = $row['category'] ?? '';
?>
    <tr>
        <td><?php echo $row['id']; ?></td>
        <td>
            <?php if ($img) { ?>
                <img src="<?php echo htmlspecialchars($img); ?>">
            <?php } else { ?>
                <span style="font-size:11px;color:#9ca3af;">No image</span>
            <?php } ?>
        </td>
        <td><?php echo htmlspecialchars($name); ?></td>
        <td><?php echo $row['price']; ?></td>
        <td><?php echo $cat ? htmlspecialchars($cat) : '—'; ?></td>
        <td class="badge-actions">
            <a class="edit" href="edit_product.php?id=<?php echo $row['id']; ?>">Edit</a>
            <a class="del" href="delete_product.php?id=<?php echo $row['id']; ?>">Delete</a>
        </td>
    </tr>
<?php } ?>

    </table>
</div>

</body>
</html>
