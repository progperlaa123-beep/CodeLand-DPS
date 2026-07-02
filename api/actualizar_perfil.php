<?php
session_start();
header("Content-Type: application/json");
require_once '../conexion.php';

if (!isset($_SESSION['id_alumno'])) {
    echo json_encode(["success" => false, "error" => "No has iniciado sesión."]);
    exit();
}

$id_alumno = $_SESSION['id_alumno'];
$datos = json_decode(file_get_contents("php://input"), true);

if (!isset($datos['descripcion'])) {
    echo json_encode(["success" => false, "error" => "Datos incompletos."]);
    exit();
}

$descripcion = trim($datos['descripcion']);

try {
    // Actualiza la biografía en la tabla gamificada
    $sql = "UPDATE perfil_gamificado SET descripcion = :descripcion WHERE id_alumno = :id_alumno";
    $stmt = $conexion->prepare($sql);
    $stmt->bindParam(':descripcion', $descripcion, PDO::PARAM_STR);
    $stmt->bindParam(':id_alumno', $id_alumno, PDO::PARAM_INT);
    $stmt->execute();

    echo json_encode(["success" => true]);
} catch (PDOException $e) {
    echo json_encode(["success" => false, "error" => $e->getMessage()]);
}
?>