<?php
// is_admin.php - returns whether current session user is admin
session_start();
require_once __DIR__ . '/config.php';

header('Content-Type: application/json; charset=utf-8');

$userId = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;
$username = isset($_SESSION['username']) ? $_SESSION['username'] : '';

if ($userId === 0) {
    echo json_encode(['is_admin' => false, 'user_id' => 0, 'username' => null]);
    exit;
}

try {
    // check if users table has is_admin column
    $colStmt = $pdo->prepare("SELECT COUNT(*) AS c FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'is_admin'");
    $colStmt->execute();
    $colRes = $colStmt->fetch();
    $hasIsAdmin = ($colRes && (int)$colRes['c'] > 0);

    if ($hasIsAdmin) {
        $stmt = $pdo->prepare('SELECT is_admin, username FROM users WHERE id = ?');
        $stmt->execute([$userId]);
        $row = $stmt->fetch();
        $isAdmin = ($row && ((int)$row['is_admin'] === 1 || $row['is_admin'] === '1'));
        $uname = $row['username'] ?? $username;
    } else {
        // fallback: treat username 'admin' as admin
        $stmt = $pdo->prepare('SELECT username FROM users WHERE id = ?');
        $stmt->execute([$userId]);
        $row = $stmt->fetch();
        $uname = $row['username'] ?? $username;
        $isAdmin = (strtolower($uname) === 'admin');
    }

    echo json_encode(['is_admin' => (bool)$isAdmin, 'user_id' => $userId, 'username' => $uname]);
} catch (Exception $e) {
    echo json_encode(['is_admin' => false, 'user_id' => $userId, 'username' => $username, 'error' => $e->getMessage()]);
}

