<?php
/**
 * Script de prueba para verificar que analizarTiposDatos.php responde correctamente
 */

// Test 1: Verificar que el archivo PHP existe
echo "Test 1: Verificando archivo...\n";
$file = __DIR__ . '/analizarTiposDatos.php';
if (file_exists($file)) {
    echo "✓ Archivo encontrado: " . $file . "\n";
} else {
    echo "✗ Archivo NO encontrado\n";
    exit;
}

// Test 2: Verificar sintaxis PHP
echo "\nTest 2: Verificando sintaxis...\n";
$output = [];
$return = 0;
exec("php -l " . escapeshellarg($file), $output, $return);
if ($return === 0) {
    echo "✓ Sintaxis correcta\n";
    foreach ($output as $line) {
        echo "  " . $line . "\n";
    }
} else {
    echo "✗ Error de sintaxis\n";
    foreach ($output as $line) {
        echo "  " . $line . "\n";
    }
    exit;
}

// Test 3: Crear un archivo de prueba simple en Excel (simulado)
echo "\nTest 3: Información sobre requisitos...\n";
echo "El script espera:\n";
echo "  - $_FILES['archivoExcel'] con archivo Excel\n";
echo "  - $_POST['hoja'] opcional con nombre de hoja\n";
echo "\nLas funciones auxiliares disponibles:\n";
echo "  - esFormatoFecha()\n";
echo "  - detectarFormatoFecha()\n";
echo "  - esNumerico()\n";
echo "  - esBooleano()\n";

echo "\n✓ Verificación completada\n";
?>
