<?php
session_start();
include 'db.php';
include 'protect_user.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Your Cart</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
body {
    margin: 0;
    padding: 0;
    background: #f6f6f6;
    font-family: 'Poppins', sans-serif;
}

.container {
    max-width: 1050px;
    margin: 40px auto;
    padding: 0 20px;
}

h1 {
    font-size: 32px;
    margin-bottom: 20px;
    font-weight: 700;
}

.cart-item {
    display: flex;
    align-items: center;
    background: white;
    padding: 18px;
    border-radius: 12px;
    margin-bottom: 18px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.06);
}

.cart-item img {
    width: 120px;
    height: 120px;
    object-fit: cover;
    border-radius: 10px;
}

.item-info {
    flex: 1;
    padding-left: 18px;
}

.item-name {
    font-size: 18px;
    font-weight: 600;
}

.item-price {
    font-size: 16px;
    color: #555;
    margin-top: 5px;
}

.quantity-box {
    display: flex;
    align-items: center;
    gap: 10px;
}

.quantity-btn {
    width: 32px;
    height: 32px;
    border: none;
    background: #000;
    color: white;
    border-radius: 6px;
    cursor: pointer;
    font-size: 18px;
}

.quantity-input {
    width: 55px;
    padding: 6px;
    font-size: 15px;
    text-align: center;
    border: 1px solid #ddd;
    border-radius: 6px;
}

.remove-btn {
    background: red;
    padding: 10px 14px;
    color: #fff;
    border-radius: 6px;
    text-decoration: none;
}

.total-box {
    background: white;
    padding: 20px;
    border-radius: 12px;
    margin-top: 20px;
    text-align: right;
    font-size: 22px;
    font-weight: 700;
}

.checkout {
    display: block;
    text-align: center;
    width: 250px;
    margin: 25px auto 50px auto;
    padding: 14px;
    background: #000;
    color: white;
    border-radius: 10px;
    font-size: 18px;
    text-decoration: none;
}

/* Recommended */
.reco-title {
    margin-top: 40px;
    font-size: 24px;
    font-weight: 600;
}

.reco-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill,minmax(200px,1fr));
    gap: 18px;
    margin-top: 16px;
}

.reco-card {
    background: white;
    padding: 14px;
    border-radius: 10px;
    text-align: center;
    box-shadow: 0 4px 10px rgba(0,0,0,0.06);
}

.reco-card img {
    width: 100%;
    height: 180px;
    border-radius: 8px;
    object-fit: cover;
}

.checkout-container {
    width: 60%;
    max-width: 600px;
    background: white;
    margin: 40px auto;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
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
    /* existing cart mobile rules … */

    /* "You may also like" title spacing */
    .reco-title {
        padding: 0 10px;
        font-size: 20px;
        margin-top: 32px;
        margin-bottom: 14px;
    }

    /* grid under "You may also like" */
    .reco-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr)); /* 2 per row */
        gap: 10px;
        padding: 0 10px 20px;
        box-sizing: border-box;
    }

    .reco-card {
        margin: 0;
    }

    .reco-card img {
        width: 100%;        /* full width of card */
        height: auto;       /* keep ratio */
        object-fit: cover;  /* crop nicely if needed */
        display: block;
    }
}

    
</style>
For each page:
</style>
</head>
<body>

<?php include 'nav.php'; ?>

