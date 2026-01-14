<?php

// Prevenir cualquier salida previa
ob_start();

/**
 * Archivo para analizar tipos de datos en Excel
 * Versión: 1.0
 * Fecha: 14 Enero 2026
 * 
 * Descripción: Lee un archivo Excel desde A1 con 20 columnas máximo
 * y determina el tipo de dato en cada celda
 */

require_once '../config/config.php';
require_once '../lib/PHPExcel/PHPExcel.php';

// Limpiar el buffer de salida
ob_end_clean();

// Configurar cabeceras para JSON
header('Content-Type: application/json; charset=utf-8');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Pragma: no-cache');

// Configurar manejo de errores
error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);
set_error_handler(function ($errno, $errstr, $errfile, $errline) {
  // Ignorar deprecated warnings
  if ($errno === E_DEPRECATED || $errno === E_USER_DEPRECATED) {
    return true;
  }

  http_response_code(400);
  echo json_encode([
    'status' => 'error',
    'message' => 'Error del servidor: ' . $errstr . ' en ' . basename($errfile) . ' línea ' . $errline
  ], JSON_UNESCAPED_UNICODE);
  exit;
});

// ======================
// Verificar archivo
// ======================
if (!isset($_FILES) || !is_array($_FILES) || !isset($_FILES['archivoExcel']) || !is_array($_FILES['archivoExcel'])) {
  echo json_encode([
    'status' => 'error',
    'message' => 'No se recibió el archivo correctamente o el formato de envío es inválido.'
  ]);
  exit;
}

$archivo = $_FILES['archivoExcel'];
$error_code = $archivo['error'] ?? UPLOAD_ERR_NO_FILE;

if ($error_code !== UPLOAD_ERR_OK) {
  $error_message = 'No se recibió el archivo';
  switch ($error_code) {
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
      $error_message = 'Error desconocido al subir el archivo (Código: ' . $error_code . ')';
      break;
  }

  echo json_encode([
    'status' => 'error',
    'message' => $error_message,
  ]);
  exit;
}

$file_type = $archivo['type'] ?? null;
$file_name = $archivo['name'] ?? null;
$file_tmp = $archivo['tmp_name'] ?? null;

// Validar que existan valores y sean strings
if (!is_string($file_name) || !is_string($file_tmp) || empty($file_name) || empty($file_tmp)) {
  echo json_encode([
    'status' => 'error',
    'message' => 'Error: Los datos del archivo son inválidos'
  ]);
  exit;
}

// Validar extensión
$allowed_extensions = ['xls', 'xlsx', 'csv'];
$file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION) ?? '');

if (empty($file_extension) || !in_array($file_extension, $allowed_extensions)) {
  echo json_encode([
    'status' => 'error',
    'message' => 'Tipo de archivo no permitido. Use .xls, .xlsx o .csv'
  ]);
  exit;
}

// Inicializar variables antes del try
$datos = [];
$tipo_datos_columnas = [];
$max_fila = 0;
$max_columna = 0;

