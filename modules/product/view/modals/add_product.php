<div id="addProductModal" class="modal" style="display: none;">
    <div class="modal-content">
        <h3>Přidat nový produkt</h3>
        <form method="post" action="/product/add" enctype="multipart/form-data">

            <label for="add-name">Název produktu</label>
            <input type="text" id="add-name" name="name" required>

            <label for="add-description">Popis</label>
            <textarea id="add-description" name="description" rows="3"></textarea>

            <label for="add-price">Cena bez DPH (Kč)</label>
            <input type="number" step="0.01" id="add-price" name="price" required>

            <label for="add-vat">Sazba DPH (%)</label>
            <input type="number" step="1" id="add-vat" name="vat_rate" value="21" required>

            <label for="add-type">Typ</label>
            <select id="add-type" name="type" required>
                <option value="product">Produkt</option>
                <option value="work">Práce</option>
            </select>

            <label for="add-image">Obrázek</label>
            <input type="file" id="add-image" name="image" accept="image/*">

            <label>Kategorie:</label>
        <div id="add-categories-checkboxes" class="category-checkboxes">
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
                    <label for="wh-<?= $w['id'] ?>">
                        <?= htmlspecialchars($w['name']) ?>:
                    </label>
                    <input type="number"
                           id="wh-<?= $w['id'] ?>"
                           name="warehouse[<?= $w['id'] ?>]"
                           value="0"
                           min="0">
                <?php endforeach; ?>
            </fieldset>

            <div class="modal-actions">
                <button type="submit" class="button">💾 Uložit produkt</button>
                <button type="button" class="button" onclick="closeModal('addProductModal')">Zrušit</button>
            </div>
        </form>
    </div>
</div>
