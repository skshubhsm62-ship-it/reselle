<?php
require 'config.php';

header('Content-Type: application/json');

// ============ SECRET KEY (Change Karo) ============
$SECRET_KEY = 'HUNTER';
// ==================================================

$secret = $_REQUEST['secret'] ?? '';
if ($secret !== $SECRET_KEY) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$action = $_REQUEST['action'] ?? '';

try {
    switch ($action) {

        // 1. Products list
        case 'products':
            $products = $pdo->query("SELECT id, name, price, stock, category FROM products WHERE stock > 0 ORDER BY id DESC")->fetchAll();
            echo json_encode(['success' => true, 'products' => $products]);
            break;

        // 2. Get user by telegram_id
        case 'get_user':
            $tg = (int)($_REQUEST['telegram_id'] ?? 0);
            $stmt = $pdo->prepare("SELECT u.id, u.username, u.wallet, bu.telegram_username FROM bot_users bu JOIN users u ON u.id = bu.user_id WHERE bu.telegram_id = ?");
            $stmt->execute([$tg]);
            $user = $stmt->fetch();
            echo json_encode(['success' => true, 'user' => $user ?: null]);
            break;

        // 3. Link telegram to website user
        case 'link':
            $tg     = (int)($_REQUEST['telegram_id'] ?? 0);
            $tgUser = trim($_REQUEST['telegram_username'] ?? '');
            $code   = strtoupper(trim($_REQUEST['code'] ?? ''));

            if ($tg <= 0 || $code === '') {
                echo json_encode(['success' => false, 'error' => 'Missing telegram_id or code']);
                break;
            }

            // Find user with this code
            $stmt = $pdo->prepare("SELECT id, username FROM users WHERE link_code = ?");
            $stmt->execute([$code]);
            $user = $stmt->fetch();

            if (!$user) {
                echo json_encode(['success' => false, 'error' => 'Invalid code']);
                break;
            }

            // Check if already linked
            $stmt = $pdo->prepare("SELECT id FROM bot_users WHERE telegram_id = ?");
            $stmt->execute([$tg]);
            if ($stmt->fetch()) {
                $pdo->prepare("UPDATE bot_users SET user_id = ?, telegram_username = ? WHERE telegram_id = ?")
                    ->execute([$user['id'], $tgUser, $tg]);
            } else {
                $pdo->prepare("INSERT INTO bot_users (telegram_id, telegram_username, user_id) VALUES (?, ?, ?)")
                    ->execute([$tg, $tgUser, $user['id']]);
            }

            // Clear the code
            $pdo->prepare("UPDATE users SET link_code = NULL WHERE id = ?")->execute([$user['id']]);

            echo json_encode(['success' => true, 'username' => $user['username']]);
            break;

        // 4. Get user's keys
        case 'my_keys':
            $tg = (int)($_REQUEST['telegram_id'] ?? 0);
            $stmt = $pdo->prepare("
                SELECT pk.`key`, pk.expires_at, pk.duration_seconds, p.name AS product_name
                FROM product_keys pk
                JOIN products p ON p.id = pk.product_id
                JOIN bot_users bu ON bu.user_id = pk.used_by_user
                WHERE bu.telegram_id = ? AND pk.is_used = 1
                ORDER BY pk.id DESC LIMIT 10
            ");
            $stmt->execute([$tg]);
            $keys = $stmt->fetchAll();
            echo json_encode(['success' => true, 'keys' => $keys]);
            break;

        // 5. Get plans of a product
        case 'plans':
            $pid = (int)($_REQUEST['product_id'] ?? 0);
            $stmt = $pdo->prepare("SELECT id, label, duration_seconds, price FROM product_plans WHERE product_id = ? ORDER BY duration_seconds ASC");
            $stmt->execute([$pid]);
            $plans = $stmt->fetchAll();
            echo json_encode(['success' => true, 'plans' => $plans]);
            break;

        default:
            echo json_encode(['error' => 'Unknown action']);
    }
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
