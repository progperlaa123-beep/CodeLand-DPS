<?php
// 1. Inicializamos la sesión actual para tener acceso a ella
session_start();

// 2. Limpiamos todas las variables superglobales de la sesión
$_SESSION = array();

// 3. Si se desea destruir la sesión completamente, también se deben borrar las cookies de sesión
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 4. Destruimos la sesión en el servidor
session_destroy();

// 5. Redirigimos al usuario a la página de inicio (index.php)
header("Location: index.php");
exit();
?>