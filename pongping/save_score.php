<?php
header("Content-Type: application/json; charset=utf-8");
require_once __DIR__ . '/config.php';

$data = json_decode(file_get_contents('php://input'), true);
if (!$data) {
    echo json_encode(['success' => false, 'message' => 'No data received']);
    exit;
}

// prefer user_id from client (after login/signup), fallback to username lookup
$userId = isset($data['user_id']) ? (int)$data['user_id'] : 0;
$username = trim($data['playerName'] ?? '');
$playerScore = isset($data['playerScore']) ? (int)$data['playerScore'] : null;
$aiScore     = isset($data['aiScore']) ? (int)$data['aiScore'] : null;
$difficulty  = trim($data['difficulty'] ?? 'medium');
$winner      = trim($data['winner'] ?? 'Draw');

if (($userId === 0 && $username === '') || $playerScore === null || $aiScore === null) {
    echo json_encode(['success' => false, 'message' => 'Missing fields']);
    exit;
}

try {
    // resolve user id if not provided
    if ($userId === 0) {
        $stmt = $pdo->prepare('SELECT id FROM users WHERE username = ?');
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        if (!$user) {
            echo json_encode(['success' => false, 'message' => 'User not found']);
            exit;
        }
        $userId = (int)$user['id'];
    }

    // insert score
    $ins = $pdo->prepare('INSERT INTO scores (user_id, player_score, ai_score, difficulty, winner, created_at) VALUES (?, ?, ?, ?, ?, NOW())');
    $ins->execute([$userId, $playerScore, $aiScore, $difficulty, $winner]);
    $savedId = (int)$pdo->lastInsertId();

    echo json_encode(['success' => true, 'message' => 'Score saved', 'score_id' => $savedId]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
