<?php
// Configuration
$base_dir = __DIR__ . '/music';
$req_dir = isset($_GET['dir']) ? (string)$_GET['dir'] : '';

// Sécurité : Empêcher de remonter dans l'arborescence
$req_dir = str_replace(['../', '..\\'], '', $req_dir);
$current_path = realpath($base_dir . '/' . $req_dir);

// Vérification que le chemin demandé est bien dans le dossier de base
if ($current_path === false || strpos($current_path, realpath($base_dir)) !== 0) {
    $current_path = realpath($base_dir);
    $req_dir = '';
}

// Lecture du répertoire
$items = @scandir($current_path) ?: [];
$folders = [];
$mp3s = [];

foreach ($items as $item) {
    if ($item === '.' || $item === '..') continue;
    
    $full_path = $current_path . '/' . $item;
    $rel_path = $req_dir !== '' ? $req_dir . '/' . $item : $item;

    if (is_dir($full_path)) {
        $folders[] = [
            'name' => $item,
            'path' => $rel_path
        ];
    } elseif (is_file($full_path) && strtolower(pathinfo($item, PATHINFO_EXTENSION)) === 'mp3') {
        // Encodage correct de l'URL pour les fichiers avec espaces ou caractères spéciaux (ex: Guns N' Roses)
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
$breadcrumbs = [];
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Web Player</title>
    <style>
        :root {
            --bg-color: #121212;
            --surface-color: #181818;
            --hover-color: #282828;
            --text-main: #ffffff;
            --text-sub: #b3b3b3;
            --accent: #1db954;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--bg-color);
            color: var(--text-main);
            margin: 0;
            padding: 0;
            padding-bottom: 90px; /* Espace pour le lecteur fixe */
        }
        header {
            background-color: var(--surface-color);
            padding: 20px;
            position: sticky;
            top: 0;
            z-index: 100;
            border-bottom: 1px solid #222;
        }
        .breadcrumb a {
            color: var(--text-sub);
            text-decoration: none;
            font-size: 1.2rem;
            font-weight: bold;
        }
        .breadcrumb a:hover {
            color: var(--text-main);
        }
        .breadcrumb span {
            color: var(--text-sub);
            margin: 0 10px;
        }
        .container {
            padding: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }
        .card {
            background-color: var(--surface-color);
            border-radius: 8px;
            padding: 15px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            text-decoration: none;
            color: var(--text-main);
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }
        .card:hover {
            background-color: var(--hover-color);
        }
        .icon-folder {
            font-size: 50px;
            margin-bottom: 10px;
            color: var(--text-sub);
        }
        .track-list {
            list-style: none;
            padding: 0;
        }
        .track-item {
            display: flex;
            align-items: center;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        .track-item:hover {
            background-color: var(--hover-color);
        }
        .track-item.active {
            color: var(--accent);
        }
        .track-icon {
            margin-right: 15px;
            color: var(--text-sub);
        }
        /* Lecteur fixe en bas */
        #player-bar {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 90px;
            background-color: #181818;
            border-top: 1px solid #282828;
            display: flex;
            align-items: center;
            padding: 0 20px;
            box-sizing: border-box;
            z-index: 1000;
        }
        #now-playing {
            width: 30%;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            font-weight: bold;
        }
        #audio-element {
            width: 40%;
            outline: none;
        }
        audio::-webkit-media-controls-panel {
            background-color: var(--text-sub);
        }
    </style>
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
                    <div class="icon-folder">📁</div>
                    <div><?= htmlspecialchars($folder['name']) ?></div>
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
                    <span><?= htmlspecialchars($mp3['name']) ?></span>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
    
    <?php if (empty($folders) && empty($mp3s)): ?>
        <p style="color: var(--text-sub);">Dossier vide.</p>
    <?php endif; ?>
</div>

<div id="player-bar">
    <div id="now-playing">Aucun titre en lecture</div>
    <audio id="audio-element" controls controlsList="nodownload">
        Votre navigateur ne supporte pas la balise audio.
    </audio>
</div>

<script>
    const audio = document.getElementById('audio-element');
    const nowPlaying = document.getElementById('now-playing');
    let currentTrackItem = null;

    function playTrack(url, name, element) {
        // Mettre à jour la source audio
        audio.src = url;
        audio.play();
        
        // Mettre à jour le texte
        nowPlaying.textContent = name;
        
        // Gérer la classe active
        if (currentTrackItem) {
            currentTrackItem.classList.remove('active');
        }
        element.classList.add('active');
        currentTrackItem = element;
    }

    // Optionnel : passer à la piste suivante à la fin
    audio.addEventListener('ended', function() {
        if (currentTrackItem && currentTrackItem.nextElementSibling) {
            currentTrackItem.nextElementSibling.click();
        }
    });
</script>

</body>
</html>
