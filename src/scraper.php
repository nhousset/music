<?php
// Exécution en CLI uniquement
if (php_sapi_name() !== 'cli') {
    die("Ce script doit être exécuté en ligne de commande.\n");
}

// Configuration des chemins selon tes instructions
$base_dir = '/MP3/MP3/'; // Chemin absolu sur l'hôte
$img_dir = __DIR__ . '/img'; // Chemin vers le sous-dossier img/ du projet web

if (!is_dir($img_dir)) {
    die("Erreur : Le dossier image '$img_dir' n'existe pas. Créez-le d'abord.\n");
}

echo "Démarrage du scraping des jaquettes...\n";

function scanDirectories($dir, $rel_path = '') {
    global $base_dir, $img_dir;
    
    $items = @scandir($dir) ?: [];
    
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') continue;
        
        $full_path = $dir . '/' . $item;
        // On construit le chemin relatif exact tel que le voit index.php
        $current_rel = $rel_path !== '' ? $rel_path . '/' . $item : $item;

        if (is_dir($full_path)) {
            // Le MD5 doit correspondre exactement au chemin relatif lu par index.php
            $hash = md5($current_rel);
            $imgPath = $img_dir . '/' . $hash . '.jpg';

            if (!file_exists($imgPath)) {
                // Nettoyage pour l'API iTunes
                // Retire les années "1987 - "
                $searchTerm = preg_replace('/^[0-9]{4}\s*-\s*/', '', $item);
                // Retire les tags "METAL - "
                $searchTerm = preg_replace('/^METAL\s*-\s*/i', '', $searchTerm);
                // Retire les mots superflus pour maximiser les chances (ex: CD 1, Live...)
                $searchTerm = preg_replace('/\(CD \d+\)/i', '', $searchTerm);
                
                $searchTerm = trim($searchTerm);

                echo "Recherche pour : " . $searchTerm . "...\n";
                
                $url = "https://itunes.apple.com/search?term=" . urlencode($searchTerm) . "&entity=album&limit=1";
                
                $context = stream_context_create([
                    "http" => ["header" => "User-Agent: PHP-Scraper/1.0\r\n"]
                ]);
                
                $response = @file_get_contents($url, false, $context);
                
                if ($response) {
                    $data = json_decode($response, true);
                    if (!empty($data['results']) && isset($data['results'][0]['artworkUrl100'])) {
                        // Récupération de l'image en 600x600 au lieu de 100x100
                        $hq_img_url = str_replace('100x100bb', '600x600bb', $data['results'][0]['artworkUrl100']);
                        
                        $image_data = @file_get_contents($hq_img_url);
                        if ($image_data) {
                            file_put_contents($imgPath, $image_data);
                            echo "✅ Trouvé et téléchargé.\n";
                        }
                    } else {
                        echo "❌ Introuvable sur l'API.\n";
                        // On crée un fichier vide pour marquer l'échec et ne pas retenter inutilement la prochaine fois
                        touch($imgPath); 
                    }
                } else {
                    echo "⚠️ Erreur de connexion à l'API.\n";
                }
                
                // Pause pour éviter de se faire bloquer par Apple
                usleep(500000); 
            }
            
            // Récursivité pour les sous-dossiers
            scanDirectories($full_path, $current_rel);
        }
    }
}

// On s'assure que le chemin de base n'a pas de slash à la fin pour éviter les doubles slashes dans la concaténation
$clean_base_dir = rtrim($base_dir, '/');
scanDirectories($clean_base_dir);

echo "Scraping terminé !\n";
