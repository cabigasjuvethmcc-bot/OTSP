<?php
if (!isset($SITE_NAME)) {
    $SITE_NAME = 'OTSP Tech Store';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo htmlspecialchars($SITE_NAME); ?></title>
    <link rel="stylesheet" href="/OTSP/assets/css/style.css" />
</head>
<body>
<header class="site-header">
    <div class="container header-inner">
        <div class="logo">
            <a href="/OTSP/index.php">OTSP<span>Tech</span></a>
        </div>
        <form action="/OTSP/products.php" method="get" class="search-bar">
            <input type="text" name="q" placeholder="Search for products" />
            <button type="submit">Search</button>
        </form>
        <nav class="main-nav">
            <a href="/OTSP/index.php">Home</a>
            <a href="/OTSP/products.php?category=laptops">Laptops</a>
            <a href="/OTSP/products.php?category=desktops">Desktops</a>
            <a href="/OTSP/products.php?category=components">Components</a>
            <a href="/OTSP/cart.php" class="cart-link">Cart</a>
        </nav>
    </div>
</header>
<main class="site-main">
