<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../backend/db.php';

// Basic stats
$totalProducts = (int)$pdo->query('SELECT COUNT(*) FROM products')->fetchColumn();
$totalCategories = (int)$pdo->query('SELECT COUNT(*) FROM categories')->fetchColumn();
$inventoryValue = (float)$pdo->query('SELECT COALESCE(SUM(price * stock), 0) FROM products')->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Dashboard - OTSP</title>
    <link rel="stylesheet" href="/OTSP/assets/css/style.css" />
</head>
<body>
    <main class="container admin-page">
        <div class="admin-page-header">
            <div>
                <h1>Admin Dashboard</h1>
                <p class="admin-breadcrumb">Welcome, <?php echo htmlspecialchars($_SESSION['admin_username'] ?? 'Admin'); ?>.</p>
            </div>
            <div>
                <a href="/OTSP/admin/logout.php" class="btn-outline">Logout</a>
            </div>
        </div>

        <section class="admin-card">
            <div class="admin-grid-3">
                <div>
                    <div class="admin-stat-label">Total products</div>
                    <div class="admin-stat-value"><?php echo $totalProducts; ?></div>
                    <div class="admin-stat-sub">All products in your catalog</div>
                </div>
                <div>
                    <div class="admin-stat-label">Categories</div>
                    <div class="admin-stat-value"><?php echo $totalCategories; ?></div>
                    <div class="admin-stat-sub">Active product categories</div>
                </div>
                <div>
                    <div class="admin-stat-label">Inventory value</div>
                    <div class="admin-stat-value">₱<?php echo number_format($inventoryValue, 2); ?></div>
                    <div class="admin-stat-sub">Price × stock for all products</div>
                </div>
            </div>
        </section>

        <section class="admin-card">
            <nav>
                <a href="/OTSP/admin/products.php" class="btn-primary">Manage Products</a>
                <a href="/OTSP/admin/orders.php" class="btn-outline">View Orders</a>
            </nav>
        </section>
    </main>
</body>
</html>