<div class="container">
    <h1>Your Cart</h1>
    <a href="products.php" style="text-decoration:none;font-size:15px">← Continue Shopping</a>
    <br><br>

    <?php
    $uid = $_SESSION['user_id'];
    $sql = "SELECT cart.id AS cartid, cart.quantity, cart.size,
                   products.name, products.price, products.image
            FROM cart
            JOIN products ON cart.product_id = products.id
            WHERE cart.user_id = $uid";
    $result = mysqli_query($conn, $sql);
    $total = 0;

    if (mysqli_num_rows($result) == 0) {
        echo "<h3>Your cart is empty.</h3>";
        // show zero totals and no coupon form
        echo '<div style="margin-top:20px;"><strong>Total: ₹0.00</strong></div>';
    } else {

        while ($row = mysqli_fetch_assoc($result)) {
            $price  = $row['price'];
            $quantity    = $row['quantity'];
            $total += $price * $quantity;
    ?>

        <div class="cart-item">
            <img src="<?php echo $row['image']; ?>" alt="<?php echo $row['name']; ?>">
            <div class="item-info">
                <div class="item-name"><?php echo $row['name']; ?></div>
                <div class="item-price">₹<?php echo $row['price']; ?></div>
                <?php if (!empty($row['size'])) echo "<div>Size: ".$row['size']."</div>"; ?>
                <div class="quantity-box">
                    <form method="GET" action="update_qty.php">
                        <input type="hidden" name="id" value="<?php echo $row['cartid']; ?>">
                        <button class="quantity-btn" name="act" value="minus">−</button>
                        <input type="text" class="quantity-input" name="quantity" value="<?php echo $quantity; ?>">
                        <button class="quantity-btn" name="act" value="plus">+</button>
                    </form>
                </div>
            </div>
            <a class="remove-btn"
               onclick="return confirm('Remove this item?')"
               href="remove_from_cart.php?id=<?php echo $row['cartid']; ?>">Remove</a>
        </div>

    <?php
        } // end while

        // ----- COUPON LOGIC -----
        $discount = 0;
        $applied_coupon = '';
        $error = '';
        $cart_total = $total;

        if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['apply_coupon'])) {
            $code = mysqli_real_escape_string($conn, trim($_POST['coupon_code']));
            $today = date('Y-m-d');

            $res = mysqli_query($conn,
                "SELECT * FROM coupons
                 WHERE code='$code'
                   AND (expires_at IS NULL OR expires_at >= '$today') 
                 LIMIT 1"
            );

            if ($rowC = mysqli_fetch_assoc($res)) {
                if ($cart_total >= (float)$rowC['min_total']) {
                    if ($rowC['type'] === 'percent') {
                        $discount = $cart_total * ((float)$rowC['value'] / 100);
                    } else {
                        $discount = (float)$rowC['value'];
                    }
                    if ($discount > $cart_total) $discount = $cart_total;
                    $_SESSION['coupon_code'] = $code;
                    $_SESSION['coupon_discount'] = $discount;
                    $applied_coupon = $code;
                } else {
                    $error = "Cart total too low for this coupon.";
                }
            } else {
                $error = "Invalid or expired coupon.";
            }
        }

        if (isset($_SESSION['coupon_discount'])) {
            $discount = (float)$_SESSION['coupon_discount'];
            $applied_coupon = $_SESSION['coupon_code'] ?? '';
        }

        $grand_total = $total - $discount;
        if ($grand_total < 0) $grand_total = 0;
    ?>

        <div class="total-box">
            Total: ₹<?php echo number_format($total, 2); ?>
        </div>

        <form method="POST" style="margin:20px 0 15px 0;">
          <input type="text" name="coupon_code" placeholder="Enter coupon code"
                 style="padding:8px 10px;border-radius:6px;border:1px solid #ccc;">
          <button type="submit" name="apply_coupon"
                  style="padding:8px 14px;border-radius:6px;border:none;background:#000;color:#fff;font-weight:600;">
            Apply
          </button>
        </form>

        <?php if (!empty($error)) { ?>
          <div style="color:red;margin-bottom:10px;"><?php echo htmlspecialchars($error); ?></div>
        <?php } ?>

        <?php if (!empty($applied_coupon)) { ?>
          <div>Coupon (<?php echo htmlspecialchars($applied_coupon); ?>): -₹<?php echo number_format($discount,2); ?></div>
        <?php } ?>

        <div><strong>Total: ₹<?php echo number_format($grand_total,2); ?></strong></div>

        <a href="checkout.php" class="checkout">Proceed to Checkout</a>

    <?php } // end else not empty ?>
</div>

<!-- Recommended -->
<div class="container">
    <div class="reco-title">You may also like</div>
    <div class="reco-grid">
    <?php
    $reco = mysqli_query($conn, "SELECT * FROM products ORDER BY RAND() LIMIT 4");
    while ($p = mysqli_fetch_assoc($reco)) { ?>
        <a href="product_details.php?id=<?php echo $p['id']; ?>" class="reco-card">
            <img src="<?php echo $p['image']; ?>">
            <div style="font-weight:600;margin-top:8px;"><?php echo $p['name']; ?></div>
            <div style="margin-top:4px;color:#444;">₹<?php echo $p['price']; ?></div>
        </a>
    <?php } ?>
    </div>
</div>

<?php
// GA4: read added item from session (set in add_to_cart.php)
$ga_add_item = $_SESSION['ga_add_to_cart'] ?? null;
if ($ga_add_item) {
    unset($_SESSION['ga_add_to_cart']); // use once
}
?>

<?php if (!empty($ga_add_item)): ?>
<script>
  if (typeof gtag === 'function') {
    gtag('event', 'add_to_cart', {
      currency: 'INR',
      value: <?php echo json_encode((float)$ga_add_item['price']); ?>,
      items: [{
        item_id: <?php echo json_encode($ga_add_item['id']); ?>,
        item_name: <?php echo json_encode($ga_add_item['name']); ?>,
        item_category: <?php echo json_encode($ga_add_item['category']); ?>,
        price: <?php echo json_encode((float)$ga_add_item['price']); ?>,
        quantity: 1
      }]
    });
  }
</script>
<?php endif; ?>

</body>
</html>
