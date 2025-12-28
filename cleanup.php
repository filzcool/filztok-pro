<?php
// cleanup.php - FilzTok Auto Cleanup v4.0

require_once 'security.php';

$files = glob(CACHE_DIR . '/*.{json,lock,zip,log}', GLOB_BRACE);
$now = time();
$deleted = 0;

foreach ($files as $file) {
    if (is_file($file) && $now - filemtime($file) > 3600) {
        unlink($file);
        $deleted++;
    }
}

// Also cleanup old analytics data (older than 30 days)
$dbFile = CACHE_DIR . '/analytics.db';
if (file_exists($dbFile)) {
    $db = new SQLite3($dbFile);
    $old = time() - (30 * 86400);
    $db->exec("DELETE FROM requests WHERE timestamp < {$old}");
    $db->exec("VACUUM");
}

echo json_encode(['status' => 'success', 'deleted' => $deleted]);
