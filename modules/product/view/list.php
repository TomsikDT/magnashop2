<?php /** @var array $products */ ?>
<?php /** @var array $warehouses */ ?>
<div class="view-switch">
    <a href="?view=cards" class="btn <?= ($_GET['view'] ?? 'cards') === 'cards' ? 'active' : '' ?>">📇 Karty</a>
    <a href="?view=table" class="btn <?= ($_GET['view'] ?? '') === 'table' ? 'active' : '' ?>">📄 Tabulka</a>
</div>

<h2>Seznam produktů</h2>

<button onclick="openAddModal()">+ Nový produkt</button>
<button onclick="openWarehouseModal()">🏢 Spravovat sklady</button>
<button onclick="openCategoryModal()">🏷️ Spravovat kategorie</button>


<table>
    <thead>
    <tr>
        <!-- <th>ID</th> -->
        <th>Náhled</th>
        <th>Název</th>
        <th>Kategorie</th>
        <th>Cena bez DPH</th>
        <th>Sazba DPH</th>
        <th>Skladem</th>
        <th>Typ</th>
       <!-- <th>Vytvořeno</th>
        <th>Upraveno</th>  -->
        <th>Akce</th>
    </tr>
    </thead>
    <tbody>
        <?php foreach ($products as $p): ?>
        <tr data-product-id="<?= $p['id'] ?>" data-category-ids="<?= implode(',', $p['category_ids'] ?? []) ?>">
        <!-- <td> //$p['id'] ?></td> -->
            <td><?php if (!empty($p['image'])): ?> <img src="/<?= htmlspecialchars($p['image']) ?>" alt="" width="60"> <?php endif; ?></td>
            <td><?= htmlspecialchars($p['name']) ?></td>
            <td>
                <?php if (!empty($p['category_ids'])): ?>
                    <?php foreach ($categories as $cat): ?>
                        <?php if (in_array($cat['id'], $p['category_ids'])): ?>
                            <span class="badge"><?= htmlspecialchars($cat['name']) ?></span>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </td>
            <td><?= number_format($p['price'], 2, ',', ' ') ?> Kč</td>
            <td><?= $p['vat_rate'] ?>%</td>
            <td><?= $p['total_quantity'] ?? 0 ?> ks</td>
            <td><?= $p['type'] === 'work' ? 'Práce' : 'Produkt' ?></td>
           <!-- <td>< ? = date('d.m.Y H:i', strtotime($p['created_at'])) ?></td>
            <td>< ? = date('d.m.Y H:i', strtotime($p['updated_at'])) ?></td> -->
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
<?php include 'modals/manage_categories.php'; ?>


<script>
    window.products = <?= json_encode($products) ?>;
</script>

<script src="/modules/product/js/product_modals.js"></script>