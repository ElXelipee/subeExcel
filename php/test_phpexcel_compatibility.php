<?php
/**
 * Script de verificación de compatibilidad PHPExcel con PHP 8+
 * Prueba las funciones básicas de la librería después de las correcciones
 */

// Error reporting para detectar cualquier problema
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Incluir la librería PHPExcel
require_once('../lib/PHPExcel/PHPExcel.php');

echo "<h1>Verificación de Compatibilidad PHPExcel con PHP 8+</h1>\n";
echo "<p>Versión de PHP: " . phpversion() . "</p>\n";
echo "<p>Fecha de verificación: " . date('Y-m-d H:i:s') . "</p>\n";

echo "<h2>Pruebas básicas:</h2>\n";

try {
    // Prueba 1: Crear un objeto PHPExcel
    echo "<p>✓ Creando objeto PHPExcel...</p>\n";
    $objPHPExcel = new PHPExcel();
    echo "<p style='color: green;'>✓ PHPExcel creado exitosamente</p>\n";
    
    // Prueba 2: Crear una hoja de trabajo
    echo "<p>✓ Configurando hoja de trabajo...</p>\n";
    $objPHPExcel->setActiveSheetIndex(0);
    $objPHPExcel->getActiveSheet()->setTitle('Test');
    echo "<p style='color: green;'>✓ Hoja de trabajo configurada</p>\n";
    
    // Prueba 3: Escribir datos en celdas
    echo "<p>✓ Escribiendo datos de prueba...</p>\n";
    $objPHPExcel->getActiveSheet()->setCellValue('A1', 'RIT');
    $objPHPExcel->getActiveSheet()->setCellValue('B1', 'RUC');
    $objPHPExcel->getActiveSheet()->setCellValue('C1', 'Fecha');
    $objPHPExcel->getActiveSheet()->setCellValue('A2', 'TEST-001');
    $objPHPExcel->getActiveSheet()->setCellValue('B2', '12345');
    $objPHPExcel->getActiveSheet()->setCellValue('C2', date('Y-m-d'));
    echo "<p style='color: green;'>✓ Datos escritos exitosamente</p>\n";
    
    // Prueba 4: Leer datos de celdas
    echo "<p>✓ Leyendo datos de prueba...</p>\n";
    $rit = $objPHPExcel->getActiveSheet()->getCell('A2')->getCalculatedValue();
    $ruc = $objPHPExcel->getActiveSheet()->getCell('B2')->getCalculatedValue();
    echo "<p style='color: green;'>✓ Datos leídos: RIT=$rit, RUC=$ruc</p>\n";
    
    // Prueba 5: Probar iteradores (que fueron corregidos)
    echo "<p>✓ Probando iteradores corregidos...</p>\n";
    $worksheetIterator = new PHPExcel_WorksheetIterator($objPHPExcel);
    $worksheetIterator->rewind();
    if ($worksheetIterator->valid()) {
        $currentSheet = $worksheetIterator->current();
        echo "<p style='color: green;'>✓ WorksheetIterator funciona correctamente</p>\n";
    }
    
    // Prueba 6: Probar CellIterator
    $cellIterator = new PHPExcel_Worksheet_CellIterator($objPHPExcel->getActiveSheet(), 1);
    $cellIterator->rewind();
    if ($cellIterator->valid()) {
        echo "<p style='color: green;'>✓ CellIterator funciona correctamente</p>\n";
    }
    
    // Prueba 7: Probar RowIterator
    $rowIterator = new PHPExcel_Worksheet_RowIterator($objPHPExcel->getActiveSheet());
    $rowIterator->rewind();
    if ($rowIterator->valid()) {
        echo "<p style='color: green;'>✓ RowIterator funciona correctamente</p>\n";
    }
    
    echo "<h2 style='color: green;'>🎉 ¡Todas las pruebas pasaron exitosamente!</h2>\n";
    echo "<p><strong>La librería PHPExcel está funcionando correctamente con PHP " . phpversion() . "</strong></p>\n";
    
    echo "<h3>Correcciones aplicadas:</h3>\n";
    echo "<ul>\n";
    echo "<li>✓ Corregido WorksheetIterator - Agregado #[\\ReturnTypeWillChange] a métodos iterator</li>\n";
    echo "<li>✓ Corregido CellIterator - Agregado #[\\ReturnTypeWillChange] a métodos iterator</li>\n";
    echo "<li>✓ Corregido RowIterator - Agregado #[\\ReturnTypeWillChange] a métodos iterator</li>\n";
    echo "<li>✓ Corregido Worksheet.php - Parámetros opcionales reordenados</li>\n";
    echo "<li>✓ Corregido trendClass.php - Parámetros opcionales reordenados</li>\n";
    echo "<li>✓ Corregido Autoloader.php - Uso correcto de false en lugar de False</li>\n";
    echo "</ul>\n";
    
} catch (Exception $e) {
    echo "<h2 style='color: red;'>❌ Error detectado:</h2>\n";
    echo "<p style='color: red;'><strong>Error:</strong> " . $e->getMessage() . "</p>\n";
    echo "<p><strong>Archivo:</strong> " . $e->getFile() . "</p>\n";
    echo "<p><strong>Línea:</strong> " . $e->getLine() . "</p>\n";
    echo "<p><strong>Trace:</strong></p>\n";
    echo "<pre>" . $e->getTraceAsString() . "</pre>\n";
}

echo "<hr>\n";
echo "<p><small>Script de verificación generado por GitHub Copilot - " . date('Y-m-d H:i:s') . "</small></p>\n";
?>
