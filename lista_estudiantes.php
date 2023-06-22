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

// Función para obtener la lista de estudiantes desde la base de datos
function obtenerEstudiantes($conn) {
    $stmt = $conn->prepare('SELECT * FROM Estudiantes');
    $stmt->execute();
    $result = $stmt->get_result();
    $estudiantes = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    return $estudiantes;
}

// Función para obtener los datos de un estudiante específico
function obtenerEstudiante($conn, $id) {
    $stmt = $conn->prepare('SELECT * FROM Estudiantes WHERE ID = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $estudiante = $result->fetch_assoc();
    $stmt->close();

    return $estudiante;
}

// Función para agregar un estudiante a la base de datos
function agregarEstudiante($conn, $nombre, $apellido, $correo, $direccion) {
    $stmt = $conn->prepare('INSERT INTO Estudiantes (nombre, apellido, correo, direccion) VALUES (?, ?, ?, ?)');
    $stmt->bind_param('ssss', $nombre, $apellido, $correo, $direccion);
    $stmt->execute();
    $stmt->close();

    // Redireccionar después de agregar el estudiante
    header('Location: lista_estudiantes.php');
    exit;
}

// Función para actualizar un estudiante en la base de datos
function actualizarEstudiante($conn, $id, $nombre, $apellido, $correo, $direccion) {
    $stmt = $conn->prepare('UPDATE Estudiantes SET nombre=?, apellido=?, correo=?, direccion=? WHERE ID=?');
    $stmt->bind_param('ssssi', $nombre, $apellido, $correo, $direccion, $id);
    $stmt->execute();
    $stmt->close();

    // Redireccionar después de actualizar el estudiante
    header('Location: lista_estudiantes.php');
    exit;
}

// Función para eliminar un estudiante de la base de datos
function eliminarEstudiante($conn, $id) {
    $stmt = $conn->prepare('DELETE FROM Estudiantes WHERE ID=?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();

    // Redireccionar después de eliminar el estudiante
    header('Location: lista_estudiantes.php');
    exit;
}

// Obtener la lista de estudiantes desde la base de datos
$estudiantes = obtenerEstudiantes($conn);

// Manejar las solicitudes POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verificar si se envió el formulario de agregar estudiante desde el modal
    if (isset($_POST['agregar_estudiante_modal'])) {
        $nombre = $_POST['nombre'];
        $apellido = $_POST['apellido'];
        $correo = $_POST['correo'];
        $direccion = $_POST['direccion'];

        agregarEstudiante($conn, $nombre, $apellido, $correo, $direccion);
    }

    // Verificar si se envió el formulario de editar estudiante
    if (isset($_POST['editar_estudiante'])) {
        $id = $_POST['id'];
        $nombre = $_POST['nombre'];
        $apellido = $_POST['apellido'];
        $correo = $_POST['correo'];
        $direccion = $_POST['direccion'];

        actualizarEstudiante($conn, $id, $nombre, $apellido, $correo, $direccion);
    }

    // Verificar si se envió el formulario de eliminar estudiante
    if (isset($_POST['eliminar_estudiante'])) {
        $id = $_POST['id'];

        eliminarEstudiante($conn, $id);
    }
}

// Cerrar la conexión
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Lista de Estudiantes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
</head>
<body>
    <div class="container">
        <h1 class="mt-4">Lista de Estudiantes</h1>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#agregarModal">
            Agregar Estudiante
        </button>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>Correo</th>
                    <th>Dirección</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($estudiantes as $estudiante): ?>
    <tr>
        <td><?php echo $estudiante['ID']; ?></td>
        <td><?php echo $estudiante['nombre']; ?></td>
        <td><?php echo $estudiante['apellido']; ?></td>
        <td><?php echo $estudiante['correo']; ?></td>
        <td><?php echo $estudiante['direccion']; ?></td>
        <td>
            <form method="POST" action="">
                <input type="hidden" name="id" value="<?php echo $estudiante['ID']; ?>">
                <input type="hidden" name="nombre" value="<?php echo $estudiante['nombre']; ?>">
                <input type="hidden" name="apellido" value="<?php echo $estudiante['apellido']; ?>">
                <input type="hidden" name="correo" value="<?php echo $estudiante['correo']; ?>">
                <input type="hidden" name="direccion" value="<?php echo $estudiante['direccion']; ?>">

                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editarModal<?php echo $estudiante['ID']; ?>">
                    Editar
                </button>
                <button type="submit" name="eliminar_estudiante" class="btn btn-danger btn-sm">Eliminar</button>
            </form>
        </td>
    </tr>
    <!-- Modal para editar estudiante -->
    <div class="modal fade" id="editarModal<?php echo $estudiante['ID']; ?>" tabindex="-1" aria-labelledby="editarModalLabel<?php echo $estudiante['ID']; ?>" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editarModalLabel<?php echo $estudiante['ID']; ?>">Editar Estudiante</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="">
                        <input type="hidden" name="id" value="<?php echo $estudiante['ID']; ?>">
                        <div class="mb-3">
                            <label class="form-label">Nombre:</label>
                            <input type="text" name="nombre" value="<?php echo $estudiante['nombre']; ?>" required class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Apellido:</label>
                            <input type="text" name="apellido" value="<?php echo $estudiante['apellido']; ?>" required class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Correo:</label>
                            <input type="email" name="correo" value="<?php echo $estudiante['correo']; ?>" required class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Dirección:</label>
                            <input type="text" name="direccion" value="<?php echo $estudiante['direccion']; ?>" required class="form-control">
                        </div>
                        <button type="submit" name="editar_estudiante" class="btn btn-primary">Guardar Cambios</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?>
            </tbody>
        </table>

        <!-- Modal para agregar estudiante -->
        <div class="modal fade" id="agregarModal" tabindex="-1" aria-labelledby="agregarModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="agregarModalLabel">Agregar Estudiante</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label class="form-label">Nombre:</label>
                                <input type="text" name="nombre" required class="form-control">
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
                            <button type="submit" name="agregar_estudiante_modal" class="btn btn-primary">Agregar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <p class="mt-4"><a href="dashboard.php">Volver al Dashboard</a></p>
    </div>
</body>
</html>
