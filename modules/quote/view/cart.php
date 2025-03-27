<h2>📄 Tvorba nabídky</h2>

<?php if (empty($items)): ?>
    <p>Košík je prázdný.</p>
    <a href="/product/list" class="button">🛒 Vybrat produkty</a>
<?php else: ?>
    <form method="post" action="/quote/update">
        <table>
            <thead>
                <tr>
                    <th>Produkt</th>
                    <th>Cena / ks</th>
                    <th>DPH</th>
                    <th>Množství</th>
                    <th>Celkem bez DPH</th>
                    <th>Celkem s DPH</th>
                    <th>Akce</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['name']) ?></td>
                        <td><?= number_format($item['price'], 2, ',', ' ') ?> Kč</td>
                        <td><?= $item['vat_rate'] ?>%</td>
                        <td>
                            <input type="number" name="qty[<?= $item['id'] ?>]" value="<?= $item['qty'] ?>" min="1">
                        </td>
                        <td><?= number_format($item['total'], 2, ',', ' ') ?> Kč</td>
                        <td><?= number_format($item['total_vat'], 2, ',', ' ') ?> Kč</td>
                        <td>
                            <a href="/quote/remove/<?= $item['id'] ?>" onclick="return confirm('Odebrat produkt z nabídky?');">🗑️</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="modal-actions">
            <button type="submit" class="button">💾 Aktualizovat množství</button>
            <a href="/quote/clear" class="button danger" onclick="return confirm('Vyprázdnit nabídku?');">🧹 Vyprázdnit</a>
        </div>
    </form>

    <hr>
    <h3>Souhrn</h3>
    <p><strong>Celkem bez DPH:</strong> <?= number_format($totalWithoutVat, 2, ',', ' ') ?> Kč</p>
    <p><strong>Celkem s DPH:</strong> <?= number_format($totalWithVat, 2, ',', ' ') ?> Kč</p>

    <form method="post" action="/quote/create">
        <button type="submit" class="button">📄 Vytvořit nabídku</button>
    </form>
<?php endif; ?>
