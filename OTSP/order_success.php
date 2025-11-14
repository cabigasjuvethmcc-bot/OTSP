<?php
session_start();
require_once __DIR__ . '/includes/functions.php';

$orderId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$SITE_NAME = 'Order Placed - OTSP Tech Store';
include __DIR__ . '/includes/header.php';
?>
<section class="container">
    <h1>Thank you for your order!</h1>
    <?php if ($orderId): ?>
        <p>Your order ID is <strong>#<?php echo $orderId; ?></strong>.</p>
    <?php endif; ?>
    <p>We have received your order details. You will receive updates via email.</p>
    <a href="/OTSP/index.php" class="btn-primary">Back to Home</a>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
