let todosLosAlumnos = []; 
let alumnosFiltrados = []; 

// Estados persistentes de los filtros en cabina
let semestreSeleccionado = 'TODOS';
let grupoSeleccionado = 'TODOS';
let textoBusqueda = '';
let alumnoSeleccionadoId = null;

// Ejecución automática al acoplar la interfaz al navegador
document.addEventListener("DOMContentLoaded", () => {
    cargarAlumnosDesdeServidor();
});

// 1. Descarga asíncrona de datos desde la API relacional
function cargarAlumnosDesdeServidor() {
    const cuerpoTabla = document.getElementById("tablaAlumnosCuerpo");
    if (!cuerpoTabla) return;

    cuerpoTabla.innerHTML = `<tr><td colspan="6" style="text-align:center; color:#38bdf8; padding:25px;">📡 Sincronizando telemetría con los servidores de CodeLand...</td></tr>`;

    fetch('api/obtener_alumnos_docente.php')
        .then(res => {
            if (!res.ok) throw new Error("Fallo en la respuesta de red del servidor.");
            return res.json();
        })
        .then(data => {
            if (data.success) {
                todosLosAlumnos = data.alumnos;
                alumnosFiltrados = [...todosLosAlumnos]; 
                renderizarTablaAlumnos();
            } else {
                cuerpoTabla.innerHTML = `
                    <tr>
                        <td colspan="6" style="text-align:center; color:#ef4444; padding:20px;">
                            ⚠️ <strong>Error de Sincronización:</strong> ${escaparHTML(data.error)}
                        </td>
                    </tr>`;
            }
        })
        .catch(err => {
            console.error("Error en Fetch de telemetría:", err);
            cuerpoTabla.innerHTML = `<tr><td colspan="6" style="text-align:center; color:#ef4444; padding:25px;">❌ No se pudo establecer conexión con los hangares de la base de datos.</td></tr>`;
        });
}

// 2. Renderizado Reactivo de filas adaptado al Plan de Estudios de Programación (30 Lecciones)
function renderizarTablaAlumnos() {
    const cuerpoTabla = document.getElementById("tablaAlumnosCuerpo");
    if (!cuerpoTabla) return;
    cuerpoTabla.innerHTML = "";

    if (alumnosFiltrados.length === 0) {
        cuerpoTabla.innerHTML = `<tr><td colspan="6" style="text-align:center; color:#64748b; font-style:italic; padding:30px;">No se encontraron astronautas con los cuadrantes de filtrado seleccionados.</td></tr>`;
        return;
    }

    alumnosFiltrados.forEach(alumno => {
        const fila = document.createElement("tr");
        fila.style.borderBottom = "1px solid #1e293b";
        
        // El plan de estudios consta exactamente de 30 lecciones en el Mundo I
        const leccionActual = parseInt(alumno.leccion_actual || 1);
        const leccionTexto = leccionActual > 30 ? "Módulo I Concluido 🎓" : `Lección ${leccionActual}/30`;

        fila.innerHTML = `
            <td style="padding: 12px 15px;">
                <div style="display:flex; align-items:center; gap:10px;">
                    <div style="font-size: 18px;">👨‍🚀</div>
                    <span style="font-weight:500; color:#f8fafc;">${escaparHTML(alumno.nombre || 'Astronauta Anónimo')}</span>
                </div>
            </td>
            <td style="padding: 12px 15px;">
                <span class="badge-grupo-docente" style="background:#1e293b; padding:4px 8px; border-radius:6px; color:#38bdf8; border: 1px solid #334155; font-size:12px; font-weight:600;">
                    ${alumno.semestre}° ${alumno.grupo}
                </span>
            </td>
            <td style="padding: 12px 15px;"><span class="badge-nivel-docente" style="font-size:13px;">${escaparHTML(alumno.nivel)}</span></td>
            <td style="padding: 12px 15px;"><span style="color:#e2e8f0; font-size:13px;">${leccionTexto}</span></td>
            <td style="padding: 12px 15px;"><span style="color:#10b981; font-weight:600;">${alumno.xp_total} XP</span></td>
            <td style="padding: 12px 15px; text-align: center;">
                <button class="btn-revisar-holograma" onclick='mostrarDetalleAlumno(${JSON.stringify(alumno)})' style="background:#38bdf8; color:#0f172a; border:none; padding:6px 14px; border-radius:6px; cursor:pointer; font-weight:600; font-size:12px; transition: transform 0.2s;">
                    👁️ Monitorear
                </button>
            </td>
        `;
        cuerpoTabla.appendChild(fila);
    });
}

// 3. Filtrado por Semestre (2°, 4°, 6° o TODOS)
function filtrarPorSemestre(semestre, elementoClick) {
    semestreSeleccionado = semestre;
    document.querySelectorAll(".semestre-btn").forEach(btn => btn.classList.remove("active"));
    if (elementoClick) elementoClick.classList.add("active");
    ejecutarFiltrosCombinados();
}

