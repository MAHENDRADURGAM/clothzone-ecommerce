<?php
session_start();
include 'db.php';
include 'protect_user.php';

// fetch cart summary for order summary box
$uid = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;
$cartSql = "SELECT c.quantity, c.size, p.name, p.price, p.image, p.id
            FROM cart c
            JOIN products p ON c.product_id = p.id
            WHERE c.user_id = $uid";
$cartRes = mysqli_query($conn, $cartSql);
$items = [];
$subtotal = 0;
if ($cartRes) {
    while ($row = mysqli_fetch_assoc($cartRes)) {
        $lineTotal = $row['price'] * $row['quantity'];
        $subtotal += $lineTotal;
        $items[] = $row;
    }
}

$shipping = 0;
$discount = 0;
$grandTotal = $subtotal - $discount + $shipping;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Checkout - CLOTHZONE</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            background: #f4f4f4;
            color: #111;
        }
        .page-wrap {
            max-width: 1200px;
            margin: 30px auto 50px;
            padding: 0 20px;
        }
        .checkout-header {
            text-align: left;
            margin-bottom: 24px;
        }
        .checkout-title {
            font-size: 28px;
            font-weight: 700;
            margin: 0 0 6px;
        }
        .checkout-sub {
            font-size: 14px;
            color: #666;
            margin: 0;
        }
        .checkout-grid {
            display: grid;
            grid-template-columns: 2fr 1.3fr;
            gap: 24px;
        }
        @media(max-width: 900px) {
            .checkout-grid {
                grid-template-columns: 1fr;
            }
        }
        .card {
            background: #fff;
            border-radius: 14px;
            padding: 22px 22px 24px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.06);
        }
        .card h3 {
            margin: 0 0 18px;
            font-size: 18px;
            font-weight: 600;
        }
        label {
            display:block;
            font-weight: 600;
            font-size: 14px;
            margin-bottom: 6px;
        }
        input, textarea, select {
            width: 100%;
            padding: 11px 12px;
            border-radius: 10px;
            border: 1px solid #ddd;
            font-size: 14px;
            margin-bottom: 14px;
            font-family: inherit;
            box-sizing: border-box;
        }
        textarea { resize: vertical; min-height: 80px; }
        .form-row {
            display:flex;
            gap:12px;
        }
        .form-row > div {
            flex:1;
        }
        @media(max-width: 600px) {
            .form-row {
                flex-direction:column;
            }
        }
        .payment-methods {
            display:flex;
            gap:12px;
            margin-bottom:14px;
        }
        .payment-method {
            flex:1;
            padding:12px;
            border:2px solid #ddd;
            border-radius:10px;
            text-align:center;
            cursor:pointer;
            background:#fff;
            transition:all 0.2s;
        }
        .payment-method.active {
            border-color:#000;
            background:#f8f8f8;
        }
        .payment-method img {
            height:24px;
            margin-bottom:4px;
        }
        .btn-primary {
            width: 100%;
            padding: 15px;
            border-radius: 999px;
            border: none;
            background: #000;
            color: #fff;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            margin-top: 12px;
        }
        .btn-primary:hover, .btn-primary:disabled:hover {
            background:#333;
        }
        .btn-primary:disabled {
            opacity:0.6;
            cursor:not-allowed;
        }
        .summary-list {
            max-height: 260px;
            overflow-y: auto;
            margin-bottom: 14px;
        }
        .summary-item {
            display:flex;
            gap:10px;
            margin-bottom:10px;
            padding-bottom:10px;
            border-bottom:1px solid #eee;
        }
        .summary-thumb {
            width:52px;
            height:52px;
            border-radius:8px;
            overflow:hidden;
            flex-shrink:0;
        }
        .summary-thumb img {
            width:100%; height:100%; object-fit:cover;
        }
        .summary-text {
            flex:1;
            font-size:13px;
        }
        .summary-name {
            font-weight:600;
            margin-bottom:2px;
        }
        .summary-meta {
            color:#777;
            font-size:12px;
        }
        .summary-line {
            display:flex;
            justify-content:space-between;
            margin:4px 0;
            font-size:14px;
        }
        .summary-line.total {
            font-weight:700;
            font-size:18px;
            margin-top:12px;
            padding-top:12px;
            border-top:2px solid #eee;
        }
   
        .loading { opacity:0.7; pointer-events:none; }
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
For each page:
    
    </style>
