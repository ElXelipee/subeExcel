<?php

/**
 * Archivo para cargar datos de Audiencias TV desde Excel
 * Versión: 3.0 - Retorna JSON para modal
 * Fecha: 24 Septiembre 2025
 */

require_once '../config/config.php';
require_once '../config/database/conexion.php';
require_once '../lib/PHPExcel/PHPExcel.php';
require_once '../lib/function.php';

// Configurar cabeceras para JSON
header('Content-Type: application/json; charset=utf-8');

function conectarBD()
{
    $servidor   = "localhost";
    $usuario    = "root";
    $password   = "";
    $base_datos = "pj_audiencias_sys";

    $conexion = new mysqli($servidor, $usuario, $password, $base_datos);
    if ($conexion->connect_error) {
        throw new Exception("Error de conexión: " . $conexion->connect_error);
    }
    $conexion->set_charset("utf8");
    return $conexion;
}

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
            'registros_error' => 0,
            'total_procesado' => 0,
            'tasa_exito' => 0,
            'errores' => ["Extensión $extensionArchivo no está permitida"]
        ]);
        exit;
    }

    // Cargar Excel
    if (strtolower($extensionArchivo) === 'csv') {
        $objReader = PHPExcel_IOFactory::createReader('CSV');
        // Configurar opciones de CSV si están disponibles
        if (method_exists($objReader, 'setDelimiter')) {
            $objReader->setDelimiter(',');
        }
        if (method_exists($objReader, 'setEnclosure')) {
            $objReader->setEnclosure('"');
        }
        if (method_exists($objReader, 'setLineEnding')) {
            $objReader->setLineEnding("\r\n");
        }
    } else {
        $objReader = PHPExcel_IOFactory::createReader('Excel2007');
        if (strtolower($extensionArchivo) === 'xls') {
            $objReader = PHPExcel_IOFactory::createReader('Excel5');
        }
    }

    // Debug: Log antes de cargar Excel
    error_log("DEBUG - Intentando cargar archivo con " . get_class($objReader));

    $objPHPExcel  = $objReader->load($archivoTemporal);
    $objWorksheet = $objPHPExcel->getActiveSheet();

    // Debug: Log después de cargar
    error_log("DEBUG - Archivo cargado exitosamente");

    $highestRow         = $objWorksheet->getHighestRow();
    $highestColumn      = $objWorksheet->getHighestColumn();
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

    // Validar que tenga al menos 12 columnas
    if ($highestColumnIndex < 12) {
        echo json_encode([
            'status' => 'error',
            'message' => 'El archivo no tiene las columnas requeridas',
            'registros_correctos' => 0,
            'registros_error' => 1,
            'total_procesado' => 0,
            'tasa_exito' => 0,
            'errores' => ["El archivo debe tener al menos 12 columnas (A-L). Encontradas: $highestColumnIndex"]
        ]);
        exit;
    }

    // ======================
    // Procesar filas Excel
    // ======================
    $datosAudienciasTV = [];

    for ($row = 2; $row <= $highestRow; $row++) {
        $fila      = [];
        $filaVacia = true;

        for ($col = 0; $col < 12; $col++) {
            $columnLetter = PHPExcel_Cell::stringFromColumnIndex($col);
            $cell         = $objWorksheet->getCell($columnLetter . $row);

            switch ($col) {
                case 0: // fecha
                    if (PHPExcel_Shared_Date::isDateTime($cell)) {
                        $dateValue = PHPExcel_Shared_Date::ExcelToPHP($cell->getCalculatedValue());
                        $fila[$col] = date('Y-m-d', (int)$dateValue);
                    } else {
                        $fila[$col] = trim($cell->getCalculatedValue());
                    }
                    break;
                case 2: // hora
                    if (PHPExcel_Shared_Date::isDateTime($cell)) {
                        $timeValue = PHPExcel_Shared_Date::ExcelToPHP($cell->getCalculatedValue());
                        $fila[$col] = date('H:i', (int)$timeValue);
                    } else {
                        $fila[$col] = trim($cell->getCalculatedValue());
                    }
                    break;
                default:
                    $fila[$col] = trim($cell->getCalculatedValue());
                    break;
            }

            if (!empty($fila[$col])) {
                $filaVacia = false;
            }
        }

        if (!$filaVacia) {
            $datosAudienciasTV[] = [
                'fila'          => $row,
                'f_audiencia'   => $fila[0],
                'sala'          => $fila[1],
                'h_inicio'      => $fila[2],
                'rit'           => $fila[3],
                'caj'           => $fila[4],
                'caratulado'    => $fila[5],
                'tipo_audiencia' => $fila[6],
                'materia'       => $fila[7],
                'juez'          => $fila[8],
                'acta'          => $fila[9],
                'ct'            => $fila[10],
                'cuenta_zoom'   => $fila[11]
            ];
        }
    }

    // Verificar que se procesaron datos
    if (empty($datosAudienciasTV)) {
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

    $tablaHorarios = [
        '08:30' => ['termino' => '09:00', 'bloque_inicio' => 3, 'bloque_termino' => 5, 'cantidad_bloques' => 3],
        '09:15' => ['termino' => '09:45', 'bloque_inicio' => 6, 'bloque_termino' => 8, 'cantidad_bloques' => 3],
        '10:00' => ['termino' => '10:30', 'bloque_inicio' => 9, 'bloque_termino' => 11, 'cantidad_bloques' => 3],
        '11:00' => ['termino' => '11:30', 'bloque_inicio' => 13, 'bloque_termino' => 15, 'cantidad_bloques' => 3],
        '11:45' => ['termino' => '12:15', 'bloque_inicio' => 16, 'bloque_termino' => 18, 'cantidad_bloques' => 3],
        '12:30' => ['termino' => '13:00', 'bloque_inicio' => 19, 'bloque_termino' => 21, 'cantidad_bloques' => 3]
    ];

    // Debug: Log antes de conectar BD
    error_log("DEBUG - Intentando conectar a base de datos");

    $conexion = conectarBD();

    // Debug: Log después de conectar
    error_log("DEBUG - Conexión a BD exitosa");

    $sql = "INSERT INTO tbl_programada (
        pro_sa_num_sala, pro_nombre_juez, pro_adm_nombre, pro_ct_nombre,
        id_tribunal, pro_cod_tribunal, pro_fn_descripcion, pro_origen,
        pro_rit, pro_motivo, pro_juez_solicitante, pro_etapa,
        fecha_programada, pro_hora_inicio, pro_hora_termino,
        pro_bloque_inicio, pro_bloque_termino, pro_cantidad_bloques,
        pro_res_descripcion, pro_caratula, pro_responsable_agenda,
        pro_au_descripcion, pro_estado, id_solicitud, pro_curador,
        pro_radicada, pro_actualizacion, pro_usu_actualizacion,
        flag_reprogramacion, pro_motivo_repro, pro_check, observacion,
        infoPublico, pro_check_inicio, pro_check_termino
    ) VALUES (" . str_repeat('?,', 34) . "?)";

    $stmt = $conexion->prepare($sql);
    if (!$stmt) {
        throw new Exception("Error preparar SQL: " . $conexion->error);
    }

    $registrosInsertados = 0;
    $registrosConError   = 0;
    $errores             = [];

    foreach ($datosAudienciasTV as $registro) {
        try {
            // Normalizar hora
            $horaInicio = date('H:i', strtotime($registro['h_inicio']));
            $infoBloques = $tablaHorarios[$horaInicio] ?? null;

            if (!$infoBloques) {
                $errores[] = "Fila {$registro['fila']}: hora '{$registro['h_inicio']}' no está en la tabla de horarios permitidos";
                $registrosConError++;
                continue;
            }

            $vars = [
                (int)$registro['sala'],
                $registro['juez'],
                $registro['acta'],
                $registro['ct'],
                4,
                null,
                null,
                null,
                $registro['rit'],
                'Carga Masiva',
                null,
                $registro['tipo_audiencia'],
                $registro['f_audiencia'],
                $horaInicio . ':00',
                $infoBloques['termino'] . ':00',
                $infoBloques['bloque_inicio'],
                $infoBloques['bloque_termino'],
                $infoBloques['cantidad_bloques'],
                null,
                $registro['caratulado'],
                'Cuenta Genérica',
                $registro['tipo_audiencia'],
                'P',
                0,
                $registro['caj'],
                'NO RADICADA',
                null,
                'Cuenta Genérica',
                null,
                null,
                'En espera',
                $registro['cuenta_zoom'],
                null,
                null,
                null
            ];

            // Generar cadena de tipos
            $types = '';
            foreach ($vars as $v) {
                if (is_int($v) || (is_numeric($v) && ctype_digit((string)$v))) {
                    $types .= 'i';
                } else {
                    $types .= 's';
                }
            }

            $stmt->bind_param($types, ...$vars);

            if ($stmt->execute()) {
                $registrosInsertados++;
            } else {
                $errores[] = "Error fila {$registro['fila']}: " . $stmt->error;
                $registrosConError++;
            }
        } catch (Exception $e) {
            $errores[] = "Error fila {$registro['fila']}: " . $e->getMessage();
            $registrosConError++;
        }
    }

    $stmt->close();
    $conexion->close();

    // Calcular tasa de éxito
    $totalProcesado = count($datosAudienciasTV);
    $tasaExito = $totalProcesado > 0 ? round(($registrosInsertados / $totalProcesado) * 100, 1) : 0;

    // Determinar status
    $status = 'success';
    if ($registrosInsertados == 0) {
        $status = 'error';
    } else if ($registrosConError > 0) {
        $status = 'warning';
    }

    // Determinar mensaje apropiado
    $mensaje = 'Proceso completado';
    if ($status === 'success') {
        $mensaje = 'Todos los registros fueron insertados exitosamente';
    } elseif ($status === 'warning') {
        $mensaje = "Se insertaron $registrosInsertados registros correctamente, pero $registrosConError tuvieron errores";
    } elseif ($status === 'error') {
        $mensaje = 'No se pudo insertar ningún registro. Revise los errores detallados';
    }

    // Debug: Log resultado final
    error_log("DEBUG - Proceso terminado: $mensaje");

    // Retornar JSON con resultados
    echo json_encode([
        'status' => $status,
        'message' => $mensaje,
        'registros_correctos' => $registrosInsertados,
        'registros_error' => $registrosConError,
        'total_procesado' => $totalProcesado,
        'tasa_exito' => $tasaExito,
        'archivo' => $nombreArchivo,
        'fecha_procesamiento' => date('d/m/Y H:i:s'),
        'errores' => $errores
    ]);
} catch (Exception $e) {
    // Log del error
    error_log("Error en cargaAudienciasTV.php: " . $e->getMessage() . " en línea " . $e->getLine());

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
