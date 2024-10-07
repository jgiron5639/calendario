<?php
include 'conexion.php';
if (!isset($mysqli)) {
    die('Error: No se pudo establecer la conexión a la base de datos.');
}

// Definir el mes y año o el que se seleccione
$mes = isset($_GET['mes']) ? $_GET['mes'] : date('m');
$year = isset($_GET['year']) ? $_GET['year'] : date('Y');
//Obtener el número de días del mes
$diasDelmes = cal_days_in_month(CAL_GREGORIAN, $mes, $year);
// Obtener el primer día del mes (0 para domingo, 6 para sábado)
$primerDia = date('w', strtotime("$year-$mes-01"));
//Días de la semana
$diasDeLaSemana = ['Dom', 'Lun', 'Mar', 'Mie', 'Jue', 'Vie', 'Sab'];
// Calcular el mes anterior y siguiente
$prevMes = date('m', mktime(0, 0, 0, $mes - 1, 1, $year));
$prevYear = date('Y', mktime(0, 0, 0, $mes - 1, 1, $year));
$nextMes = date('m', mktime(0, 0, 0, $mes + 1, 1, $year));
$nextYear = date('Y', mktime(0, 0, 0, $mes + 1, 1, $year));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendario con Recordatorios</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, td, th {
            border: 1px solid black;
        }
        td {
            height: 100px;
            text-align: center;
            vertical-align: top;
        }
        .recordatorio {
            background-color: yellow;
        }
    </style>
</head>
<body>
    <h1>Calendario de <?php echo date('F Y', strtotime("$year-$mes-01")); ?></h1>
    <!-- Mostrar calendario -->
<table>
    <tr>
        <?php foreach ($diasDeLaSemana as $dia): ?>
            <th><?php echo $dia; ?></th>
        <?php endforeach;?>    
    </tr>
    <tr>
        <?php 
        // Mostrar días vacios antes del primer día del mes
        for ($i = 0; $i < $primerDia; $i++){
            echo "<td></td>";
        }
        // Mostrar los días del mes
        for($dia = 1; $dia <= $diasDelmes; $dia++){
            $fecha = "$year-$mes-". str_pad($dia, 2, '0', STR_PAD_LEFT);

            //Verificar si hay un recordatorio para este día
            $query = "SELECT * FROM recordatorios WHERE fecha = '$fecha'";
            $resultado = $mysqli->query($query);
            $hayRecordatorio = $resultado->num_rows > 0;

            // Clase para resaltar los días con recordatorios
            $clase = $hayRecordatorio ? 'class="recordatorio"' : '';

            echo "<td $clase><a href='recordatorio.php?fecha=$fecha'>$dia</a>";

            // Mostrar los recordatorios debajo de la fecha
            if ($hayRecordatorio){
                while ($recordatorio = $resultado->fetch_assoc()){
                    echo "<div>" . $recordatorio['recordatorio'] . "</div>";
                }
            }
            echo "</td>";
            // Salto de línea después del sábado
            if (($dia + $primerDia) % 7==0){
                echo "</tr><tr>";
            }
        }
        // Completar los días restantes si el mes no termina en sabado
        if (($dia + $primerDia - 1) % 7 != 0){
            $diasRestantes = 7 - (($dia + $primerDia - 1) % 7);
            for ($i = 0; $i < $diasRestantes; $i++){
                echo "<td></td>";
            }
        }
        echo "</tr>";
        ?>
    
</table>
<a href="?mes=<?php echo $prevMes; ?>&year=<?php echo $prevYear; ?>">Mes anterior</a>
<a href="?mes=<?php echo $nextMes; ?>&year=<?php echo $nextYear; ?>">Mes siguiente</a>

</body>
</html>