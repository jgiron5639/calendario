<?php
include 'conexion.php';
if (!isset($mysqli)) {
    die('Error: No se pudo establecer la conexión a la base de datos.');
}

// Obtener la fecha desde la URL
$fecha = isset($_GET['fecha']) ? $_GET['fecha'] : null;

if (!$fecha){
    die('Error: Fecha no especificada.');
}

// Verificar si existe un recordatorio para esa fecha
$query = "SELECT * FROM recordatorios WHERE fecha = '$fecha'";
$resultado = $mysqli->query($query);
$recordatorioExistente = $resultado->fetch_assoc(); //Obtener el recordatorio si existe

// Procesar el formulario si se ha enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST'){
    $recordatorio = $_POST['recordatorio'];

    if ($recordatorioExistente){
        // Actualizar el recordatorio existente
        $updateQuery = "UPDATE recordatorios SET recordatorio = ? WHERE fecha = ?";
        $stmt = $mysqli->prepare($updateQuery);
        $stmt->bind_param('ss', $recordatorio, $fecha);
        $stmt->execute();
        $mensaje = "Recordatorio actualizado exitosamente";
    }else{
        // Inserte un nuevo recordatorio
        $insertQuery = "INSERT INTO recordatorios (fecha, recordatorio) VALUES (?, ?)";
        $stmt = $mysqli->prepare($insertQuery);
        $stmt->bind_param('ss', $fecha, $recordatorio);
        $stmt->execute();
        $mensaje = "Recordatorio creado exitosamente";
    }
    // Recargar el recordatorio actualizado
    $query = "SELECT * FROM recordatorios WHERE fecha = '$fecha'";
    $resultado = $mysqli->query($query);
    $recordatorioExistente = $resultado->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recordatorio para <?php echo $fecha; ?></title>
</head>
<body>
    <h1>Agregar recordatorio para el fecha: <?php echo $fecha; ?></h1>

    <?php if (isset($mensaje)): ?>
        <p><?php echo $mensaje; ?></p>
    <?php endif; ?>
    <form method="POST">
        <label for="descripcion">Descripción del recordatorio:</label><br>
        <textarea name="recordatorio" id="descripcion" rows="5" cols="40" required><?php echo $recordatorioExistente ? $recordatorioExistente['recordatorio'] : ''; ?></textarea><br><br>
        <input type="submit" value="<?php echo $recordatorioExistente ? 'Actualizar Recordatorio' : 'Crear Recordatorio'; ?>">
    </form>
    <a href="index.php">Volver al calendario</a>
</body>
</html>