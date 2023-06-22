<?php
session_start();

// Aquí deberías incluir el archivo de configuración de la base de datos (dbconfig.php) y realizar la conexión
require 'dbconfig.php';

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Error en la conexión a la base de datos: " . $conn->connect_error);
}

// Obtener la lista de materias disponibles para los estudiantes
$stmt = $conn->prepare('SELECT * FROM Materias');
$stmt->execute();
$result = $stmt->get_result();

// Cerrar la conexión
$stmt->close();
$conn->close();
?>

<html>
<head>
    <title>Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
</head>
<body>
    <div class="container">
        <h2 class="mt-4">Bienvenido Estudiante</h2>
        <h3>Lista de Materias Disponibles</h3>
        <?php if ($result->num_rows > 0): ?>
            <form method="POST" action="seleccionar_materias.php">
                <p>Selecciona entre 5 y 8 materias:</p>

                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="materias[]" value="<?php echo $row['ID']; ?>">
                        <label class="form-check-label">
                            <?php echo $row['nombre']; ?>
                        </label>
                    </div>
                <?php endwhile; ?>

                <button type="submit" class="btn btn-primary mt-3">Seleccionar</button>
            </form>
        <?php else: ?>
            <p>No hay materias disponibles en este momento.</p>
        <?php endif; ?>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.7.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

