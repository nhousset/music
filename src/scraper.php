<?php
// Exécution en CLI uniquement (sécurité)
if (php_sapi_name() !== 'cli') {
    die("Ce script doit être exécuté en ligne de commande.");
}

$base_dir = __DIR__ . '/music';
$img_dir = __DIR__ . '/img';

echo "Démarrage du scraping des jaquettes...\n";

function scanDirectories($dir, $rel_path = '') {
    global $base_dir, $img_dir;
    
    $items = @scandir($dir) ?: [];
    
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') continue;
        
        $full_path = $dir . '/' . $item;
        $current_rel = $rel_path !== '' ? $rel_path . '/' . $item : $item;

        if (is_dir($full_path)) {
            $hash = md5($current_rel);
            $imgPath = $img_dir . '/' . $hash . '.jpg';

            // Si l'image n'existe pas encore, on la cherche
            if (!file_exists($imgPath)) {
                // Nettoyage du nom pour la recherche
                // Ex: "1987 - Appetite For Destruction" devient "Appetite For Destruction"
                $searchTerm = preg_replace('/^[0-9]{4}\s*-\s*/', '', $item);
                
                // Ex: "METAL - Trash" devient "Trash"
                $searchTerm = preg_replace('/^METAL\s*-\s*/i', '', $searchTerm);

                echo "Recherche pour : " . $searchTerm . "...\n";
                
                $url = "https://itunes.apple.com/search?term=" . urlencode($searchTerm) . "&entity=album&limit=1";
                
                // On utilise un contexte pour éviter les erreurs HTTP si l'API bloque temporairement
                $context = stream_context_create([
                    "http" => ["header" => "User-Agent: PHP-Scraper/1.0\r\n"]
                ]);
                
                $response = @file_get_contents($url, false, $context);
                
                if ($response) {
                    $data = json_decode($response, true);
                    if (!empty($data['results']) && isset($data['results'][0]['artworkUrl100'])) {
                        // L'API renvoie une image 100x100, on modifie l'URL pour avoir du 600x600px
                        $hq_img_url = str_replace('100x100bb', '600x600bb', $data['results'][0]['artworkUrl100']);
                        
                        $image_data = @file_get_contents($hq_img_url);
                        if ($image_data) {
                            file_put_contents($imgPath, $image_data);
                            echo "✅ Trouvé et téléchargé.\n";
                        }
                    } else {
                        echo "❌ Introuvable sur l'API.\n";
                        // On crée un fichier vide pour ne pas refaire la requête à chaque passage du cron
                        touch($imgPath); 
                    }
                }
                
                // Petite pause pour ne pas surcharger l'API d'Apple (évite le ban IP temporaire)
                usleep(500000); // 0.5 seconde
            }
            
            // On descend récursivement dans les sous-dossiers
            scanDirectories($full_path, $current_rel);
        }
    }
}

scanDirectories($base_dir);
echo "Scraping terminé !\n";
