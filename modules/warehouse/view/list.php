<h2>Seznam skladů</h2>

<table border="1" cellpadding="6">
    <tr>
        <th>ID</th>
        <th>Název skladu</th>
        <th>Počet produktů</th>
        <th>Celkový počet kusů</th>
    </tr>
    <?php foreach ($warehouses as $wh): ?>
        <tr>
            <td><?= $wh['id'] ?></td>
            <td><?= htmlspecialchars($wh['name']) ?></td>
            <td><?= $wh['product_count'] ?? 0 ?></td>
            <td><?= $wh['total_quantity'] ?? 0 ?></td>
        </tr>
    <?php endforeach; ?>
<a href="/warehouse/productsall">Zobrazit produkty</a>

</table>
