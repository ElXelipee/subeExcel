<?php
  require("../config/database/conexion.php");

  function insertaAudiencias($matriz){
    //var_dump($matriz);
    global $conexion;
    $error = "";
    $correcto = "";
    foreach ($matriz as $key) {
      $sql = "INSERT INTO tbl_caratula (rit, ruc, fechaIngreso, participantes, materia) VALUES (:rit, :ruc, :fechaIngreso, :participantes, :materia)";
      $stmt = $conexion->prepare($sql);
      $stmt->bindParam(':rit', $key["rit"]);
      $stmt->bindParam(':ruc', $key["ruc"]);
      $stmt->bindParam(':fechaIngreso', $key["fechaIngreso"]);
      $stmt->bindParam(':participantes', $key["participantes"]);
      $stmt->bindParam(':materia', $key["materia"]);
      
      if (!$stmt->execute()) {
        // Manejar error
        //echo "Error al insertar: " . $stmt->errorInfo()[2];
        $error ++;
      }else {
        $correcto ++;
      }
    }
    echo "Correctos: " . $correcto . " Errores: " . $error;

  }

  function insertaLitigantes($matriz){
    //var_dump($matriz);
    global $conexion;
    $error = "";
    $correcto = "";
    foreach ($matriz as $key) {
      $sql = "INSERT INTO tbl_litigantes (rit, ruc, nombre, tipoLitigante, usuarioEliminacion, fechaEliminacion, motivoEliminacion) VALUES (:rit, :ruc, :nombre, :tipoLitigante, :usuarioEliminacion, :fechaEliminacion, :motivoEliminacion)";
      $stmt = $conexion->prepare($sql);
      $stmt->bindParam(':rit', $key["rit"]);
      $stmt->bindParam(':ruc', $key["ruc"]);
      $stmt->bindParam(':nombre', $key["nombre"]);
      $stmt->bindParam(':tipoLitigante', $key["tipoLitigante"]);
      $stmt->bindParam(':usuarioEliminacion', $key["usuarioEliminacion"]);
      $stmt->bindParam(':fechaEliminacion', $key["fechaEliminacion"]);
      $stmt->bindParam(':motivoEliminacion', $key["motivoEliminacion"]);
      if (!$stmt->execute()) {
        // Manejar error
        //echo "Error al insertar: " . $stmt->errorInfo()[2];
        $error ++;
      }else {
        $correcto ++;
      }
    }
    echo "Correctos: " . $correcto . " Errores: " . $error;

  }