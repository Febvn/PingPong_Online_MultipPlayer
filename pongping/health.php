<?php
// health.php - simple DB connectivity check. Upload this to your server and open in browser.
header('Content-Type: application/json; charset=utf-8');

require __DIR__ . '/config.php';

try {
    // run a trivial query
    $pdo->query('SELECT 1');
    echo json_encode(['success' => true, 'message' => 'DB OK']);
} catch (Exception $e) {
    http_response_code(500);
    // In prod avoid exposing error details
    echo json_encode(['success' => false, 'message' => 'DB FAILED']);
}
