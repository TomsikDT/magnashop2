<nav class="main-nav">
    
<a href="/"><img src="/src/theme/default/assets/images/logo.png" class="logo" alt="Logo"></a>
    <ul>
        <li><a href="/">Domů</a></li>
        <li><a href="/dashboard">Dashboard</a></li>
        <li><a href="/warehouse/list">Sklady</a></li>
        <li><a href="/product/list">Produkty</a></li>
        <li><a href="/quote/list">Nabídky</a></li> <!-- Budoucí nabídky -->
        
        <?php if (!empty($_SESSION['user'])): ?>
            <li><a href="/login/logout">Odhlásit se</a></li>
            
            <?php if (!empty($_SESSION['user']['is_admin'])): ?>
                <li><a href="/admin">Administrace</a></li>
                <li><a href="/product/add">Přidat produkt</a></li>
            <?php endif; ?>
            <?php if (!empty($_SESSION['user'])): ?>
    <li><a href="/quote/create">Nová nabídka</a></li>
<?php endif; ?>

        <?php else: ?>
            <li><a href="/login/login">Přihlásit</a></li>
        <?php endif; ?>
    </ul>
</nav>
<div class="page-wrapper">
    <div class="content-container">
