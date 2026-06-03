<?php
session_start();
include 'db.php';

// safety: ensure id is integer
if (!isset($_GET['id'])) {
    die("Product not found");
}
$id = (int) $_GET['id'];

// fetch the product
$product_q = mysqli_query($conn, "SELECT * FROM products WHERE id = $id LIMIT 1");
if (!$product_q || mysqli_num_rows($product_q) === 0) {
    die("Product not found");
}
$product = mysqli_fetch_assoc($product_q);

// track recently viewed in session
if (!isset($_SESSION['recent_viewed'])) {
    $_SESSION['recent_viewed'] = [];
}
$_SESSION['recent_viewed'] = array_diff($_SESSION['recent_viewed'], [$id]);
array_unshift($_SESSION['recent_viewed'], $id);
$_SESSION['recent_viewed'] = array_slice($_SESSION['recent_viewed'], 0, 6);
$recent_ids = array_filter($_SESSION['recent_viewed'], function($pid) use ($id){
    return $pid != $id;
});

$recent_products = null;
if (!empty($recent_ids)) {
    $id_list = implode(',', array_map('intval', $recent_ids));
    $sqlRecent = "SELECT * FROM products WHERE id IN ($id_list)";
    $recent_products = mysqli_query($conn, $sqlRecent);
}

// random recommendations
$rec_q = mysqli_query($conn, "SELECT * FROM products WHERE id <> $id ORDER BY RAND() LIMIT 4");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