</head>
<body>

<?php include 'nav.php'; ?>

<div class="page-wrap">
    <div class="checkout-header">
        <h1 class="checkout-title">Checkout</h1>
        <p class="checkout-sub">Complete your details and pay securely with Razorpay or Cash on Delivery.</p>
    </div>

    <div class="checkout-grid">
        <!-- Left: shipping form -->
        <div class="card">
            <h3>Delivery details</h3>
            <form id="checkoutForm">
                <label for="fullname">Full name *</label>
                <input type="text" id="fullname" name="fullname" required>

                <label for="address">Address *</label>
                <textarea id="address" name="address" rows="3" required></textarea>

                <div class="form-row">
                    <div>
                        <label for="city">City</label>
                        <input type="text" id="city" name="city">
                    </div>
                    <div>
                        <label for="state">State</label>
                        <input type="text" id="state" name="state">
                    </div>
                </div>

                <div class="form-row">
                    <div>
                        <label for="pincode">Pincode</label>
                        <input type="text" id="pincode" name="pincode">
                    </div>
                    <div>
                        <label for="phone">Phone *</label>
                        <input type="tel" id="phone" name="phone" required>
                    </div>
                </div>

                <label for="notes">Order notes (optional)</label>
                <textarea id="notes" name="notes" rows="2" placeholder="Any delivery instructions?"></textarea>

                <div class="payment-methods">
                    <div class="payment-method active" data-method="razorpay">
                        <img src="https://razorpay.com/assets/razorpay-logo.svg" alt="Razorpay">
                        <div>Card/UPI/Wallet</div>
                    </div>
                    <div class="payment-method" data-method="cod">
                        <div style="font-size:20px;">💰</div>
                        <div>COD</div>
                    </div>
                </div>

                <button type="submit" class="btn-primary" id="payButton">
                    Pay ₹<?php echo number_format($grandTotal, 2); ?> securely
                </button>
            </form>
        </div>

        <!-- Right: order summary -->
        <div class="card">
            <h3>Order summary (<?php echo count($items); ?> items)</h3>

            <?php if (empty($items)) { ?>
                <p style="text-align:center;color:#888;">Your cart is empty</p>
            <?php } else { ?>
                <div class="summary-list">
                    <?php foreach ($items as $it) { ?>
                        <div class="summary-item">
                            <div class="summary-thumb">
                                <img src="<?php echo htmlspecialchars($it['image']); ?>" alt="">
                            </div>
                            <div class="summary-text">
                                <div class="summary-name"><?php echo htmlspecialchars($it['name']); ?></div>
                                <div class="summary-meta">
                                    quantity: <?php echo (int)$it['quantity']; ?>
                                    <?php if (!empty($it['size'])) echo " • Size: ".htmlspecialchars($it['size']); ?>
                                </div>
                                <div style="font-weight:600;margin-top:4px;">
                                    ₹<?php echo number_format($it['price'] * $it['quantity'], 2); ?>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>

                <div class="summary-line">
                    <span>Subtotal</span>
                    <span>₹<?php echo number_format($subtotal, 2); ?></span>
                </div>
                <div class="summary-line">
                    <span>Shipping</span>
                    <span>Free</span>
                </div>
                <?php if ($discount > 0) { ?>
                <div class="summary-line">
                    <span>Discount</span>
                    <span>-₹<?php echo number_format($discount, 2); ?></span>
                </div>
                <?php } ?>
                <div class="summary-line total">
                    <span>To pay</span>
                    <span>₹<?php echo number_format($grandTotal, 2); ?></span>
                </div>
            <?php } ?>
        </div>
    </div>
</div>

<script>
// payment method toggle
document.querySelectorAll('.payment-method').forEach(btn => {
  btn.addEventListener('click', function () {
    document.querySelectorAll('.payment-method').forEach(b => b.classList.remove('active'));
    this.classList.add('active');
  });
});

// GA4 begin_checkout (safe: only run if gtag exists and items present)
if (typeof gtag === 'function' && <?php echo !empty($items) ? 'true' : 'false'; ?>) {
    gtag('event', 'begin_checkout', {
        currency: 'INR',
        value: <?php echo json_encode((float)$grandTotal); ?>,
        items: <?php echo json_encode($items); ?>
    });
}

// Razorpay + COD payment
const checkoutForm = document.getElementById('checkoutForm');
checkoutForm.addEventListener('submit', function(e) {
    e.preventDefault();

    const payButton = document.getElementById('payButton');
    const form = this;

    const fullname = form.fullname.value.trim();
    const address = form.address.value.trim();
    const phone   = form.phone.value.trim();

    if (!fullname || !address || !phone) {
        alert('Please fill all required fields');
        return;
    }

    payButton.disabled = true;
    const originalText = payButton.textContent;
    payButton.textContent = 'Creating payment...';
    document.body.classList.add('loading');

    const activeMethod = document.querySelector('.payment-method.active')?.dataset.method || 'razorpay';

    function resetButton() {
        payButton.disabled = false;
        payButton.textContent = originalText;
        document.body.classList.remove('loading');
    }

    // COD flow
    if (activeMethod === 'cod') {
        const formData = new FormData(form);

        fetch('place_order_cod.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.text())
        .then(text => {
            console.log('COD raw response:', text);
            let data;
            try {
                data = JSON.parse(text);
            } catch (e) {
                alert('COD raw response:\n' + text);
                resetButton();
                return;
            }
            if (!data.success) {
                alert(data.error || 'COD order failed');
                resetButton();
                return;
            }
            window.location.href = 'my_orders.php';
        })
        .catch(err => {
            alert('COD error: ' + err.message);
            resetButton();
        });

        return; // skip Razorpay code
    }

    // Razorpay flow
    const formData  = new FormData(form);
    const orderData = Object.fromEntries(formData.entries());

    fetch('payment.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify(orderData)
    })
    .then(res => res.json())
    .then(data => {
        if (!data.success) {
            alert(data.error || 'Payment setup failed');
            resetButton();
            return;
        }

        const options = {
            key: 'rzp_test_RnW80gSg2dWQom', // your Razorpay test key ID
            amount: data.amount * 100,
            currency: 'INR',
            name: 'CLOTHZONE',
            order_id: data.order_id,

            handler: function (response) {
                window.location.href =
                    'payment_success.php?payment_id=' + encodeURIComponent(response.razorpay_payment_id) +
                    '&order_id=' + encodeURIComponent(data.order_id);
            },

            prefill: {
                name: orderData.fullname,
                contact: orderData.phone // ✅ pass phone so field is editable
            },

            theme: {
                color: '#000000'
            }
        };

        new Razorpay(options).open();
    })
    .catch(err => {
        alert('Payment error: ' + err.message);
        resetButton();
    });
});
</script>

<?php if (!empty($items)) { ?>
<script>
  if (typeof gtag === 'function') {
    gtag('event', 'begin_checkout', {
      currency: 'INR',
      value: <?php echo json_encode((float)$grandTotal ?? (float)$subtotal); ?>,
      items: [
        <?php
        $first = true;
        foreach ($items as $it) {
            if (!$first) echo ",";
            $first = false;
            ?>
            {
              item_id: <?php echo json_encode($it['id']); ?>,
              item_name: <?php echo json_encode($it['name'] ?? ''); ?>,
              price: <?php echo json_encode((float)$it['price']); ?>,
              quantity: <?php echo json_encode((int)$it['quantity']); ?>
            }
        <?php } ?>
      ]
    });
  }
</script>
<?php } ?>

<?php include 'footer.php'; ?>
</body>
</html>
