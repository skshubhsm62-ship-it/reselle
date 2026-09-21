<?php
require_once __DIR__ . '/../../config.php';

function requireAdmin() {
    if (!isset($_SESSION['admin_id'])) {
        header("Location: index.php");
        exit;
    }
}

function currentAdmin($pdo) {
    $stmt = $pdo->prepare("SELECT * FROM admins WHERE id = ?");
    $stmt->execute([$_SESSION['admin_id']]);
    return $stmt->fetch();
}
?>
