let zonaActualDelAlumno = typeof ZONA_ALUMNO_REAL !== 'undefined' ? ZONA_ALUMNO_REAL : 1;
let textoNivelAlumno = typeof TEXTO_NIVEL_REAL !== 'undefined' ? TEXTO_NIVEL_REAL : "Principiante 👨‍🚀";

const nivelTexto = document.getElementById("nivelTexto");
const landing = document.getElementById("pantalla-inicio");
const mapa = document.getElementById("mapa");

// Insertamos dinámicamente el nivel real recuperado de la Base de Datos
if (nivelTexto) {
    nivelTexto.innerHTML = `Nivel: ${textoNivelAlumno}`;
}

// 📊 BANCO DE DATOS DEL MÓDULO I (30 ZONAS ACADÉMICAS)
const bancoTemasModuloI = {
    1: { titulo: 'Introducción a la Lógica Computacional', intro: 'La lógica es el motor que impulsa todas las naves en CodeLand.', tipo: 'Entrenamiento' },
    2: { titulo: '¿Qué es un Algoritmo?', intro: 'Un algoritmo es una secuencia precisa de pasos ordenados para resolver problemas.', tipo: 'Entrenamiento' },
    3: { titulo: 'Análisis de Problemas (Entrada, Proceso y Salida)', intro: 'Identificamos Suministros (Entradas), el viaje (Proceso) y el Destino (Salida).', tipo: 'Entrenamiento' },
    4: { titulo: 'Diagramas de Flujo: Símbolos Básicos', intro: 'Mapeamos los procesos mediante las figuras geométricas universales estándar.', tipo: 'Entrenamiento' },
    5: { titulo: 'Evaluación I', intro: 'Realiza las actividades de evaluación para consolidar los conocimientos del bloque.', tipo: 'Entrenamiento' },
    6: { titulo: 'Diseño de Algoritmos Mediante Pseudocódigo', intro: 'La transición perfecta entre el lenguaje humano y el lenguaje formal de máquina.', tipo: 'Entrenamiento' },
    7: { titulo: 'Clasificación de Tipos de Datos y Variables', intro: 'Instrucciones lineales que se ejecutan una tras otra en estricto orden de bitácora.', tipo: 'Entrenamiento' },
    8: { titulo: 'Operadores Aritméticos y Jerarquía de Operaciones', intro: 'Clasifiquemos datos e identifiquemos variables.', tipo: 'Entrenamiento' },
    9: { titulo: 'Operadores Relacionales y Estructuras de Comparación Lógica', intro: 'Veamos como utilizar los operadores relacionales y lógicos.', tipo: 'Entrenamiento' },
    10: { titulo: 'Evaluación II', intro: 'Tienes una segunda gran prueba para evaluar tu comprensión de conceptos estelares.', tipo: 'Entrenamiento' },
    11: { titulo: 'Estructura General de un Programa', intro: 'Comencemos a construir la bases de nuestros programas.', tipo: 'Entrenamiento' },
    12: { titulo: 'Instrucciones de Entrada y Salida de Datos', intro: 'Analisemos que naves entran y cuales salen.', tipo: 'Entrenamiento' },
    13: { titulo: 'Palabras Reservadas e Identificadores', intro: 'Revisemos la gramatica de nuestros planetas', tipo: 'Entrenamiento' },
    14: { titulo: 'Estructuras Condicionales Simples y Estructuras Condicionales Compuestas (Si-Entonces-Sino)', intro: 'Implementación real de sentencias condicionales estructuradas eficientes.', tipo: 'Entrenamiento' },
    15: { titulo: 'Evaluación III', intro: 'Integra los conceptos aprendidos en un examen comprehensivo.', tipo: 'Entrenamiento' },
    16: { titulo: 'Condicionales Anidadas y Selección Múltiple', intro: 'Implementación de estructuras condicionales complejas con múltiples opciones.', tipo: 'Entrenamiento' },
    17: { titulo: 'Estructuras Cíclicas: Bucle Para (For)', intro: 'Iteración controlada mediante un contador específico.', tipo: 'Entrenamiento' },
    18: { titulo: 'Estructuras Cíclicas: Bucle Mientras (While)', intro: 'Controlemos el flujo de ejecución mediante condiciones lógicas.', tipo: 'Entrenamiento' },
    19: { titulo: 'Estructuras Cíclicas: Bucle Repetir (Do-While / Hasta Que)', intro: 'Ejecutamos el código al menos una vez antes de evaluar la condición.', tipo: 'Entrenamiento' },
    20: { titulo: 'Evaluación IV', intro: 'Tienes una cuarta gran prueba para evaluar tu comprensión de conceptos estelares.', tipo: 'Desafío' },
    21: { titulo: 'Estructuras de Repetición: Bucles', intro: 'Ciclos automáticos para iterar tareas repetitivas de mantenimiento.', tipo: 'Entrenamiento' },
    22: { titulo: 'Variables de Control Especiales: Contadores, Acumuladores y Banderas', intro: 'Variables especiales utilizadas para controlar el flujo de ejecución en estructuras cíclicas.', tipo: 'Entrenamiento' },
    23: { titulo: 'Mecanismos de Transmisión: Paso por Valor vs Paso por Referencia', intro: 'Métodos de pasar datos a funciones y cómo afecta esto al comportamiento del programa.', tipo: 'Entrenamiento' },
    24: { titulo: 'Estructuras de Datos Lineales: Arreglos Unidimensionales (Vectores)', intro: 'Estructuras de datos que almacenan colecciones de elementos del mismo tipo.', tipo: 'Entrenamiento' },
    25: { titulo: 'Evaluación V', intro: 'Tienes una quinta gran prueba para evaluar tu comprensión de conceptos estelares.', tipo: 'Desafío' },
    26: { titulo: 'Operaciones en Vectores: Búsqueda Lineal', intro: 'Método para encontrar un elemento específico en un vector.', tipo: 'Entrenamiento' },
    27: { titulo: 'Matrices (Arreglos Bidimensionales)', intro: 'Estructuras de datos que almacenan colecciones de elementos en dos dimensiones.', tipo: 'Entrenamiento' },
    28: { titulo: 'Operaciones Avanzadas con Matrices', intro: 'Métodos para manipular y procesar matrices de manera eficiente.', tipo: 'Entrenamiento' },
    29: { titulo: 'Buenas Prácticas de Programación Estructurada', intro: 'Intercambio seguro de cargas informáticas entre bloques funcionales.', tipo: 'Entrenamiento' },
    30: { titulo: 'EVALUACION FINAL', intro: '¡Prueba Definitiva!', tipo: 'Proyecto' }
};

