<?php

/**
 * Cargador de Inventario MDA desde Excel + Inserción en BD
 * Versión: 3.4
 * Fecha: 26 Septiembre 2025
 */

require_once '../lib/PHPExcel/PHPExcel.php';
header('Content-Type: application/json; charset=utf-8');

/**
 * Configuración de conexión a MySQL
 */
$dbHost = "localhost";   // Servidor
$dbName = "todo_db";     // Nombre de la BD
$dbUser = "root";        // Usuario
$dbPass = "";            // Contraseña

try {
    $pdo = new PDO("mysql:host=$dbHost;dbname=$dbName;charset=utf8mb4", $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
} catch (PDOException $e) {
    die("❌ Error de conexión a la BD: " . $e->getMessage());
}

/**
 * Función para calcular el dígito verificador de un RUT chileno
 * Compatible con RUT bajos (ej: 100, 1400, etc.)
 */
function calcularDigitoVerificadorRUT($rut)
{
    $rut = preg_replace('/[^0-9]/', '', $rut);

    if ($rut === '' || !ctype_digit($rut)) {
        return false;
    }

    // Completar con ceros a la izquierda para cálculo
    $rutCalc = str_pad($rut, 7, '0', STR_PAD_LEFT);

    $suma = 0;
    $multiplo = 2;
    for ($i = strlen($rutCalc) - 1; $i >= 0; $i--) {
        $suma += intval($rutCalc[$i]) * $multiplo;
        $multiplo++;
        if ($multiplo == 8) $multiplo = 2;
    }

    $resto = $suma % 11;
    $dv = 11 - $resto;

    if ($dv == 11) {
        $dv = '0';
    } elseif ($dv == 10) {
        $dv = 'K';
    }

    // Devolver sin ceros innecesarios
    return intval($rut) . '-' . $dv;
}

/**
 * Validación de datos
 */
function validarInventarioMDA($datos)
{
    $errores = [];
    foreach ($datos as $index => $fila) {
        $filaExcel = $index + 2;
        if (empty($fila['tipo_equipo'])) $errores[] = ['fila' => $filaExcel, 'tipo' => 'Tipo Equipo Vacío'];
        if (empty($fila['subtipo_equipo'])) $errores[] = ['fila' => $filaExcel, 'tipo' => 'Subtipo Equipo Vacío'];
        if (empty($fila['marca_modelo'])) $errores[] = ['fila' => $filaExcel, 'tipo' => 'Marca/Modelo Vacío'];
        if (empty($fila['n_serie'])) $errores[] = ['fila' => $filaExcel, 'tipo' => 'N° Serie Vacío'];
        if (empty($fila['estado_equipo']) && $fila['estado_equipo'] !== '0') $errores[] = ['fila' => $filaExcel, 'tipo' => 'Estado Equipo Vacío'];
        if (empty($fila['empresa'])) $errores[] = ['fila' => $filaExcel, 'tipo' => 'Empresa Vacía'];

        // Validar RUT solo si viene con valor
        if (!empty($fila['rut']) && !ctype_digit($fila['rut'])) {
            $errores[] = ['fila' => $filaExcel, 'tipo' => 'RUT Inválido'];
        }
    }
    return $errores;
}

try {
    if (!isset($_FILES['archivoExcel']) || $_FILES['archivoExcel']['error'] !== UPLOAD_ERR_OK) {
        echo json_encode([
            'status' => 'error',
            'message' => 'No se pudo procesar el archivo',
            'registros_correctos' => 0,
            'registros_error' => 1,
            'total_procesado' => 0,
            'tasa_exito' => 0,
            'errores' => [['fila' => 'N/A', 'tipo' => 'Archivo', 'descripcion' => 'Error al recibir el archivo']]
        ]);
        exit;
    }

    $nombreArchivo = $_FILES['archivoExcel']['name'];
    $rutaArchivoTemporal = $_FILES['archivoExcel']['tmp_name'];
    $extensionArchivo = pathinfo($nombreArchivo, PATHINFO_EXTENSION);

    $destino = '../temp/' . time() . '_' . $nombreArchivo;
    if (!file_exists('../temp/')) mkdir('../temp/', 0777, true);
    move_uploaded_file($rutaArchivoTemporal, $destino);

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

    $datosInventario = [];
    for ($i = 2; $i <= $highestRow; $i++) {
        $valores = [];
        for ($col = 'A'; $col <= 'J'; $col++) {
            $valores[$col] = trim((string) $objWorksheet->getCell($col . $i)->getCalculatedValue());
        }
        $tieneAlgunDato = array_filter($valores);
        if ($tieneAlgunDato) {
            $datosInventario[] = [
                'tipo_equipo'     => $valores['A'],
                'subtipo_equipo'  => $valores['B'],
                'marca_modelo'    => $valores['C'],
                'n_serie'         => $valores['D'],
                'n_rotulo'        => $valores['E'] !== '' ? $valores['E'] : null,
                'n_activo_fijo'   => $valores['F'] !== '' ? $valores['F'] : null,
                'estado_equipo'   => $valores['G'],
                'empresa'         => $valores['H'],
                'rut'             => $valores['I'] !== '' ? $valores['I'] : null,
                'rut_con_dv'      => !empty($valores['I']) ? calcularDigitoVerificadorRUT($valores['I']) : null,
                'ip'              => $valores['J'],
                'nombre'          => null // tu Excel no trae nombre, se inserta NULL
            ];
        }
    }

    $errores = validarInventarioMDA($datosInventario);
    $totalRegistros = count($datosInventario);
    $registrosConError = count($errores);
    $registrosCorrectos = 0;

    // Preparar array de errores con descripción
    $erroresDetallados = [];
    foreach ($errores as $error) {
        $erroresDetallados[] = [
            'fila' => $error['fila'],
            'tipo' => $error['tipo'],
            'descripcion' => $error['tipo'] // Por ahora usamos el tipo como descripción
        ];
    }

    if (count($errores) > 0) {
        // Hay errores, no insertar
        $tasaExito = 0;
        echo json_encode([
            'status' => 'error',
            'message' => 'Se encontraron errores de validación',
            'registros_correctos' => 0,
            'registros_error' => $registrosConError,
            'total_procesado' => $totalRegistros,
            'tasa_exito' => $tasaExito,
            'errores' => $erroresDetallados
        ]);
    } else {
        // Sin errores, proceder con inserción
        $sql = "INSERT INTO mda_inventory 
            (nserie, nombreEquipo, ip, tipoEquipamiento, subTipoEquipamiento, marcaModelo, 
             nRotulo, nActivoFijo, estadoEquipo, rutFuncionario, inventariado, empresa, archivo) 
            VALUES 
            (:nserie, :nombreEquipo, :ip, :tipoEquipamiento, :subTipoEquipamiento, :marcaModelo, 
             :nRotulo, :nActivoFijo, :estadoEquipo, :rutFuncionario, :inventariado, :empresa, :archivo)";
        $stmt = $pdo->prepare($sql);

        $insertados = 0;
        $erroresInsercion = [];

        foreach ($datosInventario as $index => $fila) {
            try {
                $stmt->execute([
                    ':nserie'              => $fila['n_serie'],
                    ':nombreEquipo'        => $fila['nombre'],
                    ':ip'                  => $fila['ip'] ?: null,
                    ':tipoEquipamiento'    => $fila['tipo_equipo'],
                    ':subTipoEquipamiento' => $fila['subtipo_equipo'],
                    ':marcaModelo'         => $fila['marca_modelo'],
                    ':nRotulo'             => $fila['n_rotulo'],
                    ':nActivoFijo'         => $fila['n_activo_fijo'],
                    ':estadoEquipo'        => $fila['estado_equipo'],
                    ':rutFuncionario'      => $fila['rut_con_dv'],
                    ':inventariado'        => null,
                    ':empresa'             => $fila['empresa'],
                    ':archivo'             => $nombreArchivo
                ]);
                $insertados++;
            } catch (PDOException $e) {
                $erroresInsercion[] = [
                    'fila' => $index + 2,
                    'tipo' => 'Error BD',
                    'descripcion' => $e->getMessage()
                ];
            }
        }

        $registrosCorrectos = $insertados;
        $registrosConError = count($erroresInsercion);
        $tasaExito = $totalRegistros > 0 ? round(($registrosCorrectos / $totalRegistros) * 100, 2) : 0;

        if ($registrosConError > 0) {
            echo json_encode([
                'status' => 'warning',
                'message' => 'Carga completada con algunos errores',
                'registros_correctos' => $registrosCorrectos,
                'registros_error' => $registrosConError,
                'total_procesado' => $totalRegistros,
                'tasa_exito' => $tasaExito,
                'errores' => $erroresInsercion
            ]);
        } else {
            echo json_encode([
                'status' => 'success',
                'message' => 'Todos los registros se insertaron correctamente',
                'registros_correctos' => $registrosCorrectos,
                'registros_error' => 0,
                'total_procesado' => $totalRegistros,
                'tasa_exito' => $tasaExito,
                'errores' => []
            ]);
        }
    }

    unlink($destino);
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Error en el procesamiento',
        'registros_correctos' => 0,
        'registros_error' => 1,
        'total_procesado' => 0,
        'tasa_exito' => 0,
        'errores' => [['fila' => 'N/A', 'tipo' => 'Sistema', 'descripcion' => $e->getMessage()]]
    ]);
}
