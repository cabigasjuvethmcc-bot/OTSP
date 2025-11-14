<?php
// Database and Groq configuration for OTSP

$DB_HOST = 'localhost';
$DB_NAME = 'otsp_db';
$DB_USER = 'root';
$DB_PASS = '';

try {
    $pdo = new PDO("mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4", $DB_USER, $DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
} catch (PDOException $e) {
    die('Database connection failed: ' . htmlspecialchars($e->getMessage()));
}

$GROQ_API_KEY = 'PUT_YOUR_GROQ_API_KEY_HERE';
$GROQ_MODEL   = 'llama-3.1-8b-instant';
