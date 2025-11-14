<?php
session_start();
require_once __DIR__ . '/includes/functions.php';

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header('Location: /OTSP/cart.php');
    exit;
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer = [
        'name'    => trim($_POST['name'] ?? ''),
        'email'   => trim($_POST['email'] ?? ''),
        'phone'   => trim($_POST['phone'] ?? ''),
        'address' => trim($_POST['address'] ?? ''),
    ];

    foreach ($customer as $key => $value) {
        if ($value === '') {
            $errors[$key] = 'Required';
        }
    }

    if (empty($errors)) {
        $orderId = create_order($customer, array_values($_SESSION['cart']));
        if ($orderId) {
            $_SESSION['cart'] = [];
            header('Location: /OTSP/order_success.php?id=' . $orderId);
            exit;
        } else {
            $errors['general'] = 'Could not place order. Please try again.';
        }
    }
}

$cartItems = array_values($_SESSION['cart']);
$total = 0;
foreach ($cartItems as $item) {
    $total += $item['price'] * $item['quantity'];
}

$SITE_NAME = 'Checkout - OTSP Tech Store';
include __DIR__ . '/includes/header.php';
?>
<section class="container checkout">
    <h1>Checkout</h1>
    <?php if (!empty($errors['general'])): ?>
        <p class="error-text"><?php echo htmlspecialchars($errors['general']); ?></p>
    <?php endif; ?>
    <div class="checkout-grid">
        <form method="post" class="checkout-form">
            <label>
                Full Name
                <input type="text" name="name" value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>" />
                <?php if (!empty($errors['name'])): ?><span class="error-text">Required</span><?php endif; ?>
            </label>
            <label>
                Email
                <input type="email" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" />
                <?php if (!empty($errors['email'])): ?><span class="error-text">Required</span><?php endif; ?>
            </label>
            <label>
                Phone
                <input type="text" name="phone" value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>" />
                <?php if (!empty($errors['phone'])): ?><span class="error-text">Required</span><?php endif; ?>
            </label>
            <label>
                Address
                <textarea name="address" rows="3"><?php echo htmlspecialchars($_POST['address'] ?? ''); ?></textarea>
                <?php if (!empty($errors['address'])): ?><span class="error-text">Required</span><?php endif; ?>
            </label>
            <button type="submit" class="btn-primary">Place Order</button>
        </form>
        <div class="checkout-summary">
            <h2>Order Summary</h2>
            <ul>
                <?php foreach ($cartItems as $item): ?>
                    <li>
                        <?php echo htmlspecialchars($item['name']); ?> x <?php echo (int)$item['quantity']; ?>
                        <span>₱<?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
            <p class="checkout-total">Total: <strong>₱<?php echo number_format($total, 2); ?></strong></p>
        </div>
    </div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
