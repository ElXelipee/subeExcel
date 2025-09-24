<?php

/**
 * Script de prueba para insertaLitigantes
 */

require_once '../config/config.php';
require_once '../config/database/conexion.php';
require_once 'funcionesBD.php';

echo "<h2>Prueba de inserción de litigantes</h2>";

// Datos de prueba (usando solo un registro)
$datosTest = [
    [
        'fila' => 2,
        'rit' => 'TEST-001-2025',
        'ruc' => '12345678-9',
        'nombre' => 'Juan Pérez Prueba',
        'tipoLitigante' => 'Demandante',
        'usuarioEliminacion' => '',
        'fechaEliminacion' => '',
        'motivoEliminacion' => ''
    ]
];

echo "<h3>Datos de prueba:</h3>";
echo "<pre>" . print_r($datosTest, true) . "</pre>";

echo "<h3>Resultado de inserción:</h3>";

try {
    $resultado = insertaLitigantes($datosTest);

    if ($resultado) {
        echo "<p style='color: green;'>✅ Inserción exitosa</p>";
    } else {
        echo "<p style='color: red;'>❌ Error en la inserción</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>Excepción: " . $e->getMessage() . "</p>";
}

echo "<h3>Últimos errores de PHP:</h3>";
$error_log = error_get_last();
if ($error_log) {
    echo "<pre>" . print_r($error_log, true) . "</pre>";
}
