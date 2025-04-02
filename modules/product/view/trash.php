<h2>🗑️ Koš</h2>
<h3>Smazané produkty</h3>

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
                    <form method="post" action="/product/delete-permanent" onsubmit="return confirm('Opravdu trvale smazat? Už to nejde vzít zpátky! :)');" style="display:inline;">
                        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                        <button type="submit">🗑️</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
<h3>Smazané kategorie</h3>
<table>
    <thead>
        <tr><th>ID</th><th>Název</th><th>Akce</th></tr>
    </thead>
    <tbody>
    <?php foreach ($deletedCategories as $cat): ?>
        <tr>
            <td><?= $cat['id'] ?></td>
            <td><?= htmlspecialchars($cat['name']) ?></td>
            <td>
                <form method="post" action="/product/restoreCategory" style="display:inline;">
                    <input type="hidden" name="category_id" value="<?= $cat['id'] ?>">
                    <button type="submit">♻️ Obnovit</button>
                </form>
                <form method="post" action="/product/deleteCategoryPermanent" onsubmit="return confirm('Opravdu trvale smazat? Už to nejde vzít zpátky! :)');" style="display:inline;">
                    <input type="hidden" name="category_id" value="<?= $cat['id'] ?>">
                    <button type="submit">🗑️</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<h3>Smazané závislosti</h3>
<?php if (empty($deletedDependencies)): ?>
    <p>Žádné smazané závislosti.</p>
<?php else: ?>
<table>
    <thead>
        <tr>
            <th>Produkt</th>
            <th>Závislost</th>
            <th>Množství</th>
            <th>Typ</th>
            <th>Poznámka</th>
            <th>Smazáno</th>
            <th>Akce</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($deletedDependencies as $dep): ?>
        <tr>
            <td><?= htmlspecialchars($dep['product_name']) ?></td>
            <td><?= htmlspecialchars($dep['dependency_name']) ?></td>
            <td><?= $dep['quantity_multiplier'] ?></td>
            <td><?= $dep['auto_add'] ? 'per_unit' : 'fixed' ?></td>
            <td><?= htmlspecialchars($dep['note'] ?? '') ?></td>
            <td><?= date('d.m.Y H:i', strtotime($dep['deleted_at'])) ?></td>
            <td>
                <form method="post" action="/product/restoreDependency" style="display:inline;">
                    <input type="hidden" name="dependency_id" value="<?= $dep['id'] ?>">
                    <button type="submit">♻️</button>
                </form>
                <form method="post" action="/product/deleteDependencyPermanently" onsubmit="return confirm('Opravdu trvale smazat?');" style="display:inline;">
                    <input type="hidden" name="dependency_id" value="<?= $dep['id'] ?>">
                    <button type="submit">🗑️</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>

