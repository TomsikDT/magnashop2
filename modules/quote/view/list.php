<h2>📄 Přehled cenových nabídek</h2>

<?php if (empty($quotes)): ?>
    <p>Žádné nabídky zatím nebyly vytvořeny.</p>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Číslo nabídky</th>
                <th>Zákazník</th>
                <th>E-mail</th>
                <th>Vytvořeno</th>
                <th>Celkem</th>
                <th>Akce</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($quotes as $q): ?>
                <tr>
                    <td><?= $q['id'] ?></td>
                    <td><?= htmlspecialchars($q['quote_number']) ?></td>
                    <td><?= htmlspecialchars($q['customer_name']) ?></td>
                    <td><?= htmlspecialchars($q['customer_email']) ?></td>
                    <td><?= date('d.m.Y H:i', strtotime($q['created_at'])) ?></td>
                    <td><?= number_format($q['total_with_vat'], 2, ',', ' ') ?> Kč</td>
                    <td>
                        <a href="/quote/show/<?= $q['id'] ?>">📄 PDF</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
