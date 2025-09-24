<?php

/**
 * Archivo de prueba temporal para cargar datos de Audiencias TV
 * Este archivo simula datos para testing inicial
 */

echo "<h2>🧪 ARCHIVO DE PRUEBA - Audiencias TV</h2>";
echo "<p>Este es un archivo de prueba para simular la carga de datos de Audiencias TV</p>";
echo "<hr>";

// Simular datos de ejemplo como si vinieran del Excel
$datosAudienciasTV = [
    [
        'fila' => 2,
        'f_audiencia' => '2025-09-22',
        'sala' => '1',
        'h_inicio' => '08:30',
        'rit' => 'C-8029-2024',
        'caj' => 'No',
        'caratulado' => 'GALDAMES/GALDAMES',
        'tipo_audiencia' => 'Citación a Audiencia de Juicio',
        'materia' => '1. Alimentos, Aumento 2. Alimentos, Cesacion 3. Alimentos, Rebaja',
        'juez' => 'Vera Alejandra Garrido Crino',
        'acta' => 'FRANCISCA',
        'ct' => 'CESAR MANDUJANO',
        'cuenta_zoom' => 'zoom_3jfsantiago03@pjud.cl'
    ],
    [
        'fila' => 3,
        'f_audiencia' => '2025-09-23',
        'sala' => '2',
        'h_inicio' => '09:00',
        'rit' => 'C-8030-2024',
        'caj' => 'Sí',
        'caratulado' => 'RODRIGUEZ/MARTINEZ',
        'tipo_audiencia' => 'Audiencia de Preparación',
        'materia' => 'Divorcio por Mutuo Acuerdo',
        'juez' => 'Juan Carlos Pérez Silva',
        'acta' => 'MARIA JOSE',
        'ct' => 'PATRICIA GONZALEZ',
        'cuenta_zoom' => 'zoom_sala2santiago@pjud.cl'
    ],
    [
        'fila' => 4,
        'f_audiencia' => '2025-09-24',
        'sala' => '1',
        'h_inicio' => '14:30',
        'rit' => 'C-8031-2024',
        'caj' => 'No',
        'caratulado' => 'LOPEZ/SANCHEZ',
        'tipo_audiencia' => 'Audiencia de Juicio',
        'materia' => 'Cuidado Personal de Menores',
        'juez' => 'Ana María Torres Vega',
        'acta' => 'CARLOS',
        'ct' => 'LUIS MORALES',
        'cuenta_zoom' => 'zoom_3jfsantiago03@pjud.cl'
    ]
];

echo "<h3>🔍 Datos preparados para subir (var_dump):</h3>";
echo "<div style='background-color: #f8f9fa; padding: 15px; border: 1px solid #dee2e6; border-radius: 5px; font-family: monospace; white-space: pre-wrap; max-height: 400px; overflow-y: auto;'>";

ob_start();
var_dump($datosAudienciasTV);
$vardump_output = ob_get_clean();

echo htmlspecialchars($vardump_output);
echo "</div>";

echo "<hr>";
echo "<h3>📊 Resumen del procesamiento:</h3>";
echo "<div class='alert alert-success'>";
echo "<p><strong>✅ Datos de prueba procesados exitosamente</strong></p>";
echo "<p><strong>📊 Total de registros:</strong> " . count($datosAudienciasTV) . "</p>";
echo "<p><strong>📅 Fecha de procesamiento:</strong> " . date('d/m/Y H:i:s') . "</p>";
echo "</div>";

// Mostrar algunos ejemplos de datos
echo "<h3>📋 Registros de prueba procesados:</h3>";
echo "<div class='table-responsive'>";
echo "<table class='table table-striped table-sm'>";
echo "<thead><tr>";
echo "<th>Fila</th><th>F. Audiencia</th><th>Sala</th><th>H. Inicio</th><th>RIT</th><th>CAJ</th><th>Caratulado</th>";
echo "<th>Tipo Audiencia</th><th>Materia</th><th>Juez</th><th>Acta</th><th>CT</th><th>Cuenta Zoom</th>";
echo "</tr></thead><tbody>";

foreach ($datosAudienciasTV as $registro) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($registro['fila']) . "</td>";
    echo "<td>" . htmlspecialchars($registro['f_audiencia']) . "</td>";
    echo "<td>" . htmlspecialchars($registro['sala']) . "</td>";
    echo "<td>" . htmlspecialchars($registro['h_inicio']) . "</td>";
    echo "<td>" . htmlspecialchars($registro['rit']) . "</td>";
    echo "<td>" . htmlspecialchars($registro['caj']) . "</td>";
    echo "<td>" . htmlspecialchars(strlen($registro['caratulado']) > 20 ? substr($registro['caratulado'], 0, 20) . '...' : $registro['caratulado']) . "</td>";
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

echo "<hr>";
echo "<div class='alert alert-info'>";
echo "<h4>🔄 Estado del sistema:</h4>";
echo "<p>Este es un archivo de prueba temporal. Para usar el sistema real:</p>";
echo "<ul>";
echo "<li>Usar el archivo cargaAudienciasTV.php para carga real</li>";
echo "<li>Subir un archivo Excel con el formato correcto</li>";
echo "<li>Los datos se procesarán automáticamente</li>";
echo "</ul>";
echo "</div>";

// Agregar estilos
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
