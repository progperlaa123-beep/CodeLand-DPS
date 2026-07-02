let progresoNave = 0;
const meta = 3; // 3 respuestas correctas para ganar

function responderBatalla(esCorrecto, boton) {
    const resultadoDiv = document.getElementById('resultado-juego');
    const nave = document.getElementById('nave-progreso');
    
    // Deshabilitar botones del turno actual
    const contenedorFila = boton.parentElement;
    contenedorFila.querySelectorAll('button').forEach(b => b.disabled = true);

    if (esCorrecto) {
        progresoNave++;
        boton.style.background = "#2ecc71";
        // Mover visualmente la nave en la interfaz
        if (nave) {
            nave.style.marginLeft = (progresoNave * 30) + "%";
        }
    } else {
        boton.style.background = "#e74c3c";
    }

    // Validar fin de la batalla 
    if (progresoNave === meta) {
        resultadoDiv.className = "resultado exito";
        resultadoDiv.innerHTML = "🛸 ¡Victoria en la Batalla del Código! Has superado la velocidad de la luz. +200 XP";
    } else if (progresoNave < meta && contenedorFila.classList.contains('ultimo-turno')) {
        resultadoDiv.className = "resultado error";
        resultadoDiv.innerHTML = "💥 Tu nave se ha quedado sin combustible en combate. ¡Revisa tus bucles e inténtalo de nuevo!";
    }
}