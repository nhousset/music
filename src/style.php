<?php
// On indique au navigateur que ce fichier est une feuille de style CSS
header("Content-type: text/css; charset=UTF-8");

// Palette de couleurs Spotify
$bg_color = "#121212";
$surface_color = "#181818";
$surface_elevated = "#242424";
$hover_color = "#282828";
$text_main = "#ffffff";
$text_sub = "#b3b3b3";
$accent_color = "#1db954";
$accent_hover = "#1ed760";
?>
@import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap');

/* --- Reset et base --- */
body {
    font-family: 'Montserrat', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    background-color: <?php echo $bg_color; ?>;
    color: <?php echo $text_main; ?>;
    margin: 0;
    padding: 0;
    padding-bottom: 90px; /* Espace pour le lecteur fixe */
    -webkit-font-smoothing: antialiased;
}

/* Scrollbar façon Spotify */
::-webkit-scrollbar {
    width: 12px;
}
::-webkit-scrollbar-track {
    background: transparent;
}
::-webkit-scrollbar-thumb {
    background-color: rgba(255, 255, 255, 0.3);
}
::-webkit-scrollbar-thumb:hover {
    background-color: rgba(255, 255, 255, 0.5);
}

/* --- En-tête / Navigation --- */
header {
    background-color: rgba(18, 18, 18, 0.8);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    padding: 16px 32px;
    position: sticky;
    top: 0;
    z-index: 100;
    display: flex;
    align-items: center;
}

.breadcrumb a {
    color: <?php echo $text_sub; ?>;
    text-decoration: none;
    font-size: 1.1rem;
    font-weight: 600;
    transition: color 0.2s ease;
}

.breadcrumb a:hover {
    color: <?php echo $text_main; ?>;
}

.breadcrumb span {
    color: <?php echo $text_sub; ?>;
    margin: 0 12px;
    font-size: 0.9rem;
}

/* --- Contenu principal --- */
.container {
    padding: 24px 32px;
    max-width: 1400px;
    margin: 0 auto;
}

h2 {
    font-size: 1.5rem;
    font-weight: 700;
    margin-bottom: 24px;
    letter-spacing: -0.04em;
}

/* --- Grille des dossiers (Cartes) --- */
.grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
    gap: 24px;
    margin-bottom: 40px;
}

.card {
    background-color: <?php echo $surface_color; ?>;
    border-radius: 8px;
    padding: 16px;
    cursor: pointer;
    transition: background-color 0.3s ease;
    text-decoration: none;
    color: <?php echo $text_main; ?>;
    display: flex;
    flex-direction: column;
    align-items: center;
    position: relative;
}

.card:hover {
    background-color: <?php echo $hover_color; ?>;
}

.icon-folder {
    font-size: 64px;
    margin-bottom: 16px;
    background-color: <?php echo $surface_elevated; ?>;
    width: 100%;
    aspect-ratio: 1 / 1;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 4px;
    box-shadow: 0 8px 24px rgba(0,0,0,0.5);
}

.card-title {
    font-weight: 600;
    font-size: 1rem;
    width: 100%;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* --- Liste des MP3 --- */
.track-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.track-item {
    display: flex;
    align-items: center;
    padding: 8px 16px;
    border-radius: 4px;
    cursor: pointer;
    height: 56px;
    transition: background-color 0.2s ease;
}

.track-item:hover {
    background-color: rgba(255, 255, 255, 0.1);
}

.track-item.active {
    color: <?php echo $accent_color; ?>;
}

.track-icon {
    margin-right: 16px;
    color: <?php echo $text_sub; ?>;
    width: 24px;
    text-align: center;
}

.track-item:hover .track-icon, .track-item.active .track-icon {
    color: <?php echo $text_main; ?>;
}

/* --- Lecteur (Barre du bas) --- */
#player-bar {
    position: fixed;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 90px;
    background-color: <?php echo $surface_color; ?>;
    border-top: 1px solid <?php echo $hover_color; ?>;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 16px;
    box-sizing: border-box;
    z-index: 1000;
}

#now-playing {
    width: 30%;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    font-weight: 600;
    font-size: 0.9rem;
    padding-left: 16px;
}

.player-controls {
    width: 40%;
    display: flex;
    justify-content: center;
}

/* Personnalisation basique du lecteur HTML5 natif */
#audio-element {
    width: 100%;
    max-width: 600px;
    outline: none;
}

/* Masquer le bouton de téléchargement sur les navigateurs basés sur Chromium */
audio::-webkit-media-controls-enclosure {
    border-radius: 8px;
    background-color: <?php echo $surface_elevated; ?>;
}
audio::-webkit-media-controls-current-time-display,
audio::-webkit-media-controls-time-remaining-display,
audio::-webkit-media-controls-play-button,
audio::-webkit-media-controls-timeline,
audio::-webkit-media-controls-mute-button,
audio::-webkit-media-controls-volume-slider {
    filter: invert(1); /* Astuce rapide pour passer le lecteur natif en mode sombre */
}
