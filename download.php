<?php
// download.php - FilzTok Proxy Downloader v4.0

session_start();
require_once 'security.php';

set_time_limit(300);
ini_set('memory_limit', '512M');

try {
    SecurityValidator::validate();
    RateLimiter::check($_SERVER['REMOTE_ADDR'] . '_dl');
    
    $input = json_decode(file_get_contents('php://input'), true);
    $url = filter_var($input['url'] ?? '', FILTER_SANITIZE_URL);
    $filename = preg_replace('/[^a-z0-9_\-\.]/i', '_', $input['filename'] ?? 'download.mp4');
    
    if (!$url || !filter_var($url, FILTER_VALIDATE_URL)) {
        throw new Exception('Invalid download URL', 400);
    }
    
    // Get file info
    $headers = @get_headers($url, 1);
    $size = $headers['Content-Length'] ?? 0;
    
    if ($size > 500 * 1024 * 1024) {
        throw new Exception('File too large (>500MB)', 413);
    }
    
    // Set download headers
    header('Content-Type: ' . ($headers['Content-Type'] ?? 'application/octet-stream'));
    header('Content-Disposition: attachment; filename="' . basename($filename) . '"');
    header('Content-Length: ' . $size);
    header('Cache-Control: no-cache');
    header('X-Accel-Buffering: no'); // Disable nginx buffering
    
    // Stream file
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => false,
        CURLOPT_HEADER => false,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS => 5,
        CURLOPT_TIMEOUT => 300,
        CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_WRITEFUNCTION => function($ch, $data) {
            echo $data;
            return strlen($data);
        }
    ]);
    
    curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        throw new Exception('Download failed: ' . $error, 500);
    }
    
} catch (Exception $e) {
    http_response_code($e->getCode() ?: 500);
    header('Content-Type: application/json');
    echo json_encode(['error' => $e->getMessage()]);
    ErrorLogger::log($e);
}
