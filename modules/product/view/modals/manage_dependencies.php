<div id="dependencyModal" class="modal" style="display: none;">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Správa závislostí</h2>

        <form method="post" action="/product/saveDependency">
            <input type="hidden" name="product_id" value="">
            <input type="hidden" name="dependency_id" id="edit-dependency-id">

            <div>
                <label for="dependent_product_id">Závislý produkt</label>
                <select name="dependent_product_id" required>
                    <?php foreach ($products as $prod): ?>
                        <option value="<?= $prod['id'] ?>"><?= htmlspecialchars($prod['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label for="type">Typ závislosti</label>
                <select name="type">
                    <option value="fixed">Pevné množství</option>
                    <option value="work">Práce za jednotku</option>
                </select>
            </div>

            <div>
                <label for="note">Poznámka</label>
                <input type="text" name="note" placeholder="Např. přidat konektory">
            </div>

            <div>
                <label for="quantity">Množství / Cena</label>
                <input type="number" step="0.01" name="quantity" required>
            </div>

            <button type="submit">💾 Uložit závislost</button>
        </form>
        <hr>

        <h3>Aktuální závislosti</h3>
<table class="dependency-table">
    <thead>
        <tr>
            <th>Produkt</th>
            <th>Typ</th>
            <th>Množství</th>
            <th>Poznámka</th>
            <th>Akce</th>
        </tr>
    </thead>
    <tbody id="dependency-list">
    </tbody>
</table>


    </div>
</div>