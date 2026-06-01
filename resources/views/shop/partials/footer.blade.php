<footer class="shop-footer">
    <div class="container-fluid shop-wide">
        <div class="footer-top">
            <div class="footer-brand">
                <a class="footer-logo" href="{{ route('shop.index') }}">Infinity Figures</a>
                <p>Premium anime figure online store for customers who care about sculpt, paint, packaging, and display presence.</p>

                <div class="footer-socials" aria-label="Social links">
                    <a href="https://www.facebook.com/share/1EJNoXtyiN/" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
                    <a href="https://www.instagram.com/kimkrouy?igsh=MWd1dTltNDhnNnA2aw==" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
                    <a href="https://www.tiktok.com/@kimkrouy?_r=1&_t=ZS-96pFb002HwL" aria-label="TikTok"><i class="bi bi-tiktok"></i></a>
                    <a href="https://t.me/laykimkruy" aria-label="Telegram"><i class="bi bi-telegram"></i></a>
                </div>
            </div>

            <div class="footer-column">
                <h4>Quick Links</h4>
                <a href="{{ route('shop.index') }}">Home</a>
                <a href="{{ route('products.index') }}">Products</a>
                <a href="{{ route('shop.index') }}#categories">Categories</a>
                <a href="{{ route('cart.index') }}">Cart</a>
            </div>

            <div class="footer-column">
                <h4>Customer Care</h4>
                <a href="{{ route('orders.index') }}">Order Tracking</a>
                <a href="{{ route('wishlist.index') }}">Wishlist</a>
                <a href="{{ route('checkout.create') }}">Checkout</a>
                <a href="{{ route('login') }}">Account Login</a>
            </div>

            <div class="footer-contact">
                <h4>Store Info</h4>
                <a  href="https://maps.app.goo.gl/jS3HS8noW6CWiZAMA">
                    <i class="bi bi-geo-alt"></i>
                    <span>Phnom Penh, Cambodia</span>
                </a>
                <p >
                    <i class="bi bi-telephone"></i>
                    <span>+855 968276484</span>
                </p>
                <p >
                    <i class="bi bi-envelope"></i>
                    <span>support@infinity-figures.test</span>
                </p>
            </div>
        </div>

        
    </div>
</footer>
