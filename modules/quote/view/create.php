<h2>📄 Vytvořit nabídku</h2>

<form method="post" action="/quote/generate">
    <label for="customer_name">Zákazník</label>
    <input type="text" name="customer_name" id="customer_name" required>

    <label for="customer_email">E-mail</label>
    <input type="email" name="customer_email" id="customer_email">

    <label for="customer_note">Poznámka k zákazníkovi</label>
    <textarea name="customer_note" id="customer_note"></textarea>

    <label>Poznámka nad nabídkou:</label>
    <textarea name="note_top" rows="2"></textarea>

    <label>Poznámka pod nabídkou:</label>
    <textarea name="note_bottom" rows="2"></textarea>
    
    <h4>Položky nabídky:</h4>
    <ul>
        <?php foreach ($items as $item): ?>
            <li><?= $item['qty'] ?>× <?= htmlspecialchars($item['name']) ?> = 
                <?= number_format($item['total_vat'], 2, ',', ' ') ?> Kč (vč. DPH)</li>
        <?php endforeach; ?>
    </ul>

    <p><strong>Celkem bez DPH:</strong> <?= number_format($totalWithoutVat, 2, ',', ' ') ?> Kč</p>
    <p><strong>Celkem s DPH:</strong> <?= number_format($totalWithVat, 2, ',', ' ') ?> Kč</p>

    <button type="submit" class="button">📄 Vygenerovat PDF nabídku</button>
</form>
