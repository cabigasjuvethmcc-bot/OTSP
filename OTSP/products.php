<?php
session_start();
require_once __DIR__ . '/includes/functions.php';

$category = isset($_GET['category']) ? $_GET['category'] : '';
$q        = isset($_GET['q']) ? trim($_GET['q']) : '';
 $sort     = isset($_GET['sort']) ? $_GET['sort'] : '';

$filters = [];
if ($category) {
    $filters['category'] = $category;
}
if ($q) {
    $filters['q'] = $q;
}
if ($sort) {
    $filters['sort'] = $sort;
}

$products = get_products($filters);
$SITE_NAME = 'Products - OTSP Tech Store';
include __DIR__ . '/includes/header.php';
?>
<section class="container page-heading">
    <h1>Products</h1>
</section>
<section class="container product-filters">
    <form method="get" class="filters-form">
        <input type="text" name="q" placeholder="Search" value="<?php echo htmlspecialchars($q); ?>" />
        <select name="category">
            <option value="">All Categories</option>
            <?php foreach (get_categories() as $slug => $label): ?>
                <option value="<?php echo htmlspecialchars($slug); ?>" <?php if ($slug === $category) echo 'selected'; ?>>
                    <?php echo htmlspecialchars($label); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <select name="sort">
            <option value="">Sort by</option>
            <option value="price_asc" <?php if ($sort === 'price_asc') echo 'selected'; ?>>Price: Low to High</option>
            <option value="price_desc" <?php if ($sort === 'price_desc') echo 'selected'; ?>>Price: High to Low</option>
        </select>
        <button type="submit" class="btn-primary">Apply</button>
    </form>
</section>
<section class="container">
    <div class="product-grid">
        <?php if (empty($products)): ?>
            <p>No products found.</p>
        <?php else: ?>
            <?php foreach ($products as $product): ?>
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
                    <?php
                        $specParts = [];
                        if (!empty($product['cpu'])) {
                            $specParts[] = htmlspecialchars($product['cpu']);
                        }
                        if (!empty($product['ram'])) {
                            $specParts[] = htmlspecialchars($product['ram']);
                        }
                        if (!empty($product['storage'])) {
                            $specParts[] = htmlspecialchars($product['storage']);
                        }
                        if (!empty($specParts)) {
                            echo '<p class="product-specs">' . implode(' • ', $specParts) . '</p>';
                        }
                    ?>
                    <a class="btn-outline" href="/OTSP/product.php?id=<?php echo (int)$product['id']; ?>">View Details</a>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
