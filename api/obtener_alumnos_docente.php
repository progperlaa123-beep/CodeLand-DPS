<?php
// api/obtener_alumnos_docente.php
session_start();
header("Content-Type: application/json");

// Validación estricta del rol docente
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'docente') {
    echo json_encode(["success" => false, "error" => "No autorizado. Inicie sesión como docente."]);
    exit();
}

require_once '../conexion.php';

try {
    /* 🌟 CONSULTA INTERCOMUNICADA CON LA TABLA 'USUARIOS' 
      Nota: Si en tu base de datos la columna de unión o de nombre se llama diferente,
      ajusta 'u.id_usuario = a.id_usuario' o 'u.nombre_completo'.
    */
    $sql = "
        SELECT 
            a.id_alumno,
            u.nombre AS nombre, 
            a.id_grupo,
            CASE 
                WHEN a.id_grupo = 1 THEN 'E'
                WHEN a.id_grupo = 2 THEN 'F'
                WHEN a.id_grupo = 3 THEN 'G'
                ELSE 'Sin Grupo'
            END AS grupo,
            a.id_semestre AS semestre,
            COALESCE(p.xp_total, 0) AS xp_total,
            -- Rango o Nivel obtenido en el diagnóstico interactivo
            COALESCE(
                (SELECT CASE 
                    WHEN d.id_nivel = 1 THEN 'Principiante 🚀'
                    WHEN d.id_nivel = 2 THEN 'Intermedio 🛰️'
                    WHEN d.id_nivel = 3 THEN 'Avanzado 🌌'
                    ELSE 'Principiante'
                 END 
                 FROM diagnostico_usuario d 
                 WHERE d.id_alumno = a.id_alumno 
                 ORDER BY d.id_diagnostico DESC LIMIT 1), 'Sin Diagnóstico'
            ) AS nivel,
            -- Lección actual en la que se encuentra navegando
            COALESCE(
                (SELECT MAX(id_leccion) FROM avance_lecciones al WHERE al.id_alumno = a.id_alumno), 0
            ) + 1 AS leccion_actual,
            -- Telemetría de inactividad
            COALESCE(
                DATEDIFF(NOW(), (SELECT MAX(fecha_completado) FROM avance_lecciones al WHERE al.id_alumno = a.id_alumno)),
                0
            ) AS dias_inactivo,
            -- Conteo de logs de errores del usuario
            COALESCE(
                (SELECT COUNT(*) FROM errores_navegacion en WHERE en.id_alumno = a.id_alumno), 0
            ) AS intentos_fallidos_promedio
        FROM alumnos a
        INNER JOIN usuarios u ON u.id_usuario = a.id_usuario
        LEFT JOIN perfil_gamificado p ON a.id_alumno = p.id_alumno
        ORDER BY u.nombre ASC
    ";

    $stmt = $conexion->prepare($sql);
    $stmt->execute();
    $alumnos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(["success" => true, "alumnos" => $alumnos]);

} catch (PDOException $e) {
    echo json_encode(["success" => false, "error" => "Error de base de datos: " . $e->getMessage()]);
}
?>