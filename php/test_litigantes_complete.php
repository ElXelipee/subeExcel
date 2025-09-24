<?php

/**
 * Test directo de conexión y estructura de tabla tbl_litigantes
 */

require_once '../config/config.php';
require_once '../config/database/conexion.php';

header('Content-Type: text/html; charset=utf-8');

echo "<h2>Test de Base de Datos - Litigantes</h2>";

try {
    // 1. Verificar conexión
    echo "<h3>1. Verificar conexión</h3>";
    if ($conexion) {
        echo "✅ Conexión a BD exitosa<br>";
        echo "Base de datos: " . BD_NAME . "<br>";
        echo "Host: " . BD_HOST . "<br>";
    } else {
        echo "❌ Error de conexión<br>";
        exit;
    }

    // 2. Verificar tabla
    echo "<h3>2. Verificar tabla tbl_litigantes</h3>";
    $sql = "SHOW TABLES LIKE 'tbl_litigantes'";
    $stmt = $conexion->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll();

    if (empty($result)) {
        echo "❌ La tabla 'tbl_litigantes' NO existe<br>";

        // Crear tabla si no existe
        echo "<h4>Creando tabla tbl_litigantes...</h4>";
        $createTable = "
        CREATE TABLE `tbl_litigantes` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `rit` varchar(50) NOT NULL,
          `ruc` varchar(20) NOT NULL,
          `nombre` varchar(255) NOT NULL,
          `tipoLitigante` varchar(100) NOT NULL,
          `usuarioEliminacion` varchar(100) DEFAULT NULL,
          `fechaEliminacion` varchar(20) DEFAULT NULL,
          `motivoEliminacion` text,
          `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
          PRIMARY KEY (`id`),
          KEY `idx_rit` (`rit`),
          KEY `idx_ruc` (`ruc`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ";

        $stmt = $conexion->prepare($createTable);
        if ($stmt->execute()) {
            echo "✅ Tabla creada exitosamente<br>";
        } else {
            echo "❌ Error al crear tabla: " . print_r($stmt->errorInfo(), true) . "<br>";
        }
    } else {
        echo "✅ La tabla 'tbl_litigantes' existe<br>";
    }

    // 3. Mostrar estructura
    echo "<h3>3. Estructura de la tabla</h3>";
    $sql = "DESCRIBE tbl_litigantes";
    $stmt = $conexion->prepare($sql);
    $stmt->execute();
    $columns = $stmt->fetchAll();

    echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
    echo "<tr style='background: #f0f0f0;'><th>Campo</th><th>Tipo</th><th>Nulo</th><th>Clave</th><th>Default</th></tr>";
    foreach ($columns as $column) {
        echo "<tr>";
        echo "<td>" . $column['Field'] . "</td>";
        echo "<td>" . $column['Type'] . "</td>";
        echo "<td>" . $column['Null'] . "</td>";
        echo "<td>" . $column['Key'] . "</td>";
        echo "<td>" . ($column['Default'] ?: 'NULL') . "</td>";
        echo "</tr>";
    }
    echo "</table>";

    // 4. Test de inserción manual
    echo "<h3>4. Test de inserción manual</h3>";
    $testData = [
        'rit' => 'TEST-001-2025',
        'ruc' => '12345678-9',
        'nombre' => 'Test Usuario',
        'tipoLitigante' => 'Demandante',
        'usuarioEliminacion' => '',
        'fechaEliminacion' => '',
        'motivoEliminacion' => ''
    ];

    $sql = "INSERT INTO tbl_litigantes (rit, ruc, nombre, tipoLitigante, usuarioEliminacion, fechaEliminacion, motivoEliminacion) VALUES (:rit, :ruc, :nombre, :tipoLitigante, :usuarioEliminacion, :fechaEliminacion, :motivoEliminacion)";
    $stmt = $conexion->prepare($sql);

    $stmt->bindParam(':rit', $testData['rit'], PDO::PARAM_STR);
    $stmt->bindParam(':ruc', $testData['ruc'], PDO::PARAM_STR);
    $stmt->bindParam(':nombre', $testData['nombre'], PDO::PARAM_STR);
    $stmt->bindParam(':tipoLitigante', $testData['tipoLitigante'], PDO::PARAM_STR);
    $stmt->bindParam(':usuarioEliminacion', $testData['usuarioEliminacion'], PDO::PARAM_STR);
    $stmt->bindParam(':fechaEliminacion', $testData['fechaEliminacion'], PDO::PARAM_STR);
    $stmt->bindParam(':motivoEliminacion', $testData['motivoEliminacion'], PDO::PARAM_STR);

    if ($stmt->execute()) {
        echo "✅ Inserción manual exitosa<br>";
        echo "ID insertado: " . $conexion->lastInsertId() . "<br>";
    } else {
        echo "❌ Error en inserción manual:<br>";
        $errorInfo = $stmt->errorInfo();
        echo "SQLSTATE: " . $errorInfo[0] . "<br>";
        echo "Error Code: " . $errorInfo[1] . "<br>";
        echo "Error Message: " . $errorInfo[2] . "<br>";
    }

    // 5. Verificar registros
    echo "<h3>5. Registros en la tabla</h3>";
    $sql = "SELECT COUNT(*) as total FROM tbl_litigantes";
    $stmt = $conexion->prepare($sql);
    $stmt->execute();
    $count = $stmt->fetch();
    echo "Total de registros: <strong>" . $count['total'] . "</strong><br>";

    if ($count['total'] > 0) {
        echo "<h4>Últimos 5 registros:</h4>";
        $sql = "SELECT * FROM tbl_litigantes ORDER BY id DESC LIMIT 5";
        $stmt = $conexion->prepare($sql);
        $stmt->execute();
        $records = $stmt->fetchAll();

        echo "<table border='1' style='border-collapse: collapse; margin: 10px 0; width: 100%;'>";
        echo "<tr style='background: #f0f0f0;'><th>ID</th><th>RIT</th><th>RUC</th><th>Nombre</th><th>Tipo</th><th>Fecha Creación</th></tr>";
        foreach ($records as $record) {
            echo "<tr>";
            echo "<td>" . $record['id'] . "</td>";
            echo "<td>" . $record['rit'] . "</td>";
            echo "<td>" . $record['ruc'] . "</td>";
            echo "<td>" . $record['nombre'] . "</td>";
            echo "<td>" . $record['tipoLitigante'] . "</td>";
            echo "<td>" . ($record['created_at'] ?? 'N/A') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
    echo "<p>Archivo: " . $e->getFile() . "</p>";
    echo "<p>Línea: " . $e->getLine() . "</p>";
}
