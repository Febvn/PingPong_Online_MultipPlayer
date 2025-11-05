<?php
// view_score.php
require_once __DIR__ . '/config.php';

$user_id = 0;
if (!empty($_GET['user_id'])) {
    $user_id = (int)$_GET['user_id'];
} else {
    $input = json_decode(file_get_contents('php://input'), true);
    if (!empty($input['user_id'])) $user_id = (int)$input['user_id'];
}

if (!$user_id) {
    echo json_encode(['success' => false, 'message' => 'user_id tidak diberikan']);
    exit;
}

try {
    $stmt = $pdo->prepare(
        "SELECT s.id, s.player_score, s.ai_score, s.difficulty, s.winner, s.created_at
         FROM scores s
         WHERE s.user_id = ?
         ORDER BY s.created_at DESC"
    );
    $stmt->execute([$user_id]);
    $rows = $stmt->fetchAll();
    echo json_encode($rows);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
