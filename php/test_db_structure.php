<?php

/**
 * Script para verificar la estructura de la tabla tbl_litigantes
 */

require_once '../config/config.php';
require_once '../config/database/conexion.php';

try {
    echo "<h2>Verificación de estructura de tbl_litigantes</h2>";

    // Verificar si la tabla existe
    $sql = "SHOW TABLES LIKE 'tbl_litigantes'";
    $stmt = $conexion->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll();

    if (empty($result)) {
        echo "<p style='color: red;'>❌ La tabla 'tbl_litigantes' NO existe</p>";

        // Mostrar todas las tablas disponibles
        echo "<h3>Tablas disponibles en la base de datos:</h3>";
        $sql = "SHOW TABLES";
        $stmt = $conexion->prepare($sql);
        $stmt->execute();
        $tables = $stmt->fetchAll();

        echo "<ul>";
        foreach ($tables as $table) {
            echo "<li>" . $table[0] . "</li>";
        }
        echo "</ul>";
    } else {
        echo "<p style='color: green;'>✅ La tabla 'tbl_litigantes' existe</p>";

        // Mostrar estructura de la tabla
        echo "<h3>Estructura de la tabla tbl_litigantes:</h3>";
        $sql = "DESCRIBE tbl_litigantes";
        $stmt = $conexion->prepare($sql);
        $stmt->execute();
        $columns = $stmt->fetchAll();

        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>Campo</th><th>Tipo</th><th>Nulo</th><th>Clave</th><th>Default</th><th>Extra</th></tr>";
        foreach ($columns as $column) {
            echo "<tr>";
            echo "<td>" . $column['Field'] . "</td>";
            echo "<td>" . $column['Type'] . "</td>";
            echo "<td>" . $column['Null'] . "</td>";
            echo "<td>" . $column['Key'] . "</td>";
            echo "<td>" . $column['Default'] . "</td>";
            echo "<td>" . $column['Extra'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";

        // Verificar si hay registros
        echo "<h3>Número de registros en la tabla:</h3>";
        $sql = "SELECT COUNT(*) as total FROM tbl_litigantes";
        $stmt = $conexion->prepare($sql);
        $stmt->execute();
        $count = $stmt->fetch();
        echo "<p>Total de registros: <strong>" . $count['total'] . "</strong></p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
