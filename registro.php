<?php
session_start();
require 'dbconfig.php';

if (isset($_SESSION['loggedin'])) {
    header('Location: dashboard.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $correo = $_POST['correo'];
    $direccion = $_POST['direccion'];
    $contraseña = $_POST['contraseña'];
    $rol = $_POST['rol'];

    $stmt = $conn->prepare('INSERT INTO Usuarios (nombre, apellido, correo, direccion, contraseña, rol) VALUES (?, ?, ?, ?, ?, ?)');
    $stmt->bind_param('ssssss', $nombre, $apellido, $correo, $direccion, $contraseña, $rol);
    $stmt->execute();

    $stmt->close();
    $conn->close();

    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Registro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>    
</head>
<body style="background-color: #fff5d2;">
    <div class="container d-flex align-items-center justify-content-center vh-100">
        <div class="card">
            <div class="card-body">
                <h1 class="card-title text-center">Registro</h1>

                <form method="POST" action="registro.php">
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre:</label>
                        <input type="text" id="nombre" name="nombre" required class="form-control">
                    </div>

                    <div class="mb-3">
                        <label for="apellido" class="form-label">Apellido:</label>
                        <input type="text" id="apellido" name="apellido" required class="form-control">
                    </div>

                    <div class="mb-3">
                        <label for="correo" class="form-label">Correo:</label>
                        <input type="email" id="correo" name="correo" required class="form-control">
                    </div>

                    <div class="mb-3">
                        <label for="direccion" class="form-label">Dirección:</label>
                        <input type="text" id="direccion" name="direccion" required class="form-control">
                    </div>

                    <div class="mb-3">
                        <label for="contraseña" class="form-label">Contraseña:</label>
                        <input type="password" id="contraseña" name="contraseña" required class="form-control">
                    </div>

                    <div class="mb-3">
                        <label for="rol" class="form-label">Rol:</label>
                        <select id="rol" name="rol" required class="form-select">
                            <option value="administrador">Administrador</option>
                            <option value="maestro">Maestro</option>
                            <option value="estudiante">Estudiante</option>
                        </select>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Registrarse</button>
                    </div>
                </form>

                <p class="mt-3 text-center">¿Ya tienes una cuenta? <a href="login.php" class="btn btn-link">Inicia sesión</a></p>
            </div>
        </div>
    </div>

    
</body>
</html>
