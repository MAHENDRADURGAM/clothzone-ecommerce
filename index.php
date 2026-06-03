<?php
session_start();
include 'db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clothing Home</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            margin: 0;
            font-family: Poppins, sans-serif;
            background: #f4f4f4;
        }

        /* CATEGORY GRID */
        .categories-wrap {
            width: 100%;
            box-sizing: border-box;
            padding: 60px 40px;
            display: flex;
            justify-content: center;
        }
        .categories {
            width: 100%;
            max-width: 1200px;
            display: grid;
            gap: 25px;
            grid-template-columns: repeat(3, 1fr);
        }
        .cat-box {
            background: #fff;
            height: 250px;
            border-radius: 14px;
            overflow: hidden;
            position: relative;
            cursor: pointer;
            transition: transform 0.3s ease;
        }
        .cat-box:hover { transform: scale(1.03); }
        .cat-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            filter: brightness(70%);
        }
        .cat-title {
            position: absolute;
            bottom: 20px;
            left: 20px;
            color: white;
            font-size: 26px;
            font-weight: 600;
        }

        /* TRENDING */
        .section-title {
            text-align: center;
            font-size: 32px;
            font-weight: 600;
            margin-top: 40px;
        }
        .trending-grid {
            padding: 40px 80px;
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(230px, 1fr));
            gap: 24px;
            box-sizing: border-box;
        }

        /* TOP SEASON STRIP */
        .season-banner {
            width: 100%;
            background: linear-gradient(90deg, #ff0040 0%, #ff1e62 40%, #ff6a00 100%);
            color: #fff;
            padding: 10px 40px;
            box-sizing: border-box;
        }
        .season-inner {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 12px;
            letter-spacing: 2px;
            text-transform: uppercase;
            font-weight: 700;
        }
        .season-center {
            font-size: 16px;
            letter-spacing: 3px;
        }
        .season-left,
        .season-right {
            opacity: 0.9;
        }

        /* SLIDER STYLES */
        .slider-section {
    width: 100%;
    height: 420px;          /* was 600px */
    overflow: hidden;
    position: relative;
    margin-bottom: 0;       /* no extra gap */
}
 /* no white gap */
        }
        .slider-container {
            position: relative;
            width: 100%;
            height: 100%;
        }
        .slide {
            display: none;
            width: 100%;
            height: 100%;
            position: absolute;
            top: 0;
            left: 0;
            opacity: 0;
            transition: opacity 0.8s ease-in-out;
        }
        .slide.active {
            display: flex;
            opacity: 1;
        }
        .slide-content {
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: 100%;
    height: 100%;
    padding: 0 40px;        /* was 0 80px */
    color: white;
    gap: 30px;              /* was 60px */
}

        }
        .slide-text {
            flex: 1;
            z-index: 10;
            animation: slideInLeft 0.8s ease-out;
        }
        .slide-label {
            display: inline-block;
            font-size: 12px;
            letter-spacing: 3px;
            text-transform: uppercase;
            color: #ffb81c;
            font-weight: 700;
            margin-bottom: 12px;
        }
        .slide-title {
            font-size: 56px;
            font-weight: 700;
            line-height: 1.2;
            margin: 15px 0;
            letter-spacing: -1px;
        }
        .slide-desc {
            font-size: 16px;
            color: #e0e0e0;
            margin: 20px 0 30px;
            line-height: 1.6;
            max-width: 450px;
        }
        .slide-btn {
            background: white;
            color: #000;
            padding: 14px 42px;
            font-size: 14px;
            font-weight: 700;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s ease;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .slide-btn:hover {
            background: #ffb81c;
            transform: scale(1.05);
        }

        /* SLIDER CONTROLS */
        .slider-btn {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(255, 255, 255, 0.3);
            color: white;
            border: 1px solid white;
            width: 50px;
            height: 50px;
            font-size: 24px;
            cursor: pointer;
            z-index: 20;
            transition: all 0.3s ease;
            border-radius: 4px;
        }
        .slider-btn:hover {
            background: rgba(255, 255, 255, 0.6);
        }
        .slider-btn.prev { left: 20px; }
        .slider-btn.next { right: 20px; }

        /* DOT INDICATORS */
        .slider-dots {
            position: absolute;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 12px;
            z-index: 20;
        }
        .dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.4);
            cursor: pointer;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }
        .dot.active {
            background: white;
            width: 28px;
            border-radius: 6px;
        }
        .dot:hover {
            background: rgba(255, 255, 255, 0.8);
        }

        /* ANIMATIONS */
        @keyframes slideInLeft {
            from { opacity: 0; transform: translateX(-50px); }
            to   { opacity: 1; transform: translateX(0); }
        }

        /* BASE RESPONSIVE */
        @media (max-width: 768px) {
            body {
                font-size: 14px;
            }

            /* NAV (generic) */
            .nav,
            header,
            .navbar {
                padding: 8px 12px !important;
            }

            /* Season banner */
            .season-banner {
                padding: 8px 10px;
            }
            .season-inner {
                flex-direction: column;
                gap: 2px;
                text-align: center;
                font-size: 11px;
            }
            .season-center {
                font-size: 14px;
            }

            /* Slider */
            .slider-section {
                height: auto;
                min-height: 340px;
            }
            .slide {
                position: relative;
            }
            .slide-content {
                flex-direction: column;
                padding: 16px 12px;
                gap: 14px;
                text-align: left;
            }
            .slide-title {
                font-size: 26px;
                line-height: 1.25;
            }
            .slide-desc {
                font-size: 13px;
                max-width: 100%;
            }
            .slider-btn.prev,
            .slider-btn.next {
                width: 32px;
                height: 32px;
                font-size: 18px;
                top: auto;
                bottom: 130px;
            }
            .slider-dots {
                bottom: 16px;
            }

            /* Categories */
            .categories-wrap {
                padding: 20px 10px;
            }
            .categories {
                grid-template-columns: 1fr;
                gap: 14px;
            }
            .cat-box {
                height: 190px;
            }
            .cat-title {
                font-size: 18px;
            }

            /* Trending */
            .section-title {
                font-size: 22px;
                margin-top: 22px;
            }
            .trending-grid {
                padding: 20px 10px;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 10px;
            }
            .trending-grid > div {
                padding: 10px;
            }
            .trending-grid img {
                height: 150px !important;
            }
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


        
    </style>
</head>
<body>
<?php include 'nav.php'; ?>

<!-- SEASON BANNER -->
<section class="season-banner">
    <div class="season-inner">
        <span class="season-left">ORDER BY 10 DEC</span>
        <span class="season-center">NEW SEASON • DROP 01</span>
        <span class="season-right">GET IT IN TIME</span>
    </div>
</section>

<!-- WINTER CLOTHING SLIDER -->
<section class="slider-section">
    <div class="slider-container">
        <!-- Slide 1 -->
        <div class="slide active" style="
            background:
                linear-gradient(135deg, rgba(0,0,0,0.75), rgba(0,0,0,0.35)),
                url('assets/images/s1.png') center/cover no-repeat;">
            <div class="slide-content">
                <div class="slide-text">
                    <span class="slide-label">MEN • WINTER 2025</span>
                    <h2 class="slide-title">INSULATED STREET JACKETS</h2>
                    <p class="slide-desc">
                        Lightweight puffer and parka styles that lock in warmth without losing the clean street look.
                    </p>
                    <button class="slide-btn" onclick="window.location.href='products.php?category=Men'">
                        Shop Men’s Jackets
                    </button>
                </div>
            </div>
        </div>

        <!-- Slide 2 -->
        <div class="slide" style="
            background:
                linear-gradient(135deg, rgba(30, 60, 114, 0.75), rgba(42, 82, 152, 0.35)),
                url('assets/images/s2.png') center/cover no-repeat;">
            <div class="slide-content">
                <div class="slide-text">
                    <span class="slide-label">WOMEN • LAYER UP</span>
                    <h2 class="slide-title">COZY KNITS & HOODIES</h2>
                    <p class="slide-desc">
                        Soft sweaters, fleece hoodies, and everyday layers made for chilly city evenings.
                    </p>
                    <button class="slide-btn" onclick="window.location.href='products.php?category=Women'">
                        Shop Women’s Layers
                    </button>
                </div>
            </div>
        </div>

        <!-- Slide 3 -->
        <div class="slide" style="
            background:
                linear-gradient(135deg, rgba(30,30,30,0.80), rgba(61,61,61,0.45)),
                url('assets/images/s3.png') center/cover no-repeat;">
            <div class="slide-content">
                <div class="slide-text">
                    <span class="slide-label">PERFORMANCE • COLD READY</span>
                    <h2 class="slide-title">THERMAL TRAINING GEAR</h2>
                    <p class="slide-desc">
                        Compression tops, joggers, and base layers that keep you warm while you keep moving.
                    </p>
                    <button class="slide-btn" onclick="window.location.href='products.php?category=Men'">
                        Shop Training
                    </button>
                </div>
            </div>
        </div>

        <!-- Slide 4 -->
        <div class="slide" style="
            background:
                linear-gradient(135deg, rgba(15, 92, 41, 0.80), rgba(26, 154, 62, 0.45)),
                url('assets/images/s4.png') center/cover no-repeat;">
            <div class="slide-content">
                <div class="slide-text">
                    <span class="slide-label">KIDS • SNOW READY</span>
                    <h2 class="slide-title">PUFFERS & PARKAS FOR KIDS</h2>
                    <p class="slide-desc">
                        Warm, durable outerwear designed for school days, snow days, and everything in between.
                    </p>
                    <button class="slide-btn" onclick="window.location.href='products.php?category=Kids'">
                        Shop Kids
                    </button>
                </div>
            </div>
        </div>

        <!-- Slide 5 -->
        <div class="slide" style="
            background:
                linear-gradient(135deg, rgba(124,44,18,0.80), rgba(194,65,12,0.45)),
                url('assets/images/s5.png') center/cover no-repeat;">
            <div class="slide-content">
                <div class="slide-text">
                    <span class="slide-label">LIMITED • DROP 01</span>
                    <h2 class="slide-title">WINTER STREET CAPSULE</h2>
                    <p class="slide-desc">
                        Bold prints, oversized fits, and premium fabrics available only for this winter season.
                    </p>
                    <button class="slide-btn" onclick="window.location.href='products.php'">
                        Shop Capsule
                    </button>
                </div>
            </div>
        </div>

        <!-- Navigation Arrows -->
        <button class="slider-btn prev" onclick="changeSlide(-1)">❮</button>
        <button class="slider-btn next" onclick="changeSlide(1)">❯</button>

        <!-- Dots Indicators -->
        <div class="slider-dots">
            <span class="dot active" onclick="currentSlide(1)"></span>
            <span class="dot" onclick="currentSlide(2)"></span>
            <span class="dot" onclick="currentSlide(3)"></span>
            <span class="dot" onclick="currentSlide(4)"></span>
            <span class="dot" onclick="currentSlide(5)"></span>
        </div>
    </div>
</section>

<!-- CATEGORY BOXES -->
<div class="categories-wrap">
    <div class="categories">
        <a href="products.php?category=Men" class="cat-box">
            <img src="assets/images/men2.png" alt="Men">
            <div class="cat-title">Men</div>
        </a>

        <a href="products.php?category=Women" class="cat-box">
            <img src="assets/images/women2.png" alt="Women">
            <div class="cat-title">Women</div>
        </a>

        <a href="products.php?category=Kids" class="cat-box">
            <img src="assets/images/kidss2.png" alt="Kids">
            <div class="cat-title">Kids</div>
        </a>
    </div>
</div>

<!-- TRENDING PRODUCTS -->
<h2 class="section-title">Trending Now</h2>
<div class="trending-grid">
    <?php
    $p = mysqli_query($conn, "SELECT * FROM products ORDER BY id DESC LIMIT 8");
    while ($row = mysqli_fetch_assoc($p)) {
    ?>
        <div style="background:#fff;border-radius:14px;padding:16px;
            box-shadow:0 4px 16px rgba(0,0,0,0.12);transition:0.3s;cursor:pointer;">
            <a href="product_details.php?id=<?php echo $row['id']; ?>" style="text-decoration:none;color:inherit;">
                <img src="<?php echo $row['image']; ?>"
                     style="width:100%;height:230px;object-fit:cover;border-radius:10px;">
                <h3 style="margin:10px 0 6px;font-size:17px;font-weight:600;">
                    <?php echo $row['name']; ?>
                </h3>
                <p style="font-size:16px;font-weight:600;color:#111;">₹<?php echo $row['price']; ?></p>
                <p style="font-size:12px;letter-spacing:1px;text-transform:uppercase;color:#999;margin-top:2px;">
                    Trending
                </p>
            </a>
        </div>
    <?php } ?>
</div>

<script>
let currentSlideIndex = 1;
let slideAutoplayTimeout;

function changeSlide(n) {
    clearTimeout(slideAutoplayTimeout);
    showSlide(currentSlideIndex += n);
    autoplaySlides();
}

function currentSlide(n) {
    clearTimeout(slideAutoplayTimeout);
    showSlide(currentSlideIndex = n);
    autoplaySlides();
}

function showSlide(n) {
    let slides = document.querySelectorAll('.slide');
    let dots = document.querySelectorAll('.dot');

    if (n > slides.length) currentSlideIndex = 1;
    if (n < 1) currentSlideIndex = slides.length;

    slides.forEach(slide => slide.classList.remove('active'));
    dots.forEach(dot => dot.classList.remove('active'));

    slides[currentSlideIndex - 1].classList.add('active');
    dots[currentSlideIndex - 1].classList.add('active');
}

function autoplaySlides() {
    slideAutoplayTimeout = setTimeout(() => {
        currentSlideIndex++;
        showSlide(currentSlideIndex);
        autoplaySlides();
    }, 5000);
}

window.addEventListener('load', () => {
    showSlide(currentSlideIndex);
    autoplaySlides();
});
</script>

<?php include 'footer.php'; ?>
</body>
</html>