try {
  // Evitar que PHPExcel output cualquier cosa
  ob_start();

  // Crear objeto PHPExcel IOFactory
  $objReader = PHPExcel_IOFactory::createReaderForFile($file_tmp);
  $objPHPExcel = $objReader->load($file_tmp);

  // Limpiar cualquier output
  ob_end_clean();

  // Obtener la hoja activa
  $sheet = null;

  // Si se especificó una hoja en particular
  if (isset($_POST['hoja']) && !empty($_POST['hoja'])) {
    $sheet = $objPHPExcel->getSheetByName($_POST['hoja']);
  }

  // Si no se especificó o no existe, usar la primera
  if ($sheet === null) {
    $sheet = $objPHPExcel->getActiveSheet();
  }

  // ======================
  // Analizar datos
  // ======================

  // Limitar a 20 columnas (A a T)
  $columnas_permitidas = range('A', 'T'); // A-T son 20 columnas

  // Recorrer desde A1
  foreach ($sheet->getRowIterator() as $row) {
    $row_number = $row->getRowIndex();
    $columna_index = 0;

    foreach ($row->getCellIterator() as $cell) {
      $columna_letra = $cell->getColumn();

      // Solo procesar hasta 20 columnas
      if (!in_array($columna_letra, $columnas_permitidas)) {
        break;
      }

      // Obtener información real de la celda de Excel
      $valor_crudo = null;
      $valor_formateado = null;
      $tipo_excel = $cell->getDataType();
      $es_fecha_hora = PHPExcel_Shared_Date::isDateTime($cell);

      try {
        $valor_crudo = $cell->getValue();
        $valor_formateado = $cell->getFormattedValue();

        // Manejar RichText
        if ($valor_crudo instanceof PHPExcel_RichText) {
          $valor_crudo = $valor_crudo->getPlainText();
        }
      } catch (Exception $e) {
        $valor_crudo = '';
        $valor_formateado = '';
      }

      $tipo_dato = 'String';
      $detalles = '';
      $valor_display = $valor_formateado;

      // Determinar tipo basado estrictamente en el contenido real de Excel
      if ($valor_crudo === null || $valor_crudo === '') {
        $tipo_dato = 'Empty';
        $detalles = 'Celda sin contenido';
      } else if ($es_fecha_hora) {
        // Es una fecha u hora real de Excel
        if (esFormatoHora($valor_formateado)) {
          $tipo_dato = 'Time';
          $detalles = 'Hora (Formato nativo Excel)';
        } else {
          $tipo_dato = 'Date';
          $detalles = 'Fecha (Formato nativo Excel)';
        }
      } else {
        switch ($tipo_excel) {
          case PHPExcel_Cell_DataType::TYPE_NUMERIC:
            if (floor($valor_crudo) == $valor_crudo) {
              $tipo_dato = 'Integer';
              $detalles = 'Número entero nativo';
            } else {
              $tipo_dato = 'Float';
              $detalles = 'Número decimal nativo';
            }
            break;
          case PHPExcel_Cell_DataType::TYPE_BOOL:
            $tipo_dato = 'Boolean';
            $detalles = 'Valor lógico nativo (VERDADERO/FALSO)';
            break;
          case PHPExcel_Cell_DataType::TYPE_FORMULA:
            $tipo_dato = 'Formula';
            $detalles = 'Fórmula: ' . $valor_crudo;
            break;
          case PHPExcel_Cell_DataType::TYPE_ERROR:
            $tipo_dato = 'Error';
            $detalles = 'Error de Excel: ' . $valor_crudo;
            break;
          case PHPExcel_Cell_DataType::TYPE_STRING:
          default:
            $tipo_dato = 'String';
            $detalles = 'Texto/Cadena de caracteres';
            break;
        }
      }

      // Actualizar máximos
      if ($row_number > $max_fila) {
        $max_fila = $row_number;
      }
      if ($columna_index + 1 > $max_columna && $valor_crudo !== '') {
        $max_columna = $columna_index + 1;
      }

      // Asegurar que valor_display siempre está definido
      if ($valor_display === null) {
        $valor_display = '';
      }

      // Agregar a resultados
      $datos[] = [
        'fila' => $row_number,
        'columna' => $columna_letra,
        'valor' => $valor_display,
        'tipo_dato' => $tipo_dato,
        'detalles' => $detalles
      ];

      // Registrar tipos de datos por columna
      if (!isset($tipo_datos_columnas[$columna_letra])) {
        $tipo_datos_columnas[$columna_letra] = [];
      }
      $tipo_datos_columnas[$columna_letra][] = $tipo_dato;

      $columna_index++;
    }
  }

  // ======================
  // Contar celdas vacías
  // ======================
  $celdas_vacias = 0;
  foreach ($datos as $celda) {
    if ($celda['tipo_dato'] === 'Empty') {
      $celdas_vacias++;
    }
  }

  // ======================
  // Preparar respuesta
  // ======================
  $resumen = [
    'total_celdas' => count($datos),
    'filas' => $max_fila > 0 ? $max_fila : 0,
    'columnas' => $max_columna,
    'celdas_vacias' => $celdas_vacias,
    'archivo' => $file_name,
    'hoja' => $sheet->getTitle()
  ];

  // Limpiar cualquier output antes de enviar JSON
  ob_end_clean();

  echo json_encode([
    'status' => 'success',
    'message' => 'Análisis completado exitosamente',
    'datos' => $datos,
    'resumen' => $resumen
  ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
} catch (Exception $e) {
  // Limpiar output antes de error
  ob_end_clean();

  error_log('Error en analizarTiposDatos.php: ' . $e->getMessage());
  http_response_code(400);
  echo json_encode([
    'status' => 'error',
    'message' => 'Error al procesar el archivo: ' . $e->getMessage()
  ], JSON_UNESCAPED_UNICODE);
  exit;
}

// ======================
// Funciones auxiliares
// ======================

/**
 * Verificar si un valor tiene formato de fecha
 */
function esFormatoFecha($valor)
{
  // Validar que es string y no vacío
  if (!is_string($valor) || empty($valor)) {
    return false;
  }

  $valor = trim($valor);

  // Patrones comunes de fechas
  $patrones = [
    '/^\d{1,2}[\/\-]\d{1,2}[\/\-]\d{2,4}$/',  // DD/MM/YYYY o MM/DD/YYYY
    '/^\d{4}[\/\-]\d{1,2}[\/\-]\d{1,2}$/',    // YYYY/MM/DD
    '/^(0[1-9]|[12][0-9]|3[01])\/(0[1-9]|1[012])\/\d{4}$/', // DD/MM/YYYY
    '/^(Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec)[a-z]* \d{1,2},? \d{4}$/', // Month DD, YYYY
  ];

  foreach ($patrones as $patron) {
    if (preg_match($patron, $valor)) {
      return true;
    }
  }

  return false;
}

/**
 * Verificar si un valor tiene formato de hora
 */
function esFormatoHora($valor)
{
  if (!is_string($valor) || empty($valor)) {
    return false;
  }

  $valor = trim($valor);

  // Patrones comunes de horas: HH:MM, HH:MM:SS, HH:MM AM/PM
  $patrones = [
    '/^(?:2[0-3]|[01][0-9]):[0-5][0-9](?::[0-5][0-9])?$/', // 24h: HH:MM o HH:MM:SS
    '/^(?:1[0-2]|0?[1-9]):[0-5][0-9](?::[0-5][0-9])?\s?(?:AM|PM|am|pm)$/', // 12h AM/PM
  ];

  foreach ($patrones as $patron) {
    if (preg_match($patron, $valor)) {
      return true;
    }
  }

  return false;
}

/**
 * Detectar el formato específico de fecha
 */
function detectarFormatoFecha($valor)
{
  // Validar que es string
  if (!is_string($valor) || empty($valor)) {
    return 'Fecha';
  }

  $valor = trim($valor);

  if (preg_match('/^\d{4}[\/\-]\d{1,2}[\/\-]\d{1,2}$/', $valor)) {
    return 'YYYY-MM-DD';
  } else if (preg_match('/^\d{1,2}[\/\-]\d{1,2}[\/\-]\d{4}$/', $valor)) {
    return 'DD/MM/YYYY o MM/DD/YYYY';
  }
  return 'Fecha';
}

/**
 * Verificar si un valor es numérico
 */
function esNumerico($valor)
{
  // Validar que es string
  if (!is_string($valor) || empty($valor)) {
    return false;
  }

  return is_numeric(trim($valor));
}

/**
 * Verificar si un valor es booleano
 */
function esBooleano($valor)
{
  // Validar que es string
  if (!is_string($valor) || empty($valor)) {
    return false;
  }

  $valor_lower = strtolower(trim($valor));
  return in_array($valor_lower, ['true', 'false', 'si', 'no', 'sí', 'yes', 'no', '0', '1']);
}
