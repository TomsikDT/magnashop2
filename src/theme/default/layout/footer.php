    </div><!-- End of content-container -->
</div> <!-- konec page wrapper -->
    <footer class="site-footer">
        <!--<div class="footer-content">
            <div class="footer-section">
                <h3>About Us</h3>
                <p>MagnaShop2 - Your one-stop shop for everything you need.</p>
            </div>
            <div class="footer-section">
                <h3>Contact</h3>
                <p>Email: info@magnashop2.com</p>
                <p>Phone: +1 234 567 890</p>
            </div>
            <div class="footer-section">
                <h3>Follow Us</h3>
                <div class="social-links">
                    <a href="#">Facebook</a>
                    <a href="#">Twitter</a>
                    <a href="#">Instagram</a>
                </div>
            </div>
        </div> -->
        <div class="copyright">
            <p>&copy; <?= date('Y') ?> MagnaShop</p>
        </div>
        <button id="toggle-theme">🌓 Přepnout režim</button>
    </footer>
    
    <script src="/src/theme/default/assets/js/main.js"></script>
    <?php if (isset($extraScripts) && is_array($extraScripts)): ?>
        <?php foreach ($extraScripts as $script): ?>
            <script src="<?= $script ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>

    <script>
document.addEventListener('DOMContentLoaded', function () {
    const btn = document.getElementById('toggle-theme');
    const body = document.body;
    
    // Inicializace z localStorage
    if (localStorage.getItem('theme') === 'dark') {
        body.classList.add('dark-mode');
    }

    btn.addEventListener('click', function () {
        body.classList.toggle('dark-mode');
        const isDark = body.classList.contains('dark-mode');
        localStorage.setItem('theme', isDark ? 'dark' : 'light');
    });
});
</script>

</body>
</html>
