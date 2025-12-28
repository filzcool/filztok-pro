<?php
// lib/TikTokProcessor.php - FilzTok TikWM API v4.0

class TikTokProcessor {
    private $apiUrl = 'https://www.tikwm.com/api/';
    private $timeout = 30;
    
    public function fetch($url) {
        $url = $this->unshorten($url);
        $apiResponse = $this->curl($this->apiUrl . '?url=' . urlencode($url));
        $data = json_decode($apiResponse, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Invalid JSON response from API', 500);
        }
        
        if (empty($data['data'])) {
            throw new Exception('No data returned from TikWM. Link may be private or deleted.', 404);
        }
        
        return $this->normalize($data['data']);
    }
    
    private function unshorten($url) {
        if (!preg_match('/(vt|vm)\.tiktok\.com/', $url)) {
            return $url;
        }
        
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => true,
            CURLOPT_NOBODY => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
            CURLOPT_SSL_VERIFYPEER => false
        ]);
        
        curl_exec($ch);
        $realUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
        curl_close($ch);
        
        return $realUrl ?: $url;
    }
    
    private function curl($url) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => $this->timeout,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 5,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36',
            CURLOPT_HTTPHEADER => [
                'Accept: application/json',
                'Referer: https://www.tiktok.com/'
            ]
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($error) {
            throw new Exception('cURL Error: ' . $error, 500);
        }
        
        if ($httpCode !== 200) {
            throw new Exception('API returned HTTP ' . $httpCode, $httpCode);
        }
        
        return $response;
    }
    
    private function normalize($d) {
        // Extract video URL with priority: HD > SD > WM
        $video = $this->getFirst([$d['hdplay'] ?? '', $d['play'] ?? '', $d['wmplay'] ?? '']);
        
        // Clean all URLs
        $video = $this->cleanUrl($video);
        $images = array_map([$this, 'cleanUrl'], $d['images'] ?? []);
        
        // Extract hashtags from caption
        preg_match_all('/#(\w+)/', $d['title'] ?? '', $hashtags);
        
        return [
            'username' => $d['author']['unique_id'] ?? 'unknown',
            'avatar' => $this->cleanUrl($d['author']['avatar'] ?? ''),
            'title' => $d['title'] ?? '',
            'hashtags' => $hashtags[1] ?? [],
            'cover' => $this->cleanUrl($d['cover'] ?? ''),
            'video' => $video,
            'video_hd' => $this->cleanUrl($d['hdplay'] ?? ''),
            'video_sd' => $this->cleanUrl($d['play'] ?? ''),
            'video_wm' => $this->cleanUrl($d['wmplay'] ?? ''),
            'music' => $this->cleanUrl($d['music'] ?? ''),
            'music_title' => $d['music_info']['title'] ?? '',
            'images' => $images,
            'play_count' => $d['play_count'] ?? 0,
            'digg_count' => $d['digg_count'] ?? 0,
            'share_count' => $d['share_count'] ?? 0
        ];
    }
    
    private function getFirst($arr) {
        foreach ($arr as $item) {
            if (!empty($item)) return $item;
        }
        return '';
    }
    
    private function cleanUrl($url) {
        return $url ? strtok(trim($url), '?') : '';
    }
}
