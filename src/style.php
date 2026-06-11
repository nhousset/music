<?php header("Content-type: text/css; charset=UTF-8"); ?>
@import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap');

/* --- SPOTIFY VARIABLES --- */
:root {
    --bg-color: #121212;
    --surface-color: #181818;
    --surface-elevated: #282828;
    --surface-light: #3e3e3e;
    --text-main: #ffffff;
    --text-sub: #b3b3b3;
    --accent-color: #1db954;
    --accent-hover: #1ed760;
    --player-bg: #181818;
    --player-border: #282828;
}

/* --- RESET & BASE --- */
* {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}

body {
    font-family: 'Montserrat', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    background-color: var(--bg-color);
    color: var(--text-main);
    padding-bottom: 90px; /* Vital space for the fixed player */
    -webkit-font-smoothing: antialiased;
    overflow-x: hidden;
}

/* Custom dark scrollbar */
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

/* --- HEADER & NAVIGATION --- */
header {
    background-color: rgba(18, 18, 18, 0.85);
    backdrop-filter: blur(15px);
    -webkit-backdrop-filter: blur(15px);
    padding: 16px 32px;
    position: sticky;
    top: 0;
    z-index: 100;
    display: flex;
    align-items: center;
    border-bottom: 1px solid transparent;
}

.breadcrumb a {
    color: var(--text-sub);
    text-decoration: none;
    font-size: 1rem;
    font-weight: 600;
    transition: color 0.2s ease;
}

.breadcrumb a:hover {
    color: var(--text-main);
}

.breadcrumb span {
    color: var(--text-sub);
    margin: 0 12px;
    font-size: 0.9rem;
}

/* --- MAIN CONTAINER --- */
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
    color: var(--text-main);
}

/* --- FOLDERS GRID --- */
.grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
    gap: 24px;
    margin-bottom: 40px;
}

.card {
    background-color: var(--surface-color);
    border-radius: 8px;
    padding: 16px;
    cursor: pointer;
    text-decoration: none;
    color: var(--text-main);
    display: flex;
    flex-direction: column;
    align-items: center;
    position: relative;
    transition: background-color 0.3s ease, transform 0.2s ease;
}

.card:hover {
    background-color: var(--surface-elevated);
}

.card:active {
    transform: scale(0.98);
}

.icon-folder {
    font-size: 64px;
    margin-bottom: 16px;
    background-color: var(--surface-elevated);
    width: 100%;
    aspect-ratio: 1 / 1;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 6px;
    box-shadow: 0 8px 24px rgba(0,0,0,0.5);
}

.folder-cover {
    width: 100%;
    aspect-ratio: 1 / 1;
    object-fit: cover;
    border-radius: 6px;
    margin-bottom: 16px;
    box-shadow: 0 8px 24px rgba(0,0,0,0.5);
    background-color: var(--surface-elevated);
    transition: transform 0.3s ease;
}

.card:hover .folder-cover {
    transform: translateY(-4px); /* Slight lift effect on hover, Spotify style */
}

.card-title {
    font-weight: 600;
    font-size: 1rem;
    width: 100%;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    text-align: left;
}

/* --- TRACKS LIST --- */
.track-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.track-item {
    display: flex;
    align-items: center;
    padding: 0 16px;
    border-radius: 4px;
    cursor: pointer;
    height: 56px;
    transition: background-color 0.2s ease;
    color: var(--text-sub);
}

.track-item:hover {
    background-color: var(--surface-elevated);
    color: var(--text-main);
}

.track-item.active {
    color: var(--accent-color);
}

.track-icon {
    margin-right: 16px;
    width: 24px;
    text-align: center;
    font-size: 1.1rem;
}

.track-name {
    font-weight: 500;
    font-size: 1rem;
}

/* --- AUDIO PLAYER --- */
#player-bar {
    position: fixed;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 90px;
    background-color: var(--player-bg);
    border-top: 1px solid var(--player-border);
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 24px;
    z-index: 1000;
}

#now-playing {
    width: 30%;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    font-weight: 600;
    font-size: 0.95rem;
    color: var(--text-main);
    display: flex;
    align-items: center;
}

.player-controls {
    width: 40%;
    display: flex;
    justify-content: center;
    align-items: center;
}

.spacer {
    width: 30%;
}

/* Customization for native HTML5 audio element */
#audio-element {
    width: 100%;
    max-width: 600px;
    height: 40px;
    outline: none;
}

/* Invert native player colors to match dark mode */
audio::-webkit-media-controls-enclosure {
    background-color: var(--surface-color);
    border-radius: 20px;
}

audio::-webkit-media-controls-panel {
    background-color: var(--surface-elevated);
}

/* CSS Trick: invert default black icons to make them white */
audio::-webkit-media-controls-current-time-display,
audio::-webkit-media-controls-time-remaining-display {
    color: var(--text-main);
    text-shadow: none;
}

/* Roughly transforms to green/white */
audio::-webkit-media-controls-play-button,
audio::-webkit-media-controls-mute-button {
    filter: invert(1) sepia(1) saturate(5) hue-rotate(90deg);
}

/* --- MOBILE RESPONSIVE --- */
@media (max-width: 768px) {
    header { padding: 12px 16px; }
    .container { padding: 16px; }
    .grid { grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap: 16px; }
    body { padding-bottom: 140px; /* More space as the player stacks */
