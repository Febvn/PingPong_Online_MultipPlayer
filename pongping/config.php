<?php
// config.php
// Put this in C:\xampp\htdocs\if0_39980568_pingpong_pro\config.php

// CORS & Content-Type (dev)
// Allow requests from the frontend origin. If you're using cookies/sessions
// across origins, the frontend must send `credentials: 'include'` and
// Access-Control-Allow-Credentials must be true. When using credentials,
// Access-Control-Allow-Origin cannot be '*', so we echo back the Origin.
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if ($origin) {
    // Optionally restrict to a whitelist, e.g. if ($origin === 'https://pongpro.gamer.gd') { ... }
    header("Access-Control-Allow-Origin: $origin");
    header('Access-Control-Allow-Credentials: true');
} else {
    header('Access-Control-Allow-Origin: *');
}

// Handle preflight (OPTIONS) requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
    header('Access-Control-Max-Age: 86400');
    // no body for OPTIONS
    exit(0);
}

header("Content-Type: application/json; charset=utf-8");

$DB_HOST = 'sql207.infinityfree.com';
$DB_NAME = 'if0_39980568_pingpong_pro';
$DB_USER = 'if0_39980568';
$DB_PASS = '80ar0BRAyJRu3'; // ubah jika punya password

$dsn = "mysql:host={$DB_HOST};dbname={$DB_NAME};charset=utf8mb4";

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $DB_USER, $DB_PASS, $options);
} catch (Exception $e) {
    http_response_code(500);
    // Do not leak internal errors in production; keep minimal message
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    error_log('DB connection failed: ' . $e->getMessage());
    exit;
}
