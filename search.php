<?php
include "config/config.php";
include "aliexpress_api.php";

$query = $_GET['q'] ?? '';

?><!DOCTYPE html>
<html><head><meta charset="utf-8"><title>Search results</title>
<style>body{font-family:Arial;padding:20px} .product{border:1px solid #ddd;padding:12px;width:220px;margin:10px;float:left}.product img{width:200px;height:200px;object-fit:cover}</style>
</head><body>
<h2>Search results for: <b><?php echo htmlspecialchars($query); ?></b></h2>
<?php
if ($query=='') {
    echo '<p>Please enter a search term.</p>';
    exit;
}

// call api
$response = aliAPI('search', ['query'=>$query,'page'=>1]);

$use_demo = false;
$products = [];

// handle API errors or empty results
if (!$response || isset($response['error'])) {
    $use_demo = true;
} else {
    $extracted = extractProductsFromResponse($response);
    if (!empty($extracted)) {
        $products = $extracted;
    } else {
        $use_demo = true;
    }
}

// If demo fallback, read local demo products
if ($use_demo) {
    $demo_file = __DIR__.'/assets/products/demo_products.json';
    if (file_exists($demo_file)) {
        $products = json_decode(file_get_contents($demo_file), true);
    } else {
        $products = [];
    }
}

if (empty($products)) {
    echo '<p>No products found.</p>';
    exit;
}

// display first 30
$count=0;
foreach ($products as $p) {
    if ($count++>=30) break;
    $image = $p['image'] ?? ($p['image_url'] ?? ($p['imageUrl'] ?? ''));
    $title = $p['title'] ?? ($p['productTitle'] ?? ($p['name'] ?? 'No title'));
    $price = $p['price'] ?? ($p['sale_price'] ?? ($p['priceText'] ?? ''));
    $pid = $p['productId'] ?? ($p['product_id'] ?? ($p['id'] ?? ''));
    ?>
    <div class="product">
        <?php if ($image): ?><img src="<?php echo htmlspecialchars($image); ?>" alt=""><?php endif; ?>
        <h4><?php echo htmlspecialchars($title); ?></h4>
        <p><b>Price:</b> <?php echo htmlspecialchars($price); ?></p>
        <a href="product.php?id=<?php echo urlencode($pid); ?>"><button>View Details</button></a>
    </div>
    <?php
}
?>
</body></html>
