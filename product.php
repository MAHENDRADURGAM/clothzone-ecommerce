<?php
include "config/config.php";
include "aliexpress_api.php";

$id = $_GET['id'] ?? '';
if (!$id) { die('Product ID missing.'); }

$use_demo = false;
$product = null;

// If id starts with demo- then load from demo JSON
if (strpos($id, 'demo-') === 0) {
    $demo_file = __DIR__.'/assets/products/demo_products.json';
    if (file_exists($demo_file)) {
        $demo_list = json_decode(file_get_contents($demo_file), true);
        foreach ($demo_list as $d) {
            if (($d['product_id'] ?? '') === $id) {
                $product = $d;
                break;
            }
        }
    }
    if (!$product) {
        die('Demo product not found.');
    }
} else {
    // call real API
    $response = aliAPI('product/' . $id);
    if (!$response || isset($response['error'])) {
        $use_demo = true;
    } else {
        // try to locate product data
        $candidate = null;
        if (isset($response['data']) && isset($response['data']['result'])) $candidate = $response['data']['result'];
        if (!$candidate && isset($response['result'])) $candidate = $response['result'];
        if (!$candidate && isset($response['data'])) $candidate = $response['data'];
        if (!$candidate && isset($response['item'])) $candidate = $response['item'];
        if (!$candidate && is_array($response)) $candidate = $response;

        if ($candidate) {
            $product = $candidate;
        } else {
            $use_demo = true;
        }
    }
    // fallback to demo if API failed
    if ($use_demo && !$product) {
        $demo_file = __DIR__.'/assets/products/demo_products.json';
        if (file_exists($demo_file)) {
            $demo_list = json_decode(file_get_contents($demo_file), true);
            foreach ($demo_list as $d) {
                if (($d['product_id'] ?? '') === $id) {
                    $product = $d;
                    break;
                }
            }
        }
        if (!$product) {
            // show generic demo (first)
            if (!empty($demo_list)) $product = $demo_list[0];
        }
    }
}

$title = $product['title'] ?? $product['productTitle'] ?? ($product['name'] ?? 'No title');
$image = $product['image'] ?? $product['image_url'] ?? ($product['imageUrl'] ?? '');
$price = $product['price'] ?? $product['sale_price'] ?? ($product['priceText'] ?? '');
$desc = $product['description'] ?? $product['desc'] ?? '';

?><!DOCTYPE html><html><head><meta charset="utf-8"><title><?php echo htmlspecialchars($title); ?></title>
<style>body{font-family:Arial;padding:20px} img{width:300px;height:300px;object-fit:cover}</style>
</head><body>
<h1><?php echo htmlspecialchars($title); ?></h1>
<?php if ($image): ?><img src="<?php echo htmlspecialchars($image); ?>" alt=""><?php endif; ?>
<p><b>Price:</b> <?php echo htmlspecialchars($price); ?></p>
<h3>Description</h3>
<p><?php echo nl2br(htmlspecialchars($desc)); ?></p>
</body></html>