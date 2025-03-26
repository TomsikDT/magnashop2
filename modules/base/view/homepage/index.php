<h1>Vítejte v systému MagnaShop</h1>
<p>Tento systém slouží pro správu produktů, skladů, cenových nabídek a více.</p>

<?php if (empty($_SESSION['user'])): ?>
    <a href="/login/login">Přihlásit se</a> nebo 
    <a href="/login/register">Registrovat</a>
<?php else: ?>
    <a href="/dashboard">Přejít do systému</a>
<?php endif; ?>
