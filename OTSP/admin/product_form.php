<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../backend/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$isEdit = $id > 0;

// Load categories
$catStmt = $pdo->query('SELECT * FROM categories ORDER BY name');
$categories = $catStmt->fetchAll(PDO::FETCH_ASSOC);

$product = [
    'name' => '',
    'description' => '',
    'price' => '',
    'stock' => '',
    'category_id' => '',
    'category_slug' => '',
    'slug' => '',
    'image' => null,
];

if ($isEdit) {
    $stmt = $pdo->prepare('SELECT * FROM products WHERE id = :id');
    $stmt->execute([':id' => $id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        $product = $row;
    } else {
        header('Location: /OTSP/admin/products.php');
        exit;
    }
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = (float)($_POST['price'] ?? 0);
    $stock = (int)($_POST['stock'] ?? 0);
    $category_id = (int)($_POST['category_id'] ?? 0);
    $currentImage = $product['image'] ?? null;

    if ($name === '' || $price <= 0 || $category_id <= 0) {
        $error = 'Please fill in name, category and a valid price.';
    } else {
        // Get category slug
        $cStmt = $pdo->prepare('SELECT slug FROM categories WHERE id = :id');
        $cStmt->execute([':id' => $category_id]);
        $cat = $cStmt->fetch(PDO::FETCH_ASSOC);
        if (!$cat) {
            $error = 'Invalid category.';
        } else {
            $category_slug = $cat['slug'];
            $slug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $name));

            // Handle image upload (optional)
            $imageFilename = $currentImage;
            if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../assets/images/';
                $originalName = basename($_FILES['image']['name']);
                $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
                $allowed = ['jpg', 'jpeg', 'png', 'gif'];

                if (!in_array($ext, $allowed, true)) {
                    $error = 'Invalid image type. Please upload JPG, PNG, or GIF.';
                } else {
                    $safeName = preg_replace('/[^a-z0-9\.]+/i', '-', pathinfo($originalName, PATHINFO_FILENAME));
                    $newName = time() . '-' . $safeName . '.' . $ext;
                    $targetPath = $uploadDir . $newName;

                    if (!is_dir($uploadDir)) {
                        @mkdir($uploadDir, 0777, true);
                    }

                    if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                        $imageFilename = $newName;
                    } else {
                        $error = 'Failed to upload image. Please try again.';
                    }
                }
            }

            if ($error !== '') {
                // Skip DB write if an image error occurred
            } else {

                if ($isEdit) {
                    $stmt = $pdo->prepare('UPDATE products SET name = :name, description = :description, price = :price, stock = :stock, category_id = :category_id, category_slug = :category_slug, slug = :slug, image = :image WHERE id = :id');
                    $stmt->execute([
                        ':name' => $name,
                        ':description' => $description,
                        ':price' => $price,
                        ':stock' => $stock,
                        ':category_id' => $category_id,
                        ':category_slug' => $category_slug,
                        ':slug' => $slug,
                        ':image' => $imageFilename,
                        ':id' => $id,
                    ]);
                } else {
                    $stmt = $pdo->prepare('INSERT INTO products (name, description, price, stock, category_id, category_slug, slug, image) VALUES (:name, :description, :price, :stock, :category_id, :category_slug, :slug, :image)');
                    $stmt->execute([
                        ':name' => $name,
                        ':description' => $description,
                        ':price' => $price,
                        ':stock' => $stock,
                        ':category_id' => $category_id,
                        ':category_slug' => $category_slug,
                        ':slug' => $slug,
                        ':image' => $imageFilename,
                    ]);
                }

                header('Location: /OTSP/admin/products.php');
                exit;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo $isEdit ? 'Edit' : 'Add'; ?> Product - OTSP</title>
    <link rel="stylesheet" href="/OTSP/assets/css/style.css" />
</head>
<body>
    <main class="container admin-page" style="max-width:800px;">
        <div class="admin-page-header">
            <div>
                <h1><?php echo $isEdit ? 'Edit' : 'Add'; ?> Product</h1>
                <p class="admin-breadcrumb">
                    <a href="/OTSP/admin/dashboard.php">Dashboard</a>
                    &nbsp;/&nbsp;
                    <a href="/OTSP/admin/products.php">Products</a>
                    &nbsp;/&nbsp;
                    <span><?php echo $isEdit ? 'Edit product details' : 'Create a new product'; ?></span>
                </p>
            </div>
            <div>
                <a href="/OTSP/admin/products.php" class="btn-outline small">Back to Products</a>
            </div>
        </div>

        <section class="admin-card">
            <?php if ($error): ?>
                <p class="error-text" style="margin-bottom:0.75rem;">
                    <?php echo htmlspecialchars($error); ?>
                </p>
            <?php endif; ?>
            <form method="post" class="admin-form" enctype="multipart/form-data">
                <div class="admin-form-row">
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input id="name" type="text" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required />
                    </div>
                    <div class="form-group">
                        <label for="category_id">Category</label>
                        <select id="category_id" name="category_id" required>
                            <option value="">Select category</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo (int)$cat['id']; ?>" <?php if ($cat['id'] == $product['category_id']) echo 'selected'; ?>>
                                    <?php echo htmlspecialchars($cat['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="4" placeholder="Short details about this product"><?php echo htmlspecialchars($product['description']); ?></textarea>
                </div>

                <div class="admin-form-row">
                    <div class="form-group">
                        <label for="price">Price (₱)</label>
                        <input id="price" type="number" step="0.01" min="0" name="price" value="<?php echo htmlspecialchars($product['price']); ?>" required />
                    </div>
                    <div class="form-group">
                        <label for="stock">Stock</label>
                        <input id="stock" type="number" min="0" name="stock" value="<?php echo htmlspecialchars($product['stock']); ?>" required />
                    </div>
                </div>

                <div class="admin-form-row">
                    <div class="form-group">
                        <label for="image">Product image (optional)</label>
                        <input id="image" type="file" name="image" accept="image/*" />
                        <small style="font-size:0.8rem;color:#6b7280;">JPG, PNG, or GIF. A square image works best.</small>
                    </div>
                    <?php if (!empty($product['image'])): ?>
                        <div class="form-group">
                            <label>Current image</label>
                            <img src="/OTSP/assets/images/<?php echo htmlspecialchars($product['image']); ?>" alt="Current product image" style="max-width:160px;border-radius:0.5rem;border:1px solid #e5e7eb;" />
                        </div>
                    <?php endif; ?>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-primary"><?php echo $isEdit ? 'Update product' : 'Create product'; ?></button>
                    <a href="/OTSP/admin/products.php" class="btn-outline">Cancel</a>
                </div>
            </form>
        </section>
    </main>
</body>
</html>
