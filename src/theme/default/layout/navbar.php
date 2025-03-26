<nav class="main-nav">
        <ul>
            <li><a href="/">Domů</a></li>
            <li><a href="/products">Sklady</a></li>
            <li><a href="/cart">Nabídky</a></li>
            <li><a href="/account">Můj účet</a></li>
                <?php if (!empty($_SESSION['user'])): ?>
            <li><a href="/login/logout">Odhlásit se</a></li>
                <?php if (!empty($_SESSION['user']['is_admin'])): ?>
            <li><a href="/admin">Administrace</a></li>
                <?php endif; ?>
                <?php else: ?>
            <li><a href="/login/login">Přihlásit</a></li>
                <?php endif; ?>
        </ul>
    </nav>
    <div class="content-container">
