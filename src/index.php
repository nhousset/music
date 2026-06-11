<?php
// Configuration : ici on reste sur le montage web pour que Apache puisse servir les fichiers
$base_dir = __DIR__ . '/music';
$req_dir = isset($_GET['dir']) ? (string)$_GET['dir'] : '';

// Sécurité : Empêcher de remonter dans l'arborescence (Path Traversal)
$req_dir = str_replace(['../', '..\\'], '', $req_dir);
$current_path = realpath($base_dir . '/' . $req_dir);

// Vérification que le chemin demandé est bien dans le dossier de base
if ($current_path === false || strpos($current_path, realpath($base_dir)) !== 0) {
    $current_path = realpath($base_dir);
    $req_dir = '';
}

// Initialisation des variables
$folders = [];
$mp3s = [];
$breadcrumbs = [];

// Lecture du répertoire
$items = @scandir($current_path) ?: [];

foreach ($items as $item) {
    if ($item === '.' || $item === '..') continue;
    
    $full_path = $current_path . '/' . $item;
    $rel_path = $req_dir !== '' ? $req_dir . '/' . $item : $item;

    if (is_dir($full_path)) {
        // Recherche de l'image liée au dossier (générée par le scraper)
        $hash = md5($rel_path);
        $img_path = 'img/' . $hash . '.jpg';
        
        // Vérification de l'existence et si le fichier n'est pas vide
        $has_image = file_exists(__DIR__ . '/' . $img_path) && filesize(__DIR__ . '/' . $img_path) > 0;

        $folders[] = [
            'name' => $item,
            'path' => $rel_path,
            'image' => $has_image ? $img_path : null
        ];
    } elseif (is_file($full_path) && strtolower(pathinfo($item, PATHINFO_EXTENSION)) === 'mp3') {
        $parts = explode('/', $rel_path);
        $encoded_parts = array_map('rawurlencode', $parts);
        
        $mp3s[] = [
            'name' => $item,
            'url' => 'music/' . implode('/', $encoded_parts)
        ];
    }
}

// Tri alphabétique
usort($folders, fn($a, $b) => strcasecmp($a['name'], $b['name']));
usort($mp3s, fn($a, $b) => strcasecmp($a['name'], $b['name']));

// Fil d'Ariane
$path_parts = explode('/', $req_dir);
$build_path = '';
foreach ($path_parts as $part) {
    if (empty($part)) continue;
    $build_path .= ($build_path === '' ? '' : '/') . $part;
    $breadcrumbs[] = ['name' => $part, 'path' => $build_path];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Web Player</title>
    
    <meta name="theme-color" content="#121212">
    <link rel="manifest" href="manifest.json">
    <link rel="apple-touch-icon" href="icon-192.png">
    
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <div class="breadcrumb">
        <a href="?dir=">🏠 Home</a>
        <?php foreach ($breadcrumbs as $crumb): ?>
            <span>/</span>
            <a href="?dir=<?= urlencode($crumb['path']) ?>"><?= htmlspecialchars($crumb['name']) ?></a>
        <?php endforeach; ?>
    </div>
</header>

<div class="container">
    <?php if (!empty($folders)): ?>
        <h2>Dossiers</h2>
        <div class="grid">
            <?php foreach ($folders as $folder): ?>
                <a href="?dir=<?= urlencode($folder['path']) ?>" class="card">
                    <?php if ($folder['image']): ?>
                        <img src="<?= htmlspecialchars($folder['image']) ?>" alt="cover" class="folder-cover">
                    <?php else: ?>
                        <div class="icon-folder">📁</div>
                    <?php endif; ?>
                    <div class="card-title"><?= htmlspecialchars($folder['name']) ?></div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($mp3s)): ?>
        <h2>Titres</h2>
        <ul class="track-list">
            <?php foreach ($mp3s as $index => $mp3): ?>
                <li class="track-item" onclick="playTrack('<?= htmlspecialchars($mp3['url'], ENT_QUOTES) ?>', '<?= htmlspecialchars($mp3['name'], ENT_QUOTES) ?>', this)">
                    <span class="track-icon">🎵</span>
                    <span class="track-name"><?= htmlspecialchars($mp3['name']) ?></span>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
    
    <?php if (empty($folders) && empty($mp3s)): ?>
        <p style="color: var(--text-sub);">Dossier vide.</p>
    <?php endif; ?>
</div>

<div id="player-bar">
    <div id="now-playing">Aucun titre</div>
    <div class="player-controls">
        <audio id="audio-element" controls controlsList="nodownload">
            Votre navigateur ne supporte pas la balise audio.
        </audio>
    </div>
    <div class="spacer"></div>
</div>

<script>
    // Enregistrement PWA
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('sw.js').catch(err => {
                console.log('ServiceWorker registration failed: ', err);
            });
        });
    }

    // Lecteur Audio
    const audio = document.getElementById('audio-element');
    const nowPlaying = document.getElementById('now-playing');
    let currentTrackItem = null;

    function playTrack(url, name, element) {
        audio.src = url;
        audio.play();
        nowPlaying.textContent = name.replace('.mp3', '');
        
        if (currentTrackItem) {
            currentTrackItem.classList.remove('active');
        }
        element.classList.add('active');
        currentTrackItem = element;
    }

    audio.addEventListener('ended', function() {
        if (currentTrackItem && currentTrackItem.nextElementSibling) {
            currentTrackItem.nextElementSibling.click();
        }
    });
</script>

</body>
</html>
