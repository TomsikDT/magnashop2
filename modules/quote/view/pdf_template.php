<?php
// Vypočti číslo nabídky ve formátu YYYYMMDD001 (interní čítač můžeš nahradit v budoucnu)
$datum = date('Ymd', strtotime($created_at ?? 'now'));
$cisloNabidky = $datum . str_pad($id ?? 1, 3, '0', STR_PAD_LEFT);
?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #333; }
        .header { display: flex; justify-content: space-between; align-items: flex-start; }
        .company, .customer { width: 48%; }
        .company h3, .customer h3 { margin: 0 0 5px 0; }

        .section { margin: 20px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ccc; padding: 5px; }
        th { background: #f5f5f5; }

        .totals { margin-top: 20px; }
        .totals p { font-weight: bold; }

        .note { margin-top: 10px; font-style: italic; white-space: pre-wrap; }
        .title { text-align: center; font-size: 18px; margin-bottom: 10px; }

        .quote-number { text-align: right; font-size: 14px; margin-bottom: 10px; font-weight: bold; }
    </style>
</head>
<body>

    <div class="quote-number">
        Číslo nabídky: <?= htmlspecialchars($quoteNumber ?? '') ?>

    </div>

    <div class="header">
        <div class="customer">
            <h3>Odběratel:</h3>
            <p>
                <?= htmlspecialchars($customer_name ?? '') ?><br>
                <?= htmlspecialchars($customer_email ?? '') ?><br>
                <?= nl2br(htmlspecialchars($customer_note ?? '')) ?>
            </p>
        </div>
        <div class="company">
            <h3>Dodavatel:</h3>
            <p>
                Magnapro s.r.o.<br>
                Českolipská 325/20, 412 01 Litoměřice<br>
                IČ: 12345678<br>
                DIČ: CZ12345678<br>
                Email: info@magnapro.eu
            </p>
        </div>
    </div>

    <div class="title">Cenová nabídka</div>

    <?php if (!empty($note_top)): ?>
        <div class="note"><?= nl2br(htmlspecialchars($note_top)) ?></div>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th>Název</th>
                <th>Množství</th>
                <th>Cena bez DPH</th>
                <th>DPH</th>
                <th>Cena s DPH</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['product_name'] ?? $item['name']) ?></td>
                    <td><?= $item['qty'] ?></td>
                    <td><?= number_format($item['total'], 2, ',', ' ') ?> Kč</td>
                    <td><?= $item['vat_rate'] ?>%</td>
                    <td><?= number_format($item['total_vat'], 2, ',', ' ') ?> Kč</td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <?php if (!empty($note_bottom)): ?>
        <div class="note"><?= nl2br(htmlspecialchars($note_bottom)) ?></div>
    <?php endif; ?>

    <div class="totals">
        <p>Celkem bez DPH: <?= number_format($totalWithoutVat, 2, ',', ' ') ?> Kč</p>
        <p>Celkem s DPH: <?= number_format($totalWithVat, 2, ',', ' ') ?> Kč</p>
    </div>

</body>
</html>
