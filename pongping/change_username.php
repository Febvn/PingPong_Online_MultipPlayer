<?php
// change_username.php
session_start();
require_once __DIR__ . '/config.php';

$input = json_decode(file_get_contents('php://input'), true);

$newUsername = trim($input['new_username'] ?? '');
$postedId = isset($input['user_id']) ? (int)$input['user_id'] : 0;

// Prefer session user id when available
$userId = $_SESSION['user_id'] ?? $postedId;

if (!$userId || !$newUsername) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

try {
    $stmt = $pdo->prepare("UPDATE users SET username = ? WHERE id = ?");
    $stmt->execute([$newUsername, $userId]);

    if ($stmt->rowCount() === 0) {
        echo json_encode(['success' => false, 'message' => 'User not found or username unchanged']);
        exit;
    }

    // update session username if set
    if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $userId) {
        $_SESSION['username'] = $newUsername;
    }

    echo json_encode(['success' => true, 'message' => 'Username updated']);
} catch (PDOException $e) {
    if ($e->getCode() == 23000) {
        echo json_encode(['success' => false, 'message' => 'Username sudah dipakai']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
}
