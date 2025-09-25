<?php

/**
 * Archivo para cargar datos de Inventario MDA desde Excel
 * Versión: 2.0 - Simple y limpio
 * Fecha: 25 Septiembre 2025
 * Propósito: Mostrar datos del Excel en tabla HTML clara y legible
 */

// Solo incluir lo necesario para leer Excel
require_once '../lib/PHPExcel/PHPExcel.php';

// Siempre retornar HTML
header('Content-Type: text/html; charset=utf-8');

/**
 * Función para calcular el dígito verificador de un RUT chileno
 * @param string $rut - RUT sin dígito verificador (solo números)
 * @return string - RUT completo con formato xxxxxxxx-y
 */
function calcularDigitoVerificadorRUT($rut)
{
    // Limpiar RUT (quitar puntos, guiones, espacios)
    $rut = preg_replace('/[^0-9]/', '', $rut);

    // Validar que sea numérico y tenga longitud válida
    if (!is_numeric($rut) || strlen($rut) < 7 || strlen($rut) > 8) {
        return false;
    }

    // Algoritmo para calcular DV chileno
    $suma = 0;
    $multiplo = 2;

    // Recorrer RUT de derecha a izquierda
    for ($i = strlen($rut) - 1; $i >= 0; $i--) {
        $suma += intval($rut[$i]) * $multiplo;
        $multiplo++;
        if ($multiplo == 8) {
            $multiplo = 2;
        }
    }

    $resto = $suma % 11;
    $dv = 11 - $resto;

    // Casos especiales
    if ($dv == 11) {
        $dv = '0';
    } elseif ($dv == 10) {
        $dv = 'K';
    }

    // Formatear RUT chileno
    return number_format($rut, 0, '', '.') . '-' . $dv;
}

/**
 * Función para validar estructura de datos de inventario MDA
 * @param array $datos - Datos del Excel
 * @return array - Array con errores encontrados
 */
function validarInventarioMDA($datos)
{
    $errores = [];

    foreach ($datos as $index => $fila) {
        $filaExcel = $index + 2; // +2 porque Excel inicia en 1 y saltamos header

        // Validar campos obligatorios
        if (empty($fila['tipo_equipo'])) {
            $errores[] = ['fila' => $filaExcel, 'tipo' => 'Tipo Equipo Vacío', 'descripcion' => 'El tipo de equipo es obligatorio'];
        }
        if (empty($fila['subtipo_equipo'])) {
            $errores[] = ['fila' => $filaExcel, 'tipo' => 'Subtipo Equipo Vacío', 'descripcion' => 'El subtipo de equipo es obligatorio'];
        }
        if (empty($fila['marca_modelo'])) {
            $errores[] = ['fila' => $filaExcel, 'tipo' => 'Marca-Modelo Vacío', 'descripcion' => 'La marca-modelo es obligatoria'];
        }
        if (empty($fila['n_serie'])) {
            $errores[] = ['fila' => $filaExcel, 'tipo' => 'N° Serie Vacío', 'descripcion' => 'El número de serie es obligatorio'];
        }
        if (empty($fila['n_rotulo'])) {
            $errores[] = ['fila' => $filaExcel, 'tipo' => 'N° Rótulo Vacío', 'descripcion' => 'El número de rótulo es obligatorio'];
        }
        if (empty($fila['n_activo_fijo'])) {
            $errores[] = ['fila' => $filaExcel, 'tipo' => 'N° Activo Fijo Vacío', 'descripcion' => 'El número de activo fijo es obligatorio'];
        }
        if (empty($fila['estado_equipo']) && $fila['estado_equipo'] !== '0') {
            $errores[] = ['fila' => $filaExcel, 'tipo' => 'Estado Equipo Vacío', 'descripcion' => 'El estado del equipo es obligatorio'];
        }
        if (empty($fila['empresa'])) {
            $errores[] = ['fila' => $filaExcel, 'tipo' => 'Empresa Vacía', 'descripcion' => 'La empresa es obligatoria'];
        }
        if (empty($fila['rut'])) {
            $errores[] = ['fila' => $filaExcel, 'tipo' => 'RUT Vacío', 'descripcion' => 'El campo RUT es obligatorio'];
        } elseif (!is_numeric($fila['rut']) || strlen($fila['rut']) < 7 || strlen($fila['rut']) > 8) {
            $errores[] = ['fila' => $filaExcel, 'tipo' => 'RUT Inválido', 'descripcion' => 'El RUT debe tener entre 7 y 8 dígitos numéricos'];
        }
        if (empty($fila['nombre'])) {
            $errores[] = ['fila' => $filaExcel, 'tipo' => 'Nombre Vacío', 'descripcion' => 'El campo Nombre es obligatorio'];
        }
    }

    return $errores;
}

