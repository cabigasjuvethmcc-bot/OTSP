<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../backend/db.php';

// Fetch all orders, newest first
$stmt = $pdo->query('SELECT id, customer_name, email, phone, total_amount, created_at FROM orders ORDER BY created_at DESC');
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Orders - OTSP Admin</title>
    <link rel="stylesheet" href="/OTSP/assets/css/style.css" />
</head>
<body>
<main class="container admin-page">
    <div class="admin-page-header">
        <div>
            <h1>Orders</h1>
            <p class="admin-breadcrumb">View and trace customer orders.</p>
        </div>
        <div>
            <a href="/OTSP/admin/dashboard.php" class="btn-outline">Back to Dashboard</a>
            <a href="/OTSP/admin/logout.php" class="btn-outline">Logout</a>
        </div>
    </div>

    <section class="admin-card">
        <?php if (empty($orders)): ?>
            <p>No orders have been placed yet.</p>
        <?php else: ?>
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Customer</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Total</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><?php echo (int)$order['id']; ?></td>
                        <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                        <td><?php echo htmlspecialchars($order['email']); ?></td>
                        <td><?php echo htmlspecialchars($order['phone']); ?></td>
                        <td>₱<?php echo number_format($order['total_amount'], 2); ?></td>
                        <td><?php echo htmlspecialchars($order['created_at']); ?></td>
                        <td>
                            <a href="/OTSP/admin/order_view.php?id=<?php echo (int)$order['id']; ?>" class="btn-outline">View</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </section>
</main>
</body>
</html>
