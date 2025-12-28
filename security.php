<?php
// security.php - FilzTok Security v4.0

define('RATE_LIMIT', 10); // requests per minute
define('RATE_WINDOW', 60); // seconds
define('CACHE_DIR', sys_get_temp_dir() . '/filztok_cache');

// Create cache directory if not exists
if (!is_dir(CACHE_DIR)) {
    mkdir(CACHE_DIR, 0755, true);
}

// Load sub-modules
require_once __DIR__ . '/lib/Analytics.php';
require_once __DIR__ . '/lib/ErrorLogger.php';

// Initialize sub-modules
Analytics::init(CACHE_DIR);
ErrorLogger::init(CACHE_DIR);

class SecurityValidator {
    public static function validate() {
        // Only allow POST requests
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            throw new Exception('Method not allowed', 405);
        }
        
        // Block bots
        $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
        if (empty($ua) || preg_match('/(curl|wget|python|java|scrapy|postman|axios|node-fetch)/i', $ua)) {
            throw new Exception('Bot access detected', 403);
        }
        
        // Validate CSRF token header
        if (empty($_SERVER['HTTP_X_CSRF_TOKEN'])) {
            throw new Exception('CSRF token missing', 403);
        }
    }
}

class RateLimiter {
    public static function check($key) {
        $file = CACHE_DIR . '/rate_' . md5($key);
        $now = time();
        
        $data = file_exists($file) ? json_decode(file_get_contents($file), true) : ['time' => $now, 'count' => 0];
        
        // Reset counter if window passed
        if ($now - $data['time'] > RATE_WINDOW) {
            $data = ['time' => $now, 'count' => 1];
        } else {
            $data['count']++;
            if ($data['count'] > RATE_LIMIT) {
                $wait = RATE_WINDOW - ($now - $data['time']);
                throw new Exception("Rate limit exceeded. Please wait {$wait} seconds", 429);
            }
        }
        
        file_put_contents($file, json_encode($data));
    }
}