try {
    echo "<!DOCTYPE html>";
    echo "<html lang='es'>";
    echo "<head>";
    echo "<meta charset='UTF-8'>";
    echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
    echo "<title>Análisis Inventario MDA</title>";
    echo "<style>";
    echo "body { font-family: Arial, sans-serif; margin: 20px; background-color: #f5f5f5; }";
    echo ".container { max-width: 1200px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }";
    echo "table { border-collapse: collapse; width: 100%; margin: 20px 0; }";
    echo "th, td { border: 1px solid #ddd; padding: 8px; text-align: left; font-size: 12px; }";
    echo "th { background-color: #4CAF50; color: white; font-weight: bold; }";
    echo "tr:nth-child(even) { background-color: #f9f9f9; }";
    echo ".error { background-color: #ffebee; padding: 15px; border-left: 4px solid #f44336; margin: 10px 0; }";
    echo ".success { background-color: #e8f5e8; padding: 15px; border-left: 4px solid #4caf50; margin: 10px 0; }";
    echo ".info { background-color: #e3f2fd; padding: 15px; border-left: 4px solid #2196f3; margin: 10px 0; }";
    echo ".rut-dv { color: #2e7d32; font-weight: bold; }";
    echo ".empty { color: #999; font-style: italic; }";
    echo "</style>";
    echo "</head>";
    echo "<body>";
    echo "<div class='container'>";

    echo "<h1>📊 Análisis de Inventario MDA</h1>";
    echo "<hr>";

    // Verificar archivo
    if (!isset($_FILES['archivoExcel']) || $_FILES['archivoExcel']['error'] !== UPLOAD_ERR_OK) {
        echo "<div class='error'>";
        echo "<h2>❌ Error al recibir archivo</h2>";
        echo "<p>No se pudo procesar el archivo. Intenta nuevamente.</p>";
        echo "</div></div></body></html>";
        exit;
    }

    echo "<div class='info'>";
    echo "<h2>📁 Información del archivo</h2>";
    echo "<p><strong>Nombre:</strong> " . htmlspecialchars($_FILES['archivoExcel']['name']) . "</p>";
    echo "<p><strong>Tamaño:</strong> " . number_format($_FILES['archivoExcel']['size'] / 1024, 2) . " KB</p>";
    echo "</div>";

    // Procesar Excel
    $nombreArchivo = $_FILES['archivoExcel']['name'];
    $rutaArchivoTemporal = $_FILES['archivoExcel']['tmp_name'];
    $extensionArchivo = pathinfo($nombreArchivo, PATHINFO_EXTENSION);

    // Mover archivo a directorio temporal
    $destino = '../temp/' . time() . '_' . $nombreArchivo;
    if (!file_exists('../temp/')) {
        mkdir('../temp/', 0777, true);
    }

    if (!move_uploaded_file($rutaArchivoTemporal, $destino)) {
        echo "<div class='error'><h2>❌ Error al procesar archivo</h2></div></div></body></html>";
        exit;
    }

    // Cargar Excel
    if (strtolower($extensionArchivo) === 'csv') {
        $objReader = PHPExcel_IOFactory::createReader('CSV');
    } else {
        $objReader = PHPExcel_IOFactory::createReader('Excel2007');
        if (strtolower($extensionArchivo) === 'xls') {
            $objReader = PHPExcel_IOFactory::createReader('Excel5');
        }
    }

    $objPHPExcel = $objReader->load($destino);
    $objWorksheet = $objPHPExcel->getActiveSheet();
    $highestRow = $objWorksheet->getHighestRow();

    echo "<div class='info'>";
    echo "<h2>📋 Estructura del Excel</h2>";
    echo "<p><strong>Total de filas:</strong> $highestRow</p>";
    echo "<p><strong>Estructura esperada:</strong> 12 columnas (A-L)</p>";
    echo "<ul style='columns: 2; font-size: 12px;'>";
    echo "<li><strong>A:</strong> Tipo de equipo</li>";
    echo "<li><strong>B:</strong> Subtipo de equipo</li>";
    echo "<li><strong>C:</strong> Marca-Modelo</li>";
    echo "<li><strong>D:</strong> N° Serie</li>";
    echo "<li><strong>E:</strong> N° Rótulo</li>";
    echo "<li><strong>F:</strong> N° Activo fijo</li>";
    echo "<li><strong>G:</strong> Estado equipo</li>";
    echo "<li><strong>H:</strong> Empresa</li>";
    echo "<li><strong>I:</strong> RUT (sin DV)</li>";
    echo "<li><strong>J:</strong> Nombre</li>";
    echo "<li><strong>K:</strong> F.Inventariado</li>";
    echo "<li><strong>L:</strong> IP</li>";
    echo "</ul>";
    echo "</div>";

    // Extraer datos según estructura de 12 columnas (A-L)
    $datosInventario = [];
    for ($i = 2; $i <= $highestRow; $i++) {
        $valores = [];
        for ($col = 'A'; $col <= 'L'; $col++) {
            $valores[$col] = trim($objWorksheet->getCell($col . $i)->getCalculatedValue());
        }

        // Verificar si la fila tiene algún dato
        $tieneAlgunDato = false;
        foreach ($valores as $valor) {
            if (!empty($valor)) {
                $tieneAlgunDato = true;
                break;
            }
        }

        if ($tieneAlgunDato) {
            $datosInventario[] = [
                'fila_excel' => $i,
                'tipo_equipo' => $valores['A'],
                'subtipo_equipo' => $valores['B'],
                'marca_modelo' => $valores['C'],
                'n_serie' => $valores['D'],
                'n_rotulo' => $valores['E'],
                'n_activo_fijo' => $valores['F'],
                'estado_equipo' => $valores['G'],
                'empresa' => $valores['H'],
                'rut' => $valores['I'],
                'rut_con_dv' => !empty($valores['I']) ? calcularDigitoVerificadorRUT($valores['I']) : 'N/A',
                'nombre' => $valores['J'],
                'f_inventariado' => $valores['K'],
                'ip' => $valores['L']
            ];
        }
    }

    $totalRegistros = count($datosInventario);
    echo "<div class='success'>";
    echo "<h2>✅ Extracción completada</h2>";
    echo "<p><strong>Registros extraídos:</strong> $totalRegistros</p>";
    echo "</div>";

    // Mostrar tabla con datos
    if (!empty($datosInventario)) {
        echo "<h2>📊 Datos Extraídos del Excel</h2>";
        echo "<table>";
        echo "<thead>";
        echo "<tr>";
        echo "<th>Fila</th>";
        echo "<th>Tipo Eq.</th>";
        echo "<th>Subtipo</th>";
        echo "<th>Marca</th>";
        echo "<th>N° Serie</th>";
        echo "<th>N° Rótulo</th>";
        echo "<th>N° Act.Fijo</th>";
        echo "<th>Estado</th>";
        echo "<th>Empresa</th>";
        echo "<th>RUT Original</th>";
        echo "<th>RUT con DV</th>";
        echo "<th>Nombre</th>";
        echo "<th>F.Inventariado</th>";
        echo "<th>IP</th>";
        echo "</tr>";
        echo "</thead>";
        echo "<tbody>";

        foreach ($datosInventario as $registro) {
            echo "<tr>";
            echo "<td>" . $registro['fila_excel'] . "</td>";
            echo "<td>" . ($registro['tipo_equipo'] ?: '<span class="empty">vacío</span>') . "</td>";
            echo "<td>" . ($registro['subtipo_equipo'] ?: '<span class="empty">vacío</span>') . "</td>";
            echo "<td>" . ($registro['marca_modelo'] ?: '<span class="empty">vacío</span>') . "</td>";
            echo "<td>" . ($registro['n_serie'] ?: '<span class="empty">vacío</span>') . "</td>";
            echo "<td>" . ($registro['n_rotulo'] ?: '<span class="empty">vacío</span>') . "</td>";
            echo "<td>" . ($registro['n_activo_fijo'] ?: '<span class="empty">vacío</span>') . "</td>";
            echo "<td>" . ($registro['estado_equipo'] ?: '<span class="empty">vacío</span>') . "</td>";
            echo "<td>" . ($registro['empresa'] ?: '<span class="empty">vacío</span>') . "</td>";
            echo "<td>" . ($registro['rut'] ?: '<span class="empty">vacío</span>') . "</td>";
            echo "<td class='rut-dv'>" . $registro['rut_con_dv'] . "</td>";
            echo "<td>" . ($registro['nombre'] ?: '<span class="empty">vacío</span>') . "</td>";
            echo "<td>" . ($registro['f_inventariado'] ?: '<span class="empty">vacío</span>') . "</td>";
            echo "<td>" . ($registro['ip'] ?: '<span class="empty">vacío</span>') . "</td>";
            echo "</tr>";
        }

        echo "</tbody>";
        echo "</table>";
    }

    // Validación de datos
    $errores = validarInventarioMDA($datosInventario);
    $registrosConError = count($errores);
    $registrosCorrectos = $totalRegistros - $registrosConError;

    if ($registrosConError > 0) {
        echo "<div class='error'>";
        echo "<h2>⚠️ Errores de Validación ($registrosConError errores encontrados)</h2>";
        echo "<table>";
        echo "<thead>";
        echo "<tr><th>Fila</th><th>Tipo de Error</th><th>Descripción</th></tr>";
        echo "</thead>";
        echo "<tbody>";
        foreach ($errores as $error) {
            echo "<tr>";
            echo "<td>" . $error['fila'] . "</td>";
            echo "<td>" . $error['tipo'] . "</td>";
            echo "<td>" . $error['descripcion'] . "</td>";
            echo "</tr>";
        }
        echo "</tbody>";
        echo "</table>";
        echo "</div>";
    } else {
        echo "<div class='success'>";
        echo "<h2>✅ Todos los datos son válidos</h2>";
        echo "<p>No se encontraron errores de validación.</p>";
        echo "</div>";
    }

    // Resumen final
    echo "<div class='info'>";
    echo "<h2>📈 Resumen Final</h2>";
    echo "<ul>";
    echo "<li><strong>Total de registros procesados:</strong> $totalRegistros</li>";
    echo "<li><strong>Registros correctos:</strong> $registrosCorrectos</li>";
    echo "<li><strong>Registros con errores:</strong> $registrosConError</li>";
    $tasaExito = $totalRegistros > 0 ? round(($registrosCorrectos / $totalRegistros) * 100, 2) : 0;
    echo "<li><strong>Tasa de éxito:</strong> $tasaExito%</li>";
    echo "</ul>";
    echo "</div>";

    // Limpiar archivo temporal
    unlink($destino);

    echo "</div></body></html>";
} catch (Exception $e) {
    echo "<div class='error'>";
    echo "<h2>❌ Error en el procesamiento</h2>";
    echo "<p><strong>Mensaje:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "</div></div></body></html>";
}
