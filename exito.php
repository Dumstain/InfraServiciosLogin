<?php
session_start();

// Si no está logueado, redirige al login
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit();
}

// Configuración de la conexión a la base de datos
$host = '10.0.0.2';
$port = 5432;
$dbname = 'mibd';
$dbuser = 'webuser';
$dbpassword = 'contra1234';  // Reemplaza con la contraseña real

$dsn = "pgsql:host=$host;port=$port;dbname=$dbname;";

try {
    // Conectar a PostgreSQL usando PDO
    $pdo = new PDO($dsn, $dbuser, $dbpassword, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    die("Conexión fallida: " . $e->getMessage());
}

// Recuperar los productos de la base de datos
try {
    $stmt = $pdo->query("SELECT * FROM productos");
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error al recuperar productos: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Productos</title>
</head>
<body>
    <h2>Lista de Productos</h2>
    <ul>
        <?php foreach ($productos as $producto): ?>
            <li>
                <?php echo htmlspecialchars($producto['nombre']) . " - $" . htmlspecialchars($producto['precio']) . " - Stock: " . htmlspecialchars($producto['stock']); ?>
            </li>
        <?php endforeach; ?>
    </ul>
    <p><a href="logout.php">Cerrar sesión</a></p>
</body>
</html>
