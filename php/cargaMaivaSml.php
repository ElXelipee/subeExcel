<?php
// Conexión a la base de datos
$pdo = new PDO('mysql:host=localhost;dbname=pj_jfsantiago3', 'root', '');

// Definir fechas de inicio y fin
$fechaInicio = new DateTime('2024-08-29');
$fechaFin = new DateTime('2030-12-31');

// Intervalo de 1 día
$intervalo = new DateInterval('P1D');
$periodo = new DatePeriod($fechaInicio, $intervalo, $fechaFin);

foreach ($periodo as $fecha) {
    $diaSemana = $fecha->format('N'); // 1 para lunes, 2 para martes, ..., 7 para domingo
    $horas = [];

    // Asignar horas basado en el día de la semana
    if ($diaSemana == 1 || $diaSemana == 3) { // Lunes y Miércoles
        $horas = ['10:00', '10:15', '10:30'];
    } elseif ($diaSemana == 2 || $diaSemana == 4) { // Martes y Jueves
        $horas = ['10:45', '11:00', '11:15'];
    } elseif ($diaSemana == 5) { // Viernes
        $horas = ['13:00', '13:15', '13:30'];
    }

    // Añadir horas adicionales de lunes a jueves
    if ($diaSemana >= 1 && $diaSemana <= 4) {
        $horas = array_merge($horas, ['14:30', '14:45', '15:00']);
    }

    // Preparar y ejecutar la query para cada hora
    foreach ($horas as $hora) {
        $query = "INSERT INTO tbl_horas_sml (fecha, hora) VALUES (:fecha, :hora)";
        $stmt = $pdo->prepare($query);
        $stmt->execute(['fecha' => $fecha->format('Y-m-d'), 'hora' => $hora]);
    }
}