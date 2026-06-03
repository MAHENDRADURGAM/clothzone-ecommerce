<?php
// nav.php
include 'db.php';

$current = basename($_SERVER['PHP_SELF']);
function isActive($file) {
    global $current;
    return $current === $file ? 'active-link' : '';
}

$cart_count = 0;
if (isset($_SESSION['user_id'])) {
    $uid = (int)$_SESSION['user_id'];
    $resCount = mysqli_query($conn, "SELECT SUM(quantity) AS c FROM cart WHERE user_id = $uid");
    $rowCount = mysqli_fetch_assoc($resCount);
    $cart_count = (int)$rowCount['c'];
}
?>

<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-WK7T91NLDX"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', 'G-WK7T91NLDX');
</script>

<style>
    body { margin:0; }

    .nav-bar {
        display:flex;
        align-items:center;
        justify-content:space-between;
        padding:12px 40px;
        background:#000;
        color:#fff;
        font-family:'Poppins',sans-serif;
        position:relative;
        z-index:200;
    }
    .nav-left,
    .nav-center,
    .nav-right {
        display:flex;
        align-items:center;
        gap:24px;
    }
    .brand {
        color:#fff;
        text-decoration:none;
        font-weight:600;
        font-size:20px;
        letter-spacing:2px;
    }
    .nav-center a {
        color:#fff;
        text-decoration:none;
        font-weight:500;
        font-size:15px;
    }
    .nav-center a:hover { text-decoration:underline; }

    .icon-btn {
        background:none;
        border:none;
        color:#fff;
        cursor:pointer;
        font-size:20px;
        position:relative;
    }
    .icon-btn a {
        color:#fff;
        text-decoration:none;
    }

    .account-wrapper { position:relative; }
    .account-menu {
        position:absolute;
        right:0;
        top:34px;
        background:#111;
        border-radius:10px;
        box-shadow:0 8px 24px rgba(0,0,0,0.45);
        padding:10px 0;
        width:210px;
        display:none;
    }
    .account-menu a {
        display:block;
        padding:9px 16px;
        color:#f5f5f5;
        text-decoration:none;
        font-size:14px;
    }
    .account-menu a:hover { background:#1f1f1f; }
    .account-menu-header {
        padding:8px 16px 6px;
        font-size:13px;
        text-transform:uppercase;
        letter-spacing:1px;
        color:#888;
    }

    @media (max-width: 768px) {
    .nav-bar {
        padding: 6px 10px;       /* less vertical padding */
        flex-wrap: wrap;
        row-gap: 6px;
    }
    .nav-center { display: none; }
}

    .nav-center a.active-link {
        border-bottom:2px solid #fff;
        padding-bottom:2px;
    }

    /* navbar search box (replaces blue button) */
    .nav-search-box {
    display:flex;
    align-items:center;
    background:#f5f5f5;
    border-radius:999px;
    padding:3px 10px;      /* was 4px 10px */
    max-width:210px;       /* was 260px */
    width:210px;           /* was 260px */
    margin-right:8px;
}


 .nav-search-box i {
        color:#777;
        font-size:14px;

 }
.nav-search-box input {
    border:none;
    outline:none;
    background:transparent;
    flex:1;
    padding:4px 6px;       /* slightly smaller height */
    font-size:12px;        /* a bit smaller text */
}


    /* Clothzone search overlay – now half page (top) */
    .cz-search-overlay {
        position: fixed;
        left: 0;
        right: 0;
        top: 0;
        height: 50vh;          /* half page */
        background:#fff;
        z-index: 9999;
        display:none;
        flex-direction:column;
        border-bottom:1px solid #eee;
    }

    /* header with search bar */
    .cz-search-header {
        display:flex;
        align-items:center;
        gap:16px;
        padding:16px 32px;
        border-bottom:1px solid #eee;
    }

    .cz-logo-text {
        font-weight:700;
        letter-spacing:2px;
        font-size:14px;
    }

    .cz-search-bar-wrap {
        flex:1;
        display:flex;
        align-items:center;
        background:#f5f5f5;
        border-radius:999px;
        padding:8px 14px;
        gap:10px;
    }

    .cz-search-bar-wrap i {
        color:#777;
    }

    #cz-search-input {
        border:none;
        outline:none;
        background:transparent;
        width:100%;
        font-size:15px;
    }

    .cz-close-btn {
        border:none;
        background:transparent;
        font-size:14px;
        cursor:pointer;
        color:#555;
    }

    /* body with chips */
    .cz-search-body {
        padding:18px 32px 32px;
        overflow-y:auto;
    }

    .cz-search-body h4 {
        font-size:13px;
        font-weight:600;
        margin:0 0 10px;
    }

    .cz-tag-list {
        display:flex;
        flex-wrap:wrap;
        gap:10px;
        margin-bottom:18px;
    }

    .cz-tag {
        border:none;
        border-radius:999px;
        padding:8px 16px;
        font-size:13px;
        background:#f5f5f5;
        cursor:pointer;
    }

    .cz-tag:hover {
        background:#e5e5e5;
    }

    @media(max-width:768px){
        .cz-search-header {
            padding:12px 16px;
        }
        .cz-search-body {
            padding:14px 16px 24px;
        }
    }
    
    @media (max-width: 768px) {
    .nav-bar {
        padding: 8px 10px;
        flex-wrap: wrap;                /* allow wrapping */
        row-gap: 6px;
    }

    /* left: logo full width */
    .nav-left {
        flex: 1 1 100%;
        justify-content: flex-start;
    }

    /* center menu hidden on mobile (already) */
    .nav-center {
        display: none;
    }

    /* right: search + icons on second row */
    .nav-right {
        flex: 1 1 100%;
        justify-content: space-between;
        gap: 8px;
    }

    /* search takes most of the width */
    .nav-search-box {
        flex: 1 1 auto;
        max-width: none;
        width: auto;
        margin-right: 0;
    }

    /* icons grouped but always visible */
    .account-wrapper,
    .icon-btn {
        flex: 0 0 auto;
    }
}

    
</style>

