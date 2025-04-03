<?php

/** @var array $products */ ?>
<?php /** @var array $categories */ ?>
<?php /** @var array $childrenMap */ ?>
<?php
function renderCategoryBranch($category, $categoryId, $childrenMap, $warehouseId)
{
    $hasChildren = !empty($category['children']);
    $active = isset($categoryId) && $categoryId == $category['id'];
    $paddingLeft = 30 * ($category['level'] ?? 0);

    // Pokud nemá parent_id, přiřadíme jako parent sklad
    $parent = $category['parent_id'] ?? "warehouse-" . $warehouseId;

    echo '<li class="category-item hidden"
        data-id="' . $category['id'] . '"
        data-parent="' . $parent . '"
        ' . ($active ? 'data-active="1"' : '') . '>';

    // ⬇️ paddingLeft se použije zde
    echo '<div class="category-row ' . ($active ? 'active' : '') . '"
             data-id="' . $category['id'] . '"
             onclick="toggleSubcategories(this)"
             style="padding-left:' . $paddingLeft . 'px;">';

    echo $hasChildren
        ? '<span class="category-toggle">+</span>'
        : '<span class="category-toggle"></span>';

    echo '<span class="category-name">
            <a href="?category=' . $category['id'] . '&view=cards">' .
        (($category['level'] ?? 0) > 0 ? '↳ ' : '') . htmlspecialchars($category['name']) .
        '</a></span>';

    echo '</div>';

    // Rekurzivní výpis podkategorií
    if ($hasChildren) {
        foreach ($category['children'] as $child) {
            renderCategoryBranch($child, $categoryId, $childrenMap, $warehouseId);
        }
    }

    echo '</li>';
}

?>
<div class="product-view-wrapper">
    <div class="view-switch">
        <a href="?view=cards" class="btn <?= ($_GET['view'] ?? 'cards') === 'cards' ? 'active' : '' ?>">📇 Karty</a>
        <a href="?view=table" class="btn <?= ($_GET['view'] ?? '') === 'table' ? 'active' : '' ?>">📄 Tabulka</a>
    </div>

    <div class="product-layout">
        <aside class="category-sidebar">
            <h3>Kategorie</h3>
            <ul class="category-tree">
    <li class="category-item<?= is_null($categoryId) ? ' selected' : '' ?>">
        <a href="/product/list" class="category-row">Všechny produkty</a>
    </li>

    <?php foreach ($warehouses as $wh): ?>
        <li class="category-item"
            data-id="warehouse-<?= $wh['id'] ?>"
            data-parent=""
            data-warehouse-id="<?= $wh['id'] ?>">
            <div class="category-row" data-id="warehouse-<?= $wh['id'] ?>" onclick="toggleSubcategories(this)">
                <span class="category-toggle">+</span>
                <span class="category-name" style="padding-left: 0; font-weight: bold;">
                    <?= htmlspecialchars($wh['name']) ?>
                </span>
            </div>

            <ul>
                <?php foreach ($categoryTreeByWarehouse[$wh['id']] ?? [] as $cat): ?>
                    <?php if ($cat['parent_id'] === null): ?>
                        <?php renderCategoryBranch($cat, $categoryId, $childrenMap, $wh['id']); ?>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ul>
        </li>
    <?php endforeach; ?>
</ul>



        </aside>
        <div class="product-content">
            <?php if (!empty($breadcrumb)): ?>
                <div class="breadcrumb">
                    <?php foreach ($breadcrumb as $i => $cat): ?>
                        <?php if ($i > 0): ?> → <?php endif; ?>
                        <a href="?category=<?= $cat['id'] ?>&warehouse=<?= $warehouseId ?>&view=cards"><?= htmlspecialchars($cat['name']) ?></a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
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