<div id="categoryModal" class="modal" style="display: none;">
    <div class="modal-content">
        <h3>Správa kategorií</h3>

        <form method="post" action="/product/saveCategory" id="categoryForm">
            <input type="hidden" name="category_id" id="category_id">
            <input type="text" name="name" id="category_name" placeholder="Název kategorie" required>
            <label>Nadřazená kategorie</label>
            <select name="parent_id" id="category_parent">
                <option value="">Žádná (hlavní kategorie)</option>
                <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>" data-level="<?= $cat['level'] ?? 0 ?>">
                <?= str_repeat('—', $cat['level'] ?? 0) . ' ' . htmlspecialchars($cat['name']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <button type="submit" id="category_submit">Přidat kategorii</button>
</form>

        <hr>
        <h4>Existující kategorie</h4>
        <div class="category-tree-controls">
    <button type="button" onclick="toggleAllCategories(false)">🔽 Rozbalit vše</button>
    <button type="button" onclick="toggleAllCategories(true)">🔼 Sbalit vše</button>
</div>

<ul class="category-tree">
<?php foreach ($categories as $cat): ?>
    <li class="category-item<?= $cat['parent_id'] ? ' hidden' : '' ?>" data-id="<?= $cat['id'] ?>" data-parent="<?= $cat['parent_id'] ?>">
        <div class="category-row" data-id="<?= $cat['id'] ?>" onclick="toggleSubcategories(this)">
            <?php if (!empty($childrenMap[$cat['id']])): ?>
                <span class="category-toggle">+</span>
            <?php else: ?>
                <span class="category-toggle"></span>
            <?php endif; ?>
            <span class="category-name" style="padding-left: <?= 20 * ($cat['level'] ?? 0) ?>px;">
    <?= ($cat['level'] ?? 0) > 0 ? '↳ ' : '' ?><?= htmlspecialchars($cat['name']) ?>
</span>
<div class="category-actions">
                    <button type="button" onclick="editCategory(<?= $cat['id'] ?>, '<?= htmlspecialchars($cat['name'], ENT_QUOTES) ?>', <?= $cat['parent_id'] ?? 'null' ?>)">✏️</button>
                    <form method="post" action="/product/deleteCategory" onsubmit="return confirm('Opravdu smazat?')">
                        <input type="hidden" name="category_id" value="<?= $cat['id'] ?>">
                        <button type="submit">🗑️</button>
                    </form>
        </div>
    </li>
<?php endforeach; ?>
</ul>





        <div class="modal-actions">
            <button onclick="closeModal('categoryModal')">Zavřít</button>
        </div>
    </div>
</div>
