<?php
session_start();

// Si no está logueado, redirige al login
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Éxito</title>
</head>
<body>
    <h2>¡Éxito al ingresar!</h2>
    <p>Bienvenido, has iniciado sesión correctamente.</p>
    <p><a href="logout.php">Cerrar sesión</a></p>
</body>
</html>
