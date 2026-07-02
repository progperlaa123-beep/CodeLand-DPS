let secuenciaUsuario = [];

// Seleccionar un bloque del diagrama
function seleccionarBloqueEF(idBloque) {
    const boton = document.getElementById(idBloque);
    
    // Si ya está seleccionado, lo quitamos
    if (secuenciaUsuario.includes(idBloque)) {
        secuenciaUsuario = secuenciaUsuario.filter(item => item !== idBloque);
        boton.style.background = "rgba(255, 255, 255, 0.05)";
    } else {
        secuenciaUsuario.push(idBloque);
        boton.style.background = "#9b51e0"; // Color de activo
    }
    
    // Mostrar orden actual en pantalla
    document.getElementById('orden-actual').innerText = "Tu diagrama: " + secuenciaUsuario.join(" ➔ ");
}

// Verificar la secuencia del diagrama de flujo 
function verificarCodEF(secuenciaCorrecta) {
    const resultadoDiv = document.getElementById('resultado-juego');
    
    if (JSON.stringify(secuenciaUsuario) === JSON.stringify(secuenciaCorrecta)) {
        resultadoDiv.className = "resultado exito";
        resultadoDiv.innerHTML = "🗺️ ¡Diagrama perfecto! Has mapeado el algoritmo con éxito espacial. +120 XP";
    } else {
        resultadoDiv.className = "resultado error";
        resultadoDiv.innerHTML = "❌ El flujo está roto. El programa se quedaría atrapado en el espacio. ¡Reordena los bloques!";
    }
}

function reiniciarCodEF() {
    secuenciaUsuario = [];
    document.getElementById('orden-actual').innerText = "Tu diagrama: Ninguno";
    document.querySelectorAll('.ef-block').forEach(b => b.style.background = "rgba(255, 255, 255, 0.05)");
}