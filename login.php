<?php
// Inicializar variables de mensajes
$error = "";
$success = "";

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

// Procesar la solicitud POST para agregar un producto
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $precio = $_POST['precio'];
    $stock = $_POST['stock'];

    try {
        // Iniciar una transacción
        $pdo->beginTransaction();

        // Preparar y ejecutar la consulta para insertar el producto
        $stmt = $pdo->prepare("INSERT INTO productos (nombre, precio, stock) VALUES (:nombre, :precio, :stock)");
        $stmt->execute([
            ':nombre' => $nombre,
            ':precio' => $precio,
            ':stock'  => $stock
        ]);

        $pdo->commit();
        $success = "Producto agregado correctamente.";
    } catch (PDOException $e) {
        $pdo->rollBack();
        $error = "Error al agregar producto: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agregar Producto</title>
</head>
<body>
    <h2>Agregar Producto</h2>
    <?php if ($error): ?>
        <p style="color:red;"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>
    <?php if ($success): ?>
        <p style="color:green;"><?php echo htmlspecialchars($success); ?></p>
    <?php endif; ?>
    <form method="post" action="agregar_producto.php">
        <label for="nombre">Nombre:</label>
        <input type="text" id="nombre" name="nombre" required>
        <br><br>
        <label for="precio">Precio:</label>
        <input type="number" step="0.01" id="precio" name="precio" required>
        <br><br>
        <label for="stock">Stock:</label>
        <input type="number" id="stock" name="stock" required>
        <br><br>
        <input type="submit" value="Agregar Producto">
    </form>
    <p><a href="exito.php">Ver productos</a></p>
</body>
</html>
