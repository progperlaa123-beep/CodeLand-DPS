// Filtro Frontend preventivo de lenguaje obsceno/vulgar
const palabrasProhibidas = ["groseria1", "vulgaridad2", "palabrota3"]; 

function verificarLenguajeInapropiado(texto) {
    const textoLimpio = texto.toLowerCase();
    return palabrasProhibidas.some(palabra => textoLimpio.includes(palabra));
}

function guardarDescripcion() {
    const descripcion = document.getElementById("txtDescripcion").value;
    const alerta = document.getElementById("msgFiltroAlerta");

    if (verificarLenguajeInapropiado(descripcion)) {
        alerta.style.display = "block";
        alerta.className = "error-msg";
        alerta.textContent = "⚠️ Tu descripción contiene palabras no permitidas en la academia espacial.";
        return;
    }
    
    alerta.style.display = "none";

    // Enviar al backend de PHP para almacenamiento persistente en la BD
    fetch('api/actualizar_perfil.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ descripcion: descripcion })
    })
    .then(res => res.json())
    .then(data => {
        if(data.success) {
            document.getElementById("vistaDescripcionText").textContent = descripcion;
            document.getElementById("editorBioBox").style.display = "none";
            alert("¡Estado de misión actualizado de forma persistente!");
        } else {
            alert("Error al actualizar: " + data.error);
        }
    });
}

function conmutarEditorBio() {
    const contenedor = document.getElementById("editorBioBox");
    contenedor.style.display = (contenedor.style.display === "none") ? "block" : "none";
}

function abrirSelectorFoto() {
    alert("Función del hangar de fotografía lista. Conéctala con tu backend subir_foto.php");
}

// =========================================================================
// OPERACIONES CRUD EN TIEMPO REAL PARA NOTAS ESTELARES (MIGRADO DE LOCALSTORAGE A BD)
// =========================================================================

function crearNotaServidor() {
    const contenido = document.getElementById("nuevaNotaTexto").value.trim();
    const fecha = document.getElementById("nuevaNotaFecha").value;

    if(contenido === "") {
        alert("Escribe un mensaje para guardarlo en la bitácora.");
        return;
    }

    fetch('api/procesar_notas.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ accion: 'crear', contenido: contenido, fecha: fecha })
    })
    .then(res => res.json())
    .then(data => {
        if(data.success) {
            // Recargar la página para visualizar el renderizado PHP limpio y estructurado de la nota
            window.location.reload();
        } else {
            alert("Error de telemetría: " + data.error);
        }
    });
}

function conmutarCheckNota(idNota, nuevoEstado) {
    fetch('api/procesar_notas.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ accion: 'completar', id_nota: idNota, completada: nuevoEstado })
    })
    .then(res => res.json())
    .then(data => {
        if(data.success) {
            window.location.reload();
        }
    });
}

function eliminarNotaServidor(idNota) {
    if(!confirm("¿Deseas eliminar permanentemente esta nota de la base de datos de la misión?")) return;

    fetch('api/procesar_notas.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ accion: 'eliminar', id_nota: idNota })
    })
    .then(res => res.json())
    .then(data => {
        if(data.success) {
            document.getElementById("nota_" + idNota).remove();
        }
    });
}
function abrirSelectorFoto() {
    document.getElementById('inputFotoOculto').click();
}

function subirFotoPerfilAlServidor() {
    const inputArchivo = document.getElementById('inputFotoOculto');
    if (!inputArchivo.files || inputArchivo.files.length === 0) return;

    const archivoImagen = inputArchivo.files[0];
    const formularioDatos = new FormData();
    formularioDatos.append('foto_perfil', archivoImagen);

    fetch('cambiar_foto.php', {
        method: 'POST',
        body: formularioDatos
    })
    .then(respuesta => {
        if (!respuesta.ok) throw new Error("Error en la respuesta del servidor.");
        return respuesta.json();
    })
    .then(datos => {
        if (datos.success) {
            // Actualiza la imagen en tiempo real manteniendo el diseño
            document.getElementById('fotoPerfilVista').src = datos.foto_url + '?t=' + new Date().getTime();
    alert("🚀 ¡Foto de perfil guardada con éxito!");
        } else {
            alert("⚠️ Error: " + datos.error);
        }
    })
    .catch(error => {
        console.error("Error en la conexión:", error);
        alert("❌ No se pudo conectar con el servidor.");
    });
}