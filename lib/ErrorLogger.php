<?php
// lib/ErrorLogger.php - FilzTok Error Logger v4.0

class ErrorLogger {
    private static $logFile = null;
    private static $maxLogSize = 10 * 1024 * 1024; // 10MB
    private static $maxLines = 2000;
    
    public static function init($cacheDir) {
        // Monthly rotated log file
        self::$logFile = $cacheDir . '/errors_' . date('Y-m') . '.log';
    }
    
    public static function log($exceptionOrMessage, $context = '') {
        if (!self::$logFile) return;
        
        $timestamp = date('Y-m-d H:i:s');
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $url = $_SERVER['REQUEST_URI'] ?? 'unknown';
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $ua = substr($_SERVER['HTTP_USER_AGENT'] ?? 'unknown', 0, 150);
        
        if ($exceptionOrMessage instanceof Exception || $exceptionOrMessage instanceof Throwable) {
            $message = sprintf(
                "[%s] [%s %s] [IP: %s] [UA: %s] %s: %s in %s:%d\nStack Trace:\n%s\n\n",
                $timestamp,
                $method,
                $url,
                $ip,
                $ua,
                get_class($exceptionOrMessage),
                $exceptionOrMessage->getMessage(),
                $exceptionOrMessage->getFile(),
                $exceptionOrMessage->getLine(),
                $exceptionOrMessage->getTraceAsString()
            );
        } else {
            $message = sprintf(
                "[%s] [%s %s] [IP: %s] [Context: %s] %s\n\n",
                $timestamp,
                $method,
                $url,
                $ip,
                $context,
                $exceptionOrMessage
            );
        }
        
        // Append to log
        file_put_contents(self::$logFile, $message, FILE_APPEND);
        
        // Rotate if too large
        self::rotateIfNeeded();
    }
    
    private static function rotateIfNeeded() {
        if (!file_exists(self::$logFile)) return;
        
        if (filesize(self::$logFile) > self::$maxLogSize) {
            $lines = file(self::$logFile);
            if (count($lines) > self::$maxLines) {
                $lines = array_slice($lines, -self::$maxLines);
                file_put_contents(self::$logFile, implode('', $lines));
            }
        }
    }
    
    public static function getLogs($limit = 100) {
        if (!file_exists(self::$logFile)) return [];
        
        $lines = file(self::$logFile);
        $logs = [];
        $currentLog = '';
        
        foreach (array_reverse($lines) as $line) {
            if (strpos($line, '[20') === 0 && $currentLog) {
                $logs[] = $currentLog;
                if (count($logs) >= $limit) break;
                $currentLog = $line;
            } else {
                $currentLog .= $line;
            }
        }
        
        if ($currentLog) $logs[] = $currentLog;
        return array_reverse($logs);
    }
    
    public static function clearLogs() {
        if (file_exists(self::$logFile)) {
            file_put_contents(self::$logFile, '');
        }
    }
}
