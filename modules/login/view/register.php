<h2>Registrace</h2>
<?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>
<form method="post">
    <label>Email: <input type="email" name="email" required></label><br>
    <label>Heslo: <input type="password" name="password" required></label><br>
    <label>Heslo znovu: <input type="password" name="password2" required></label><br>
    <button type="submit">Registrovat</button>
</form>
<a href="/login/login">Máte účet? Přihlaste se</a>