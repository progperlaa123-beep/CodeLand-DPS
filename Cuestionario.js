const preguntas = [
    {
        pregunta: "¿Qué es un algoritmo?",
        respuestas: [
            { texto: "Un tipo de computadora", correcta: false },
            { texto: "Una serie de pasos para resolver un problema", correcta: true },
            { texto: "Un lenguaje de programación", correcta: false },
            { texto: "Un programa terminado", correcta: false }
        ]
    },
    {
        pregunta: "¿Cuál de los siguientes dispositivos es de entrada?",
        respuestas: [
            { texto: "Monitor", correcta: false },
            { texto: "Bocina", correcta: false },
            { texto: "Teclado", correcta: true },
            { texto: "Impresora", correcta: false }
        ]
    },
    {
        pregunta: "¿Qué hace la siguiente operación? 5 + 3 × 2",
        respuestas: [
            { texto: "11", correcta: true },
            { texto: "16", correcta: false },
            { texto: "13", correcta: false },
            { texto: "10", correcta: false }
        ]
    },
    {
        pregunta: "¿Qué representa un diagrama de flujo?",
        respuestas: [
            { texto: "Un dibujo decorativo", correcta: false },
            { texto: "Un mapa de internet", correcta: false },
            { texto: "La representación gráfica de un proceso o algoritmo", correcta: true },
            { texto: "Una base de datos", correcta: false }
        ]
    },
    {
        pregunta: "¿Cuál es la función de una estructura condicional?",
        respuestas: [
            { texto: "Repetir instrucciones infinitamente", correcta: false },
            { texto: "Tomar decisiones dependiendo de una condición", correcta: true },
            { texto: "Guardar información en memoria", correcta: false },
            { texto: "Diseñar páginas web", correcta: false }
        ]
    },
    {
        pregunta: `Observa el siguiente pseudocódigo:
<pre>
Inicio
Leer edad

Si edad >= 18 Entonces
   Mostrar "Mayor de edad"
SiNo
   Mostrar "Menor de edad"
FinSi
Fin
</pre>
¿Qué mostrará si la edad ingresada es 20?`,
        respuestas: [
            { texto: "Error", correcta: false },
            { texto: "Menor de edad", correcta: false },
            { texto: "Mayor de edad", correcta: true },
            { texto: "Ninguna opción", correcta: false }
        ]
    },
    {
        pregunta: "¿Cuál corresponde a un lenguaje de programación?",
        respuestas: [
            { texto: "HTML", correcta: false },
            { texto: "Windows", correcta: false },
            { texto: "Java", correcta: true },
            { texto: "Google Chrome", correcta: false }
        ]
    },
    {
        pregunta: `¿Qué resultado tendrá el siguiente fragmento lógico?
<pre>
contador = 1
Mientras contador <= 3 Hacer
   Mostrar contador
   contador = contador + 1
FinMientras
</pre>`,
        respuestas: [
            { texto: "1 2 3", correcta: true },
            { texto: "1 1 1", correcta: false },
            { texto: "3 2 1", correcta: false },
            { texto: "Error de sintaxis", correcta: false }
        ]
    },
    {
        pregunta: "¿Cuál es la principal función de una base de datos?",
        respuestas: [
            { texto: "Dibujar interfaces gráficas", correcta: false },
            { texto: "Almacenar y organizar información", correcta: true },
            { texto: "Crear sistemas operativos", correcta: false },
            { texto: "Diseñar redes sociales", correcta: false }
        ]
    },
    {
        pregunta: "¿Qué tecnología da estilo visual a una página web?",
        respuestas: [
            { texto: "Java", correcta: false },
            { texto: "CSS", correcta: true },
            { texto: "SQL", correcta: false },
            { texto: "Python", correcta: false }
        ]
    }
];

let preguntaActual = 0;
let puntaje = 0;

const pregunta = document.getElementById("pregunta");
const respuestas = document.getElementById("respuestas");

function mostrarPregunta(){
    respuestas.innerHTML = "";
    pregunta.innerHTML = preguntas[preguntaActual].pregunta;

    preguntas[preguntaActual].respuestas.forEach(respuesta => {
        const boton = document.createElement("button");
        boton.innerText = respuesta.texto;
        boton.classList.add("respuesta");
        boton.addEventListener("click", () => seleccionarRespuesta(boton, respuesta.correcta));
        respuestas.appendChild(boton);
    });
}

function seleccionarRespuesta(boton, correcta){
    boton.classList.add("correcta");

    if(correcta){
        puntaje++;
    }

    setTimeout(() => {
        preguntaActual++;

        if(preguntaActual < preguntas.length){
            mostrarPregunta();
        } else {
            // Cuando termine el test, mostramos resultados y enviamos a la BD
            mostrarResultado();
        }
    }, 700);
}

function mostrarResultado(){
    document.querySelector(".contenedor").style.display = "none";
    document.getElementById("resultado").classList.remove("oculto");

    const nivel = document.getElementById("nivel");
    const descripcion = document.getElementById("descripcion");
    
    // Declaramos la variable en el ámbito superior de la función
    let nivelTexto = ""; 

    if(puntaje <= 4){
        nivelTexto = "Principiante";
        nivel.innerText = "Nivel Principiante";
        descripcion.innerText = "Necesitas reforzar lógica básica, uso de computadoras y conceptos fundamentales de programación.";
    }
    else if(puntaje <= 7){
        nivelTexto = "Intermedio";
        nivel.innerText = "Nivel Intermedio";
        descripcion.innerText = "Comprendes conceptos básicos de programación y puedes resolver problemas sencillos mediante algoritmos.";
    }
    else{
        nivelTexto = "Avanzado";
        nivel.innerText = "Nivel Avanzado";
        descripcion.innerText = "Demuestras conocimientos sólidos en lógica, programación estructurada y desarrollo básico de software.";
    }

    // Ejecutamos la función encargada de guardar el resultado enviando los datos correctos
    guardarResultado(nivelTexto);
}

function guardarResultado(nivelTexto){
    fetch("guardar_diagnostico.php", { 
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({
            puntaje: puntaje,
            nivel: nivelTexto
        })
    })
    .then(response => response.text())
    .then(data => {
        console.log("Respuesta del servidor:", data);
        
        if(data.includes("Resultado guardado") || data.includes("éxito")) {
            
            // 🔥 CAPTURAMOS EL ID ENVIADO DESDE EL PHP
            try {
                const partes = data.split("|id:");
                if(partes.length > 1) {
                    const idReal = partes[1].trim();
                    localStorage.setItem("id_alumno_sesion", idReal);
                    console.log("ID del alumno guardado dinámicamente:", idReal);
                }
            } catch(e) {
                console.error("No se pudo procesar el ID:", e);
            }
            
            setTimeout(() => {
                window.location.href = "mundoI.php"; 
            }, 1500); 
            
        } else {
            alert("Hubo un problema al guardar tu progreso, pero puedes continuar.");
            window.location.href = "mundoI.php";
        }
    })
    .catch(error => {
        console.error("Error en el envío fetch:", error);
        window.location.href = "mundoI.php"; 
    });
}
// Inicializar la primera pregunta al cargar la página
mostrarPregunta();