// 4. Filtrado por Grupo (E, F, G o TODOS)
function filtrarPorGrupo(grupo, elementoClick) {
    grupoSeleccionado = grupo;
    document.querySelectorAll(".aula-btn").forEach(btn => btn.classList.remove("active"));
    if (elementoClick) elementoClick.classList.add("active");
    ejecutarFiltrosCombinados();
}

// 5. Captura reactiva de caracteres desde el input de búsqueda
function filtrarPorNombre() {
    const input = document.getElementById("buscadorAlumno");
    textoBusqueda = input ? input.value.toLowerCase().trim() : "";
    ejecutarFiltrosCombinados();
}

// 6. MOTOR UNIFICADO DE CRUCE DE DATOS (Semestre && Grupo && Nombre)
function ejecutarFiltrosCombinados() {
    alumnosFiltrados = todosLosAlumnos.filter(alumno => {
        // Validación del campo de Semestre
        const coincideSemestre = (semestreSeleccionado === 'TODOS' || parseInt(alumno.semestre) === parseInt(semestreSeleccionado));
        
        // Validación del campo de Grupo
        const coincideGrupo = (grupoSeleccionado === 'TODOS' || alumno.grupo === grupoSeleccionado);
        
        // Validación por coincidencia de cadena en nombre completo
        const nombreCompleto = alumno.nombre ? alumno.nombre.toLowerCase() : "";
        const coincideNombre = nombreCompleto.includes(textoBusqueda);

        return coincideSemestre && coincideGrupo && coincideNombre;
    });

    renderizarTablaAlumnos();
}

// 7. Despliegue pormenorizado de analíticas individuales en el módulo inferior
function mostrarDetalleAlumno(datosAlumno) {
    alumnoSeleccionadoId = datosAlumno.id_alumno;
    
    const vista = document.getElementById("vistaDetalleAlumno");
    if (!vista) return;
    
    vista.style.display = "block";
    
    // Desplazamiento suave para comodidad del docente al seleccionar
    vista.scrollIntoView({ behavior: 'smooth', block: 'nearest' });

    const leccionActual = parseInt(datosAlumno.leccion_actual || 1);
    const leccionTexto = leccionActual > 30 ? "Módulo I Completo 🎓" : `Lección ${leccionActual} de 30`;

    document.getElementById("detNombre").textContent = datosAlumno.nombre || 'Astronauta Recluta';
    document.getElementById("detGrupo").textContent = `Semestre: ${datosAlumno.semestre}° | Grupo: ${datosAlumno.grupo}`;
    document.getElementById("detNivel").textContent = datosAlumno.nivel;
    document.getElementById("detLecciones").textContent = leccionTexto;

    // --- ALGORITMO INTEGRADO DE TELEMETRÍA DE ERRORES ---
    let diagnosticoSistema = "Progreso nominal óptimo. El estudiante mantiene una trayectoria estable en la órbita de CodeLand.";
    const inactividad = parseInt(datosAlumno.dias_inactivo || 0);
    const fallosPromedio = parseInt(datosAlumno.intentos_fallidos_promedio || 0);

    if (leccionActual <= 1 && inactividad > 5) {
        diagnosticoSistema = "🚨 Alto riesgo de deserción: El alumno completó su diagnóstico pero lleva más de 5 días sin iniciar las lecciones del Mundo I.";
    } else if (fallosPromedio > 3) {
        diagnosticoSistema = `❌ Alerta de bloqueo lógico: El alumno registra un volumen alto de incidencias guardadas (${fallosPromedio} errores en consola). Se sugiere revisar sus estructuras lógicas de control.`;
    } else if (inactividad > 7) {
        diagnosticoSistema = `⚠️ Navegación pausada: El astronauta lleva ${inactividad} días sin reportar avance en los retos de código del servidor.`;
    }

    document.getElementById("txtDificultades").textContent = diagnosticoSistema;
}

// 8. Envío asíncrono de alertas/observaciones hacia el mapa del estudiante
function enviarMensajeAAlumno() {
    const textarea = document.getElementById("mensajeDocente");
    const cuerpoMensaje = textarea ? textarea.value.trim() : "";

    if (!cuerpoMensaje) {
        alert("⚠️ Por favor, redacte una instrucción o sugerencia válida antes de despacharla.");
        return;
    }

    fetch('api/enviar_observacion.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            id_alumno: alumnoSeleccionadoId,
            mensaje: cuerpoMensaje
        })
    })
    .then(res => {
        if (!res.ok) throw new Error("Fallo en la red.");
        return res.json();
    })
    .then(data => {
        if (data.success) {
            alert("🚀 ¡Alerta estelar transmitida con éxito! El alumno la visualizará en su hangar del mapa.");
            textarea.value = "";
        } else {
            alert("Error al despachar el paquete de datos: " + data.error);
        }
    })
    .catch(err => {
        console.error("Error en comunicación:", err);
        alert("❌ Error de comunicación: El servidor no pudo procesar la alerta.");
    });
}

// Helper preventivo contra inyecciones XSS maliciosas en nombres
function escaparHTML(texto) {
    if (!texto) return "";
    return texto.replace(/[&<>'"]/g, 
        caracter => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#39;', '"': '&quot;' }[caracter] || caracter)
    );
}