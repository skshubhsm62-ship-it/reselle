<?php
session_start();

// Local config (agar hai to use karo)
if (file_exists(__DIR__ . '/config-local.php')) {
    require __DIR__ . '/config-local.php';
} else {
    $host = 'localhost';
    $db   = 'reseller_portal';
    $user = 'root';
    $pass = '';
}

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("DB Error: " . $e->getMessage());
}

function requireLogin() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit;
    }
}
?>
