<?php
// footer.php
?>
<footer class="site-footer">
    <div class="footer-top">
        <div class="footer-brand">
            <h2>CLOTHING</h2>
            <p>Premium streetwear and everyday essentials for Men, Women &amp; Kids.</p>
        </div>

        <!-- SHOP -->
        <div class="footer-section">
            <button class="footer-toggle">SHOP</button>
            <div class="footer-links footer-list">
                <a href="products.php?category=Men">Men</a>
                <a href="products.php?category=Women">Women</a>
                <a href="products.php?category=Kids">Kids</a>
                <a href="products.php">All Products</a>
            </div>
        </div>

        <!-- SUPPORT -->
        <div class="footer-section">
            <button class="footer-toggle">SUPPORT</button>
            <div class="footer-links footer-list">
                <a href="cart.php">Cart</a>
                <a href="my_orders.php">My Orders</a>
                <a href="profile.php">My Profile</a>
            </div>
        </div>

        <!-- CONTACT -->
        <div class="footer-section">
            <button class="footer-toggle">CONTACT</button>
            <div class="footer-contact footer-list">
                <p>Mahendra Durgam</p>
                <p>Phone: 6305146685</p>
                <p>Email: <a href="mailto:mahendradurgam801@gmail.com">mahendradurgam801@gmail.com</a></p>
                <p>Hyderabad, India</p>
            </div>
        </div>
    </div>

    <div class="footer-bottom">
        <span>© <?php echo date('Y'); ?> CLOTHING. All rights reserved.</span>
        <span>Designed by Mahendra Durgam</span>
    </div>
</footer>

<style>
.site-footer {
    background:#000;
    color:#f5f5f5;
    padding:40px 70px 22px;
    font-family:'Poppins',sans-serif;
    margin-top:60px;
}
.footer-top {
    display:grid;
    grid-template-columns:2fr 1fr 1fr 1.4fr;
    gap:30px;
    border-bottom:1px solid #333;
    padding-bottom:25px;
}
.footer-brand h2 {
    margin:0 0 8px;
    font-size:26px;
    letter-spacing:2px;
}
.footer-brand p {
    margin:0;
    color:#b3b3b3;
    font-size:14px;
}
.footer-links h4,
.footer-contact h4 {
    margin:0 0 10px;
    font-size:15px;
    text-transform:uppercase;
    letter-spacing:1px;
}
.footer-links a {
    display:block;
    color:#d9d9d9;
    text-decoration:none;
    font-size:14px;
    margin-bottom:6px;
}
.footer-links a:hover {
    color:#fff;
}
.footer-contact p {
    margin:0 0 4px;
    font-size:14px;
    color:#d9d9d9;
}
.footer-contact a {
    color:#d9d9d9;
    text-decoration:none;
}
.footer-contact a:hover {
    color:#fff;
}
.footer-bottom {
    display:flex;
    justify-content:space-between;
    align-items:center;
    padding-top:14px;
    font-size:13px;
    color:#9c9c9c;
}

/* desktop: accordion buttons hidden, lists always visible */
.footer-section {
    margin:0;
}
.footer-toggle {
    display:none;
}

/* tablet */
@media(max-width:768px){
    .site-footer {
        padding:30px 20px 18px;
    }
    .footer-top {
        grid-template-columns:1fr 1fr;
    }
}

/* mobile accordion */
@media(max-width:500px){
    .footer-top {
        grid-template-columns:1fr;
    }
    .footer-bottom {
        flex-direction:column;
        gap:6px;
        text-align:center;
    }

    .footer-section {
        border-top:1px solid #333;
        padding:10px 0;
    }

    

    .footer-toggle {
        width:100%;
        background:transparent;
        color:#fff;
        border:none;
        padding:6px 0;
        text-align:left;
        font-size:15px;
        font-weight:600;
        letter-spacing:1px;
        display:flex;
        justify-content:space-between;
        align-items:center;
    }

    /* icon on the right */
    .footer-toggle::after {
        content:'›';              /* closed state arrow */
        font-size:18px;
        transform:rotate(90deg);  /* pointing down */
        transition:transform 0.2s;
    }

    .footer-section.open .footer-toggle::after {
        transform:rotate(-90deg); /* pointing up when open */
    }

    .footer-list {
        margin-top:4px;
        display:none;
    }
    .footer-section.open .footer-list {
        display:block;
    }


    .footer-links a,
    .footer-contact p {
        font-size:13px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const sections = document.querySelectorAll('.footer-section');
    sections.forEach(sec => {
        const btn = sec.querySelector('.footer-toggle');
        if (!btn) return;
        btn.addEventListener('click', () => {
            sections.forEach(s => { if (s !== sec) s.classList.remove('open'); });
            sec.classList.toggle('open');
        });
    });
});
</script>
