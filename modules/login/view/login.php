<h2>Přihlášení</h2>
<?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>
<form method="post">
    <label>Email: <input type="email" name="email" required></label><br>
    <label>Heslo: <input type="password" name="password" required></label><br>
    <button type="submit">Přihlásit se</button>
</form>
<a href="/login/register">Nemáte účet? Zaregistrujte se</a>
