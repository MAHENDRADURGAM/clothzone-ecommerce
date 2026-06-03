<?php include 'nav.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Page not found - CLOTHZONE</title>
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <style>
        .notfound-wrap {
            min-height:70vh;
            display:flex;
            flex-direction:column;
            align-items:center;
            justify-content:center;
            font-family:'Poppins',sans-serif;
            text-align:center;
            color:#111;
        }
        .notfound-code {
            font-size:70px;
            font-weight:800;
            letter-spacing:4px;
        }
        .notfound-title {
            font-size:22px;
            margin-top:6px;
        }
        .notfound-text {
            font-size:14px;
            margin-top:10px;
            color:#666;
        }
        .notfound-links {
            margin-top:18px;
            display:flex;
            gap:12px;
            flex-wrap:wrap;
            justify-content:center;
        }
        .nf-btn {
            padding:9px 18px;
            border-radius:999px;
            border:none;
            text-decoration:none;
            font-size:14px;
            font-weight:600;
        }
        .nf-primary {
            background:#000;
            color:#fff;
        }
        .nf-secondary {
            background:#f3f3f3;
            color:#111;
        }
    </style>
</head>
<body>
<div class="notfound-wrap">
    <div class="notfound-code">404</div>
    <div class="notfound-title">We couldn’t find that page</div>
    <div class="notfound-text">
        The link may be broken or the item was moved. Let’s get you back to shopping.
    </div>
    <div class="notfound-links">
        <a href="index.php" class="nf-btn nf-primary">Go to Home</a>
        <a href="products.php" class="nf-btn nf-secondary">Browse Products</a>
    </div>
</div>
<?php include 'footer.php'; ?>
</body>
</html>
