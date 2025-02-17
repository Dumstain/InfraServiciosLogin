<?php
session_start();

// Si ya está logueado, redirige a exito.php
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    header("Location: exito.php");
    exit();
}

$error = "";

// Configuración de la conexión a la base de datos
$host = '10.0.0.2';      // IP del Servidor de Base de Datos
$port = 5432;
$dbname = 'mibd';
$dbuser = 'webuser';
$dbpassword = 'TU_CONTRASEÑA_AQUI';  // Reemplaza con la contraseña real

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $correo = $_POST['correo'];
    $contrasena = $_POST['contrasena'];

    try {
        // Conectar a PostgreSQL usando PDO
        $dsn = "pgsql:host=$host;port=$port;dbname=$dbname;";
        $pdo = new PDO($dsn, $dbuser, $dbpassword, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);

        // Iniciar una transacción
        $pdo->beginTransaction();

        // Preparar la consulta para obtener la contraseña del usuario
        $stmt = $pdo->prepare("SELECT password FROM users WHERE email = :correo");
        $stmt->execute([':correo' => $correo]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            // En este ejemplo, se compara directamente. 
            // Si usas contraseñas hasheadas, reemplaza con: if (password_verify($contrasena, $row['password']))
            if ($contrasena === $row['password']) {
                $_SESSION['loggedin'] = true;
                $pdo->commit();
                header("Location: exito.php");
                exit();
            } else {
                $error = "Credenciales inválidas. Intenta nuevamente.";
                $pdo->rollBack();
            }
        } else {
            $error = "Credenciales inválidas. Intenta nuevamente.";
            $pdo->rollBack();
        }
    } catch (PDOException $e) {
        $error = "Error en la conexión o en la transacción: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
</head>
<body>
    <h2>Iniciar Sesión</h2>
    <?php if ($error): ?>
        <p style="color:red;"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>
    <form method="post" action="login.php">
        <label for="correo">Correo:</label>
        <input type="email" id="correo" name="correo" required>
        <br><br>
        <label for="contrasena">Contraseña:</label>
        <input type="password" id="contrasena" name="contrasena" required>
        <br><br>
        <input type="submit" value="Ingresar">
    </form>
</body>
</html>
