<?php

/**
 * Archivo para cargar datos de Carátula desde Excel
 * Versión: 3.0 - Retorna JSON para modal
 * Fecha: 24 Septiembre 2025
 */

require_once '../config/config.php';
require_once '../config/database/conexion.php';
require_once '../lib/PHPExcel/PHPExcel.php';
require_once '../lib/function.php';
require_once 'funcionesBD.php';

// Configurar cabeceras para JSON
header('Content-Type: application/json; charset=utf-8');

// ======================
// Verificar archivo
// ======================
if (!isset($_FILES['archivoExcel']) || $_FILES['archivoExcel']['error'] !== UPLOAD_ERR_OK) {
  $error_message = 'No se recibió el archivo';

  if (isset($_FILES['archivoExcel']['error'])) {
    switch ($_FILES['archivoExcel']['error']) {
      case UPLOAD_ERR_INI_SIZE:
      case UPLOAD_ERR_FORM_SIZE:
        $error_message = 'El archivo es demasiado grande. Tamaño máximo permitido: 50MB';
        break;
      case UPLOAD_ERR_PARTIAL:
        $error_message = 'El archivo se subió parcialmente. Intente nuevamente';
        break;
      case UPLOAD_ERR_NO_FILE:
        $error_message = 'No se seleccionó ningún archivo';
        break;
      case UPLOAD_ERR_NO_TMP_DIR:
        $error_message = 'Error del servidor: falta carpeta temporal';
        break;
      case UPLOAD_ERR_CANT_WRITE:
        $error_message = 'Error del servidor: no se puede escribir el archivo';
        break;
      default:
        $error_message = 'Error desconocido al subir el archivo';
        break;
    }
  }

  echo json_encode([
    'status' => 'error',
    'message' => $error_message,
    'registros_correctos' => 0,
    'registros_error' => 1,
    'total_procesado' => 0,
    'tasa_exito' => 0,
    'errores' => [$error_message]
  ]);
  exit;
}

