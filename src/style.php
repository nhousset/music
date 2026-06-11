<?php header("Content-type: text/css; charset=UTF-8"); ?>
@import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap');

/* --- VARIABLES SPOTIFY --- */
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
    padding-bottom: 90px; /* Espace vital pour le lecteur fixe */
    -webkit-font-smoothing: antialiased;
    overflow-x: hidden;
}

/* Scrollbar personnalisée sombre */
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
    transition: background-color 0.3s;
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

/* --- CONTENEUR PRINCIPAL --- */
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

/* --- GRILLE DES
