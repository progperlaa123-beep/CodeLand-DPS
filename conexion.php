<?php
if ($_SERVER['SERVER_NAME'] == 'localhost' || $_SERVER['SERVER_ADDR'] == '127.0.0.1') {
    // Credenciales para tu VSC / XAMPP local
    $host = 'localhost';
    $dbname = 'codeland_dps'; 
    $username = 'root';
    $password = '';
} else {
    // Credenciales para InfinityFree (Producción)
    $host = "sql300.infinityfree.com";
    $dbname = "if0_42037036_codeland_dps";
    $username = "if0_42037036";
    $password = "nancyrios123";
} 

try {
    // Un solo bloque de conexión usando las variables correctas
    $conexion = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>