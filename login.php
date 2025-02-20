<?php
// Configuración de conexión a la base de datos
$host = '10.0.0.2';
$port = 5432;
$dbname = 'mibd';
$dbuser = 'webuser';
$dbpassword = 'contra1234';  // Asegúrate de usar la contraseña correcta

$dsn = "pgsql:host=$host;port=$port;dbname=$dbname;";

try {
    // Conectar a PostgreSQL usando PDO
    $pdo = new PDO($dsn, $dbuser, $dbpassword, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    die("Conexión fallida: " . $e->getMessage());
}

// Procesar la solicitud POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Iniciar una transacción
        $pdo->beginTransaction();

        if (isset($_POST["agregar"])) {
            $nombre = $_POST["nombre"];
            $precio = $_POST["precio"];
            $stock = $_POST["stock"];

            // Uso de consulta preparada para evitar inyección SQL
            $stmt = $pdo->prepare("INSERT INTO productos (nombre, precio, stock) VALUES (:nombre, :precio, :stock)");
            $stmt->execute([
                ':nombre' => $nombre,
                ':precio' => $precio,
                ':stock'  => $stock
            ]);
        } elseif (isset($_POST["eliminar"])) {
            $id = $_POST["id"];

            $stmt = $pdo->prepare("DELETE FROM productos WHERE id = :id");
            $stmt->execute([
                ':id' => $id
            ]);
        }
        $pdo->commit();
    } catch (PDOException $e) {
        $pdo->rollBack();
        die("Error en la transacción: " . $e->getMessage());
    }
}

// Obtener los productos de la base de datos
$stmt = $pdo->query("SELECT * FROM productos");
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Tienda Skyvel</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f2f5;
            text-align: center;
        }
        h1 {
            color: #1877f2;
        }
        form {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            display: inline-block;
            margin-bottom: 20px;
        }
        input, button {
            margin: 10px;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        button {
            background-color: #1877f2;
            color: white;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background-color: #155db2;
        }
        ul {
            list-style: none;
            padding: 0;
        }
        li {
            background: white;
            margin: 10px auto;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 300px;
        }
        form.inline {
            display: inline;
        }
    </style>
</head>
<body>
    <h1>Tienda Skyvel</h1>
    <form method="POST">
        <input name="nombre" placeholder="Nombre" required>
        <input name="precio" placeholder="Precio" type="number" required>
        <input name="stock" placeholder="Stock" type="number" required>
        <button type="submit" name="agregar">Agregar</button>
    </form>
    <ul>
        <?php foreach ($productos as $row) { ?>
            <li>
                <?php echo htmlspecialchars($row["nombre"]) . " - $" . htmlspecialchars($row["precio"]) . " - Stock: " . htmlspecialchars($row["stock"]); ?>
                <form method="POST" class="inline">
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($row['id']); ?>">
                    <button type="submit" name="eliminar">Eliminar</button>
                </form>
            </li>
        <?php } ?>
    </ul>
</body>
</html>
