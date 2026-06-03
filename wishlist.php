<?php
session_start();
include 'protect_user.php';
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = (int)$_SESSION['user_id'];

// remove item from wishlist (run BEFORE any HTML/nav output)
if (isset($_GET['remove']) && ctype_digit($_GET['remove'])) {
    $wid = (int)$_GET['remove'];
    $stmt = mysqli_prepare($conn,
        "DELETE FROM wishlist WHERE id = ? AND user_id = ?");
    mysqli_stmt_bind_param($stmt, "ii", $wid, $user_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    header("Location: wishlist.php");
    exit();
}

// only now include nav and render page
include 'nav.php';

// get wishlist items with product info
$sql = "SELECT w.id AS wid, p.*
        FROM wishlist w
        JOIN products p ON w.product_id = p.id
        WHERE w.user_id = $user_id
        ORDER BY w.id DESC";
$res = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>My Wishlist - CLOTHZONE</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            margin:0;
            font-family:'Poppins',sans-serif;
            background:#f5f5f5;
            min-height:100vh;
            display:flex;
            flex-direction:column;
        }
        .page-wrap {
            flex:1;
            padding:30px 20px 50px;
        }
        .wishlist-header {
            max-width:1100px;
            margin:0 auto 10px;
            display:flex;
            justify-content:space-between;
            align-items:center;
        }
        .wishlist-header h1 {
            margin:0;
            font-size:26px;
        }
        .wishlist-count {
            font-size:14px;
            color:#777;
        }
        .wishlist-grid {
            max-width:1100px;
            margin:10px auto 0;
            display:grid;
            grid-template-columns:repeat(auto-fill,minmax(260px,1fr));
            gap:24px;
        }
        .card {
            background:#fff;
            border-radius:14px;
            overflow:hidden;
            box-shadow:0 6px 18px rgba(0,0,0,0.08);
            display:flex;
            flex-direction:column;
        }
        .card img {
            width:100%;
            height:230px;
            object-fit:cover;
        }
        .card-body {
            padding:14px 16px 12px;
            display:flex;
            flex-direction:column;
            gap:6px;
        }
        .card-title {
            font-size:16px;
            font-weight:600;
            margin:0;
        }
        .card-price {
            font-size:15px;
            font-weight:600;
            color:#444;
        }
        .card-actions {
            margin-top:8px;
            display:flex;
            gap:10px;
        }
        .btn {
            flex:1;
            padding:8px 10px;
            border-radius:999px;
            border:none;
            font-size:13px;
            font-weight:600;
            cursor:pointer;
            text-align:center;
            text-decoration:none;
        }
        .btn-primary {
            background:#000;
            color:#fff;
        }
        .btn-primary:hover {
            background:#333;
        }
        .btn-ghost {
            background:#f2f2f2;
            color:#111;
        }
        .btn-ghost:hover {
            background:#e0e0e0;
        }
        .empty {
            max-width:1100px;
            margin:40px auto;
            text-align:center;
            color:#555;
        }
        .empty a {
            color:#000;
            text-decoration:none;
            font-weight:600;
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
        @media (max-width: 768px) {
    /* wishlist product cards */
    .wishlist-item img,
    .wishlist-card img,
    .wishlist-grid img {
        width: 100%;
        height: auto;
        object-fit: cover;
        display: block;
    }

    .wishlist-item,
    .wishlist-card {
        width: 100%;
        max-width: 100%;
    }

    /* if you use a grid for wishlist */
    .wishlist-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px;
    }
}



    </style>
</head>
<body>

<div class="page-wrap">
    <div class="wishlist-header">
        <h1>My Wishlist</h1>
        <div class="wishlist-count">
            <?php echo mysqli_num_rows($res); ?> item(s) saved
        </div>
    </div>

    <?php if (mysqli_num_rows($res) == 0) { ?>
        <div class="empty">
            Your wishlist is empty.<br><br>
            <a href="products.php">Browse products →</a>
        </div>
    <?php } else { ?>
        <div class="wishlist-grid">
            <?php while($row = mysqli_fetch_assoc($res)) {
                $wid   = $row['wid'];
                $id    = $row['id'];
                $name  = htmlspecialchars($row['name']);
                $price = number_format($row['price'], 2);
                $image = htmlspecialchars($row['image']);
            ?>
                <div class="card">
                    <a href="product_details.php?id=<?php echo $id; ?>">
                        <img src="<?php echo $image; ?>" alt="<?php echo $name; ?>">
                    </a>
                    <div class="card-body">
                        <p class="card-title"><?php echo $name; ?></p>
                        <p class="card-price">₹<?php echo $price; ?></p>
                        <div class="card-actions">
                            <!-- Goes to add_to_cart.php, which now handles quantity when coming from product page -->
                            <a class="btn btn-primary" href="add_to_cart.php?id=<?php echo $id; ?>">Add to Cart</a>
                            <a class="btn btn-ghost" href="wishlist.php?remove=<?php echo $wid; ?>">Remove</a>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    <?php } ?>
</div>

<?php include 'footer.php'; ?>
</body>
</html>
