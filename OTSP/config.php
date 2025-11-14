<?php
// Basic site & database configuration

// ---- DATABASE SETTINGS ----
$DB_HOST = 'localhost';
$DB_NAME = 'otsp_db';
$DB_USER = 'root';
$DB_PASS = '';

// Create PDO connection (used by includes/functions.php)
try {
    $pdo = new PDO("mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4", $DB_USER, $DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
} catch (PDOException $e) {
    die('Database connection failed: ' . htmlspecialchars($e->getMessage()));
}

// ---- GROQ API SETTINGS ----
// Put your Groq API key here. IMPORTANT: never expose this in JS.
$GROQ_API_KEY = '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';
$GROQ_MODEL   = 'llama-3.1-8b-instant';

// ---- GENERAL SITE SETTINGS ----
$SITE_NAME = 'OTSP Tech Store';