try {
  set_time_limit(2000000);
  ini_set('memory_limit', '-1');

  $archivoTemporal   = $_FILES['archivoExcel']['tmp_name'];
  $nombreArchivo     = $_FILES['archivoExcel']['name'];
  $extensionArchivo  = pathinfo($nombreArchivo, PATHINFO_EXTENSION);

  // Debug: Log información del archivo
  error_log("DEBUG - Archivo recibido: $nombreArchivo, Extensión: $extensionArchivo, Tamaño: " . $_FILES['archivoExcel']['size']);

  // Validar extensión
  $extensionesPermitidas = ['xlsx', 'xls', 'csv'];
  if (!in_array(strtolower($extensionArchivo), $extensionesPermitidas)) {
    echo json_encode([
      'status' => 'error',
      'message' => "Extensión no permitida: $extensionArchivo",
      'registros_correctos' => 0,
      'registros_error' => 1,
      'total_procesado' => 0,
      'tasa_exito' => 0,
      'errores' => ["Extensión $extensionArchivo no está permitida"]
    ]);
    exit;
  }

  // Crear copia de respaldo
  $destino = "bak_" . $nombreArchivo;
  if (!copy($archivoTemporal, $destino)) {
    echo json_encode([
      'status' => 'error',
      'message' => 'Error al crear copia de respaldo del archivo',
      'registros_correctos' => 0,
      'registros_error' => 1,
      'total_procesado' => 0,
      'tasa_exito' => 0,
      'errores' => ['No se pudo crear la copia de respaldo']
    ]);
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

  // Debug: Log antes de cargar Excel
  error_log("DEBUG - Intentando cargar archivo con " . get_class($objReader));

  $objPHPExcel = $objReader->load($destino);
  $objWorksheet = $objPHPExcel->getActiveSheet();

  // Debug: Log después de cargar
  error_log("DEBUG - Archivo cargado exitosamente");

  $highestRow = $objWorksheet->getHighestRow();
  $highestColumn = $objWorksheet->getHighestColumn();
  $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);

  // Validar que el archivo tenga contenido
  if ($highestRow < 2) {
    echo json_encode([
      'status' => 'error',
      'message' => 'El archivo está vacío o solo tiene encabezados',
      'registros_correctos' => 0,
      'registros_error' => 1,
      'total_procesado' => 0,
      'tasa_exito' => 0,
      'errores' => ['El archivo debe tener al menos una fila de datos además del encabezado']
    ]);
    exit;
  }

  // Validar que tenga al menos 5 columnas (A-E)
  if ($highestColumnIndex < 5) {
    echo json_encode([
      'status' => 'error',
      'message' => 'El archivo no tiene las columnas requeridas',
      'registros_correctos' => 0,
      'registros_error' => 1,
      'total_procesado' => 0,
      'tasa_exito' => 0,
      'errores' => ["El archivo debe tener al menos 5 columnas (A-E). Encontradas: $highestColumnIndex"]
    ]);
    exit;
  }

  // ======================
  // Procesar filas Excel
  // ======================
  $datosCaratula = [];

  for ($i = 2; $i <= $highestRow; $i++) {
    $rit = trim($objWorksheet->getCell('A' . $i)->getCalculatedValue());

    if (!empty($rit)) {
      $datosCaratula[] = [
        'fila' => $i,
        'rit' => $rit,
        'ruc' => trim($objWorksheet->getCell('B' . $i)->getCalculatedValue()),
        'fechaIngreso' => trim($objWorksheet->getCell('C' . $i)->getCalculatedValue()),
        'participantes' => trim($objWorksheet->getCell('D' . $i)->getCalculatedValue()),
        'materia' => trim($objWorksheet->getCell('E' . $i)->getCalculatedValue())
      ];
    }
  }

  // Verificar que se procesaron datos
  if (empty($datosCaratula)) {
    echo json_encode([
      'status' => 'error',
      'message' => 'No se encontraron datos válidos para procesar',
      'registros_correctos' => 0,
      'registros_error' => 1,
      'total_procesado' => 0,
      'tasa_exito' => 0,
      'errores' => ['El archivo no contiene datos válidos o todas las filas están vacías']
    ]);
    exit;
  }

  // ======================
  // Inserción a BD
  // ======================

  // Debug: Log antes de insertar
  error_log("DEBUG - Intentando insertar " . count($datosCaratula) . " registros");

  $resultado = insertaAudiencias($datosCaratula);

  if ($resultado) {
    // Calcular estadísticas
    $totalProcesado = count($datosCaratula);
    $registrosInsertados = $totalProcesado; // Asumimos que si no hay error, se insertaron todos
    $registrosConError = 0;
    $tasaExito = 100.0;

    // Debug: Log resultado exitoso
    error_log("DEBUG - Inserción exitosa: $registrosInsertados registros");

    echo json_encode([
      'status' => 'success',
      'message' => 'Todos los registros fueron insertados exitosamente',
      'registros_correctos' => $registrosInsertados,
      'registros_error' => $registrosConError,
      'total_procesado' => $totalProcesado,
      'tasa_exito' => $tasaExito,
      'archivo' => $nombreArchivo,
      'fecha_procesamiento' => date('d/m/Y H:i:s'),
      'errores' => []
    ]);
  } else {
    // Debug: Log error en inserción
    error_log("DEBUG - Error en inserción de datos");

    echo json_encode([
      'status' => 'error',
      'message' => 'Error al insertar los datos en la base de datos',
      'registros_correctos' => 0,
      'registros_error' => count($datosCaratula),
      'total_procesado' => count($datosCaratula),
      'tasa_exito' => 0,
      'archivo' => $nombreArchivo,
      'fecha_procesamiento' => date('d/m/Y H:i:s'),
      'errores' => ['Error al insertar los datos en la base de datos']
    ]);
  }
} catch (Exception $e) {
  // Log del error
  error_log("Error en cargaCaratula.php: " . $e->getMessage() . " en línea " . $e->getLine());

  // Determinar tipo de error para dar mensaje más específico
  $mensaje_error = $e->getMessage();
  if (strpos($mensaje_error, 'conexión') !== false || strpos($mensaje_error, 'connect') !== false) {
    $mensaje_error = "Error de conexión con la base de datos. Verifique la configuración.";
  } elseif (strpos($mensaje_error, 'Excel') !== false || strpos($mensaje_error, 'PHPExcel') !== false) {
    $mensaje_error = "Error al procesar el archivo Excel. Verifique el formato del archivo.";
  } elseif (strpos($mensaje_error, 'extensión') !== false || strpos($mensaje_error, 'Extension') !== false) {
    $mensaje_error = "Formato de archivo no válido. Use Excel (.xlsx, .xls) o CSV.";
  }

  // Retornar JSON de error
  echo json_encode([
    'status' => 'error',
    'message' => $mensaje_error,
    'registros_correctos' => 0,
    'registros_error' => 1,
    'total_procesado' => 0,
    'tasa_exito' => 0,
    'errores' => [$mensaje_error],
    'error_tecnico' => $e->getMessage() . " (Línea: " . $e->getLine() . ")"
  ]);
}
