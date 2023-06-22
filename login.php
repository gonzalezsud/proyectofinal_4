<?php
session_start();
require 'dbconfig.php';

if (isset($_SESSION['loggedin'])) {
    header('Location: dashboard.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = $_POST['correo'];
    $contraseña = $_POST['contraseña'];

    $stmt = $conn->prepare('SELECT ID, nombre, apellido, rol FROM Usuarios WHERE correo = ? AND contraseña = ?');
    $stmt->bind_param('ss', $correo, $contraseña);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($userID, $nombre, $apellido, $rol);
        $stmt->fetch();

        $_SESSION['loggedin'] = true;
        $_SESSION['userID'] = $userID;
        $_SESSION['nombre'] = $nombre;
        $_SESSION['apellido'] = $apellido;
        $_SESSION['rol'] = $rol;

        header('Location: dashboard.php');
        exit;
    } else {
        $errorMensaje = 'Correo o contraseña incorrectos.';
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Iniciar sesión</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>  
    <style>
        body {
            background-color: #fff5d2; /* Color de fondo */
        }
        .container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }
        .logo {
            width: 350px; /* Ajusta el tamaño del logo */
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <img src="./assets/img/logo.jpg" alt="Logo" class="logo">
        <h1 class="mt-5">Iniciar sesión</h1>

        <?php if (isset($errorMensaje)): ?>
            <div class="alert alert-danger"><?php echo $errorMensaje; ?></div>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <div class="mb-3">
                <label for="correo" class="form-label">Correo:</label>
                <input type="email" id="correo" name="correo" required class="form-control">
            </div>

            <div class="mb-3">
                <label for="contraseña" class="form-label">Contraseña:</label>
                <input type="password" id="contraseña" name="contraseña" required class="form-control">
            </div>

            <button type="submit" class="btn btn-primary">Iniciar sesión</button>
        </form>

        <p class="mt-3">No tienes una cuenta? <a href="registro.php">Regístrate</a></p>
    </div>

</body>
</html>