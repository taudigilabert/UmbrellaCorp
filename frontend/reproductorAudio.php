<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<style>
    :root {
        --color-umbrella-rojo-oscuro: #cf0606;
        --color-umbrella-rojo: #8f0505;
        --color-umbrella-negro: #000000;
        --color-umbrella-gris-oscuro: #666666;
        --color-umbrella-blanco: #ffffff;
    }

    .audio-wrapper {
        position: fixed;
        top: 10px;
        right: 10px;
        z-index: 999;
        text-align: center;
    }

    .audio-toggle-label {
        color: var(--color-umbrella-blanco);
        font-weight: normal;
        cursor: pointer;
        background-color: var(--color-umbrella-negro);
        padding: 5px 10px;
        border: 1px solid var(--color-umbrella-rojo);
    }

    .audio-container {
        background-color: var(--color-umbrella-negro);
        color: var(--color-umbrella-blanco);
        border-radius: 0px;
        width: 220px;
        padding: 10px;
        text-align: center;
        opacity: 0;
        transform: translateY(-10px);
        transition: all 0.3s ease;
        pointer-events: none;
    }

    .audio-wrapper:hover .audio-container {
        opacity: 1;
        transform: translateY(0);
        pointer-events: auto;
    }

    .audio-title {
        font-size: 14px;
        color: var(--color-umbrella-blanco);
        margin-bottom: 8px;
    }

    .audio-controls {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 10px;
    }

    .audio-btn {
        background: none;
        border: none;
        color: var(--color-umbrella-blanco);
        font-size: 20px;
        cursor: pointer;
        padding: 8px;
    }

    .audio-btn:hover {
        color: var(--color-umbrella-rojo-oscuro);
    }

    .progress-container {
        flex-grow: 1;
        height: 8px;
        background-color: var(--color-umbrella-gris-oscuro);
        cursor: pointer;
    }

    .progress-bar {
        height: 100%;
        width: 0%;
        background-color: var(--color-umbrella-rojo-oscuro);
        transition: width 0.25s linear;
    }

    .volume-slider {
        width: 100%;
        margin-top: 5px;
        color: var(--color-umbrella-gris-oscuro);
        border-radius: 0px !important;
    }

    .volume-control input[type="range"] {
        width: 100%;
        margin: 10px 0;
        cursor: pointer;
        border-radius: 0px !important;
        accent-color: var(--color-umbrella-rojo-oscuro);
    }
</style>

<div class="audio-wrapper">
    <div class="audio-toggle-label">Control de audio</div>
    <div class="audio-container">
        <p class="audio-title" id="audio-title">Resident Evil</p>
        <div class="audio-controls">
            <button onclick="togglePlayPause()" id="playPause" class="audio-btn">
                <i class="fas fa-play"></i>
            </button>
            <div class="progress-container" id="progress-bar">
                <div id="progress" class="progress-bar"></div>
            </div>
        </div>
        <div class="volume-control">
            <input type="range" id="volumeSlider" min="0" max="1" step="0.01" value="1">
        </div>
    </div>
</div>

<audio id="backgroundAudio" autoplay loop>
    <source src="Marilyn Manson - Resident Evil Main Title Theme (Corp. Umbrella).mp3" type="audio/mp3" />
</audio>

<script>
    const tracks = [{
        src: "Marilyn Manson - Resident Evil Main Title Theme (Corp. Umbrella).mp3",
        title: "Resident Evil - Tema Principal"
    }];

    const audioPlayer = document.getElementById("backgroundAudio");
    const playPauseButton = document.getElementById("playPause").getElementsByTagName('i')[0];
    const audioTitle = document.getElementById("audio-title");
    const volumeSlider = document.getElementById("volumeSlider");
    const progress = document.getElementById('progress');

    let currentTrackIndex = 0;

    function loadTrack(index) {
        audioPlayer.src = tracks[index].src;
        audioTitle.textContent = tracks[index].title;

        // Restaurar posición si existe
        audioPlayer.addEventListener('loadedmetadata', () => {
            const savedTime = localStorage.getItem("currentTime");

            if (!sessionStorage.getItem('sessionStarted')) {
                // Primera vez en la sesión → iniciar desde 0
                audioPlayer.currentTime = 0;
                sessionStorage.setItem('sessionStarted', 'true');
            } else if (savedTime !== null) {
                // Recarga dentro de la misma sesión → reanudar
                audioPlayer.currentTime = parseFloat(savedTime);
            }
        });


        // Reproducir automáticamente
        audioPlayer.play();
        playPauseButton.classList.remove("fa-play");
        playPauseButton.classList.add("fa-pause");
    }

    function togglePlayPause() {
        if (audioPlayer.paused) {
            audioPlayer.play();
            playPauseButton.classList.remove("fa-play");
            playPauseButton.classList.add("fa-pause");
        } else {
            audioPlayer.pause();
            playPauseButton.classList.remove("fa-pause");
            playPauseButton.classList.add("fa-play");
        }
    }

    // Actualizar barra de progreso
    audioPlayer.ontimeupdate = () => {
        const percentage = (audioPlayer.currentTime / audioPlayer.duration) * 100;
        progress.style.width = percentage + '%';
    };

    // Control de volumen
    volumeSlider.addEventListener('input', () => {
        audioPlayer.volume = volumeSlider.value;
        localStorage.setItem('volume', audioPlayer.volume);
    });

    // Barra de progreso interactiva
    document.getElementById('progress-bar').addEventListener('click', function(e) {
        const rect = this.getBoundingClientRect();
        const offsetX = e.clientX - rect.left;
        const percentage = offsetX / this.offsetWidth;
        audioPlayer.currentTime = percentage * audioPlayer.duration;
    });

    // Guardar tiempo al recargar
    window.addEventListener("beforeunload", () => {
        localStorage.setItem("currentTime", audioPlayer.currentTime);
    });

    // Si se cierra la pestaña o ventana, limpiar currentTime
    document.addEventListener("visibilitychange", () => {
        if (document.visibilityState === "hidden") {
            if (!performance.getEntriesByType("navigation")[0].type.includes("reload")) {
                localStorage.removeItem("currentTime");
            }
        }
    });

    window.onload = () => {
        loadTrack(currentTrackIndex);

        // Restaurar volumen si estaba guardado
        const savedVolume = localStorage.getItem('volume');
        if (savedVolume !== null) {
            audioPlayer.volume = savedVolume;
            volumeSlider.value = savedVolume;
        }
    };
</script>