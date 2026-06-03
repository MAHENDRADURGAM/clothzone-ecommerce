<?php
// Aliexpress API wrapper - uses config/config.php for keys
$config_path = __DIR__ . '/config/config.php';
if (file_exists($config_path)) {
    include $config_path;
    $CONFIG_LOCAL = $CONFIG ?? [];
} else {
    $CONFIG_LOCAL = [];
}

function aliAPI($endpoint, $params = []) {
    global $CONFIG_LOCAL;
    $base = rtrim($CONFIG_LOCAL['aliexpress_base_url'] ?? 'https://ali-express1.p.rapidapi.com/', '/').'/';
    $host = $CONFIG_LOCAL['rapidapi_host'] ?? 'ali-express1.p.rapidapi.com';
    $key = $CONFIG_LOCAL['rapidapi_key'] ?? '';

    // Build URL
    $url = $base . ltrim($endpoint, '/');
    if (!empty($params)) {
        $url .= '?' . http_build_query($params);
    }

    $headers = [
        'x-rapidapi-host: ' . $host,
        'x-rapidapi-key: ' . $key
    ];

    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_TIMEOUT => 20
    ]);

    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);

    if ($err) {
        return ['error' => $err];
    }

    $decoded = json_decode($response, true);
    if ($decoded === null) {
        // Not valid JSON? return raw.
        return ['raw' => $response];
    }
    return $decoded;
}

// Helper: try to extract product list from various response shapes
function extractProductsFromResponse($response) {
    // try several common paths
    $paths = [
        ['data','result','products'],
        ['result','products'],
        ['data','products'],
        ['products'],
        ['data','result','searchResult','products'],
        ['data','result','searchResult','searchResult'],
        ['data','result','searchResult'],
        ['data','result','searchResult','_trafficResult'],
        ['data','result']
    ];
    foreach ($paths as $path) {
        $node = $response;
        foreach ($path as $p) {
            if (is_array($node) && array_key_exists($p, $node)) {
                $node = $node[$p];
            } else {
                $node = null;
                break;
            }
        }
        if (is_array($node) && count($node) > 0) {
            // If the node contains nested fields, try to find product-like entries
            // Heuristic: look for items that contain title or productId or price
            $sample = null;
            if (array_values($node) !== $node) {
                // associative, maybe a single product
                $sample = [$node];
            } else {
                $sample = $node;
            }
            $valid = [];
            foreach ($sample as $item) {
                if (!is_array($item)) continue;
                $keys = array_keys($item);
                $joined = implode(',', $keys);
                if (stripos($joined,'title')!==false || stripos($joined,'product')!==false || stripos($joined,'image')!==false) {
                    $valid[] = $item;
                } else {
                    // still push but lower priority
                    $valid[] = $item;
                }
            }
            if (count($valid)>0) return $valid;
        }
    }
    return [];
}
?>