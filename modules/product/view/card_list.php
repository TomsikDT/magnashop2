<?php /** @var array $products */ ?>
<?php /** @var array $categories */ ?>
<?php /** @var array $childrenMap */ ?>

<div class="product-view-wrapper">
    <div class="view-switch">
        <a href="?view=cards" class="btn <?= ($_GET['view'] ?? 'cards') === 'cards' ? 'active' : '' ?>">📇 Karty</a>
        <a href="?view=table" class="btn <?= ($_GET['view'] ?? '') === 'table' ? 'active' : '' ?>">📄 Tabulka</a>
    </div>

    <div class="product-layout">
        <aside class="category-sidebar">
            <h3>Kategorie</h3>
            <ul class="category-tree">
                <?php foreach ($categories as $cat): ?>
                    <li class="category-item<?= $cat['parent_id'] ? ' hidden' : '' ?>"
                        data-id="<?= $cat['id'] ?>"
                        data-parent="<?= $cat['parent_id'] ?>">
                        <div class="category-row" data-id="<?= $cat['id'] ?>" onclick="toggleSubcategories(this)">
                            <?php if (!empty($childrenMap[$cat['id']] ?? false)): ?>
                                <span class="category-toggle">+</span>
                            <?php else: ?>
                                <span class="category-toggle"></span>
                            <?php endif; ?>
                            <span class="category-name" style="padding-left: <?= 20 * ($cat['level'] ?? 0) ?>px;">
                                <a href="?category=<?= $cat['id'] ?>&view=cards">
                                    <?= ($cat['level'] ?? 0) > 0 ? '↳ ' : '' ?><?= htmlspecialchars($cat['name']) ?>
                                </a>
                            </span>

                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        </aside>

        <div class="product-grid">
            <?php foreach ($products as $p): ?>
                <?php if ($p['type'] === 'work') continue; ?>
                <div class="product-card" data-product-id="<?= $p['id'] ?>" data-category-ids="<?= implode(',', $p['category_ids'] ?? []) ?>">
                    <h3 class="product-name"><?= htmlspecialchars($p['name']) ?></h3>
                    <div class="product-image">
                        <img src="/<?= $p['image'] ?: 'src/theme/default/assets/images/no-image.webp' ?>" alt="<?= htmlspecialchars($p['name']) ?>">
                    </div>
                    <div class="product-price">
                        <?= number_format($p['price'], 2, ',', ' ') ?> Kč bez DPH<br>
                        <?= number_format($p['price'] * (1 + $p['vat_rate'] / 100), 2, ',', ' ') ?> Kč s DPH (<?= $p['vat_rate'] ?>%)
                    </div>
                    <div class="product-stock"><?= (int) $p['total_quantity'] ?> ks skladem</div>
                    <div class="product-actions">
                        <button onclick="addToQuote(<?= $p['id'] ?>)">📄 Do nabídky</button>
                        <div class="dropdown">
                            <button class="dropdown-toggle">⚙️ Akce</button>
                            <div class="dropdown-menu">
                                <button onclick="openEditModal(<?= $p['id'] ?>)">✏️ Upravit</button>
                                <button onclick="openDependencyModal(<?= $p['id'] ?>)">⚖️ Závislosti</button>
                                <form method="post" action="/product/delete" onsubmit="return confirm('Opravdu smazat?');">
                                    <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
                                    <button type="submit">🗑️ Smazat</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
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
