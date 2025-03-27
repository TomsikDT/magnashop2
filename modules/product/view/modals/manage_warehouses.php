<div id="warehouseModal" class="modal" style="display: none;">
    <div class="modal-content">
        <h3>Správa skladů</h3>

        <!-- 🏗️ Formulář pro přidání nového skladu -->
        <form method="post" action="/product/warehouse-add" class="form-inline">
            <h4>➕ Nový sklad</h4>
            <input type="text" name="name" placeholder="Název skladu" required>
            <input type="text" name="address" placeholder="Adresa">
            <input type="text" name="note" placeholder="Poznámka">
            <button type="submit" class="button">💾 Přidat</button>
        </form>

        <hr>

        <!-- 📝 Úprava existujících skladů -->
        <h4>📋 Stávající sklady</h4>
        <?php foreach ($warehouses as $w): ?>
        <form method="post" action="/product/warehouse-update" class="form-inline">
            <input type="hidden" name="warehouse_id" value="<?= $w['id'] ?>">

            <input type="text" name="name" value="<?= htmlspecialchars($w['name']) ?? '' ?>" required>
            <input type="text" name="address" value="<?= htmlspecialchars($wh['address'] ?? '') ?>">
            <input type="text" name="note" value="<?= htmlspecialchars($wh['note'] ?? '') ?>">


            <button type="submit">💾 Uložit</button>
        </form>

        <form method="post" action="/product/warehouse-delete" onsubmit="return confirm('Opravdu smazat sklad?');" style="display:inline;">
            <input type="hidden" name="warehouse_id" value="<?= $w['id'] ?>">
            <button type="submit" class="button danger">🗑️</button>
        </form>

        <hr>
        <?php endforeach; ?>

        <div class="modal-actions">
            <button type="button" class="button" onclick="closeModal('warehouseModal')">Zavřít</button>
        </div>
    </div>
</div>
