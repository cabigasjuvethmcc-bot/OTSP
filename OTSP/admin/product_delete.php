<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../backend/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    $stmt = $pdo->prepare('DELETE FROM products WHERE id = :id');
    $stmt->execute([':id' => $id]);
}

header('Location: /OTSP/admin/products.php');
exit;
