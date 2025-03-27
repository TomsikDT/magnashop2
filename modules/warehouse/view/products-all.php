<h2>Produkty dle skladů</h2>

<?php foreach ($productsByWarehouse as $data): ?>
    <h3><?= htmlspecialchars($data['warehouse']['name']) ?></h3>

    <?php if (count($data['products'])): ?>
        <table border="1" cellpadding="6">
            <tr>
                <th>Název</th>
                <th>Typ</th>
                <th>Počet kusů</th>
                <th>Cena bez DPH</th>
                <th>DPH</th>
            </tr>
            <?php foreach ($data['products'] as $p): ?>
                <tr>
                    <td><?= htmlspecialchars($p['name']) ?></td>
                    <td><?= $p['type'] === 'work' ? 'Práce' : 'Produkt' ?></td>
                    <td><?= $p['quantity'] ?> ks</td>
                    <td><?= number_format($p['price'], 2, ',', ' ') ?> Kč</td>
                    <td><?= $p['vat_rate'] ?>%</td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p><em>Žádné produkty v tomto skladu.</em></p>
    <?php endif; ?>

    <hr>
<?php endforeach; ?>
