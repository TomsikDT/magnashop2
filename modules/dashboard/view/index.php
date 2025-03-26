<h2>Můj dashboard</h2>

<p>Vítejte, <?= htmlspecialchars($user['email']) ?>!</p>

<?php if (!empty($user['is_admin'])): ?>
    <p><strong>Jste administrátor.</strong></p>
    <p><a href="/admin">Přejít do administrace</a></p>
<?php endif; ?>

<p><a href="/product/list">Zobrazit produkty</a></p>
<p><a href="/login/logout">Odhlásit se</a></p>
