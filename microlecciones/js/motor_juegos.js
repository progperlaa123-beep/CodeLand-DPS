let secuenciaUsuario = [];

// 1. Lógica para Quizzes simples
function verificarQuiz(respuestaCorrecta) {
    const opciones = document.querySelectorAll('input[name="quiz-opt"]');
    const resultadoDiv = document.getElementById('resultado-juego');
    let seleccionada = null;

    opciones.forEach(opt => { if (opt.checked) seleccionada = opt.value; });

    if (!seleccionada) {
        resultadoDiv.className = "resultado error";
        resultadoDiv.innerHTML = "⚠️ ¡Astronauta! Selecciona una respuesta antes de enviar.";
        return;
    }

    if (seleccionada === respuestaCorrecta) {
        resultadoDiv.className = "resultado exito";
        resultadoDiv.innerHTML = "🌟 ¡Correcto! Has ganado 50 XP y dominas este concepto orbital.";
    } else {
        resultadoDiv.className = "resultado error";
        resultadoDiv.innerHTML = "👾 Señal perdida. La respuesta no es correcta. ¡Analiza de nuevo!";
    }
}

// 2. Lógica para Code Pair
function verificarCodePair(parejasCorrectas) {
    let aciertos = 0;
    const selectores = document.querySelectorAll('.code-pair-select');
    
    selectores.forEach(select => {
        const item = select.getAttribute('data-item');
        if (select.value === parejasCorrectas[item]) aciertos++;
    });

    const resultadoDiv = document.getElementById('resultado-juego');
    if (aciertos === Object.keys(parejasCorrectas).length) {
        resultadoDiv.className = "resultado exito";
        resultadoDiv.innerHTML = "🚀 ¡Excelente sincronización! Enlaces de datos establecidos. +100 XP";
    } else {
        resultadoDiv.className = "resultado error";
        resultadoDiv.innerHTML = `📡 Interferencia detectada. Tienes ${aciertos} de ${Object.keys(parejasCorrectas).length} emparejamientos correctos.`;
    }
}

// 3. Lógica para CodEF (Diagramas de Flujo)
function seleccionarBloqueEF(idBloque) {
    const boton = document.getElementById(idBloque);
    if (secuenciaUsuario.includes(idBloque)) {
        secuenciaUsuario = secuenciaUsuario.filter(item => item !== idBloque);
        boton.style.background = "rgba(255, 255, 255, 0.05)";
    } else {
        secuenciaUsuario.push(idBloque);
        boton.style.background = "#9b51e0";
    }
    document.getElementById('orden-actual').innerText = "Ruta trazada: " + secuenciaUsuario.join(" ➔ ");
}

function verificarCodEF(secuenciaCorrecta) {
    const resultadoDiv = document.getElementById('resultado-juego');
    if (JSON.stringify(secuenciaUsuario) === JSON.stringify(secuenciaCorrecta)) {
        resultadoDiv.className = "resultado exito";
        resultadoDiv.innerHTML = "🗺️ ¡Ruta calculada con éxito! El mapa de flujo es correcto. +120 XP";
    } else {
        resultadoDiv.className = "resultado error";
        resultadoDiv.innerHTML = "❌ ¡Alerta de colisión! El flujo lógico es incorrecto. Limpia y redefine.";
    }
}

function reiniciarCodEF() {
    secuenciaUsuario = [];
    document.getElementById('orden-actual').innerText = "Ruta trazada: Ninguna";
    document.querySelectorAll('.ef-block').forEach(b => b.style.background = "rgba(255, 255, 255, 0.05)");
}

// 4. Lógica para Debug Pro / Consolas
function verificarDebugPro(solucion) {
    const entrada = document.getElementById('codigo-usuario').value.trim();
    const resultadoDiv = document.getElementById('resultado-juego');

    if (entrada.replace(/\s/g, "") === solucion.replace(/\s/g, "")) {
        resultadoDiv.className = "resultado exito";
        resultadoDiv.innerHTML = "🔧 ¡Bug eliminado! Sistema restaurado con éxito. +150 XP";
    } else {
        resultadoDiv.className = "resultado error";
        resultadoDiv.innerHTML = "❌ Error de sintaxis persistente. Sigue intentándolo.";
    }
}