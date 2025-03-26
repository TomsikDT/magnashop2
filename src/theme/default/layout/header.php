<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <?php if (!empty($metaDescription)): ?>
        <meta name="description" content="<?= htmlspecialchars($metaDescription) ?>">
    <?php endif; ?>
    
    <?php foreach ($metaTags as $name => $content): ?>
        <meta name="<?= htmlspecialchars($name) ?>" content="<?= htmlspecialchars($content) ?>">
    <?php endforeach; ?>
    
    <link rel="stylesheet" href="/src/theme/default/assets/css/style.css">
</head>
<body>
    <header>
        <div class="logo">
            <a href="/"><img src="/src/theme/default/assets/images/logo.png" alt="Logo"></a>
        </div>
    </header>