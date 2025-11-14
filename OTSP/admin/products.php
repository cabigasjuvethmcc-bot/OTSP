<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../backend/db.php';

// Filters
$search = trim($_GET['search'] ?? '');
$categoryFilter = (int)($_GET['category_id'] ?? 0);
$sort = $_GET['sort'] ?? 'newest';

// Build query
$sql = 'SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id';
$where = [];
$params = [];

if ($search !== '') {
    $where[] = 'p.name LIKE :search';
    $params[':search'] = '%' . $search . '%';
}

if ($categoryFilter > 0) {
    $where[] = 'p.category_id = :category_id';
    $params[':category_id'] = $categoryFilter;
}

if ($where) {
    $sql .= ' WHERE ' . implode(' AND ', $where);
}

// Sort
switch ($sort) {
    case 'price_asc':
        $sql .= ' ORDER BY p.price ASC';
        break;
    case 'price_desc':
        $sql .= ' ORDER BY p.price DESC';
        break;
    case 'stock_asc':
        $sql .= ' ORDER BY p.stock ASC';
        break;
    case 'stock_desc':
        $sql .= ' ORDER BY p.stock DESC';
        break;
    default:
        $sql .= ' ORDER BY p.id DESC';
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Categories for filter
$catStmt = $pdo->query('SELECT id, name FROM categories ORDER BY name');
$categories = $catStmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Manage Products - OTSP</title>
    <link rel="stylesheet" href="/OTSP/assets/css/style.css" />
</head>
<body>
    <main class="container admin-page">
        <div class="admin-page-header">
            <div>
                <h1>Products</h1>
                <p class="admin-breadcrumb">
                    <a href="/OTSP/admin/dashboard.php">Dashboard</a>
                    &nbsp;/&nbsp;
                    <span>Manage products</span>
                </p>
            </div>
            <div>
                <a href="/OTSP/admin/product_form.php" class="btn-primary">Add Product</a>
            </div>
        </div>

        <section class="admin-card">
            <form class="admin-filters" method="get">
                <input type="text" name="search" placeholder="Search by name" value="<?php echo htmlspecialchars($search); ?>" />

                <select name="category_id">
                    <option value="0">All categories</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo (int)$cat['id']; ?>" <?php if ($categoryFilter === (int)$cat['id']) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($cat['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <select name="sort">
                    <option value="newest" <?php if ($sort === 'newest') echo 'selected'; ?>>Newest first</option>
                    <option value="price_asc" <?php if ($sort === 'price_asc') echo 'selected'; ?>>Price: low to high</option>
                    <option value="price_desc" <?php if ($sort === 'price_desc') echo 'selected'; ?>>Price: high to low</option>
                    <option value="stock_asc" <?php if ($sort === 'stock_asc') echo 'selected'; ?>>Stock: low to high</option>
                    <option value="stock_desc" <?php if ($sort === 'stock_desc') echo 'selected'; ?>>Stock: high to low</option>
                </select>

                <button type="submit" class="btn-primary small">Apply</button>
                <a href="/OTSP/admin/products.php" class="btn-outline small">Reset</a>
            </form>

            <div class="admin-table-wrapper">
                <table class="admin-table">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th style="width:130px;">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($products)): ?>
                        <tr>
                            <td colspan="6" class="admin-table-empty">No products found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($products as $product): ?>
                            <tr>
                                <td><?php echo (int)$product['id']; ?></td>
                                <td><?php echo htmlspecialchars($product['name']); ?></td>
                                <td><?php echo htmlspecialchars($product['category_name'] ?? ''); ?></td>
                                <td>₱<?php echo number_format($product['price'], 2); ?></td>
                                <td><?php echo (int)$product['stock']; ?></td>
                                <td>
                                    <a href="/OTSP/admin/product_form.php?id=<?php echo (int)$product['id']; ?>">Edit</a>
                                    &nbsp;&middot;&nbsp;
                                    <a href="/OTSP/admin/product_delete.php?id=<?php echo (int)$product['id']; ?>" onclick="return confirm('Delete this product?');">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</body>
</html>