<script>
    function toggleAccountMenu(){
        const menu = document.getElementById('accountMenu');
        if (!menu) return;
        menu.style.display = (menu.style.display === 'block') ? 'none' : 'block';
    }
    window.addEventListener('click', function(e){
        const wrapper = document.getElementById('accountWrapper');
        const menu = document.getElementById('accountMenu');
        if (!wrapper || !menu) return;
        if (!wrapper.contains(e.target)) {
            menu.style.display = 'none';
        }
    });

    // Clothzone search overlay logic
    document.addEventListener('DOMContentLoaded', () => {
        const czOverlay    = document.getElementById('cz-search-overlay');
        const czCloseBtn   = document.getElementById('cz-close-search');
        const czInput      = document.getElementById('cz-search-input');
        const czRecentBox  = document.getElementById('cz-recent-tags');
        const czPopularBtns = document.querySelectorAll('#cz-popular-tags .cz-tag');
        const czNavInput   = document.getElementById('cz-nav-input');

        function loadRecent() {
            const stored = JSON.parse(localStorage.getItem('cz_recent_searches') || '[]');
            czRecentBox.innerHTML = '';
            stored.forEach(term => {
                const btn = document.createElement('button');
                btn.className = 'cz-tag';
                btn.textContent = term;
                btn.onclick = () => submitSearch(term);
                czRecentBox.appendChild(btn);
            });
        }

        function saveRecent(term) {
            if (!term.trim()) return;
            let list = JSON.parse(localStorage.getItem('cz_recent_searches') || '[]');
            list = [term, ...list.filter(t => t !== term)].slice(0,7);
            localStorage.setItem('cz_recent_searches', JSON.stringify(list));
            loadRecent();
        }

        function submitSearch(term) {
            const q = encodeURIComponent(term);
            window.location.href = 'products.php?search=' + q;
        }

        function czOpenSearch() {
            czOverlay.style.display = 'flex';
            setTimeout(() => czInput.focus(), 50);
            loadRecent();
        }
        function czCloseSearch() {
            czOverlay.style.display = 'none';
        }

        if (czCloseBtn) czCloseBtn.addEventListener('click', czCloseSearch);
        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') czCloseSearch();
        });

        // Enter key from overlay input
        czInput.addEventListener('keydown', e => {
            if (e.key === 'Enter') {
                const term = czInput.value;
                saveRecent(term);
                submitSearch(term);
            }
        });

        // clicking popular tags
        czPopularBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                const term = btn.textContent;
                saveRecent(term);
                submitSearch(term);
            });
        });

        // navbar search submit + focus opens half-screen panel
        window.czSubmitFromNav = function(e){
            e.preventDefault();
            const term = czNavInput.value.trim();
            if (!term) {
                czOpenSearch();
                return;
            }
            saveRecent(term);
            submitSearch(term);
        };

        if (czNavInput) {
            czNavInput.addEventListener('focus', () => {
                czOpenSearch();
                czInput.value = czNavInput.value;
            });
        }

        loadRecent();
    });
