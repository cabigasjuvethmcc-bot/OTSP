<?php
session_start();
require_once __DIR__ . '/includes/functions.php';

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['remove_id'])) {
        $removeId = (int)$_POST['remove_id'];
        if (isset($_SESSION['cart'][$removeId])) {
            unset($_SESSION['cart'][$removeId]);
        }
    }

    if (isset($_POST['update'])) {
        foreach ($_POST['qty'] as $id => $qty) {
            $id  = (int)$id;
            $qty = max(0, (int)$qty);
            if ($qty === 0) {
                unset($_SESSION['cart'][$id]);
            } elseif (isset($_SESSION['cart'][$id])) {
                $_SESSION['cart'][$id]['quantity'] = $qty;
            }
        }
    }
}

$cartItems = array_values($_SESSION['cart']);
$total = 0;
foreach ($cartItems as $item) {
    $total += $item['price'] * $item['quantity'];
}

$SITE_NAME = 'Cart - OTSP Tech Store';
include __DIR__ . '/includes/header.php';
?>
<section class="container">
    <h1>Your Cart</h1>
    <?php if (empty($cartItems)): ?>
        <p>Your cart is empty.</p>
    <?php else: ?>
        <form method="post" class="cart-form">
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Subtotal</th>
                        <th>Remove</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($cartItems as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['name']); ?></td>
                        <td>₱<?php echo number_format($item['price'], 2); ?></td>
                        <td>
                            <input type="number" name="qty[<?php echo (int)$item['id']; ?>]" min="0" value="<?php echo (int)$item['quantity']; ?>" />
                        </td>
                        <td>₱<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                        <td>
                            <button type="submit" name="remove_id" value="<?php echo (int)$item['id']; ?>" class="btn-outline">Remove</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <div class="cart-summary">
                <p>Total: <strong>₱<?php echo number_format($total, 2); ?></strong></p>
                <button type="submit" name="update" class="btn-outline">Update Cart</button>
                <a href="/OTSP/checkout.php" class="btn-primary">Proceed to Checkout</a>
            </div>
        </form>
    <?php endif; ?>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
