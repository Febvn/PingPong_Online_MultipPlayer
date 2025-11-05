<?php
// leaderboard.php
require_once __DIR__ . '/config.php';

try {
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
    if ($limit <= 0) $limit = 10;

    // Order by player_score desc, then by (player_score - ai_score) desc for tiebreaker, then earliest date
    $sql = "SELECT u.username, s.player_score, s.ai_score, s.difficulty, s.winner, s.created_at,
                   (s.player_score - s.ai_score) AS score_diff
            FROM scores s
            JOIN users u ON s.user_id = u.id
            ORDER BY s.player_score DESC, score_diff DESC, s.created_at ASC
            LIMIT :lim";

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
    $stmt->execute();
    $rows = $stmt->fetchAll();
    echo json_encode($rows);
} catch (Exception $e) {
    // Log the error to server error log for diagnosis
    error_log('[leaderboard.php] Exception: ' . $e->getMessage());
    error_log('[leaderboard.php] Trace: ' . $e->getTraceAsString());

    // Return 500 status code for clients
    http_response_code(500);

    // If developer requests debug info via ?debug=1, include the detailed message (temporary)
    $debug = isset($_GET['debug']) && $_GET['debug'] == '1';
    if ($debug) {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage(), 'trace' => $e->getTraceAsString()]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Internal Server Error']);
    }
}
