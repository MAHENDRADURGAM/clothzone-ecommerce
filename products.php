<?php
session_start();
include 'db.php';
include 'nav.php';

// Pagination setup
$page  = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit = 12;
$offset = ($page - 1) * $limit;

// Sanitize filters
$category = $_GET['category'] ?? 'All';
$search   = isset($_GET['search']) ? trim($_GET['search']) : '';
$minPrice = $_GET['min'] ?? '';
$maxPrice = $_GET['max'] ?? '';
$sortBy   = $_GET['sort'] ?? 'newest';

$where = [];

// Category filter
if ($category !== 'All') {
    $cat_safe = mysqli_real_escape_string($conn, $category);
    $where[] = "category = '$cat_safe'";
}

// Text search (works with navbar/overlay/search box)
if ($search !== '') {
    $s = mysqli_real_escape_string($conn, $search);
    $where[] = "(name LIKE '%$s%' OR category LIKE '%$s%')";
}

// Price range filter
if ($minPrice !== '' && is_numeric($minPrice)) {
    $where[] = "price >= " . floatval($minPrice);
}
if ($maxPrice !== '' && is_numeric($maxPrice)) {
    $where[] = "price <= " . floatval($maxPrice);
}

// Sorting
$orderBy = "ORDER BY id DESC";
if ($sortBy === 'price_low') {
    $orderBy = "ORDER BY price ASC";
} elseif ($sortBy === 'price_high') {
    $orderBy = "ORDER BY price DESC";
} elseif ($sortBy === 'name') {
    $orderBy = "ORDER BY name ASC";
}

// Count query for total products (for pagination + subtitle)
$countSql = "SELECT COUNT(*) as total FROM products";
if (!empty($where)) {
    $countSql .= " WHERE " . implode(" AND ", $where);
}
$countResult   = mysqli_query($conn, $countSql);
$totalProducts = mysqli_fetch_assoc($countResult)['total'];
$totalPages    = ceil($totalProducts / $limit);

// Main product query with same filters + sort + pagination
$sql = "SELECT * FROM products";
if (!empty($where)) {
    $sql .= " WHERE " . implode(" AND ", $where);
}
$sql .= " $orderBy LIMIT $limit OFFSET $offset";
$result = mysqli_query($conn, $sql);

