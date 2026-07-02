<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include("conexion.php");

if($_SERVER["REQUEST_METHOD"] == "POST"){

    $nombre = $_POST['nombre'];
    $correo = trim($_POST['correo']);
    $password = $_POST['password'];
    $confirmar_password = $_POST['confirmar_password'];
    $semestre = $_POST['semestre'];
    $grupo = $_POST['grupo'];

    /* VALIDAR PASSWORD */
    if($password != $confirmar_password){
        die("Las contraseñas no coinciden");
    }

    /* ENCRIPTAR PASSWORD */
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    try {
        /* VERIFICAR CORREO DUPLICADO EN BD */
        $sqlVerificar = "SELECT correo FROM usuarios WHERE correo = :correo";
        $stmtVerificar = $conexion->prepare($sqlVerificar);
        $stmtVerificar->bindParam(':correo', $correo);
        $stmtVerificar->execute();

        if($stmtVerificar->rowCount() > 0){
            die("Este correo ya está registrado.");
        }

        /* INSERTAR USUARIO */
        $sqlUsuario = "INSERT INTO usuarios (nombre, correo, password) VALUES (:nombre, :correo, :password)";
        $stmtUsuario = $conexion->prepare($sqlUsuario);
        $stmtUsuario->bindParam(':nombre', $nombre);
        $stmtUsuario->bindParam(':correo', $correo);
        $stmtUsuario->bindParam(':password', $passwordHash);

        if($stmtUsuario->execute()){
            $id_usuario = $conexion->lastInsertId();

            /* INSERTAR ALUMNO */
            $sqlAlumno = "INSERT INTO alumnos (id_usuario, id_semestre, id_grupo) VALUES (:id_usuario, :semestre, :grupo)";
            $stmtAlumno = $conexion->prepare($sqlAlumno);
            $stmtAlumno->bindParam(':id_usuario', $id_usuario);
            $stmtAlumno->bindParam(':semestre', $semestre);
            $stmtAlumno->bindParam(':grupo', $grupo);

            if($stmtAlumno->execute()){
                header("Location: InicioSesion.html");
                exit();
            } else {
                echo "Error al insertar alumno";
            }
        } else {
            echo "Error al insertar usuario";
        }

    } catch (PDOException $e) {
        echo "Error en la base de datos: " . $e->getMessage();
    }
}
?>