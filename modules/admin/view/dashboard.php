<h2>Administrace</h2>

<p>Vítej, <?= htmlspecialchars($_SESSION['user']['email']) ?>!</p>

<h3>Nastavení modulu: Přihlášení / Registrace</h3>

<p>
    Veřejná registrace je:
    <strong style="color: <?= $loginPublic ? 'green' : 'red' ?>">
        <?= $loginPublic ? 'Povolena' : 'Zakázána' ?>
    </strong>
</p>

<form action="/admin/toggleRegistration" method="post">
    <button type="submit">
        <?= $loginPublic ? 'Zakázat' : 'Povolit' ?> registraci
    </button>
</form>

<hr>

<a href="/">← Zpět na úvod</a>
