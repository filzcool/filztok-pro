<?php
// batch.php - FilzTok Batch ZIP v4.0

session_start();
require_once 'security.php';

set_time_limit(600);
ini_set('memory_limit', '512M');

try {
    SecurityValidator::validate();
    RateLimiter::check($_SERVER['REMOTE_ADDR'] . '_zip');
    
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (empty($input['type'])) {
        throw new Exception('Invalid request type', 400);
    }
    
    $zip = new ZipArchive();
    $zipName = sys_get_temp_dir() . '/batch_' . uniqid() . '.zip';
    
    if ($zip->open($zipName, ZipArchive::CREATE) !== TRUE) {
        throw new Exception('Cannot create ZIP file', 500);
    }
    
    if ($input['type'] === 'slides' && !empty($input['urls'])) {
        foreach ($input['urls'] as $i => $url) {
            $content = @file_get_contents($url);
            if ($content) {
                $zip->addFromString('slide_' . ($i + 1) . '.jpg', $content);
            }
        }
    } elseif ($input['type'] === 'mixed' && !empty($input['media'])) {
        foreach ($input['media'] as $item) {
            $content = @file_get_contents($item['url']);
            if ($content) {
                $zip->addFromString($item['name'], $content);
            }
        }
    } else {
        throw new Exception('Invalid batch data', 400);
    }
    
    $zip->close();
    
    // Send ZIP
    header('Content-Type: application/zip');
    header('Content-Disposition: attachment; filename="filztok_batch_' . date('Y-m-d_H-i-s') . '.zip"');
    header('Content-Length: ' . filesize($zipName));
    header('Cache-Control: no-cache');
    
    readfile($zipName);
    unlink($zipName);
    
} catch (Exception $e) {
    http_response_code($e->getCode() ?: 500);
    header('Content-Type: application/json');
    echo json_encode(['error' => $e->getMessage()]);
    ErrorLogger::log($e);
}
