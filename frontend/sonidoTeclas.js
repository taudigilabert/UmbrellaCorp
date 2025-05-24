// Definir sonidos
const sonidoBorrar = new Audio("../sounds/sonidoTeclaIn.mp3");
const sonidoEscribir = new Audio("../sounds/sonidoTeclaOut.mp3");

function playEscribir() {
    sonidoEscribir.currentTime = 0;
    sonidoEscribir.play();
}

function playBorrar() {
    sonidoBorrar.currentTime = 0;
    sonidoBorrar.play();
}

const inputsYTextareas = document.querySelectorAll("input, textarea");

inputsYTextareas.forEach((input) => {
    input.addEventListener("input", function (event) {
        if (event.inputType === "deleteContentBackward") {
            playBorrar();
        } else {
            playEscribir();
        }
    });

    input.addEventListener("keydown", function () {
        playEscribir();
    });
});