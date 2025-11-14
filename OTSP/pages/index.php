<?php
session_start();
require_once __DIR__ . '/../backend/db.php';
require_once __DIR__ . '/../includes/functions.php';
$SITE_NAME = 'OTSP Tech Store - Home';
$featuredProducts = get_products(['sort' => 'price_desc']);
$featuredProducts = array_slice($featuredProducts, 0, 8);
include __DIR__ . '/../components/header.php';
?>
<section class="hero">
    <div class="container hero-inner">
        <div class="hero-text">
            <h1>Your One-Stop PC & Tech Shop</h1>
            <p>Build your next gaming rig or find the perfect work-from-home setup.</p>
            <a href="/OTSP/pages/products.php" class="btn-primary">Browse Products</a>
        </div>
        <div class="hero-image">
            <div class="hero-card">Premium components, curated for performance.</div>
        </div>
    </div>
</section>
<section class="categories">
    <div class="container">
        <h2>Shop by Category</h2>
        <div class="category-grid">
            <a href="/OTSP/pages/products.php?category=laptops" class="category-card">Laptops</a>
            <a href="/OTSP/pages/products.php?category=desktops" class="category-card">Desktops</a>
            <a href="/OTSP/pages/products.php?category=components" class="category-card">Components</a>
            <a href="/OTSP/pages/products.php?category=peripherals" class="category-card">Peripherals</a>
        </div>
    </div>
</section>
<section class="products">
    <div class="container">
        <h2>Featured Products</h2>
        <div class="product-grid">
            <?php foreach ($featuredProducts as $product): ?>
                <div class="product-card">
                    <?php if (!empty($product['image'])): ?>
                        <div class="product-image-placeholder" style="background:#ffffff;padding:0;border:0;">
                            <img src="/OTSP/assets/images/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" style="width:100%;height:160px;object-fit:cover;border-radius:0.75rem;" />
                        </div>
                    <?php else: ?>
                        <div class="product-image-placeholder">Image</div>
                    <?php endif; ?>
                    <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                    <p class="product-price">₱<?php echo number_format($product['price'], 2); ?></p>
                    <a class="btn-outline" href="/OTSP/pages/product-details.php?id=<?php echo (int)$product['id']; ?>">View Details</a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php include __DIR__ . '/../components/footer.php'; ?>
