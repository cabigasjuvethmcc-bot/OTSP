<?php
require_once __DIR__ . '/../backend/db.php';

function get_categories(): array {
    return [
        'laptops'    => 'Laptops',
        'desktops'   => 'Desktops',
        'components' => 'Components',
        'peripherals'=> 'Peripherals',
    ];
}

function get_products(array $filters = []): array {
    global $pdo;
    $sql = 'SELECT * FROM products WHERE 1=1';
    $params = [];

    if (!empty($filters['category'])) {
        $sql .= ' AND category_slug = :category';
        $params[':category'] = $filters['category'];
    }

    if (!empty($filters['q'])) {
        $sql .= ' AND (name LIKE :q OR description LIKE :q)';
        $params[':q'] = '%' . $filters['q'] . '%';
    }

    if (!empty($filters['sort'])) {
        if ($filters['sort'] === 'price_asc') {
            $sql .= ' ORDER BY price ASC';
        } elseif ($filters['sort'] === 'price_desc') {
            $sql .= ' ORDER BY price DESC';
        } else {
            $sql .= ' ORDER BY id DESC';
        }
    } else {
        $sql .= ' ORDER BY id DESC';
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function get_product_by_id(int $id): ?array {
    global $pdo;
    $stmt = $pdo->prepare('SELECT * FROM products WHERE id = :id');
    $stmt->execute([':id' => $id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    return $product ?: null;
}

function create_order(array $customer, array $cartItems): ?int {
    global $pdo;

    if (empty($cartItems)) {
        return null;
    }

    $pdo->beginTransaction();
    try {
        $total = 0;
        foreach ($cartItems as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        $stmt = $pdo->prepare('INSERT INTO orders (customer_name, email, phone, address, total_amount, created_at) VALUES (:name, :email, :phone, :address, :total, NOW())');
        $stmt->execute([
            ':name'    => $customer['name'],
            ':email'   => $customer['email'],
            ':phone'   => $customer['phone'],
            ':address' => $customer['address'],
            ':total'   => $total,
        ]);

        $orderId = (int)$pdo->lastInsertId();

        $itemStmt = $pdo->prepare('INSERT INTO order_items (order_id, product_id, quantity, unit_price) VALUES (:order_id, :product_id, :quantity, :unit_price)');

        foreach ($cartItems as $item) {
            $itemStmt->execute([
                ':order_id'   => $orderId,
                ':product_id' => $item['id'],
                ':quantity'   => $item['quantity'],
                ':unit_price' => $item['price'],
            ]);
        }

        $pdo->commit();
        return $orderId;
    } catch (Exception $e) {
        $pdo->rollBack();
        error_log('Order creation failed: ' . $e->getMessage());
        return null;
    }
}