</script>

<div class="nav-bar">
    <div class="nav-left">
        <a href="index.php" class="brand">CLOTHZONE</a>
    </div>

    <div class="nav-center">
        <a href="index.php" class="<?php echo isActive('index.php'); ?>">Home</a>
        <a href="products.php?category=Men"
           class="<?php echo ($current==='products.php' && ($_GET['category'] ?? '')==='Men') ? 'active-link' : ''; ?>">Men</a>
        <a href="products.php?category=Women"
           class="<?php echo ($current==='products.php' && ($_GET['category'] ?? '')==='Women') ? 'active-link' : ''; ?>">Women</a>
        <a href="products.php?category=Kids"
           class="<?php echo ($current==='products.php' && ($_GET['category'] ?? '')==='Kids') ? 'active-link' : ''; ?>">Kids</a>
    </div>

    <div class="nav-right">
        <!-- Search box in navbar (replaces blue button) -->
        <form class="nav-search-box" onsubmit="czSubmitFromNav(event)">
            <i class="fas fa-search"></i>
            <input id="cz-nav-input" type="text" placeholder="Search Clothzone..." autocomplete="off">
        </form>

        <!-- Cart icon -->
        <button class="icon-btn" title="Cart">
            <a href="cart.php">🛒</a>
            <?php if ($cart_count > 0) { ?>
            <span style="
    position:absolute;
    top:-2px;
    right:0;
    background:#ff4d4d;
    color:#fff;
    font-size:11px;
    padding:1px 6px;
    border-radius:999px;
    font-weight:600;
">

                    <?php echo $cart_count; ?>
                </span>
            <?php } ?>
        </button>

        <!-- Account icon + dropdown -->
        <div class="account-wrapper" id="accountWrapper">
            <button class="icon-btn" type="button" onclick="toggleAccountMenu()" title="Account">
                👤
            </button>
            <div class="account-menu" id="accountMenu">
                <?php if (isset($_SESSION['user_id'])) { ?>
                    <div class="account-menu-header">My Account</div>
                    <a href="profile.php">Profile</a>
                    <a href="wishlist.php">Wishlist</a>
                    <a href="my_orders.php">My Orders</a>
                    <a href="logout_user.php">Logout</a>
                <?php } else { ?>
                    <div class="account-menu-header">Welcome</div>
                    <a href="login.php">Login</a>
                    <a href="register.php">Join Us</a>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

<!-- Clothzone half-screen search overlay -->
<div id="cz-search-overlay" class="cz-search-overlay">
    <div class="cz-search-header">
        <span class="cz-logo-text">CLOTHZONE</span>
        <div class="cz-search-bar-wrap">
            <i class="fas fa-search"></i>
            <input id="cz-search-input" type="text" placeholder="Search" autocomplete="off">
        </div>
        <button id="cz-close-search" class="cz-close-btn">Cancel</button>
    </div>

    <div class="cz-search-body">
        <h4>Popular Search Terms</h4>
        <div id="cz-popular-tags" class="cz-tag-list">
            <button class="cz-tag">men jackets</button>
            <button class="cz-tag">oversized t-shirt</button>
            <button class="cz-tag">baggy jeans</button>
            <button class="cz-tag">hoodies</button>
            <button class="cz-tag">women kurti</button>
            <button class="cz-tag">kids winter</button>
        </div>

        <h4 class="cz-recent-title">Recent Searches</h4>
        <div id="cz-recent-tags" class="cz-tag-list"></div>
    </div>
</div>