// Titles
if ($search !== '') {
    $pageTitle = 'Search CLOTHZONE';
    $subtitle  = 'Results for "' . htmlspecialchars($search) . '" (' . $totalProducts . ' items)';
} else {
    $pageTitle = htmlspecialchars($category) . ' Collection';
    $subtitle  = 'Browse the latest drops for Men, Women & Kids. (' . $totalProducts . ' items)';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Products - CLOTHZONE</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background: #fafafa;
            color: #111;
            min-height:100vh;
            display:flex;
            flex-direction:column;
        }
        .page-wrap { flex:1; padding-bottom:40px; }
        .page-header-wrap { padding: 40px 20px 10px; }
        .page-header-inner { max-width:1200px; margin:0 auto; }
        .page-title { font-size: 34px; font-weight: 700; letter-spacing: 1px; color: #111; margin:0 0 6px; }
        .page-subtitle { font-size:14px; color:#777; margin:0; }
        .filter-bar {
            max-width: 1200px; margin: 20px auto 15px; padding: 0 20px;
            display:flex; flex-wrap:wrap; gap:15px; align-items:center; justify-content:flex-start;
        }
        .filter-bar form { display:flex; flex-wrap:wrap; gap:12px; align-items:center; }
        .filter-input { padding:8px 10px; border-radius:999px; border:1px solid #ccc; font-size:14px; min-width:160px; }
        .filter-btn { padding:9px 18px; border-radius:999px; border:none; background:#000; color:#fff; font-weight:600; cursor:pointer; font-size:14px; }
        .filter-btn:hover { background:#333; }
        .sort-bar {
            max-width:1200px; margin:10px auto; padding:0 20px;
            display:flex; justify-content:space-between; align-items:center;
            flex-wrap:wrap; gap:10px;
        }
        .sort-select { padding:8px 14px; border-radius:999px; border:1px solid #ccc; font-size:14px; background:#fff; cursor:pointer; }
        .category-filter { max-width:1200px; margin: 5px auto 20px; padding:0 20px; display: flex; justify-content:flex-start; gap: 16px; flex-wrap: wrap; }
        .category-filter button {
            background: none; border: 2px solid #333; border-radius: 30px; padding: 8px 22px;
            font-size: 14px; font-weight: 600; cursor: pointer; transition: all 0.3s ease; color: #333;
        }
        .category-filter button.active, .category-filter button:hover {
            background: #000; color: #fff; border-color: #000;
        }
        .product-grid {
            max-width: 1200px; margin: 0 auto 40px; padding: 0 20px;
            display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 35px;
        }
        .product-card {
            position: relative; background: #fff; border-radius: 15px; overflow: hidden;
            box-shadow: 0 8px 20px rgba(0,0,0,0.08); transition: transform 0.3s ease, box-shadow 0.3s ease;
            cursor: pointer; display: flex; flex-direction: column;
        }
        .product-card:hover { transform: translateY(-10px); box-shadow: 0 16px 40px rgba(0,0,0,0.15); }
        .product-card img {
            width: 100%; height: 300px; object-fit: cover; transition: transform 0.4s ease;
        }
        .product-card:hover img { transform: scale(1.08); }
        .product-info { padding: 18px 18px 16px; flex-grow: 1; display: flex; flex-direction: column; justify-content: space-between; }
        .product-name { font-size: 18px; font-weight: 700; margin: 0 0 6px; color: #111; text-transform: capitalize; }
        .product-price { font-size: 16px; font-weight: 600; color: #666; margin-bottom: 10px; }
        .product-actions { display: flex; justify-content: space-between; gap:10px; }
        .btn {
            flex:1; padding: 9px 10px; background: #000; color: white; border: none; border-radius: 30px;
            font-weight: 600; font-size: 14px; cursor: pointer; text-decoration: none; text-align:center; transition: background 0.3s ease;
        }
        .btn:hover { background: #444; }
        .empty-msg {
            text-align:center; margin:40px 0 60px; font-size:18px; color:#555;
        }
        .pagination {
            max-width:1200px; margin:40px auto 60px; padding:0 20px;
            display:flex; justify-content:center; gap:10px; flex-wrap:wrap;
        }
        .page-link {
            padding:10px 16px; border:2px solid #333; border-radius:8px; background:#fff; color:#333;
            text-decoration:none; font-weight:600; font-size:14px; transition:all 0.3s;
        }
        .page-link:hover, .page-link.active { background:#000; color:#fff; border-color:#000; }
        .page-link.disabled { opacity:0.4; pointer-events:none; }
        @media(max-width:768px){
            .page-header-wrap { padding-top:30px; }
            .page-title { font-size:26px; }
            .filter-bar { justify-content:center; }
            .category-filter { justify-content:center; }
            .sort-bar { justify-content:center; }
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
    <script>
        function setCategory(cat) {
            const url = new URL(window.location.href);
            if (cat === 'All') url.searchParams.delete('category');
            else url.searchParams.set('category', cat);
            url.searchParams.delete('page');
            window.location.href = url.toString();
        }
        function changeSort() {
            const sort = document.getElementById('sortSelect').value;
            const url = new URL(window.location.href);
            if (sort) url.searchParams.set('sort', sort);
            else url.searchParams.delete('sort');
            url.searchParams.delete('page');
            window.location.href = url.toString();
        }
        window.onload = () => {
            const url = new URL(window.location.href);
            const cat = url.searchParams.get('category') || 'All';
            document.querySelectorAll('.category-filter button').forEach(btn => {
                if (btn.dataset.cat === cat) btn.classList.add('active');
            });
            const sortVal = url.searchParams.get('sort') || 'newest';
            const sortSelect = document.getElementById('sortSelect');
            if (sortSelect) sortSelect.value = sortVal;
        };
    </script>
</head>
<body>

<div class="page-wrap">
    <div class="page-header-wrap">
        <div class="page-header-inner">
            <h1 class="page-title"><?php echo $pageTitle; ?></h1>
            <p class="page-subtitle"><?php echo $subtitle; ?></p>
        </div>
    </div>

<div class="filter-bar">
    <form method="GET">
        <?php if ($category !== 'All') { ?>
            <input type="hidden" name="category" value="<?php echo htmlspecialchars($category); ?>">
        <?php } ?>
        <input type="text" class="filter-input" name="search"
               placeholder="Search CLOTHZONE..."
               value="<?php echo htmlspecialchars($search); ?>">
        <input type="number" step="0.01" class="filter-input" name="min"
               placeholder="Min price"
               value="<?php echo htmlspecialchars($minPrice); ?>">
        <input type="number" step="0.01" class="filter-input" name="max"
               placeholder="Max price"
               value="<?php echo htmlspecialchars($maxPrice); ?>">
        <button type="submit" class="filter-btn">Apply</button>
    </form>
</div>


    <div class="sort-bar">
        <div style="font-size:14px;color:#666;">Showing <?php echo mysqli_num_rows($result); ?>
            of <?php echo $totalProducts; ?> products</div>
        <select id="sortSelect" class="sort-select" onchange="changeSort()">
            <option value="newest">Newest First</option>
            <option value="price_low">Price: Low to High</option>
            <option value="price_high">Price: High to Low</option>
            <option value="name">Name: A to Z</option>
        </select>
    </div>

    <div class="category-filter">
        <button data-cat="All" onclick="setCategory('All');return false;">All</button>
        <button data-cat="Men" onclick="setCategory('Men');return false;">Men</button>
        <button data-cat="Women" onclick="setCategory('Women');return false;">Women</button>
        <button data-cat="Kids" onclick="setCategory('Kids');return false;">Kids</button>
    </div>

    <div class="product-grid">
        <?php if (mysqli_num_rows($result) == 0) { ?>
            <div class="empty-msg" style="grid-column:1/-1;">
                No products found for this filter.
            </div>
        <?php } else { ?>
            <?php while ($row = mysqli_fetch_assoc($result)) {
                $id    = $row['id'];
                $name  = htmlspecialchars($row['name']);
                $price = number_format($row['price'], 2);
                $image = htmlspecialchars($row['image']);
            ?>
                <div class="product-card">
                    <a href="product_details.php?id=<?php echo $id; ?>">
                        <img src="<?php echo $image; ?>" alt="<?php echo $name; ?>">
                    </a>
                    <div class="product-info">
                        <div>
                            <div class="product-name"><?php echo $name; ?></div>
                            <div class="product-price">₹<?php echo $price; ?></div>
                        </div>
                        <div class="product-actions">
                            <a class="btn" href="product_details.php?id=<?php echo $id; ?>">View</a>
                            <a class="btn" href="add_to_cart.php?id=<?php echo $id; ?>">Add to Cart</a>
                        </div>
                    </div>
                </div>
            <?php } ?>
        <?php } ?>
    </div>

    <?php if ($totalPages > 1) { ?>
        <div class="pagination">
            <?php
            $params = $_GET;
            unset($params['page']);
            $baseUrl = 'products.php?' . http_build_query($params) . '&page=';
            ?>
            <a href="<?php echo $baseUrl . max(1, $page - 1); ?>" class="page-link <?php if ($page <= 1) echo 'disabled'; ?>">← Prev</a>
            <?php for ($i = 1; $i <= $totalPages; $i++) {
                if ($i == 1 || $i == $totalPages || abs($i - $page) <= 2) {
                    $activeClass = $i == $page ? 'active' : '';
                    echo "<a href=\"{$baseUrl}{$i}\" class=\"page-link {$activeClass}\">{$i}</a>";
                } elseif (abs($i - $page) == 3) {
                    echo '<span style="padding:10px 5px;">...</span>';
                }
            } ?>
            <a href="<?php echo $baseUrl . min($totalPages, $page + 1); ?>" class="page-link <?php if ($page >= $totalPages) echo 'disabled'; ?>">Next →</a>
        </div>
    <?php } ?>
</div>

<?php include 'footer.php'; ?>
</body>
</html>
