<h2>Produkty ve skladu: <?= htmlspecialchars($warehouse['name']) ?></h2>

<table border="1" cellpadding="6">
    <tr>
        <th>Název</th>
        <th>Typ</th>
        <th>Počet kusů</th>
        <th>Cena bez DPH</th>
        <th>DPH</th>
    </tr>
    <?php foreach ($products as $p): ?>
        <tr>
            <td><?= htmlspecialchars($p['name']) ?></td>
            <td><?= $p['type'] === 'work' ? 'Práce' : 'Produkt' ?></td>
            <td><?= $p['quantity'] ?> ks</td>
            <td><?= number_format($p['price'], 2, ',', ' ') ?> Kč</td>
            <td><?= $p['vat_rate'] ?>%</td>
        </tr>
    <?php endforeach; ?>
</table>
