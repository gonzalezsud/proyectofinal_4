<?php
session_start();

// Verificar si el usuario ha iniciado sesión y obtener el rol
if (!isset($_SESSION['loggedin'])) {
    header('Location: login.php');
    exit;
}

$rol = $_SESSION['rol'];

// Verificar si el usuario tiene permisos de administrador para acceder a esta página
if ($rol !== 'administrador') {
    header('Location: dashboard.php');
    exit;
}

// Incluir el archivo de configuración de la base de datos (dbconfig.php)
require 'dbconfig.php';

// Función para obtener la lista de maestros desde la base de datos
function obtenerMaestros($conn) {
    $stmt = $conn->prepare('SELECT * FROM Maestros');
    $stmt->execute();
    $result = $stmt->get_result();
    $maestros = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    return $maestros;
}

// Función para obtener los datos de un maestro específico
function obtenerMaestro($conn, $id) {
    $stmt = $conn->prepare('SELECT * FROM Maestros WHERE ID = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $maestro = $result->fetch_assoc();
    $stmt->close();

    return $maestro;
}

// Función para agregar un maestro a la base de datos
function agregarMaestro($conn, $nombre, $asignaturas, $apellido, $correo, $direccion) {
    $stmt = $conn->prepare('INSERT INTO Maestros (nombre, asignaturas, apellido, correo, direccion) VALUES (?, ?, ?, ?, ?)');
    $stmt->bind_param('sssss', $nombre, $asignaturas, $apellido, $correo, $direccion );
    $stmt->execute();
    $stmt->close();

    // Redireccionar después de agregar el maestro
    header('Location: lista_maestros.php');
    exit;

}

// Función para actualizar un maestro en la base de datos
function actualizarMaestro($conn, $id, $nombre, $asignaturas, $apellido, $correo, $direccion) {
    $stmt = $conn->prepare('UPDATE Maestros SET nombre=?, asignaturas=?, apellido=?, correo=?, direccion=? WHERE ID=?');
    $stmt->bind_param('sssssi', $nombre, $asignaturas, $apellido, $correo, $direccion, $id);
    $stmt->execute();
    $stmt->close();

    // Redireccionar después de actualizar el maestro
    header('Location: lista_maestros.php');
    exit;
}

// Función para eliminar un maestro de la base de datos
function eliminarMaestro($conn, $id) {
    $stmt = $conn->prepare('DELETE FROM Maestros WHERE ID=?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();

    // Redireccionar después de eliminar el maestro
    header('Location: lista_maestros.php');
    exit;
}

// Obtener la lista de maestros desde la base de datos
$maestros = obtenerMaestros($conn);

// Manejar las solicitudes POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verificar si se envió el formulario de agregar maestro
    if (isset($_POST['agregar_maestro'])) {
        $nombre = $_POST['nombre'];
        $asignaturas = $_POST['asignaturas'];
        $apellido = $_POST['apellido'];
        $correo = $_POST['correo'];
        $direccion = $_POST['direccion'];

        agregarMaestro($conn, $nombre, $asignaturas, $apellido, $correo, $direccion);

        // Actualizar la lista de maestros después de agregar uno nuevo
        $maestros = obtenerMaestros($conn);
    }

    // Verificar si se envió el formulario de editar maestro
    if (isset($_POST['editar_maestro'])) {
        $id = $_POST['id'];
        $nombre = $_POST['nombre'];
        $asignaturas = $_POST['asignaturas'];
        $apellido = $_POST['apellido'];
        $correo = $_POST['correo'];
        $direccion = $_POST['direccion'];

        actualizarMaestro($conn, $id, $nombre, $asignaturas, $apellido, $correo, $direccion);
    }

    // Verificar si se envió el formulario de eliminar maestro
    if (isset($_POST['eliminar_maestro'])) {
        $id = $_POST['id'];

        eliminarMaestro($conn, $id);
    }
}

// Cerrar la conexión
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Lista de Maestros</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
</head>
<body>
    <div class="container">
        <h1 class="mt-4">Lista de Maestros</h1>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#agregarModal">
            Agregar Maestro
        </button>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Asignaturas</th>
                    <th>Apellido</th>
                    <th>Correo</th>
                    <th>Dirección</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($maestros as $maestro): ?>
                    <tr>
                        <td><?php echo $maestro['ID']; ?></td>
                        <td><?php echo $maestro['nombre']; ?></td>
                        <td><?php echo $maestro['asignaturas']; ?></td>
                        <td><?php echo $maestro['apellido']; ?></td>
                        <td><?php echo $maestro['correo']; ?></td>
                        <td><?php echo $maestro['direccion']; ?></td>

                        <td>
                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editarModal<?php echo $maestro['ID']; ?>">
                                Editar
                            </button>
                            <form method="POST" action="">
                                <input type="hidden" name="id" value="<?php echo $maestro['ID']; ?>">
                                <input type="hidden" name="nombre" value="<?php echo $maestro['nombre']; ?>">
                                <input type="hidden" name="asignaturas" value="<?php echo $maestro['asignaturas']; ?>">
                                <input type="hidden" name="apellido" value="<?php echo $maestro['apellido']; ?>">
                                <input type="hidden" name="correo" value="<?php echo $maestro['correo']; ?>">
                                <input type="hidden" name="direccion" value="<?php echo $maestro['direccion']; ?>">

                                <input type="submit" name="eliminar_maestro" value="Eliminar" class="btn btn-danger btn-sm">
                            </form>
                        </td>
                    </tr>

                    <!-- Modal para editar maestro -->
                    <div class="modal fade" id="editarModal<?php echo $maestro['ID']; ?>" tabindex="-1" aria-labelledby="editarModalLabel<?php echo $maestro['ID']; ?>" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editarModalLabel<?php echo $maestro['ID']; ?>">Editar Maestro</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form method="POST" action="">
                                        <input type="hidden" name="id" value="<?php echo $maestro['ID']; ?>">
                                        <div class="mb-3">
                                            <label class="form-label">Nombre:</label>
                                            <input type="text" name="nombre" value="<?php echo $maestro['nombre']; ?>" required class="form-control">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Asignaturas:</label>
                                            <input type="text" name="asignaturas" value="<?php echo $maestro['asignaturas']; ?>" required class="form-control">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Apellido:</label>
                                            <input type="text" name="apellido" value="<?php echo $maestro['apellido']; ?>" required class="form-control">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Correo:</label>
                                            <input type="email" name="correo" value="<?php echo $maestro['correo']; ?>" required class="form-control">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Dirección:</label>
                                            <input type="text" name="direccion" value="<?php echo $maestro['direccion']; ?>" required class="form-control">
                                        </div>
                                        <input type="submit" name="editar_maestro" value="Guardar Cambios" class="btn btn-primary">
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </tbody>
        </table>

        <p class="mt-4"><a href="dashboard.php">Volver al Dashboard</a></p>
    </div>

    <!-- Modal para agregar maestro -->
    <div class="modal fade" id="agregarModal" tabindex="-1" aria-labelledby="agregarModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="agregarModalLabel">Agregar Maestro</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label class="form-label">Nombre:</label>
                            <input type="text" name="nombre" required class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Asignaturas:</label>
                            <input type="text" name="asignaturas" required class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Apellido:</label>
                            <input type="text" name="apellido" required class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Correo:</label>
                            <input type="email" name="correo" required class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Dirección:</label>
                            <input type="text" name="direccion" required class="form-control">
                        </div>
                        <input type="submit" name="agregar_maestro" value="Agregar" class="btn btn-primary">
                    </form>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
