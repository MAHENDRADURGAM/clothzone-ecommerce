<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

/* PROCESS FORM */
if (isset($_POST['add'])) {
    $name     = trim($_POST['name'] ?? '');
    $price    = trim($_POST['price'] ?? '');
    $image    = trim($_POST['image'] ?? '');
    $category = trim($_POST['category'] ?? '');

    if ($name !== '' && $price !== '' && $image !== '' && $category !== '') {
        $stmt = mysqli_prepare(
            $conn,
            "INSERT INTO products (name, price, image, category) VALUES (?, ?, ?, ?)"
        );
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "sdss", $name, $price, $image, $category);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
        header("Location: products_list.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Product - CLOTHZONE Admin</title>
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <style>
        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background: #0f0f0f;
            color: white;
        }
        header {
            background: #000;
            padding: 25px;
            text-align: center;
            font-size: 30px;
            font-weight: 600;
            border-bottom: 1px solid #222;
            letter-spacing: 1px;
        }
        .box {
            width: 50%;
            margin: 50px auto;
            background: #111;
            padding: 35px;
            border-radius: 18px;
            box-shadow: 0 0 25px rgba(255,255,255,0.05);
            border: 1px solid #222;
        }
        label {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 8px;
            display: block;
            color: #ddd;
        }
        input, select {
            width: 100%;
            padding: 14px;
            margin-bottom: 22px;
            border-radius: 10px;
            border: 1px solid #444;
            background: #1b1b1b;
            color: white;
            font-size: 16px;
            transition: 0.3s;
        }
        input:focus, select:focus {
            border-color: #888;
            outline: none;
            background: #222;
        }
        .btn {
            width: 100%;
            padding: 14px;
            border-radius: 10px;
            border: none;
            font-size: 18px;
            font-weight: 600;
            background: white;
            color: black;
            letter-spacing: 0.5px;
            cursor: pointer;
            transition: 0.3s;
        }
        .btn:hover {
            background: #e6e6e6;
            transform: scale(1.03);
        }
        .back-btn {
            display: block;
            width: fit-content;
            margin: 20px auto 0;
            padding: 10px 18px;
            border-radius: 8px;
            text-decoration: none;
            background: #333;
            color: white;
            font-weight: 600;
            transition: 0.3s;
        }
        .back-btn:hover {
            background: #444;
        }
    </style>
</head>
<body>

<header>Add New Product</header>

<div class="box">
    <form method="POST">
        <label>Product Name</label>
        <input type="text" name="name" required placeholder="Enter product name">

        <label>Price (₹)</label>
        <input type="number" step="0.01" name="price" required placeholder="Enter product price">

        <label>Image URL</label>
        <input type="text" name="image" required placeholder="Paste product image link">

        <label>Category</label>
        <select name="category" required>
            <option value="Men">Men</option>
            <option value="Women">Women</option>
            <option value="Kids">Kids</option>
        </select>

        <button type="submit" class="btn" name="add">Add Product</button>
    </form>
</div>

<a href="products_list.php" class="back-btn">← Back to Products</a>

</body>
</html>
