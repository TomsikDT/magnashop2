<?php /** @var array $products */ ?>
<?php /** @var array $warehouses */ ?>

<h2>Seznam produktů</h2>

<button onclick="openAddModal()">+ Nový produkt</button>
<button onclick="openWarehouseModal()">🏢 Spravovat sklady</button>

<table>
    <thead>
    <tr>
        <th>ID</th>
        <th>Náhled</th>
        <th>Název</th>
        <th>Cena bez DPH</th>
        <th>Sazba DPH</th>
        <th>Skladem</th>
        <th>Typ</th>
        <th>Vytvořeno</th>
        <th>Upraveno</th>
        <th>Akce</th>
    </tr>
    </thead>
    <tbody>
        <?php foreach ($products as $p): ?>
        <tr>
        <td><?= $p['id'] ?></td>
            <td><?php if (!empty($p['image'])): ?> <img src="/<?= htmlspecialchars($p['image']) ?>" alt="" width="60"> <?php endif; ?></td>
            <td><?= htmlspecialchars($p['name']) ?></td>
            <td><?= number_format($p['price'], 2, ',', ' ') ?> Kč</td>
            <td><?= $p['vat_rate'] ?>%</td>
            <td><?= $p['stock_quantity'] ?? 0 ?> ks</td>
            <td><?= $p['type'] === 'work' ? 'Práce' : 'Produkt' ?></td>
            <td><?= date('d.m.Y H:i', strtotime($p['created_at'])) ?></td>
            <td><?= date('d.m.Y H:i', strtotime($p['updated_at'])) ?></td>
            <td>
                <button onclick="addToQuote(<?= $p['id'] ?>)">📄</button>
                <button onclick="openEditModal(<?= $p['id'] ?>)">✏️</button>
                <button onclick="openDependencyModal(<?= $p['id'] ?>)">⚖️</button>
                <form method="post" action="/product/delete" onsubmit="return confirm('Opravdu smazat?');" style="display:inline;">
                    <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
                    <button type="submit">🗑️</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<!-- Modaly -->
<?php include 'modals/add_product.php'; ?>
<?php include 'modals/edit_product.php'; ?>
<?php include 'modals/manage_warehouses.php'; ?>
<?php include 'modals/manage_dependencies.php'; ?>

<script>
    window.products = <?= json_encode($products) ?>;
</script>

<script src="/modules/product/js/product_modals.js"></script>