<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Web Player</title>
    <link rel="stylesheet" href="style.php">
</head>
<body>

<header>
    <div class="breadcrumb" id="breadcrumb">
        <a href="#" onclick="loadContent('')">🏠 Home</a>
    </div>
</header>

<div class="container" id="main-content">
    </div>

<div id="player-bar">
    <div id="now-playing">No track selected</div>
    <div class="player-controls">
        <audio id="audio-element" controls controlsList="nodownload"></audio>
    </div>
    <div class="spacer"></div>
</div>

<script>
    // Chargement dynamique du contenu sans recharger la page
    function loadContent(dir) {
        fetch('api.php?dir=' + encodeURIComponent(dir))
            .then(response => response.json())
            .then(data => {
                const container = document.getElementById('main-content');
                container.innerHTML = ''; // Nettoyer

                // Affichage des dossiers
                if (data.folders.length > 0) {
                    const grid = document.createElement('div');
                    grid.className = 'grid';
                    data.folders.forEach(folder => {
                        grid.innerHTML += `
                            <a href="#" class="card" onclick="loadContent('${folder.path}')">
                                ${folder.image ? `<img src="${folder.image}" class="folder-cover">` : `<div class="icon-folder">📁</div>`}
                                <div class="card-title">${folder.name}</div>
                            </a>`;
                    });
                    container.appendChild(grid);
                }

                // Affichage des titres
                if (data.mp3s.length > 0) {
                    const list = document.createElement('ul');
                    list.className = 'track-list';
                    data.mp3s.forEach(mp3 => {
                        list.innerHTML += `
                            <li class="track-item" onclick="playTrack('${mp3.url}', '${mp3.name.replace(/'/g, "\\'")}', this)">
                                🎵 ${mp3.name}
                            </li>`;
                    });
                    container.appendChild(list);
                }
            });
    }

    // Lecteur
    const audio = document.getElementById('audio-element');
    function playTrack(url, name, element) {
        audio.src = url;
        audio.play();
        document.getElementById('now-playing').textContent = name;
    }

    // Chargement initial
    loadContent('');
</script>
</body>
</html>
