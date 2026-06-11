<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Web Player</title>
    <link rel="stylesheet" href="style.php">
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
                    <span><?= htmlspecialchars($mp3['name']) ?></span>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
    
    <?php if (empty($folders) && empty($mp3s)): ?>
        <p style="color: #b3b3b3;">Dossier vide.</p>
    <?php endif; ?>
</div>

<div id="player-bar">
    <div id="now-playing">Aucun titre en lecture</div>
    <div class="player-controls">
        <audio id="audio-element" controls controlsList="nodownload">
            Votre navigateur ne supporte pas la balise audio.
        </audio>
    </div>
    <div style="width: 30%;"></div> </div>

<script>
    const audio = document.getElementById('audio-element');
    const nowPlaying = document.getElementById('now-playing');
    let currentTrackItem = null;

    function playTrack(url, name, element) {
        audio.src = url;
        audio.play();
        nowPlaying.textContent = name.replace('.mp3', ''); // Supprime l'extension mp3 pour l'affichage
        
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
