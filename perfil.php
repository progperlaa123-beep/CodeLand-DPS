<?php
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'alumno') {
    header("Location: login.php");
    exit();
}

require_once 'conexion.php';

$id_alumno = $_SESSION['id_alumno'];
$nombre_usuario = $_SESSION['nombre'];

// Valores base por defecto
$semestre    = "4";
$grupo       = "No asignado"; 
$xp_actual   = "0";
$nivel_real  = "Principiante";
$estado      = "Activo";
$descripcion = "Explorando el espacio exterior en CodeLand DPS. 🚀✨";
$foto_usuario = "astronauta_selfie.png"; // Imagen por defecto inicial

try {
    // 1. Obtener datos del alumno, diagnóstico y perfil gamificado (Corregido a foto_perfil)
    $sql_perfil = "
        SELECT 
            a.id_semestre,
            a.id_grupo,
            d.id_nivel,
            p.xp_total,
            p.descripcion AS bio_gamificada,
            p.foto_perfil
        FROM alumnos a
        LEFT JOIN diagnostico_usuario d ON a.id_alumno = d.id_alumno
        LEFT JOIN perfil_gamificado p ON a.id_alumno = p.id_alumno
        WHERE a.id_alumno = :id_alumno
        ORDER BY d.id_diagnostico DESC
        LIMIT 1
    ";

    $stmt = $conexion->prepare($sql_perfil);
    $stmt->bindParam(':id_alumno', $id_alumno, PDO::PARAM_INT);
    $stmt->execute();
    $datos_perfil = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($datos_perfil) {
        if (!empty($datos_perfil['id_semestre'])) $semestre = $datos_perfil['id_semestre'];
        
        if (!empty($datos_perfil['id_grupo'])) {
            switch ($datos_perfil['id_grupo']) {
                case 1:  $grupo = "E"; break;
                case 2:  $grupo = "F"; break;
                case 3:  $grupo = "G"; break; 
                default: $grupo = "G"; break; 
            }
        }
        
        if (isset($datos_perfil['xp_total'])) $xp_actual = $datos_perfil['xp_total'];
        
        // Cargar descripción desde perfil_gamificado si existe
        if (!empty($datos_perfil['bio_gamificada'])) {
            $descripcion = $datos_perfil['bio_gamificada'];
        }

        if (!empty($datos_perfil['id_nivel'])) {
            switch ($datos_perfil['id_nivel']) {
                case 1:  $nivel_real = "Principiante 🚀"; break;
                case 2:  $nivel_real = "Intermedio 🛰️"; break;
                case 3:  $nivel_real = "Avanzado 🌌"; break; 
                default: $nivel_real = "Principiante"; break;
            }
        }

        // 🌟 CORRECCIÓN 1 y 2: Asignación correcta utilizando $datos_perfil y el campo real de la tabla
        if (!empty($datos_perfil['foto_perfil'])) {
            $foto_usuario = $datos_perfil['foto_perfil'];
        }
    }

    // 2. Obtener las notas guardadas en la BD para este alumno
    $sql_notas = "SELECT * FROM notas_alumno WHERE id_alumno = :id_alumno ORDER BY id_nota DESC";
    $stmt_notas = $conexion->prepare($sql_notas);
    $stmt_notas->bindParam(':id_alumno', $id_alumno, PDO::PARAM_INT);
    $stmt_notas->execute();
    $lista_notas = $stmt_notas->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Error de base de datos: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil Estelar - CodeLand DPS</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="Style_CL.css">
    <link rel="stylesheet" href="perfil.css">
</head>
<body class="body-perfil-instagram">

    <header class="navbar">
        <div class="logo"><span class="azul">CodeLand</span><span class="verde">DPS</span></div>
        <nav class="menu">
            <a href="mundoI.php">Inicio</a>
            <a href="perfil.php" class="active">Mi Perfil</a>
            <a href="logout.php" class="btn-logout-nav">Cerrar Sesión</a>
        </nav>                  
    </header>

    <div class="linea"></div>

    <div class="perfil-insta-container">        
        <header class="insta-header">
            <div class="insta-avatar-wrapper">
                <div class="avatar-marco">
                    <img src="<?php echo $foto_usuario; ?>" alt="Avatar" id="fotoPerfilVista" onerror="this.src='astronauta_selfie.png';" class="avatar-imagen">
                </div>
                <button class="btn-cambiar-avatar" onclick="abrirSelectorFoto()">📷 Cambiar Foto</button>
                <input type="file" id="inputFotoOculto" accept="image/jpeg, image/jpg, image/png" style="display: none;" onchange="subirFotoPerfilAlServidor()">
            </div>

            <div class="insta-stats-info">
                <div class="insta-username-row">
                    <h2 id="nombreUsuario"><?php echo htmlspecialchars($nombre_usuario); ?></h2>
                    <span class="badge-estado-insta estado-activo">● <?php echo htmlspecialchars($estado); ?></span>
                </div>

                <div class="stats-grid-row">
                    <div class="stat-box">
                        <span class="stat-number"><?php echo htmlspecialchars($semestre); ?>° Semestre</span>
                        <span class="stat-label">Grupo <?php echo htmlspecialchars($grupo); ?></span>
                    </div>
                    <div class="stat-box">
                        <span class="stat-number azul-neon"><?php echo htmlspecialchars($nivel_real); ?></span>
                        <span class="stat-label">Rango de Vuelo</span>
                    </div>
                    <div class="stat-box">
                        <span class="stat-number verde-neon"><?php echo htmlspecialchars($xp_actual); ?> XP</span>
                        <span class="stat-label">Energía Acumulada</span>
                    </div>
                </div>

                <div class="insta-biografia">
                    <span class="bio-username"><?php echo htmlspecialchars($nombre_usuario); ?></span>
                    <p id="vistaDescripcionText"><?php echo htmlspecialchars($descripcion); ?></p>
                    <button class="btn-editar-bio" onclick="conmutarEditorBio()">✏️ Editar Estado</button>
                    
                    <div id="editorBioBox" style="display: none; margin-top: 10px;">
                        <textarea id="txtDescripcion" class="textarea-insta"><?php echo htmlspecialchars($descripcion); ?></textarea>
                        <button class="btn-guardar-insta" onclick="guardarDescripcion()">Guardar Cambios</button>
                    </div>
                    <p id="msgFiltroAlerta" class="error-msg" style="display:none;"></p>
                </div>
            </div>
        </header>

        <main class="seccion-notas-estudiante">
            <div class="notas-header">
                <h3>📌 Notas Estelares de la Misión</h3>
                <p>Gestiona tus recordatorios y apuntes privados directamente en los servidores de la academia.</p>
            </div>

            <div class="agregar-nota-form" style="margin-bottom: 20px; display: flex; gap: 10px; flex-wrap: wrap;">
                <input type="text" id="nuevaNotaTexto" placeholder="Ej: Estudiar llaves foráneas..." style="flex: 1; padding: 10px; border-radius: 8px; border: 1px solid #334155; background: #0f172a; color: white;">
                <input type="date" id="nuevaNotaFecha" value="<?php echo date('Y-m-d'); ?>" style="padding: 10px; border-radius: 8px; border: 1px solid #334155; background: #0f172a; color: white;">
                <button onclick="crearNotaServidor()" style="background: #10b981; color: white; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; font-weight: 600;">+ Agregar</button>
            </div>

            <div id="contenedorNotasLista" style="display: flex; flex-direction: column; gap: 12px;">
                <?php if (empty($lista_notas)): ?>
                    <p id="sinNotasMsg" style="color: #64748b; font-style: italic;">No tienes notas pendientes en este sector de la galaxia.</p>
                <?php else: ?>
                    <?php foreach ($lista_notas as $nota): ?>
                        <div class="tarjeta-nota <?php echo $nota['completada'] ? 'nota-completada' : ''; ?>" id="nota_<?php echo $nota['id_nota']; ?>" style="background: #1e293b; padding: 15px; border-radius: 8px; border-left: 5px solid <?php echo $nota['completada'] ? '#10b981' : '#38bdf8'; ?>; display: flex; justify-content: space-between; align-items: center; gap: 15px;">
                            <div style="flex: 1;">
                                <p class="texto-nota-render" style="margin: 0; color: #f8fafc; font-size: 14px; text-decoration: <?php echo $nota['completada'] ? 'line-through' : 'none'; ?>; color: <?php echo $nota['completada'] ? '#64748b' : '#f8fafc'; ?>;">
                                    <?php echo htmlspecialchars($nota['contenido']); ?>
                                </p>
                                <small style="color: #94a3b8; font-size: 11px;">📅 Recordatorio: <?php echo htmlspecialchars($nota['fecha_recordatorio']); ?></small>
                            </div>
                            <div style="display: flex; gap: 8px;">
                                <button onclick="conmutarCheckNota(<?php echo $nota['id_nota']; ?>, <?php echo $nota['completada'] ? '0' : '1'; ?>)" style="background: none; border: none; cursor: pointer; font-size: 16px;"><?php echo $nota['completada'] ? '🔄' : '✅'; ?></button>
                                <button onclick="eliminarNotaServidor(<?php echo $nota['id_nota']; ?>)" style="background: none; border: none; cursor: pointer; font-size: 16px;">❌</button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <script src="perfil_alumno.js"></script>
</body>
</html>