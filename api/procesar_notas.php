<?php
session_start();
header("Content-Type: application/json");
require_once '../conexion.php';

if (!isset($_SESSION['id_alumno'])) {
    echo json_encode(["success" => false, "error" => "Sesión no autorizada."]);
    exit();
}

$id_alumno = $_SESSION['id_alumno'];
$datos = json_decode(file_get_contents("php://input"), true);

if (!$datos || !isset($datos['accion'])) {
    echo json_encode(["success" => false, "error" => "Acción no válida."]);
    exit();
}

try {
    if ($datos['accion'] === 'crear') {
        $contenido = trim($datos['contenido']);
        $fecha = $datos['fecha'];
        
        if (empty($contenido)) {
            echo json_encode(["success" => false, "error" => "La nota no puede estar vacía."]);
            exit();
        }

        $sql = "INSERT INTO notas_alumno (id_alumno, contenido, fecha_recordatorio) VALUES (:id_alumno, :contenido, :fecha)";
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':id_alumno', $id_alumno, PDO::PARAM_INT);
        $stmt->bindParam(':contenido', $contenido, PDO::PARAM_STR);
        $stmt->bindParam(':fecha', $fecha, PDO::PARAM_STR);
        $stmt->execute();
        
        echo json_encode(["success" => true, "id_nota" => $conexion->lastInsertId()]);
        exit();
    }

    if ($datos['accion'] === 'completar') {
        $id_nota = intval($datos['id_nota']);
        $completada = intval($datos['completada']);

        $sql = "UPDATE notas_alumno SET completada = :completada WHERE id_nota = :id_nota AND id_alumno = :id_alumno";
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':completada', $completada, PDO::PARAM_INT);
        $stmt->bindParam(':id_nota', $id_nota, PDO::PARAM_INT);
        $stmt->bindParam(':id_alumno', $id_alumno, PDO::PARAM_INT);
        $stmt->execute();

        echo json_encode(["success" => true]);
        exit();
    }

    if ($datos['accion'] === 'eliminar') {
        $id_nota = intval($datos['id_nota']);

        $sql = "DELETE FROM notas_alumno WHERE id_nota = :id_nota AND id_alumno = :id_alumno";
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':id_nota', $id_nota, PDO::PARAM_INT);
        $stmt->bindParam(':id_alumno', $id_alumno, PDO::PARAM_INT);
        $stmt->execute();

        echo json_encode(["success" => true]);
        exit();
    }

} catch (PDOException $e) {
    echo json_encode(["success" => false, "error" => "Error de servidor: " . $e->getMessage()]);
}
?>