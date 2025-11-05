<?php
// reset_leaderboard.php - admin-only endpoint to clear scores
session_start();
require_once __DIR__ . '/config.php';

header('Content-Type: application/json; charset=utf-8');

$userId = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;
if ($userId === 0) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

// check admin status
try {
    $stmt = $pdo->prepare("SELECT COLUMN_NAME FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'is_admin'");
    $stmt->execute();
    $hasIsAdmin = (bool)$stmt->fetch();

    $isAdmin = false;
    if ($hasIsAdmin) {
        $g = $pdo->prepare('SELECT is_admin FROM users WHERE id = ?');
        $g->execute([$userId]);
        $r = $g->fetch();
        $isAdmin = ($r && ((int)$r['is_admin'] === 1 || $r['is_admin'] === '1'));
    } else {
        $g = $pdo->prepare('SELECT username FROM users WHERE id = ?');
        $g->execute([$userId]);
        $r = $g->fetch();
        $isAdmin = ($r && strtolower($r['username']) === 'admin');
    }

    if (!$isAdmin) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Forbidden: admin only']);
        exit;
    }

    // perform delete inside transaction
    $pdo->beginTransaction();
    $pdo->exec('DELETE FROM scores');
    $pdo->exec('ALTER TABLE scores AUTO_INCREMENT = 1');
    $pdo->commit();

    echo json_encode(['success' => true, 'message' => 'Leaderboard reset']);
} catch (Exception $e) {
    if ($pdo && $pdo->inTransaction()) $pdo->rollBack();
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
