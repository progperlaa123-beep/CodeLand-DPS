<?php
session_start();
header("Content-Type: application/json");

if(isset($_SESSION['id_alumno'])){
    echo json_encode(["id_alumno" => $_SESSION['id_alumno']]);
} else {
    echo json_encode(["id_alumno" => null]);
}
?>