<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header('Location: /OTSP/admin/login.php');
    exit;
}

