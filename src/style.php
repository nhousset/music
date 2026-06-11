/* --- RESPONSIVE MOBILE --- */
.spacer {
    width: 30%;
}
.track-name {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    display: block;
    width: calc(100% - 40px);
}

@media (max-width: 768px) {
    header {
        padding: 12px 16px;
    }
    .container {
        padding: 16px;
    }
    .grid {
        grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
        gap: 16px;
    }
    body {
        padding-bottom: 120px; /* Plus d'espace pour le lecteur mobile */
    }
    #player-bar {
        flex-direction: column;
        height: auto;
        padding: 12px 16px;
        gap: 10px;
    }
    #now-playing {
        width: 100%;
        text-align: center;
        padding: 0;
    }
    .player-controls {
        width: 100%;
    }
    #audio-element {
        width: 100%;
    }
    .spacer {
        display: none;
    }
}
