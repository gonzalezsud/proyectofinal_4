<?php
session_start();

// Verificar si el usuario ha iniciado sesión y obtener el rol
if (!isset($_SESSION['loggedin'])) {
    header('Location: login.php');
    exit;
}

$rol = $_SESSION['rol'];

// Verificar si el usuario tiene el rol de estudiante para acceder a esta página
if ($rol !== 'estudiante') {
    header('Location: dashboard.php');
    exit;
}

// Incluir el archivo de configuración de la base de datos (dbconfig.php)
require 'dbconfig.php';

// Función para obtener la lista de materias disponibles
function obtenerMateriasDisponibles($conn) {
    $stmt = $conn->prepare('SELECT * FROM Materias');
    $stmt->execute();
    $result = $stmt->get_result();
    $materias = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    return $materias;
}

// Función para obtener las materias seleccionadas por el estudiante actual
function obtenerMateriasSeleccionadas($conn, $estudianteId) {
    $stmt = $conn->prepare('SELECT * FROM MateriaEstudiante WHERE estudiante_id = ?');
    $stmt->bind_param('i', $estudianteId);
    $stmt->execute();
    $result = $stmt->get_result();
    $materias = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    return $materias;
}

// Función para contar las materias seleccionadas por el estudiante actual
// function contarMateriasSeleccionadas($conn, $estudianteId) {
//     $stmt = $conn->prepare('SELECT COUNT(*) FROM MateriaEstudiante WHERE estudiante_id = ?');
//     $stmt->bind_param('i', $estudianteId);
//     $stmt->execute();
//     $stmt->bind_result($count);
//     $stmt->fetch();
//     $stmt->close();

//     return $count;
// }

// Función para agregar una materia seleccionada por el estudiante
function agregarMateriaSeleccionada($conn, $estudianteId, $materiaId) {
    $stmt = $conn->prepare('INSERT INTO MateriaEstudiante (estudiante_id, materia_id) VALUES (?, ?)');
    $stmt->bind_param('ii', $estudianteId, $materiaId);
    $stmt->execute();
    $stmt->close();
}

// Función para eliminar una materia seleccionada por el estudiante
function eliminarMateriaSeleccionada($conn, $estudianteId, $materiaId) {
    $stmt = $conn->prepare('DELETE FROM MateriaEstudiante WHERE estudiante_id = ? AND materia_id = ?');
    $stmt->bind_param('ii', $estudianteId, $materiaId);
    $stmt->execute();
    $stmt->close();
}

// Obtener el ID del estudiante actual desde la sesión
$estudianteId = $_SESSION['usuario_id'];

// Obtener la lista de materias disponibles
$materiasDisponibles = obtenerMateriasDisponibles($conn);

// Obtener las materias seleccionadas por el estudiante actual
$materiasSeleccionadas = obtenerMateriasSeleccionadas($conn, $estudianteId);

// Contar las materias seleccionadas por el estudiante actual
// $countMateriasSeleccionadas = contarMateriasSeleccionadas($conn, $estudianteId);

// Manejar las solicitudes POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verificar si se envió el formulario de selección de materias
    if (isset($_POST['seleccionar_materias'])) {
        // Obtener las materias seleccionadas del formulario
        $materiasSeleccionadas = isset($_POST['materias']) ? $_POST['materias'] : [];

        // Verificar la cantidad de materias seleccionadas
        $countMateriasSeleccionadas = count($materiasSeleccionadas);
        
        // Verificar si se ha seleccionado la cantidad correcta de materias
        if ($countMateriasSeleccionadas >= 5 && $countMateriasSeleccionadas <= 8) {
            // Eliminar las materias previamente seleccionadas por el estudiante
            $stmt = $conn->prepare('DELETE FROM MateriaEstudiante WHERE estudiante_id = ?');
            $stmt->bind_param('i', $estudianteId);
            $stmt->execute();
            $stmt->close();

            // Agregar las nuevas materias seleccionadas por el estudiante
            foreach ($materiasSeleccionadas as $materiaId) {
                agregarMateriaSeleccionada($conn, $estudianteId, $materiaId);
            }

            // Redireccionar a una página de éxito
            header('Location: seleccionar_materias.php?success=true');
            exit;
        } else {
            // Redireccionar a una página de error
            header('Location: seleccionar_materias.php?error=incorrect_count');
            exit;
        }
    }
}

// Cerrar la conexión
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Seleccionar Materias</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>

</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Seleccionar Materias</h1>

        <?php if (isset($_GET['success']) && $_GET['success'] === 'true'): ?>
            <div class="alert alert-success" role="alert">
                Materias seleccionadas con éxito.
            </div>
        <?php elseif (isset($_GET['error']) && $_GET['error'] === 'incorrect_count'): ?>
            <div class="alert alert-danger" role="alert">
                Selecciona entre 5 y 8 materias.
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <h2>Materias Disponibles</h2>
            <?php foreach ($materiasDisponibles as $materia): ?>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="materias[]" value="<?php echo $materia['ID']; ?>"
                    <?php if (in_array($materia['ID'], array_column($materiasSeleccionadas, 'materia_id'))) echo 'checked'; ?>>
                    <label class="form-check-label"><?php echo $materia['nombre']; ?></label>
                </div>
            <?php endforeach; ?>

            <p class="mt-3">
                <strong>Cantidad de materias seleccionadas:</strong> <?php echo $countMateriasSeleccionadas; ?>
            </p>

            <button type="submit" name="seleccionar_materias" class="btn btn-primary">Guardar Selección</button>
        </form>

        <p class="mt-3"><a href="dashboard.php" class="btn btn-secondary">Volver al Dashboard</a></p>
    </div>

</body>
</html>
