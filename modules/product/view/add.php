<h2>Přidat produkt</h2>

<?php if (!empty($error)): ?>
    <p style="color: red;"><?= $error ?></p>
<?php endif; ?>

<form method="post">
    <label>Název:<br><input type="text" name="name"></label><br><br>
    <label>Popis:<br><textarea name="description"></textarea></label><br><br>
    <label>Cena bez DPH:<br><input type="number" step="0.01" name="price"></label><br><br>
    <label>Sazba DPH:<br><input type="number" step="0.01" name="vat_rate" value="21"></label><br><br>
    <label>Typ:<br>
        <select name="type">
            <option value="product">Produkt</option>
            <option value="work">Práce</option>
        </select>
    </label><br><br>
    <label>Obrázek (cesta):<br><input type="text" name="image"></label><br><br>

    <fieldset>
        <legend>Počet kusů na skladech:</legend>
        <?php foreach ($warehouses as $wh): ?>
            <label><?= htmlspecialchars($wh['name']) ?>:
                <input type="number" name="warehouse_<?= $wh['id'] ?>" value="0" min="0">
            </label><br>
        <?php endforeach; ?>
    </fieldset><br>

    <button type="submit">Uložit produkt</button>
</form>
