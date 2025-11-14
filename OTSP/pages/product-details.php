<?php
session_start();
require_once __DIR__ . '/../backend/db.php';
require_once __DIR__ . '/../includes/functions.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$product = $id ? get_product_by_id($id) : null;

if (!$product) {
    http_response_code(404);
    echo 'Product not found';
    exit;
}

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $quantity = max(1, (int)($_POST['quantity'] ?? 1));

    if (!isset($_SESSION['cart'][$id])) {
        $_SESSION['cart'][$id] = [
            'id'       => $product['id'],
            'name'     => $product['name'],
            'price'    => (float)$product['price'],
            'quantity' => 0,
        ];
    }

    $_SESSION['cart'][$id]['quantity'] += $quantity;
    header('Location: /OTSP/cart.php');
    exit;
}

$SITE_NAME = $product['name'] . ' - OTSP Tech Store';
include __DIR__ . '/../components/header.php';
?>
<section class="container product-detail">
    <div class="product-detail-grid">
        <div class="product-detail-image">
            <div class="product-image-placeholder large">Image</div>
        </div>
        <div class="product-detail-info">
            <h1><?php echo htmlspecialchars($product['name']); ?></h1>
            <p class="product-price">₱<?php echo number_format($product['price'], 2); ?></p>
            <p class="product-desc"><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
            <?php
            $hasSpecs = !empty($product['cpu']) || !empty($product['ram']) || !empty($product['storage']) || !empty($product['gpu']) || !empty($product['display']) || !empty($product['os']);
            if ($hasSpecs): ?>
                <div class="product-specs-block">
                    <h2>Specifications</h2>
                    <table class="product-specs-table">
                        <tbody>
                        <?php if (!empty($product['cpu'])): ?>
                            <tr><th>CPU</th><td><?php echo htmlspecialchars($product['cpu']); ?></td></tr>
                        <?php endif; ?>
                        <?php if (!empty($product['ram'])): ?>
                            <tr><th>RAM</th><td><?php echo htmlspecialchars($product['ram']); ?></td></tr>
                        <?php endif; ?>
                        <?php if (!empty($product['storage'])): ?>
                            <tr><th>Storage</th><td><?php echo htmlspecialchars($product['storage']); ?></td></tr>
                        <?php endif; ?>
                        <?php if (!empty($product['gpu'])): ?>
                            <tr><th>GPU</th><td><?php echo htmlspecialchars($product['gpu']); ?></td></tr>
                        <?php endif; ?>
                        <?php if (!empty($product['display'])): ?>
                            <tr><th>Display</th><td><?php echo htmlspecialchars($product['display']); ?></td></tr>
                        <?php endif; ?>
                        <?php if (!empty($product['os'])): ?>
                            <tr><th>OS</th><td><?php echo htmlspecialchars($product['os']); ?></td></tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
            <form method="post" class="add-to-cart-form">
                <label>
                    Quantity
                    <input type="number" name="quantity" value="1" min="1" />
                </label>
                <button type="submit" name="add_to_cart" class="btn-primary">Add to Cart</button>
            </form>
        </div>
    </div>
</section>
<?php include __DIR__ . '/../components/footer.php'; ?>
