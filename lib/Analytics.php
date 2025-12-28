<?php
// lib/Analytics.php - FilzTok Analytics v4.0

class Analytics {
    private static $db = null;
    private static $dbPath = null;
    
    public static function init($cacheDir) {
        self::$dbPath = $cacheDir . '/analytics.db';
    }
    
    private static function getDB() {
        if (self::$db === null) {
            self::$db = new SQLite3(self::$dbPath);
            self::$db->exec("CREATE TABLE IF NOT EXISTS requests (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                ip TEXT NOT NULL,
                url TEXT NOT NULL,
                status TEXT NOT NULL,
                timestamp INTEGER NOT NULL,
                user_agent TEXT,
                country TEXT,
                response_time_ms INTEGER
            )");
            
            // Create indexes for performance
            self::$db->exec("CREATE INDEX IF NOT EXISTS idx_timestamp ON requests(timestamp)");
            self::$db->exec("CREATE INDEX IF NOT EXISTS idx_ip ON requests(ip)");
            self::$db->exec("CREATE INDEX IF NOT EXISTS idx_status ON requests(status)");
        }
        return self::$db;
    }
    
    public static function log($url, $status, $responseTime = 0) {
        $db = self::getDB();
        $stmt = $db->prepare("INSERT INTO requests (ip, url, status, timestamp, user_agent, country, response_time_ms) 
                              VALUES (:ip, :url, :status, :timestamp, :ua, :country, :rt)");
        
        // Get country from IP (simple geo lookup)
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $country = 'Unknown';
        if ($ip !== 'unknown' && $ip !== '127.0.0.1') {
            $geo = @json_decode(@file_get_contents("http://ip-api.com/json/{$ip}?fields=country"), true);
            $country = $geo['country'] ?? 'Unknown';
        }
        
        $stmt->bindValue(':ip', $ip);
        $stmt->bindValue(':url', substr($url, 0, 500));
        $stmt->bindValue(':status', $status);
        $stmt->bindValue(':timestamp', time());
        $stmt->bindValue(':ua', $_SERVER['HTTP_USER_AGENT'] ?? 'unknown');
        $stmt->bindValue(':country', $country);
        $stmt->bindValue(':rt', $responseTime);
        $stmt->execute();
    }
    
    public static function getStats($hours = 24) {
        $db = self::getDB();
        $since = time() - ($hours * 3600);
        
        $result = $db->query("SELECT 
            COUNT(*) as total_requests,
            SUM(CASE WHEN status = 'success' THEN 1 ELSE 0 END) as success_count,
            SUM(CASE WHEN status = 'error' THEN 1 ELSE 0 END) as error_count,
            COUNT(DISTINCT ip) as unique_ips,
            AVG(response_time_ms) as avg_response_time,
            MIN(timestamp) as earliest,
            MAX(timestamp) as latest
            FROM requests WHERE timestamp > {$since}");
        
        return $result->fetchArray(SQLITE3_ASSOC) ?? [];
    }
    
    public static function cleanup($days = 30) {
        $db = self::getDB();
        $old = time() - ($days * 86400);
        $db->exec("DELETE FROM requests WHERE timestamp < {$old}");
        $db->exec("VACUUM");
    }
}
