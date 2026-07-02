<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 1. Iniciar la sesión inmediatamente en la primerísima línea
session_start();

// Incluimos tu archivo de conexión original
include("conexion.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Recibimos los datos desde InicioSesion.html
    $correo = trim($_POST['correo']);
    $password = $_POST['password'];

    // =======================================================
    // 1. AUTENTICACIÓN DE DOCENTES
    // =======================================================
    $sql_docente = "SELECT id_docente, nombre, password FROM docentes WHERE correo = :correo";
    $stmt_doc = $conexion->prepare($sql_docente);
    $stmt_doc->bindParam(':correo', $correo);
    $stmt_doc->execute();
    
    $docente = $stmt_doc->fetch(PDO::FETCH_ASSOC);

    if ($docente && password_verify($password, $docente['password'])) {
        $_SESSION['user_id'] = $docente['id_docente'];
        $_SESSION['nombre']  = $docente['nombre'];
        $_SESSION['rol']     = 'docente'; 

        header("Location: panel_docente.php");
        exit();
    }

    // =======================================================
    // 2. AUTENTICACIÓN DE ALUMNOS (Limpia y Corregida)
    // =======================================================
    $sql_alumno = "
        SELECT u.id_usuario, u.correo, u.password, u.nombre, a.id_alumno 
        FROM usuarios u
        INNER JOIN alumnos a ON u.id_usuario = a.id_usuario
        WHERE u.correo = :correo
    ";

    $stmt_al = $conexion->prepare($sql_alumno);
    $stmt_al->bindParam(':correo', $correo);
    $stmt_al->execute();

    $usuario = $stmt_al->fetch(PDO::FETCH_ASSOC);

    if ($usuario && password_verify($password, $usuario['password'])) {

        // Guardamos las variables de sesión reales
        $_SESSION['id_usuario'] = $usuario['id_usuario'];
        $_SESSION['id_alumno']  = $usuario['id_alumno']; 
        $_SESSION['nombre']     = $usuario['nombre']; // Columna 'nombre' de tu tabla usuarios
        $_SESSION['correo']     = $usuario['correo'];
        $_SESSION['rol']        = 'alumno'; 

        // Verificamos si este alumno ya tiene su registro en 'diagnostico_usuario'
        $sql_verificar = "
            SELECT COUNT(*) AS total 
            FROM diagnostico_usuario 
            WHERE id_alumno = :id_alumno
        ";
        
        $stmt_verificar = $conexion->prepare($sql_verificar);
        $stmt_verificar->bindParam(':id_alumno', $_SESSION['id_alumno'], PDO::PARAM_INT);
        $stmt_verificar->execute();
        
        $resultado = $stmt_verificar->fetch(PDO::FETCH_ASSOC);

        if ($resultado['total'] > 0) {
            // Ya hizo el diagnóstico, va al mapa estelar
            header("Location: mundoI.php"); 
            exit();
        } else {
            // Alumno nuevo sin test, va al cuestionario diagnóstico
            header("Location: Cuestionario_Diagnostico.html");
            exit();
        }

    } else {
        echo "
        <script>
            alert('Correo o contraseña incorrectos');
            window.location = 'InicioSesion.html';
        </script>
        ";
        exit();
    }
}
?>