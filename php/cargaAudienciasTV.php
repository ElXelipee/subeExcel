<?php

/**
 * Archivo para cargar datos de Audiencias TV desde Excel
 * Versión: 1.0
 * Fecha: 24 Septiembre 2025
 */

// Incluir archivos necesarios
require_once '../config/config.php';
require_once '../config/database/conexion.php';
require_once '../lib/PHPExcel/PHPExcel.php';
require_once '../lib/function.php';

// Verificar que se recibió el archivo
if (!isset($_FILES['archivoExcel']) || $_FILES['archivoExcel']['error'] !== UPLOAD_ERR_OK) {
    die(json_encode([
        'status' => 'error',
        'message' => '❌ Error: No se recibió el archivo o hubo un problema en la carga.'
    ]));
}

try {
    // Información del archivo
    $archivoTemporal = $_FILES['archivoExcel']['tmp_name'];
    $nombreArchivo = $_FILES['archivoExcel']['name'];
    $extensionArchivo = pathinfo($nombreArchivo, PATHINFO_EXTENSION);

    echo "<h2>📺 Procesando Audiencias TV</h2>";
    echo "<p><strong>Archivo:</strong> $nombreArchivo</p>";
    echo "<p><strong>Extensión:</strong> $extensionArchivo</p>";
    echo "<hr>";

    // Validar extensión
    $extensionesPermitidas = ['xlsx', 'xls', 'csv'];
    if (!in_array(strtolower($extensionArchivo), $extensionesPermitidas)) {
        throw new Exception("❌ Extensión de archivo no permitida: $extensionArchivo");
    }

    // Cargar el archivo Excel
    echo "<h3>🔄 Cargando archivo...</h3>";

    if (strtolower($extensionArchivo) === 'csv') {
        $objReader = PHPExcel_IOFactory::createReader('CSV');
        $objReader->setDelimiter(',');
        $objReader->setEnclosure('"');
        $objReader->setLineEnding("\r\n");
        $objReader->setSheetIndex(0);
    } else {
        $objReader = PHPExcel_IOFactory::createReader('Excel2007');
        if (strtolower($extensionArchivo) === 'xls') {
            $objReader = PHPExcel_IOFactory::createReader('Excel5');
        }
    }

    $objPHPExcel = $objReader->load($archivoTemporal);
    $objWorksheet = $objPHPExcel->getActiveSheet();

    // Obtener el rango de datos
    $highestRow = $objWorksheet->getHighestRow();
    $highestColumn = $objWorksheet->getHighestColumn();
    $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);

    echo "<p>✅ Archivo cargado exitosamente</p>";
    echo "<p><strong>Filas encontradas:</strong> $highestRow</p>";
    echo "<p><strong>Columnas encontradas:</strong> $highestColumn ($highestColumnIndex columnas)</p>";
    echo "<hr>";

    // Verificar que tenga al menos 12 columnas (A-L)
    if ($highestColumnIndex < 12) {
        throw new Exception("❌ El archivo debe tener al menos 12 columnas (A-L). Encontradas: $highestColumnIndex");
    }

    // Verificar que tenga al menos 2 filas (encabezado + datos)
    if ($highestRow < 2) {
        throw new Exception("❌ El archivo debe tener al menos 2 filas (encabezado + datos). Encontradas: $highestRow");
    }

    // Leer encabezados (fila 1)
    echo "<h3>📋 Encabezados encontrados:</h3>";
    $encabezados = [];
    for ($col = 0; $col < 12; $col++) {
        $columnLetter = PHPExcel_Cell::stringFromColumnIndex($col);
        $cellValue = $objWorksheet->getCell($columnLetter . '1')->getCalculatedValue();
        $encabezados[$col] = trim($cellValue);
        echo "<p><strong>Columna $columnLetter:</strong> " . htmlspecialchars($encabezados[$col]) . "</p>";
    }
    echo "<hr>";

    // Preparar matriz para almacenar los datos
    echo "<h3>🔍 Procesando datos...</h3>";
    $datosAudienciasTV = [];

    // Procesar cada fila de datos (desde la fila 2)
    for ($row = 2; $row <= $highestRow; $row++) {
        $filaVacia = true;
        $fila = [];

        // Leer las 12 columnas (A-L)
        for ($col = 0; $col < 12; $col++) {
            $columnLetter = PHPExcel_Cell::stringFromColumnIndex($col);
            $cell = $objWorksheet->getCell($columnLetter . $row);

            // Manejar diferentes tipos de datos según la columna
            switch ($col) {
                case 0: // Columna A: F. Audiencia (fecha)
                    if (PHPExcel_Shared_Date::isDateTime($cell)) {
                        $dateValue = PHPExcel_Shared_Date::ExcelToPHP($cell->getCalculatedValue());
                        $fila[$col] = date('Y-m-d', $dateValue);
                    } else {
                        $fila[$col] = trim($cell->getCalculatedValue());
                    }
                    break;

                case 1: // Columna B: Sala (general)
                    $fila[$col] = trim($cell->getCalculatedValue());
                    break;

                case 2: // Columna C: H. Inicio (hora personalizada)
                    if (PHPExcel_Shared_Date::isDateTime($cell)) {
                        $timeValue = PHPExcel_Shared_Date::ExcelToPHP($cell->getCalculatedValue());
                        $fila[$col] = date('H:i', $timeValue);
                    } else {
                        $fila[$col] = trim($cell->getCalculatedValue());
                    }
                    break;

                default: // Columnas D-L: Todas generales
                    $fila[$col] = trim($cell->getCalculatedValue());
                    break;
            }

            // Verificar si la fila no está vacía
            if (!empty($fila[$col])) {
                $filaVacia = false;
            }
        }

        // Solo agregar la fila si no está completamente vacía
        if (!$filaVacia) {
            $datosAudienciasTV[] = [
                'fila' => $row,
                'f_audiencia' => $fila[0],    // F. Audiencia
                'sala' => $fila[1],           // Sala
                'h_inicio' => $fila[2],       // H. Inicio
                'rit' => $fila[3],            // RIT
                'caj' => $fila[4],            // CAJ
                'caratulado' => $fila[5],     // Caratulado
                'tipo_audiencia' => $fila[6], // Tipo Audiencia
                'materia' => $fila[7],        // Materia
                'juez' => $fila[8],           // Juez
                'acta' => $fila[9],           // Acta
                'ct' => $fila[10],            // CT
                'cuenta_zoom' => $fila[11]    // Cuenta Zoom
            ];
        }
    }

    echo "<p>✅ Datos procesados: " . count($datosAudienciasTV) . " registros</p>";
    echo "<hr>";

    // Mostrar los datos preparados con var_dump
    echo "<h3>🔍 Datos preparados para subir (var_dump):</h3>";
    echo "<div style='background-color: #f8f9fa; padding: 15px; border: 1px solid #dee2e6; border-radius: 5px; font-family: monospace; white-space: pre-wrap; max-height: 400px; overflow-y: auto;'>";

    // Usar buffer de salida para capturar el var_dump
    ob_start();
    var_dump($datosAudienciasTV);
    $vardump_output = ob_get_clean();

    echo htmlspecialchars($vardump_output);
    echo "</div>";

    echo "<hr>";
    echo "<h3>📊 Resumen del procesamiento:</h3>";
    echo "<div class='alert alert-success'>";
    echo "<p><strong>✅ Archivo procesado exitosamente</strong></p>";
    echo "<p><strong>📁 Archivo:</strong> $nombreArchivo</p>";
    echo "<p><strong>📊 Total de registros:</strong> " . count($datosAudienciasTV) . "</p>";
    echo "<p><strong>📅 Fecha de procesamiento:</strong> " . date('d/m/Y H:i:s') . "</p>";
    echo "</div>";

    // Mostrar algunos ejemplos de datos
    if (count($datosAudienciasTV) > 0) {
        echo "<h3>📋 Primeros 3 registros procesados:</h3>";
        echo "<div class='table-responsive'>";
        echo "<table class='table table-striped table-sm'>";
        echo "<thead><tr>";
        echo "<th>Fila</th><th>F. Audiencia</th><th>Sala</th><th>H. Inicio</th><th>RIT</th><th>CAJ</th><th>Caratulado</th>";
        echo "<th>Tipo Audiencia</th><th>Materia</th><th>Juez</th><th>Acta</th><th>CT</th><th>Cuenta Zoom</th>";
        echo "</tr></thead><tbody>";

        $maxRegistros = min(3, count($datosAudienciasTV));
        for ($i = 0; $i < $maxRegistros; $i++) {
            $registro = $datosAudienciasTV[$i];
            echo "<tr>";
            echo "<td>" . htmlspecialchars($registro['fila']) . "</td>";
            echo "<td>" . htmlspecialchars($registro['f_audiencia']) . "</td>";
            echo "<td>" . htmlspecialchars($registro['sala']) . "</td>";
            echo "<td>" . htmlspecialchars($registro['h_inicio']) . "</td>";
            echo "<td>" . htmlspecialchars($registro['rit']) . "</td>";
            echo "<td>" . htmlspecialchars($registro['caj']) . "</td>";
            echo "<td>" . htmlspecialchars(strlen($registro['caratulado']) > 30 ? substr($registro['caratulado'], 0, 30) . '...' : $registro['caratulado']) . "</td>";
            echo "<td>" . htmlspecialchars(strlen($registro['tipo_audiencia']) > 20 ? substr($registro['tipo_audiencia'], 0, 20) . '...' : $registro['tipo_audiencia']) . "</td>";
            echo "<td>" . htmlspecialchars(strlen($registro['materia']) > 30 ? substr($registro['materia'], 0, 30) . '...' : $registro['materia']) . "</td>";
            echo "<td>" . htmlspecialchars($registro['juez']) . "</td>";
            echo "<td>" . htmlspecialchars($registro['acta']) . "</td>";
            echo "<td>" . htmlspecialchars($registro['ct']) . "</td>";
            echo "<td>" . htmlspecialchars($registro['cuenta_zoom']) . "</td>";
            echo "</tr>";
        }

        echo "</tbody></table>";
        echo "</div>";

        if (count($datosAudienciasTV) > 3) {
            echo "<p><em>... y " . (count($datosAudienciasTV) - 3) . " registros más.</em></p>";
        }
    }

    echo "<hr>";
    echo "<div class='alert alert-info'>";
    echo "<h4>🔄 Siguiente paso:</h4>";
    echo "<p>Los datos han sido preparados y validados correctamente.</p>";
    echo "<p>Para implementar la inserción en base de datos, será necesario:</p>";
    echo "<ul>";
    echo "<li>Crear la tabla correspondiente en la base de datos</li>";
    echo "<li>Implementar las validaciones específicas del negocio</li>";
    echo "<li>Agregar el código de inserción SQL</li>";
    echo "</ul>";
    echo "</div>";
} catch (Exception $e) {
    echo "<div class='alert alert-danger'>";
    echo "<h3>❌ Error en el procesamiento</h3>";
    echo "<p><strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><strong>Línea:</strong> " . $e->getLine() . "</p>";
    echo "<p><strong>Archivo:</strong> " . $e->getFile() . "</p>";
    echo "</div>";

    // Log del error
    error_log("Error en cargaAudienciasTV.php: " . $e->getMessage() . " en línea " . $e->getLine());
}

// Agregar estilos Bootstrap para mejor presentación
echo "
<style>
body { 
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
    margin: 20px;
    background-color: #f8f9fa;
}
.alert {
    padding: 15px;
    margin: 15px 0;
    border-radius: 5px;
}
.alert-success {
    background-color: #d4edda;
    border: 1px solid #c3e6cb;
    color: #155724;
}
.alert-danger {
    background-color: #f8d7da;
    border: 1px solid #f5c6cb;
    color: #721c24;
}
.alert-info {
    background-color: #d1ecf1;
    border: 1px solid #bee5eb;
    color: #0c5460;
}
.table {
    width: 100%;
    border-collapse: collapse;
    margin: 15px 0;
}
.table th, .table td {
    border: 1px solid #dee2e6;
    padding: 8px;
    text-align: left;
    font-size: 12px;
}
.table th {
    background-color: #ff6b35;
    color: white;
}
.table-striped tbody tr:nth-child(odd) {
    background-color: #f9f9f9;
}
h2, h3 { 
    color: #ff6b35; 
    border-bottom: 2px solid #ff6b35;
    padding-bottom: 5px;
}
hr { 
    border: 1px solid #ff6b35; 
    margin: 20px 0;
}
</style>
";
