<?php
// cambiar_foto.php (Ubicado en la raíz de tu proyecto)
session_start();
header("Content-Type: application/json");

require_once 'conexion.php';

if (!isset($_SESSION['id_alumno'])) {
    echo json_encode(["success" => false, "error" => "Sesión no válida."]);
    exit();
}

$id_alumno = $_SESSION['id_alumno'];

// Verificar si se envió un archivo
if (!isset($_FILES['foto_perfil']) || $_FILES['foto_perfil']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(["success" => false, "error" => "No se recibió ninguna imagen o hubo un error en la carga."]);
    exit();
}

$file = $_FILES['foto_perfil'];
$allowed_types = ['image/jpeg', 'image/jpg', 'image/png'];

if (!in_array($file['type'], $allowed_types)) {
    echo json_encode(["success" => false, "error" => "Formato no permitido. Solo se aceptan imágenes JPG, JPEG o PNG."]);
    exit();
}

if ($file['size'] > 2 * 1024 * 1024) {
    echo json_encode(["success" => false, "error" => "La imagen es muy pesada. El límite son 2MB."]);
    exit();
}

$extension = pathinfo($file['name'], PATHINFO_EXTENSION);
$nuevo_nombre = "avatar_user_" . $id_alumno . "_" . time() . "." . $extension;

// 🌟 SOLUCIÓN DEFINITIVA DE PERMISOS Y RUTA: Usamos __DIR__ para el servidor físico
$directorio_destino = __DIR__ . '/uploads/perfiles/';
$ruta_destino_completa = $directorio_destino . $nuevo_nombre;

// Intentar crear el directorio si no existe con permisos máximos de escritura
if (!is_dir($directorio_destino)) {
    if (!mkdir($directorio_destino, 0777, true)) {
        echo json_encode(["success" => false, "error" => "El servidor denegó la creación de la carpeta uploads/perfiles. Créala manualmente."]);
        exit();
    }
}

// Mover el archivo temporal a su destino final
if (move_uploaded_file($file['tmp_name'], $ruta_destino_completa)) {
    // La ruta relativa exacta que se guardará en la base de datos para renderizarse en el HTML
    $ruta_db = 'uploads/perfiles/' . $nuevo_nombre;

    try {
        $sql = "
            INSERT INTO perfil_gamificado (id_alumno, xp_total, foto_perfil) 
            VALUES (:id_alumno, 0, :foto_url_nueva)
            ON DUPLICATE KEY UPDATE 
            foto_perfil = :foto_url_update
        ";
        
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':id_alumno', $id_alumno, PDO::PARAM_INT);
        $stmt->bindParam(':foto_url_nueva', $ruta_db, PDO::PARAM_STR);
        $stmt->bindParam(':foto_url_update', $ruta_db, PDO::PARAM_STR);
        $stmt->execute();

        echo json_encode(["success" => true, "foto_url" => $ruta_db]);
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "error" => "Error al escribir en la BD: " . $e->getMessage()]);
    }
} else {
    // Si llegamos aquí, el servidor web rechazó mover el archivo temporal
    echo json_encode([
        "success" => false, 
        "error" => "Error de escritura: No se pudo guardar físicamente el archivo. Verifica los permisos de escritura de tu hosting."
    ]);
}
?>