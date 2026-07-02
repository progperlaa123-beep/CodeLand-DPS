<?php
// 1. Mantener la sesión activa para reconocer al alumno
session_start();

// Redirección preventiva si no hay una sesión válida
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'alumno') {
    header("Location: login.php");
    exit();
}

// 📁 CONEXIÓN EN LA RAÍZ: Tu archivo original de producción
require_once 'conexion.php';
$id_alumno = $_SESSION['id_alumno'];

// Variables de interfaz por defecto
$nivel_texto = "Principiante";
$estacion_de_arranque = 1; 

try {
    // Buscar el id_nivel del último diagnóstico realizado por este alumno
    $sql_nivel = "
        SELECT id_nivel 
        FROM diagnostico_usuario 
        WHERE id_alumno = :id_alumno
        ORDER BY fecha_realizacion DESC 
        LIMIT 1
    ";
    $stmt_lvl = $conexion->prepare($sql_nivel);
    $stmt_lvl->bindParam(':id_alumno', $id_alumno, PDO::PARAM_INT);
    $stmt_lvl->execute();
    $resultado = $stmt_lvl->fetch(PDO::FETCH_ASSOC);

    if ($resultado) {
        $id_nivel_real = intval($resultado['id_nivel']);
        
        // Asignación base por diagnóstico
        if ($id_nivel_real === 3) {
            $nivel_texto = "Avanzado 🌌 (Nivel máximo detectado, saltas a fases de optimización.)";
            $estacion_de_arranque = 21;
        } elseif ($id_nivel_real === 2) {
            $nivel_texto = "Intermedio 🚀 (Ya tienes base, avanzarás más rápido.)";
            $estacion_de_arranque = 11;
        } else {
            $nivel_texto = "Principiante 👨‍🚀";
            $estacion_de_arranque = 1;
        }
    }

    // 🌟 REPARACIÓN AQUÍ: Consultar si ya existe un progreso guardado en la base de datos
    $sql_progreso = "SELECT estacion_actual FROM progreso_alumno WHERE id_alumno = :id_alumno LIMIT 1";
    $stmt_p = $conexion->prepare($sql_progreso);
    $stmt_p->bindParam(':id_alumno', $id_alumno, PDO::PARAM_INT);
    $stmt_p->execute();
    $res_p = $stmt_p->fetch(PDO::FETCH_ASSOC);

    if ($res_p) {
        // Si ya completó lecciones, usamos el valor dinámico de su progreso real
        $estacion_de_arranque = intval($res_p['estacion_actual']);
    } else {
        // Si no tiene registro en progreso_alumno, creamos su punto de partida inicial
        $sql_ins = "INSERT INTO progreso_alumno (id_alumno, estacion_actual) VALUES (:id_alumno, :estacion)";
        $stmt_ins = $conexion->prepare($sql_ins);
        $stmt_ins->bindParam(':id_alumno', $id_alumno, PDO::PARAM_INT);
        $stmt_ins->bindParam(':estacion', $estacion_de_arranque, PDO::PARAM_INT);
        $stmt_ins->execute();
    }

} catch (PDOException $e) {
    $nivel_texto = "Principiante";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Módulo I - CodeLand</title>
    <link rel="stylesheet" href="modI_Style.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap">
</head>
<body>
    <script>
        // ✔️ Forzamos a que imprima numéricamente la estación real guardada en la BD
        const ZONA_ALUMNO_REAL = <?php echo intval($estacion_de_arranque); ?>;
        const ID_ALUMNO_SESSION = <?php echo json_encode($id_alumno); ?>;
        const TEXTO_NIVEL_REAL = <?php echo json_encode($nivel_texto); ?>;
    </script>

    <nav class="navbar">
        <div class="logo">
            <span class="azul">CodeLand</span><span class="verde">DPS</span>
        </div>
        <div class="menu">
            <a href="perfil.php">Mi Perfil</a>
            <a href="logout.php" style="color: #ff4a4a;">Cerrar sesion</a>
        </div>
    </nav>

    <main class="contenedor-principal">
        
       <section id="pantalla-inicio" class="tarjeta-mision">
            <div class="contenedor-visual" style="margin-bottom: 20px;">
                <div class="capa-planeta"></div>
                <div class="capa-astronauta"></div>
            </div>
            
            <div class="info-mision">
                <h1 class="titulo-modulo">Módulo 1: Desarrolla software de aplicación con programación estructurada</h1>
                <div class="badge-contenedor">
                    <h2 id="nivelTexto" class="nivel-texto">Conectando con el servidor central de CodeLand...</h2>
                </div>
                
                <div>
                    <button class="btn-comenzar" onclick="iniciarRuta()">Comenzar misión</button>
                </div>
            </div>
        </section>

        <section id="mapa" class="mapa-interactivo oculto">
            <div class="mapa-header-navegacion">
                <button class="btn-regresar" onclick="regresarALanding()">⬅ Regresar al hangar</button>
                <h2>🗺️ Mapa de Sectores Estelares - Módulo I</h2>
            </div>

            <div class="mapa-nodos"></div>

            <div class="astronauta-guia-contenedor" id="astronautaGuia">
                <div class="cuadro-dialogo-espacial">
                    <h3 id="dialogo-titulo">Comandante DPS</h3>
                    <p id="dialogo-texto">Selecciona un sector desbloqueado en tu interfaz holográfica para sincronizar los objetivos de la micro-lección.</p>
                    
                    <button class="btn-comenzar" id="btnIniciarMision" style="display:none; margin-top: 15px; padding: 10px 20px; font-size: 14px; width: 100%;">Iniciar Microlección 🚀</button>
                </div>
                <div class="sprite-astronauta-animado"></div>
            </div>
        </section>

    </main>

    <script src="mundoI.js"></script>
</body>
</html>