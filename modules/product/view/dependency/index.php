<h2>Závislosti produktu: <?= htmlspecialchars($product['name']) ?></h2>

<h3>Přidat závislost</h3>
<form method="post">
    <label>Vyber produkt:
        <select name="dependency_product_id">
            <?php foreach ($allProducts as $p): ?>
                <?php if ($p['id'] !== $product['id']): ?>
                    <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['name']) ?> (<?= $p['type'] ?>)</option>
                <?php endif; ?>
            <?php endforeach; ?>
        </select>
    </label><br><br>

    <label>Počet na jednotku:
        <input type="number" step="0.01" name="quantity_multiplier" value="1">
    </label><br><br>

    <label>Automaticky přidávat:
        <input type="checkbox" name="auto_add" value="1" checked>
    </label><br><br>

    <label>Poznámka:
        <input type="text" name="note">
    </label><br><br>

    <button type="submit">Přidat závislost</button>
</form>

<hr>

<h3>Existující závislosti</h3>
<table border="1" cellpadding="6">
    <tr>
        <th>Název</th>
        <th>Typ</th>
        <th>Koeficient</th>
        <th>Auto</th>
        <th>Poznámka</th>
    </tr>
    <?php foreach ($dependencies as $d): ?>
        <tr>
            <td><?= htmlspecialchars($d['dependency_name']) ?></td>
            <td><?= $d['dependency_type'] === 'work' ? 'Práce' : 'Produkt' ?></td>
            <td><?= $d['quantity_multiplier'] ?></td>
            <td><?= $d['auto_add'] ? 'Ano' : 'Ne' ?></td>
            <td><?= htmlspecialchars($d['note'] ?? '') ?></td>
        </tr>
    <?php endforeach; ?>
</table>
