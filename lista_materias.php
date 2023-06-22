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

// Función para obtener la lista de materias desde la base de datos
function obtenerMaterias($conn) {
    $stmt = $conn->prepare('SELECT * FROM Materias');
    $stmt->execute();
    $result = $stmt->get_result();
    $materias = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    return $materias;
}

// Función para obtener los datos de una materia específica
function obtenerMateria($conn, $id) {
    $stmt = $conn->prepare('SELECT * FROM Materias WHERE ID = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $materia = $result->fetch_assoc();
    $stmt->close();

    return $materia;
}

// Función para agregar una materia a la base de datos
function agregarMateria($conn, $nombre, $maestro_id) {
    $stmt = $conn->prepare('INSERT INTO Materias (nombre, maestro_id) VALUES (?, ?)');
    $stmt->bind_param('ss', $nombre, $maestro_id);
    $stmt->execute();
    $stmt->close();

    // Redireccionar después de agregar la materia
    header('Location: lista_materias.php');
    exit;
}

// Función para actualizar una materia en la base de datos
function actualizarMateria($conn, $id, $nombre, $maestro_id) {
    $stmt = $conn->prepare('UPDATE Materias SET nombre=?, maestro_id=? WHERE ID=?');
    $stmt->bind_param('ssi', $nombre, $maestro_id, $id);
    $stmt->execute();
    $stmt->close();

    // Redireccionar después de actualizar la materia
    header('Location: lista_materias.php');
    exit;
}

// Función para eliminar una materia de la base de datos
function eliminarMateria($conn, $id) {
    $stmt = $conn->prepare('DELETE FROM Materias WHERE ID=?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();

    // Redireccionar después de eliminar la materia
    header('Location: lista_materias.php');
    exit;
}

// Obtener la lista de materias desde la base de datos
$materias = obtenerMaterias($conn);

// Manejar las solicitudes POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verificar si se envió el formulario de agregar materia
    if (isset($_POST['agregar_materia_modal'])) {
        $nombre = $_POST['nombre'];
        $maestro_id = $_POST['maestro_id'];

        agregarMateria($conn, $nombre, $maestro_id);

        // Actualizar la lista de materias después de agregar una nueva
        $materias = obtenerMaterias($conn);
    }

    // Verificar si se envió el formulario de editar materia
    if (isset($_POST['editar_materia'])) {
        $id = $_POST['id'];
        $nombre = $_POST['nombre'];
        $maestro_id = $_POST['maestro_id'];

        actualizarMateria($conn, $id, $nombre, $maestro_id);
    }

    // Verificar si se envió el formulario de eliminar materia
    if (isset($_POST['eliminar_materia'])) {
        $id = $_POST['id'];

        eliminarMateria($conn, $id);
    }
}

// Cerrar la conexión
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Lista de Materias</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
</head>
<body>
    <div class="container">
        <h1 class="mt-4">Lista de Materias</h1>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#agregarModal">
            Agregar Materia
        </button>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Maestro Asignado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($materias as $materia): ?>
                    <tr>
                        <td><?php echo $materia['ID']; ?></td>
                        <td><?php echo $materia['nombre']; ?></td>
                        <td><?php echo $materia['maestro_id']; ?></td>
                        <td>
                            <form method="POST" action="">
                                <input type="hidden" name="id" value="<?php echo $materia['ID']; ?>">
                                <input type="hidden" name="nombre" value="<?php echo $materia['nombre']; ?>">
                                <input type="hidden" name="maestro_id" value="<?php echo $materia['maestro_id']; ?>">
                                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editarModal<?php echo $materia['ID']; ?>">
                                    Editar
                                </button>
                                <button type="submit" name="eliminar_materia" class="btn btn-danger btn-sm">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                    <!-- Modal para editar materia -->
                    <div class="modal fade" id="editarModal<?php echo $materia['ID']; ?>" tabindex="-1" aria-labelledby="editarModalLabel<?php echo $materia['ID']; ?>" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editarModalLabel<?php echo $materia['ID']; ?>">Editar Materia</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form method="POST" action="">
                                        <input type="hidden" name="id" value="<?php echo $materia['ID']; ?>">
                                        <div class="mb-3">
                                            <label class="form-label">Nombre:</label>
                                            <input type="text" name="nombre" value="<?php echo $materia['nombre']; ?>" required class="form-control">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Maestro Asignado:</label>
                                            <input type="text" name="maestro_id" value="<?php echo $materia['maestro_id']; ?>" required class="form-control">
                                        </div>
                                        <button type="submit" name="editar_materia" class="btn btn-primary">Guardar Cambios</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Modal para agregar materia -->
        <div class="modal fade" id="agregarModal" tabindex="-1" aria-labelledby="agregarModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="agregarModalLabel">Agregar Materia</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label class="form-label">Nombre:</label>
                                <input type="text" name="nombre" required class="form-control">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Maestro Asignado:</label>
                                <input type="text" name="maestro_id" required class="form-control">
                            </div>
                            <button type="submit" name="agregar_materia_modal" class="btn btn-primary">Agregar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <p class="mt-4"><a href="dashboard.php">Volver al Dashboard</a></p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
