<div id="editProductModal" class="modal" style="display: none;">
    <div class="modal-content">
        <h3>Upravit produkt</h3>
        <form method="post" action="/product/update">

            <input type="hidden" id="edit-id" name="product_id">

            <label for="edit-name">Název produktu</label>
            <input type="text" id="edit-name" name="name" required>

            <label for="edit-price">Cena bez DPH (Kč)</label>
            <input type="number" step="0.01" id="edit-price" name="price" required>

            <label for="edit-vat">Sazba DPH (%)</label>
            <input type="number" step="1" id="edit-vat" name="vat_rate" required>

            <label for="edit-type">Typ</label>
            <select id="edit-type" name="type" required>
                <option value="product">Produkt</option>
                <option value="work">Práce</option>
            </select>

            <label>Kategorie:</label>
        <div id="edit-categories-checkboxes" class="category-checkboxes">
            <?php foreach ($categories as $cat): ?>
                <label>
                    <input type="checkbox" name="categories[]" value="<?= $cat['id'] ?>">
                    <?= htmlspecialchars($cat['name']) ?>
                </label><br>
            <?php endforeach; ?>
        </div>



            <fieldset>
                <legend>Množství ve skladech</legend>
                <?php foreach ($warehouses as $w): ?>
                    <label for="edit-wh-<?= $w['id'] ?>">
                        <?= htmlspecialchars($w['name']) ?>:
                    </label>
                    <input type="number"
                           id="edit-wh-<?= $w['id'] ?>"
                           name="warehouse[<?= $w['id'] ?>]"
                           value="0"
                           min="0"
                           data-warehouse-id="<?= $w['id'] ?>">
                <?php endforeach; ?>
            </fieldset>

            <div class="modal-actions">
                <button type="submit" class="button">💾 Uložit změny</button>
                <button type="button" class="button" onclick="closeModal('editProductModal')">Zrušit</button>
            </div>
        </form>
    </div>
</div>
