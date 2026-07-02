<?php
session_start();
header("Content-Type: application/json");

// Permitir peticiones asíncronas desde el entorno local compartiendo cookies de sesión
header("Access-Control-Allow-Origin: http://localhost");
header("Access-Control-Allow-Credentials: true");

// ❌ ANTES: 'conexion.php' | ✔️ AHORA: '../conexion.php' (Sale de api/ a la raíz)
require_once '../conexion.php';

if (!isset($_SESSION['id_alumno'])) {
    echo json_encode(["success" => false, "error" => "No se detectó una sesión estelar activa."]);
    exit();
}

$id_alumno = $_SESSION['id_alumno'];
$datos = json_decode(file_get_contents("php://input"), true);

if (!$datos || !isset($datos['xp_ganada']) || !isset($datos['id_leccion'])) {
    echo json_encode(["success" => false, "error" => "Datos de telemetría incompletos o inválidos."]);
    exit();
}

$xp_ganada = intval($datos['xp_ganada']);
$id_leccion = intval($datos['id_leccion']);

try {
    $conexion->beginTransaction();

    // Asegurar que el alumno tenga una fila base en la tabla gamificada por si es nuevo
    $sql_check = "SELECT COUNT(*) FROM perfil_gamificado WHERE id_alumno = :id_alumno";
    $stmt_check = $conexion->prepare($sql_check);
    $stmt_check->bindParam(':id_alumno', $id_alumno, PDO::PARAM_INT);
    $stmt_check->execute();

    if ($stmt_check->fetchColumn() == 0) {
        $sql_init = "INSERT INTO perfil_gamificado (id_alumno, xp_total, descripcion) VALUES (:id_alumno, 0, 'Explorando el espacio exterior en CodeLand DPS. 🚀✨')";
        $stmt_init = $conexion->prepare($sql_init);
        $stmt_init->bindParam(':id_alumno', $id_alumno, PDO::PARAM_INT);
        $stmt_init->execute();
    }

    // 1. OPERACIÓN ACUMULATIVA MATEMÁTICA REAL (Tu código existente)
    $sql = "UPDATE perfil_gamificado SET xp_total = xp_total + :xp_ganada WHERE id_alumno = :id_alumno";
    $stmt = $conexion->prepare($sql);
    $stmt->bindParam(':xp_ganada', $xp_ganada, PDO::PARAM_INT);
    $stmt->bindParam(':id_alumno', $id_alumno, PDO::PARAM_INT);
    $stmt->execute();

    // 🔥 LO NUEVO: ACTUALIZAR EL PROGRESO DE LA ESTACIÓN SI COMPLETÓ SU NIVEL ACTUAL
    // Calculamos cuál sería la siguiente lección
    $siguiente_leccion = $id_leccion + 1;

    // Actualizamos la tabla progreso_alumno solo si la lección completada es igual o mayor a su avance guardado
    $sql_progreso = "
        INSERT INTO progreso_alumno (id_alumno, estacion_actual) 
        VALUES (:id_alumno, :siguiente)
        ON DUPLICATE KEY UPDATE 
        estacion_actual = IF(:siguiente2 > estacion_actual, :siguiente3, estacion_actual)
    ";
    $stmt_progreso = $conexion->prepare($sql_progreso);
    $stmt_progreso->bindParam(':id_alumno', $id_alumno, PDO::PARAM_INT);
    $stmt_progreso->bindParam(':siguiente', $siguiente_leccion, PDO::PARAM_INT);
    $stmt_progreso->bindParam(':siguiente2', $siguiente_leccion, PDO::PARAM_INT);
    $stmt_progreso->bindParam(':siguiente3', $siguiente_leccion, PDO::PARAM_INT);
    $stmt_progreso->execute();

    // Confirmar cambios
    $conexion->commit();
    echo json_encode(["success" => true, "mensaje" => "¡XP acumulada y progreso actualizado de forma exitosa!"]);

} catch (PDOException $e) {
    if ($conexion->inTransaction()) {
        $conexion->rollBack();
    }
    echo json_encode(["success" => false, "error" => "Error de base de datos: " . $e->getMessage()]);
}
?>