<title><?php echo htmlspecialchars($product['name']); ?> — CLOTHZONE</title>
<meta name="viewport" content="width=device-width,initial-scale=1" />
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/premium.css?v=3">
<style>
.product-page { max-width:1200px; margin:40px auto; padding:0 16px; display:flex; gap:40px; align-items:flex-start; }
@media (max-width: 980px) { .product-page { flex-direction:column; } }
.image-section { flex:1; min-width:320px; }
.image-wrap { width:100%; overflow:hidden; border-radius:12px; }
.image-wrap img { width:100%; display:block; transition: transform .35s ease; transform-origin: center center; }
.image-wrap.zoom img:hover { transform: scale(1.15); }
.thumbs { display:flex; gap:12px; margin-top:14px; }
.thumb { width:80px; height:80px; border-radius:8px; overflow:hidden; cursor:pointer; border:2px solid transparent; }
.thumb img { width:100%; height:100%; object-fit:cover; display:block; }
.thumb.active { border-color:#111; }
.details { flex:1; }
.details h1 { font-size:30px; margin-bottom:8px; color:#111; }
.price { font-size:26px; font-weight:800; color:#111; margin-bottom:14px; }
.sizes { display:flex; gap:10px; flex-wrap:wrap; margin:12px 0; }
.size-btn { padding:10px 16px; border-radius:8px; border:1px solid #ddd; background:#fff; cursor:pointer; font-weight:600; }
.size-btn.active { background:#111; color:#fff; border-color:#111; }
.quantity { margin-top:12px; display:flex; gap:10px; align-items:center; }
.quantity input[type="number"] { width:80px; padding:10px; border-radius:8px; border:1px solid #e6e6e6; }
.cta-row { margin-top:18px; display:flex; gap:12px; align-items:center; flex-wrap:wrap; }
.btn-primary { background:#000; color:#fff; border:none; padding:10px 20px; border-radius:999px; font-weight:600; cursor:pointer; }
.btn-ghost { background:#fff; color:#000; border:1px solid #ddd; padding:10px 20px; border-radius:999px; font-weight:600; text-decoration:none; display:inline-block; text-align:center; }
.wishlist-btn { background:transparent; border:1px solid #f0f0f0; padding:10px 12px; border-radius:8px; cursor:pointer; font-size:18px; text-decoration:none; color:#111; display:inline-block; }
.desc-box { margin-top:28px; color:#444; line-height:1.6; }
.recommendations { margin-top:40px; }
.recommendations h3 { margin-bottom:14px; }
.reco-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(200px,1fr)); gap:18px; }
/* Recently viewed */
.recent-section { margin-top:40px; }
.recent-title { font-size:20px; font-weight:600; margin-bottom:10px; }
.recent-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(200px,1fr)); gap:18px; }
.recent-card { background:#fff; border-radius:12px; overflow:hidden; box-shadow:0 4px 14px rgba(0,0,0,0.08); cursor:pointer; text-decoration:none; color:inherit; }
.recent-card img { width:100%; height:170px; object-fit:cover; }
.recent-card-body { padding:10px 12px 12px; }
.recent-name { margin:0 0 4px; font-size:14px; font-weight:600; }
.recent-price { font-size:14px; font-weight:600; color:#444; }
/* sticky add-to-cart for mobile */
.sticky-cart { display:none; }
@media (max-width:900px) {
  .sticky-cart {
    display:flex;
    position:fixed;
    left:50%;
    transform:translateX(-50%);
    bottom:14px;
    background:#111;
    color:#fff;
    padding:12px 16px;
    border-radius:10px;
    gap:12px;
    z-index:2200;
    align-items:center;
    box-shadow: 0 10px 30px rgba(0,0,0,0.4);
  }
  .sticky-cart .btn-primary { padding:10px 18px; border-radius:8px; }
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
    /* Product / recommendation cards (full-width white cards) */
    .product-card,
    .recent-card,
    .reco-card {
        width: 100%;
        max-width: 100%;
        box-sizing: border-box;
    }

    .product-card img,
    .recent-card img,
    .reco-card img {
        width: 100%;      /* take full card width */
        height: auto;     /* keep aspect ratio */
        object-fit: cover;
        display: block;
    }
}


</style>
</head>
<body>

<?php include 'nav.php'; ?>

<main class="product-page">
  <section class="image-section">
    <div class="image-wrap zoom" id="mainImageWrap">
      <img id="mainImage" src="<?php echo htmlspecialchars($product['image'] ?: 'assets/images/product-fallback.png'); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
    </div>

    <div class="thumbs" id="thumbs">
      <div class="thumb active" data-src="<?php echo htmlspecialchars($product['image'] ?: 'assets/images/product-fallback.png'); ?>">
        <img src="<?php echo htmlspecialchars($product['image'] ?: 'assets/images/product-fallback.png'); ?>" alt="">
      </div>
      <div class="thumb" data-src="assets/images/product-fallback.png">
        <img src="assets/images/product-fallback.png" alt="">
      </div>
      <div class="thumb" data-src="assets/images/product-fallback.png">
        <img src="assets/images/product-fallback.png" alt="">
      </div>
    </div>
  </section>

  <section class="details">
    <h1><?php echo htmlspecialchars($product['name']); ?></h1>
    <div class="price">₹<?php echo number_format((float)$product['price'], 2); ?></div>

    <div>
      <strong>Select size</strong>
      <div class="sizes" id="sizes">
        <button type="button" class="size-btn" data-size="S">S</button>
        <button type="button" class="size-btn" data-size="M">M</button>
        <button type="button" class="size-btn" data-size="L">L</button>
        <button type="button" class="size-btn" data-size="XL">XL</button>
      </div>
    </div>

    <!-- quantity + wishlist -->
    <div class="quantity">
      <div>
        <strong>Quantity</strong><br>
        <input type="number" id="quantityInput" value="1" min="1">
      </div>
      <div style="margin-left:auto">
        <!-- Heart is a pure link, no form -->
        <a href="add_to_wishlist.php?id=<?php echo (int)$product['id']; ?>"
           class="wishlist-btn"
           title="Add to wishlist">♡</a>
      </div>
    </div>

    <!-- cart -->
    <div class="cta-row">
      <form method="GET" action="add_to_cart.php" id="addToCartForm" style="display:inline-block">
        <input type="hidden" name="id" value="<?php echo (int)$product['id']; ?>">
        <input type="hidden" name="size" id="formSize" value="">
        <input type="hidden" name="quantity" id="formquantity" value="1">
        <button type="submit" class="btn-primary">Add to cart</button>
      </form>

      <a href="products.php" class="btn-ghost">Back to collection</a>
    </div>

    <div class="desc-box">
      <h3>Product Details</h3>
      <p><?php echo nl2br(htmlspecialchars($product['description'] ?: "High-quality premium fabric clothing.")); ?></p>
    </div>

    <div class="recommendations">
      <h3>You may also like</h3>
      <div class="reco-grid">
        <?php while ($r = mysqli_fetch_assoc($rec_q)) { ?>
          <div class="card">
            <a href="product_details.php?id=<?php echo (int)$r['id']; ?>">
              <img src="<?php echo htmlspecialchars($r['image'] ?: 'assets/images/product-fallback.png'); ?>" alt="<?php echo htmlspecialchars($r['name']); ?>" style="height:160px; object-fit:cover; border-radius:6px;">
            </a>
            <div style="padding-top:10px">
              <div style="font-weight:700"><?php echo htmlspecialchars($r['name']); ?></div>
              <div style="color:#6b7280; margin-top:6px;">₹<?php echo number_format((float)$r['price'],2); ?></div>
            </div>
          </div>







        <?php } ?>
      </div>
    </div>



    <?php if (!empty($recent_ids) && $recent_products && mysqli_num_rows($recent_products) > 0) { ?>
      <div class="recent-section">
        <div class="recent-title">Recently Viewed</div>
        <div class="recent-grid">
          <?php while($rp = mysqli_fetch_assoc($recent_products)) { ?>
            <a class="recent-card" href="product_details.php?id=<?php echo $rp['id']; ?>">
              <img src="<?php echo htmlspecialchars($rp['image']); ?>" alt="<?php echo htmlspecialchars($rp['name']); ?>">
              <div class="recent-card-body">
                  <p class="recent-name"><?php echo htmlspecialchars($rp['name']); ?></p>
                  <p class="recent-price">₹<?php echo $rp['price']; ?></p>
              </div>
            </a>
          <?php } ?>
        </div>
      </div>
    <?php } ?>
  </section>
</main>

<div class="sticky-cart" id="stickyCart">
  <div style="font-weight:700;">₹<span id="stickyPrice"><?php echo number_format((float)$product['price'],2); ?></span></div>
  <button class="btn-primary" id="stickyAddCart">Add to cart</button>
</div>

<script>
document.querySelectorAll('.thumb').forEach(function(t){
  t.addEventListener('click', function(){
    document.querySelectorAll('.thumb').forEach(function(x){ x.classList.remove('active'); });
    this.classList.add('active');
    var src = this.getAttribute('data-src');
    document.getElementById('mainImage').src = src;
  });
});

document.querySelectorAll('.size-btn').forEach(function(btn){
  btn.addEventListener('click', function(){
    document.querySelectorAll('.size-btn').forEach(function(b){ b.classList.remove('active'); });
    this.classList.add('active');
    document.getElementById('formSize').value = this.getAttribute('data-size');
  });
});

var quantityInput = document.getElementById('quantityInput');
var formquantity  = document.getElementById('formquantity');

quantityInput.addEventListener('input', function(){
  formquantity.value = Math.max(1, parseInt(this.value) || 1);
});

document.getElementById('stickyAddCart').addEventListener('click', function(){
  document.getElementById('formquantity').value = Math.max(1, parseInt(quantityInput.value) || 1);
  document.getElementById('addToCartForm').submit();
});

function handleSticky() {
  var s = document.getElementById('stickyCart');
  if (window.innerWidth <= 900) s.style.display = 'flex'; else s.style.display = 'none';
}
window.addEventListener('resize', handleSticky);
handleSticky();
</script>

<?php include 'footer.php'; ?>
</body>
</html>
