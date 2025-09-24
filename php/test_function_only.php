<?php

/**
 * Test final de las funciones corregidas
 */

require_once '../config/config.php';
require_once '../config/database/conexion.php';
require_once 'funcionesBD.php';

// Configurar para que no haya output buffering
ob_clean();
header('Content-Type: application/json; charset=utf-8');

try {
    // Datos de prueba muy simples
    $datosTest = [
        [
            'fila' => 2,
            'rit' => 'TEST-FUNC-001',
            'ruc' => '11111111-1',
            'nombre' => 'Test Función',
            'tipoLitigante' => 'Demandante',
            'usuarioEliminacion' => '',
            'fechaEliminacion' => '',
            'motivoEliminacion' => ''
        ]
    ];

    // Probar insertaLitigantes
    $resultado = insertaLitigantes($datosTest);

    if ($resultado) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Función insertaLitigantes funciona correctamente',
            'registros_insertados' => 1
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'La función insertaLitigantes retornó false',
            'registros_insertados' => 0
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Excepción: ' . $e->getMessage(),
        'line' => $e->getLine(),
        'file' => $e->getFile()
    ]);
}
