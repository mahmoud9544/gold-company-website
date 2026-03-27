<?php
$isSubPage = strpos($_SERVER['PHP_SELF'], '/pages/') !== false;
$basePath = $isSubPage ? '../' : '';
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' | ' : ''; ?>شركة الذهب الملكي</title>
    <link rel="stylesheet" href="<?php echo $basePath; ?>assets/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
    <nav class="navbar">
        <div class="container nav-container">
            <a href="<?php echo $basePath; ?>index.php" class="logo">
                <i class="fas fa-crown"></i>
                <span>الذهب الملكي</span>
            </a>
            <button class="nav-toggle" id="navToggle" aria-label="toggle navigation">
                <i class="fas fa-bars"></i>
            </button>
            <ul class="nav-links" id="navLinks">
                <li><a href="<?php echo $basePath; ?>index.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">الرئيسية</a></li>
                <li><a href="<?php echo $basePath; ?>pages/about.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'about.php' ? 'active' : ''; ?>">من نحن</a></li>
                <li><a href="<?php echo $basePath; ?>pages/products.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'products.php' ? 'active' : ''; ?>">منتجاتنا</a></li>
                <li><a href="<?php echo $basePath; ?>pages/contact.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'contact.php' ? 'active' : ''; ?>">اتصل بنا</a></li>
            </ul>
        </div>
    </nav>
