// DINÁMICA 1: Code Pair (Relación de conceptos)
function verificarCodePair(parejasCorrectas) {
    let aciertos = 0;
    const selectores = document.querySelectorAll('.code-pair-select');
    
    selectores.forEach(select => {
        const concepto = select.getAttribute('data-concepto');
        const respuestaUsuario = select.value;
        
        if (parejasCorrectas[concepto] === respuestaUsuario) {
            select.style.borderColor = "#2ecc71";
            select.style.backgroundColor = "#e8f8f5";
            aciertos++;
        } else {
            select.style.borderColor = "#e74c3c";
            select.style.backgroundColor = "#fceae9";
        }
    });

    const resultadoDiv = document.getElementById('resultado-juego');
    if (aciertos === Object.keys(parejasCorrectas).length) {
        resultadoDiv.className = "resultado exito";
        resultadoDiv.innerHTML = "🚀 ¡Excelente astronauta! Has relacionado todos los conceptos correctamente. +100 XP";
    } else {
        resultadoDiv.className = "resultado error";
        resultadoDiv.innerHTML = `👾 ¡Cuidado con los asteroides! Tienes ${aciertos} de ${Object.keys(parejasCorrectas).length} correctas. ¡Inténtalo de nuevo!`;
    }
}

// DINÁMICA 2: Debug Pro (Identificar errores)
function verificarDebugPro(solucionCorrecta) {
    const codigoUsuario = document.getElementById('codigo-usuario').value.trim();
    const resultadoDiv = document.getElementById('resultado-juego');

    // Remover espacios o puntos y coma extras para validación simple
    if (codigoUsuario.replace(/\s/g, "") === solucionCorrecta.replace(/\s/g, "")) {
        resultadoDiv.className = "resultado exito";
        resultadoDiv.innerHTML = "🔧 ¡Bug eliminado con éxito! Código reparado. +100 XP";
    } else {
        resultadoDiv.className = "resultado error";
        resultadoDiv.innerHTML = "❌ El código sigue roto. Revisa la sintaxis e inténtalo de nuevo.";
    }
}
// DINÁMICA 3: CodeDetective (Resolver problemas lógicos) 
function verificarCodeDetective(opcionCorrecta) {
    const opciones = document.querySelectorAll('.detective-option');
    const resultadoDiv = document.getElementById('resultado-juego');
    let seleccionada = null;

    opciones.forEach(radio => {
        if (radio.checked) seleccionada = radio.value;
    });

    if (!seleccionada) {
        resultadoDiv.className = "resultado error";
        resultadoDiv.innerHTML = "⚠️ Por favor, selecciona una pista antes de resolver el caso.";
        return;
    }

    if (seleccionada === opcionCorrecta) {
        resultadoDiv.className = "resultado exito";
        resultadoDiv.innerHTML = "🕵️‍♂️ ¡Caso cerrado, Detective! Descubriste el comportamiento del código. +100 XP";
    } else {
        resultadoDiv.className = "resultado error";
        resultadoDiv.innerHTML = "❌ Pista incorrecta. El código tomó un rumbo galáctico diferente. Revisa las condiciones.";
    }
}