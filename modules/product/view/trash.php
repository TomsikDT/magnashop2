<h2>🗑️ Koš – smazané produkty</h2>

<div style="margin-bottom: 20px;">
    <form method="post" action="/product/restore-all" style="display:inline;">
        <button type="submit" class="button">♻️ Obnovit vše</button>
    </form>
    <form method="post" action="/product/delete-all-permanent" onsubmit="return confirm('Opravdu trvale smazat vše?');" style="display:inline;">
        <button type="submit" class="button danger">🧹 Smazat vše trvale</button>
    </form>
</div>

<?php if (empty($trashed)): ?>
    <p>Koš je prázdný.</p>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>Název</th>
                <th>Cena</th>
                <th>Typ</th>
                <th>Obnoveno</th>
                <th>Akce</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($trashed as $product): ?>
            <tr>
                <td><?= htmlspecialchars($product['name']) ?></td>
                <td><?= number_format($product['price'], 2, ',', ' ') ?> Kč</td>
                <td><?= $product['type'] === 'work' ? 'Práce' : 'Produkt' ?></td>
                <td><?= date('d.m.Y H:i', strtotime($product['deleted_at'])) ?></td>
                <td>
                    <form method="post" action="/product/restore" style="display:inline;">
                        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                        <button type="submit">♻️</button>
                    </form>
                    <form method="post" action="/product/delete-permanent" onsubmit="return confirm('Opravdu trvale smazat?');" style="display:inline;">
                        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                        <button type="submit">🗑️</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
