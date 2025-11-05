<?php
// change_password.php
require_once __DIR__ . '/config.php';

// Expect JSON body: { user_id, current_password, new_password }
$input = json_decode(file_get_contents('php://input'), true);
$userId = isset($input['user_id']) ? intval($input['user_id']) : 0;
$current = $input['current_password'] ?? '';
$new = $input['new_password'] ?? '';

header('Content-Type: application/json');

if (!$userId || !$current || !$new) {
    echo json_encode(['success' => false, 'message' => 'Missing parameters']);
    exit;
}

try {
    $stmt = $pdo->prepare('SELECT password FROM users WHERE id = ?');
    $stmt->execute([$userId]);
    $user = $stmt->fetch();

    if (!$user) {
        echo json_encode(['success' => false, 'message' => 'User not found']);
        exit;
    }

    if (!password_verify($current, $user['password'])) {
        echo json_encode(['success' => false, 'message' => 'Current password incorrect']);
        exit;
    }

    $hash = password_hash($new, PASSWORD_DEFAULT);
    $u = $pdo->prepare('UPDATE users SET password = ? WHERE id = ?');
    $u->execute([$hash, $userId]);

    echo json_encode(['success' => true, 'message' => 'Password updated']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
