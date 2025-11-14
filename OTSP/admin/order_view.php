<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../backend/db.php';

$orderId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($orderId <= 0) {
    header('Location: /OTSP/admin/orders.php');
    exit;
}

// Fetch order
$stmt = $pdo->prepare('SELECT * FROM orders WHERE id = :id');
$stmt->execute([':id' => $orderId]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    header('Location: /OTSP/admin/orders.php');
    exit;
}

// Fetch order items with product names
$itemStmt = $pdo->prepare('SELECT oi.quantity, oi.unit_price, p.name AS product_name
    FROM order_items oi
    JOIN products p ON p.id = oi.product_id
    WHERE oi.order_id = :order_id');
$itemStmt->execute([':order_id' => $orderId]);
$items = $itemStmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Order #<?php echo (int)$order['id']; ?> - OTSP Admin</title>
    <link rel="stylesheet" href="/OTSP/assets/css/style.css" />
</head>
<body>
<main class="container admin-page">
    <div class="admin-page-header">
        <div>
            <h1>Order #<?php echo (int)$order['id']; ?></h1>
            <p class="admin-breadcrumb">Placed on <?php echo htmlspecialchars($order['created_at']); ?></p>
        </div>
        <div>
            <a href="/OTSP/admin/orders.php" class="btn-outline">Back to Orders</a>
            <a href="/OTSP/admin/logout.php" class="btn-outline">Logout</a>
        </div>
    </div>

    <section class="admin-card">
        <h2>Customer Info</h2>
        <p><strong>Name:</strong> <?php echo htmlspecialchars($order['customer_name']); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($order['email']); ?></p>
        <p><strong>Phone:</strong> <?php echo htmlspecialchars($order['phone']); ?></p>
        <p><strong>Address:</strong><br><?php echo nl2br(htmlspecialchars($order['address'])); ?></p>
        <p><strong>Total Amount:</strong> ₱<?php echo number_format($order['total_amount'], 2); ?></p>
    </section>

    <section class="admin-card">
        <h2>Items</h2>
        <?php if (empty($items)): ?>
            <p>No items found for this order.</p>
        <?php else: ?>
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Unit Price</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                        <td><?php echo (int)$item['quantity']; ?></td>
                        <td>₱<?php echo number_format($item['unit_price'], 2); ?></td>
                        <td>₱<?php echo number_format($item['unit_price'] * $item['quantity'], 2); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </section>
</main>
</body>
</html>
