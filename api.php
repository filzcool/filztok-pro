<?php
// api.php - FilzTok Pro API v4.0

session_start();
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: " . ($_SERVER['HTTP_ORIGIN'] ?? "*"));
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, X-CSRF-Token, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once 'security.php';

try {
    // Validate security
    SecurityValidator::validate();
    RateLimiter::check($_SERVER['REMOTE_ADDR']);
    
    // Validate input
    $input = json_decode(file_get_contents('php://input'), true);
    if (empty($input['csrfToken'])) {
        throw new Exception('Invalid CSRF token', 403);
    }
    
    $url = filter_var($input['url'] ?? '', FILTER_SANITIZE_URL);
    if (empty($url) || !filter_var($url, FILTER_VALIDATE_URL)) {
        throw new Exception('Invalid TikTok URL', 400);
    }
    
    // Check cache
    $cacheKey = md5($url);
    $cacheFile = CACHE_DIR . '/' . $cacheKey . '.json';
    
    if (file_exists($cacheFile) && time() - filemtime($cacheFile) < 3600) {
        $data = json_decode(file_get_contents($cacheFile), true);
        echo json_encode(['status' => 'success', 'source' => 'cache', 'data' => $data]);
        exit;
    }
    
    // Process request
    require_once 'lib/TikTokProcessor.php';
    $start = microtime(true);
    $processor = new TikTokProcessor();
    $data = $processor->fetch($url);
    $responseTime = round((microtime(true) - $start) * 1000);
    
    // Cache and log
    file_put_contents($cacheFile, json_encode($data));
    Analytics::log($url, 'success', $responseTime);
    
    echo json_encode(['status' => 'success', 'source' => 'api', 'data' => $data]);
    
} catch (Exception $e) {
    http_response_code($e->getCode() ?: 500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    ErrorLogger::log($e);
}