function iniciarRuta() {
    if (landing && mapa) {
        landing.classList.add("oculto");
        mapa.classList.remove("oculto");
        
        // 🌟 REPARACIÓN AQUÍ: Forzamos a generar los nodos en el momento que se muestra el mapa
        generarNodosDelMapa(); 
    }
}

function regresarALanding() {
    mapa.classList.add("oculto");
    const pantallaInicio = document.getElementById("pantalla-inicio");
    if(pantallaInicio) pantallaInicio.classList.remove("oculto");
}

function generarNodosDelMapa() {
    // 🌟 REPARACIÓN AQUÍ: Sincronizar la variable con los datos frescos que PHP leyó de la BD
    if (typeof ZONA_ALUMNO_REAL !== 'undefined') {
        zonaActualDelAlumno = parseInt(ZONA_ALUMNO_REAL);
    }

    const contenedorNodos = document.querySelector(".mapa-nodos");
    if (!contenedorNodos) return;
    
    contenedorNodos.innerHTML = ""; 

    Object.keys(bancoTemasModuloI).forEach(zonaId => {
        const idStr = parseInt(zonaId);
        
        const nodo = document.createElement("div");
        nodo.classList.add("nodo-estacion"); // Aplica los estilos circulares de tu CSS
        
        // 🛰️ Evaluación de órbitas consecutivas reales
        if (idStr < zonaActualDelAlumno) {
            nodo.classList.add("completado");
            nodo.innerHTML = `<div class="icono-nodo">${idStr}</div><span class="etiqueta-nodo">✔</span>`;
            nodo.onclick = () => seleccionarNodo(idStr);
        } else if (idStr === zonaActualDelAlumno) {
            nodo.classList.add("actual");
            nodo.innerHTML = `<div class="icono-nodo">${idStr}</div><span class="etiqueta-nodo">📡</span>`;
            nodo.onclick = () => seleccionarNodo(idStr);
        } else {
            nodo.classList.add("bloqueado");
            nodo.innerHTML = `<div class="icono-nodo">${idStr}</div><span class="etiqueta-nodo">🔒</span>`;
            nodo.onclick = () => desplegarMensajeBloqueado(idStr);
        }

        contenedorNodos.appendChild(nodo);
    });
}
function seleccionarNodo(idZona) {
    const datosTema = bancoTemasModuloI[idZona];
    const cuadro = document.querySelector(".cuadro-dialogo-espacial");
    const titulo = document.getElementById("dialogo-titulo");
    const texto = document.getElementById("dialogo-texto");
    const btnMision = document.getElementById("btnIniciarMision");

    if (datosTema.tipo === 'Proyecto' || datosTema.tipo === 'Desafío') {
        cuadro.style.borderColor = "#ff3b3b";
        cuadro.style.boxShadow = "0 0 25px rgba(255, 59, 59, 0.4)";
        titulo.innerHTML = `<span style="color:#ff3b3b;">⚠️ ALERT [Sector ${idZona}]:</span> ${datosTema.titulo}`;
        texto.innerHTML = `¡Recluta, atención! Esta sección corresponde a una fase evaluativa crítica. Tu código debe ser impecable.<br><br>${datosTema.intro}`;
        btnMision.innerText = "Iniciar Desafío ⚔️";
    } else {
        cuadro.style.borderColor = "#00c2ff";
        cuadro.style.boxShadow = "0 15px 35px rgba(0,0,0,0.6)";
        titulo.innerHTML = `<span style="color:#00ff87;">Zona ${idZona}:</span> ${datosTema.titulo}`;
        texto.innerText = datosTema.intro;
        btnMision.innerText = "Iniciar Microlección 🚀";
    }
    
    btnMision.style.display = "block";
    btnMision.onclick = () => {
        // En lugar de ir directo a un HTML plano, enviamos al alumno a un cargador interactivo de PHP
        window.location.href = `microlecciones/leccion${idZona}.html`;
    };
}

function desplegarMensajeBloqueado() {
    const cuadro = document.querySelector(".cuadro-dialogo-espacial");
    cuadro.style.borderColor = "#00c2ff";
    cuadro.style.boxShadow = "0 15px 35px rgba(0,0,0,0.6)";
    document.getElementById("dialogo-titulo").innerText = "🔒 Sector Encriptado";
    document.getElementById("dialogo-texto").innerText = "Este cuadrante estelar requiere completar los retos de telemetría lógicos anteriores.";
    document.getElementById("btnIniciarMision").style.display = "none";
}
