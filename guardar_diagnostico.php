<?php
session_start(); 

header("Access-Control-Allow-Origin: http://localhost");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST");

include("conexion.php");

if(!isset($_SESSION['id_alumno'])){
    die("Error: No se detectó una sesión activa de estudiante.");
}

$id_alumno = $_SESSION['id_alumno']; 

$json_data = file_get_contents("php://input");
$datos = json_decode($json_data, true);

if (!$datos) {
    die("Error: No se recibieron datos válidos en el servidor.");
}

$puntaje = $datos['puntaje']; 
$nivel_texto = $datos['nivel']; 

$id_nivel = 1; 
if ($nivel_texto === "Intermedio") {
    $id_nivel = 2; 
} elseif ($nivel_texto === "Avanzado") {
    $id_nivel = 3; 
}

$sql_insert = "
    INSERT INTO diagnostico_usuario (id_alumno, puntaje, id_nivel, fecha_realizacion) 
    VALUES (:id_alumno, :puntaje, :id_nivel, NOW())
";

$stmt = $conexion->prepare($sql_insert);
$stmt->bindParam(':id_alumno', $id_alumno);
$stmt->bindParam(':puntaje', $puntaje);
$stmt->bindParam(':id_nivel', $id_nivel); 

// Se corrigió eliminando la duplicidad del execute() anterior
if($stmt->execute()){
    echo "Resultado guardado con éxito|id:" . $_SESSION['id_alumno'];
} else {
    $error = $stmt->errorInfo();
    echo "Error al guardar el diagnóstico: " . $error[2];
}
?>