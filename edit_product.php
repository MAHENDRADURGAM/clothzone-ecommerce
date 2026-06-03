<?php
session_start();
include 'db.php';

// use the same session key as admin_login.php
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

/* CHECK PRODUCT ID */
if (!isset($_GET['id'])) {
    die("Product ID missing.");
}

$id = intval($_GET['id']);

/* FETCH PRODUCT */
$stmt = mysqli_prepare($conn, "SELECT id, name, price, image, category FROM products WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result  = mysqli_stmt_get_result($stmt);
$product = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$product) {
    die("Product not found.");
}

/* UPDATE PRODUCT */
if (isset($_POST['update'])) {

    $name     = $_POST['name'];
    $price    = $_POST['price'];
    $image    = $_POST['image'];
    $category = $_POST['category'];

    $up = mysqli_prepare(
        $conn,
        "UPDATE products SET name = ?, price = ?, image = ?, category = ? WHERE id = ?"
    );
    mysqli_stmt_bind_param($up, "ssssi", $name, $price, $image, $category, $id);
    mysqli_stmt_execute($up);
    mysqli_stmt_close($up);

    header("Location: products_list.php");
    exit();
}
?>


<!DOCTYPE html>
<html>
<head>
<title>Edit Product</title>

<style>
    body {
        margin: 0;
        padding: 0;
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

    .form-box {
        width: 50%;
        margin: 50px auto;
        background: #111;
        padding: 35px;
        border-radius: 18px;
        border: 1px solid #222;
        box-shadow: 0 0 25px rgba(255,255,255,0.05);
    }

    label {
        font-size: 16px;
        font-weight: 600;
        margin-bottom: 6px;
        display: block;
        color: #ccc;
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
        cursor: pointer;
        transition: 0.3s;
        letter-spacing: 0.5px;
    }

    .btn:hover {
        background: #e6e6e6;
        transform: scale(1.03);
    }

    .back-btn {
        display: block;
        width: fit-content;
        margin: 20px auto 0;
        background: #333;
        color: white;
        padding: 10px 18px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        transition: 0.3s ease;
    }

    .back-btn:hover {
        background: #444;
    }

</style>

</head>

<body>

<header>Edit Product</header>

<div class="form-box">

<form method="POST">

    <label>Product Name</label>
    <input type="text" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>

    <label>Price (₹)</label>
    <input type="number" name="price" value="<?php echo htmlspecialchars($product['price']); ?>" required>

    <label>Image URL</label>
    <input type="text" name="image" value="<?php echo htmlspecialchars($product['image']); ?>" required>

    <label>Category</label>
    <select name="category" required>
        <option value="Men"   <?php if($product['category']=="Men") echo "selected"; ?>>Men</option>
        <option value="Women" <?php if($product['category']=="Women") echo "selected"; ?>>Women</option>
        <option value="Kids"  <?php if($product['category']=="Kids") echo "selected"; ?>>Kids</option>
    </select>

    <button type="submit" name="update" class="btn">Update Product</button>

</form>

</div>

<a href="products_list.php" class="back-btn">← Back to Products</a>

</body>
</html>
