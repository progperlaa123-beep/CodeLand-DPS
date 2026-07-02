<?php
// panel_docente.php
// 1. INICIALIZAR Y VALIDAR LA SESIÓN DE INMEDIATO (CANDADO DE SEGURIDAD)
session_start();

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'docente') {
    header("Location: login.php?error=Acceso denegado");
    exit();
}

// 2. CARGAR DATOS DEL MAESTRO
require_once 'conexion.php';
$id_docente = $_SESSION['user_id'] ?? 0;
$nombre_docente = $_SESSION['nombre'] ?? 'Docente de la Academia';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Docente - CodeLand DPS</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="panel_docente.css"> 
</head>
<body class="panel-body-espacial">

    <div class="contenedor-panel">
        
        <header class="panel-header">
            <div class="panel-brand">
                <span style="color: #38bdf8;">CodeLand</span>
                <span style="color: #4ade80;">DPS</span>
                <span class="badge-rol">DOCENTE</span>
            </div>
            <div class="usuario-info-docente">
                <span>Comandante: <strong><?php echo htmlspecialchars($nombre_docente); ?></strong></span>
                <a href="logout.php" style="color: #ef4444; margin-left: 15px; text-decoration: none; font-size: 14px;">🔒 Salir</a>
            </div>
        </header>

        <div class="panel-main-layout" style="display: grid; grid-template-columns: 260px 1fr; gap: 20px; align-items: start;">
            
            <aside class="sidebar-filtros" style="background: rgba(30, 41, 59, 0.4); border: 1px solid #334155; border-radius: 12px; padding: 20px; backdrop-filter: blur(10px);">
                
                <div class="bloque-filtro" style="margin-bottom: 25px;">
                    <h3 style="font-size: 12px; color: #64748b; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 12px; font-weight: 700;">📅 Semestres</h3>
                    <div style="display: flex; flex-direction: column; gap: 8px;">
                        <button class="grupo-item semestre-btn active" onclick="filtrarPorSemestre('TODOS', this)" style="text-align: left; width: 100%;">🌌 Todos</button>
                        <button class="grupo-item semestre-btn" onclick="filtrarPorSemestre(2, this)" style="text-align: left; width: 100%;">2° Semestre</button>
                        <button class="grupo-item semestre-btn" onclick="filtrarPorSemestre(4, this)" style="text-align: left; width: 100%;">4° Semestre</button>
                        <button class="grupo-item semestre-btn" onclick="filtrarPorSemestre(6, this)" style="text-align: left; width: 100%;">6° Semestre</button>
                    </div>
                </div>

                <div class="bloque-filtro">
                    <h3 style="font-size: 12px; color: #64748b; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 12px; font-weight: 700;">🛸 Grupos / Aulas</h3>
                    <div style="display: flex; flex-direction: column; gap: 8px;">
                        <button class="grupo-item aula-btn active" onclick="filtrarPorGrupo('TODOS', this)" style="text-align: left; width: 100%;">⭐ Todos los Grupos</button>
                        <button class="grupo-item aula-btn" onclick="filtrarPorGrupo('E', this)" style="text-align: left; width: 100%;">Grupo E</button>
                        <button class="grupo-item aula-btn" onclick="filtrarPorGrupo('F', this)" style="text-align: left; width: 100%;">Grupo F</button>
                        <button class="grupo-item aula-btn" onclick="filtrarPorGrupo('G', this)" style="text-align: left; width: 100%;">Grupo G</button>
                    </div>
                </div>

            </aside>

            <main class="panel-contenido">
                
                <div class="barra-busqueda-docente" style="margin-bottom: 20px;">
                    <div class="buscador-relativo" style="position: relative; width: 100%;">
                        <span class="icono-lupa" style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #64748b;">🔍</span>
                        <input 
                            type="text" 
                            id="buscadorAlumno" 
                            placeholder="Buscar astronauta por nombre..." 
                            oninput="filtrarPorNombre()"
                            style="width: 100%; box-sizing: border-box; padding: 12px 12px 12px 45px; background: #0f172a; border: 1px solid #334155; border-radius: 8px; color: white; font-family: inherit; font-size: 14px;"
                        >
                    </div>
                </div>

                <div class="workspace-docente" style="display: grid; grid-template-columns: 1fr; gap: 20px;" id="contenedorTablasYDetalle">
                    
                    <section class="seccion-tabla-alumnos">
                        <div class="tabla-contenedor-espacial" style="background: rgba(30, 41, 59, 0.2); border: 1px solid #334155; border-radius: 12px; overflow-x: auto;">
                            <table style="width: 100%; border-collapse: collapse; text-align: left; min-width: 600px;">
                                <thead>
                                    <tr style="background: #1e293b; color: #94a3b8; font-size: 13px; border-bottom: 1px solid #334155;">
                                        <th style="padding: 15px;">Astronauta Recluta</th>
                                        <th style="padding: 15px;">Filiación Académica</th>
                                        <th style="padding: 15px;">Rango Diagnóstico</th>
                                        <th style="padding: 15px;">Progreso de Retos</th>
                                        <th style="padding: 15px;">Puntaje Total</th>
                                        <th style="padding: 15px; text-align: center;">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="tablaAlumnosCuerpo">
                                    </tbody>
                            </table>
                        </div>
                    </section>

                    <section id="vistaDetalleAlumno" class="seccion-detalle-alumno" style="display: none; background: rgba(30, 41, 59, 0.5); border: 1px solid #334155; border-radius: 12px; padding: 25px; backdrop-filter: blur(10px);">
                        <div class="detalle-header" style="border-bottom: 1px solid #334155; padding-bottom: 15px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <h2 id="detNombre" style="margin: 0 0 5px 0; font-size: 22px; color: #38bdf8;">Astronauta Seleccionado</h2>
                                <span id="detGrupo" style="color: #94a3b8; font-size: 14px;">Semestre: - | Grupo: -</span>
                            </div>
                            <button onclick="document.getElementById('vistaDetalleAlumno').style.display='none'" style="background: none; border: none; color: #64748b; cursor: pointer; font-size: 20px;">✕</button>
                        </div>

                        <div class="metricas-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 25px;">
                            <div class="metrica-item" style="background: #0f172a; padding: 15px; border-radius: 8px; border: 1px solid #1e293b;">
                                <span class="metrica-titulo" style="font-size: 12px; color: #64748b; text-transform: uppercase;">Rango Diagnóstico</span>
                                <p id="detNivel" class="metrica-valor" style="margin: 5px 0 0 0; font-size: 18px; font-weight: 600; color: #4ade80;">-</p>
                            </div>
                            <div class="metrica-item" style="background: #0f172a; padding: 15px; border-radius: 8px; border: 1px solid #1e293b;">
                                <span class="metrica-titulo" style="font-size: 12px; color: #64748b; text-transform: uppercase;">Progreso de Vuelo</span>
                                <p id="detLecciones" class="metrica-valor" style="margin: 5px 0 0 0; font-size: 18px; font-weight: 600; color: #e2e8f0;">-</p>
                            </div>
                        </div>

                        <div id="areaDificultades" class="alerta-critica-espacial" style="background: rgba(239, 68, 68, 0.08); border: 1px solid rgba(239, 68, 68, 0.2); border-radius: 8px; padding: 15px; margin-bottom: 25px;">
                            <h4 style="margin: 0 0 5px 0; color: #fca5a5; font-size: 14px;">⚠️ Alerta del Sistema Analítico Docente:</h4>
                            <p id="txtDificultades" style="margin: 0; color: #cbd5e1; font-size: 13px;">Analizando datos de navegación en el Mundo 1...</p>
                        </div>

                        <div class="envio-observacion-box">
                            <h3 style="font-size: 16px; margin: 0 0 5px 0; color: #f8fafc;">Sugerencia Pedagógica para el Astronauta Guía</h3>
                            <p class="instruccion-texto" style="font-size: 12px; color: #64748b; margin: 0 0 12px 0;">El mensaje redactado se le entregará al alumno como un cuadro de diálogo interactivo la próxima vez que explore el mapa.</p>
                            <textarea id="mensajeDocente" placeholder="Ej: He notado problemas con tus condicionales IF/ELSE en el Mundo I. ¡Repasa la teoría de la lección antes de avanzar!" style="width: 100%; box-sizing: border-box; height: 90px; background: #0f172a; border: 1px solid #334155; border-radius: 8px; color: white; padding: 12px; font-family: inherit; font-size: 13.5px; resize: vertical;"></textarea>
                            <button class="btn-enviar-espacial" onclick="enviarMensajeAAlumno()" style="margin-top: 10px; background: #38bdf8; color: #0f172a; border: none; padding: 10px 20px; border-radius: 6px; font-weight: 600; cursor: pointer; width: 100%;">🚀 Enviar Alerta Espacial</button>
                        </div>
                    </section>

                </div>
            </main>
        </div>
    </div>

    <script src="panel_docente.js"></script>
</body>
</html>