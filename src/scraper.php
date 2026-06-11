<?php
// Run in CLI mode only (Command Line Interface)
if (php_sapi_name() !== 'cli') {
    die("This script must be run from the command line.\n");
}

// Absolute paths configuration
$base_dir = '/MP3/MP3/'; // Absolute path on the host
$img_dir = __DIR__ . '/img'; // Path to the img/ subdirectory

// Check if the image directory exists
if (!is_dir($img_dir)) {
    die("Error: The image directory '$img_dir' does not exist. Please create it first.\n");
}

echo "Starting cover scraping...\n";

function scanDirectories($dir, $rel_path = '') {
    global $base_dir, $img_dir;
    
    $items = @scandir($dir) ?: [];
    
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') continue;
        
        $full_path = $dir . '/' . $item;
        // Build the exact relative path as seen by index.php
        $current_rel = $rel_path !== '' ? $rel_path . '/' . $item : $item;

        if (is_dir($full_path)) {
            // The MD5 hash must exactly match the relative path read by index.php
            $hash = md5($current_rel);
            $imgPath = $img_dir . '/' . $hash . '.jpg';

            if (!file_exists($imgPath)) {
                // Clean up folder name for iTunes API search
                // Remove years like "1987 - "
                $searchTerm = preg_replace('/^[0-9]{4}\s*-\s*/', '', $item);
                // Remove tags like "METAL - "
                $searchTerm = preg_replace('/^METAL\s*-\s*/i', '', $searchTerm);
                // Remove superfluous words to maximize matches (e.g., CD 1, Live...)
                $searchTerm = preg_replace('/\(CD \d+\)/i', '', $searchTerm);
                
                $searchTerm = trim($searchTerm);

                echo "Searching for: " . $searchTerm . "...\n";
                
                $url = "https://itunes.apple.com/search?term=" . urlencode($searchTerm) . "&entity=album&limit=1";
                
                $context = stream_context_create([
                    "http" => ["header" => "User-Agent: PHP-Scraper/1.0\r\n"]
                ]);
                
                $response = @file_get_contents($url, false, $context);
                
                if ($response) {
                    $data = json_decode($response, true);
                    if (!empty($data['results']) && isset($data['results'][0]['artworkUrl100'])) {
                        // Get 600x600 image instead of 100x100
                        $hq_img_url = str_replace('100x100bb', '600x600bb', $data['results'][0]['artworkUrl100']);
                        
                        $image_data = @file_get_contents($hq_img_url);
                        if ($image_data) {
                            file_put_contents($imgPath, $image_data);
                            echo "[OK] Found and downloaded.\n";
                        }
                    } else {
                        echo "[X] Not found on API.\n";
                        // Create an empty file to mark failure and avoid useless retries next time
                        touch($imgPath); 
                    }
                } else {
                    echo "[!] API connection error.\n";
                }
                
                // Pause to avoid being blocked by Apple (0.5 seconds)
                usleep(500000); 
            }
            
            // Recursive call for subdirectories
            scanDirectories($full_path, $current_rel);
        }
    }
}

// Ensure the base path doesn't have a trailing slash to prevent double slashes
$clean_base_dir = rtrim($base_dir, '/');
scanDirectories($clean_base_dir);

echo "Scraping completed!\n";
