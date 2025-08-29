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

  function eliminaLitigantes($matriz){
    //var_dump($matriz);
    global $conexion;
    $error = "";
    $correcto = "";
    foreach ($matriz as $key) {
      $sql = "DELETE FROM tbl_litigantes WHERE (rit = :rit AND ruc = :ruc AND nombre = :nombre AND tipoLitigante = :tipoLitigante)";
      $stmt = $conexion->prepare($sql);
      $stmt->bindParam(':rit', $key["rit"]);
      $stmt->bindParam(':ruc', $key["ruc"]);
      $stmt->bindParam(':nombre', $key["nombre"]);
      $stmt->bindParam(':tipoLitigante', $key["tipoLitigante"]);
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

  function insertaHorasAudiencias($matriz){
    global $conexion;
    $error = 0;
    $correcto =  0;
    foreach ($matriz as $key) {
      $sql = "INSERT INTO tbl_programacion_audiencias (fechaHora, sala, tipoBloque, juezAudiencia, cuentaZoom, estado) VALUES (:fechaHora, :sala, :tipoBloque, :juezAudiencia, :zoom, :estado)";
      $stmt = $conexion->prepare($sql);
      $stmt->bindParam(':fechaHora', $key["fechaHora"]);
      $stmt->bindParam(':sala', $key["sala"]);
      $stmt->bindParam(':tipoBloque', $key["tipoBloque"]);
      $stmt->bindParam(':juezAudiencia', $key["juezAudiencia"]);
      $stmt->bindParam(':zoom', $key["zoom"]);
      $stmt->bindParam(':estado', $key["estado"]);
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

  function cargaCursosFuncionarios1($matriz){
    global $conexion;
    $error = 0;
    $correcto =  0;
    var_dump($matriz);
    foreach ($matriz as $key) {
      //primreo obtengo la unidad del funcionario.
      global $conexion;
      $sql = "SELECT id_unidad AS unidad FROM tbl_funcionarios_vigencia WHERE CURDATE() BETWEEN desde AND hasta AND rut = :rut limit 1";
      $stmt = $conexion->prepare($sql);
      $stmt->bindParam(':rut', $key['rut']);
      if (!$stmt->execute()) {
        echo 'Error al pillar la unidad, el rut es $key["rut"]';
        exit();
      }else{
        $unidad = $stmt->fetch(PDO::FETCH_ASSOC);
      }
      $sql = "INSERT INTO tbl_ausentismo_funcionarios (rut, desde, hasta, estado, unidad, tipoPermiso) VALUES (:rut, :desde, :hasta, 3, :unidad, 5)";
      $stmt = $conexion->prepare($sql);
      $stmt->bindParam(':rut', $key["rut"]);
      $stmt->bindParam(':desde', $key["fecha"]);
      $stmt->bindParam(':hasta', $key["fecha"]);
      $stmt->bindParam(':unidad', $unidad["unidad"]);
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

  function cargaCursosFuncionarios2($matriz){
    global $conexion;
    $error = 0;
    $correcto =  0;
    var_dump($matriz);
    foreach ($matriz as $key) {
      //primreo obtengo la unidad del funcionario.
      global $conexion;
      $sql = "SELECT id_unidad AS unidad FROM tbl_funcionarios_vigencia WHERE CURDATE() BETWEEN desde AND hasta AND rut = :rut limit 1";
      $stmt = $conexion->prepare($sql);
      $stmt->bindParam(':rut', $key['rut']);
      if (!$stmt->execute()) {
        echo 'Error al pillar la unidad, el rut es $key["rut"]';
        exit();
      }else{
        $unidad = $stmt->fetch(PDO::FETCH_ASSOC);
      }
      $sql = "INSERT INTO tbl_ausentismo_funcionarios (rut, desde, hasta, estado, unidad, tipoPermiso) VALUES (:rut, :desde, :hasta, 3, :unidad, 5)";
      $stmt = $conexion->prepare($sql);
      $stmt->bindParam(':rut', $key["rut"]);
      $stmt->bindParam(':desde', $key["fechaInicio"]);
      $stmt->bindParam(':hasta', $key["fechaTermino"]);
      $stmt->bindParam(':unidad', $unidad["unidad"]);
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

  function cargaCursosFuncionarios3($matriz){
    global $conexion;
    $error = 0;
    $correcto =  0;
    var_dump($matriz);
    foreach ($matriz as $key) {
      //primreo obtengo la unidad del funcionario.
      global $conexion;

      $sql = "INSERT INTO tbl_ausentismo_jueces (rut, desde, hasta, tipoPermiso, estado) VALUES (:rut, :desde, :hasta, 5, 3)";
      $stmt = $conexion->prepare($sql);
      $stmt->bindParam(':rut', $key["rut"]);
      $stmt->bindParam(':desde', $key["fecha"]);
      $stmt->bindParam(':hasta', $key["fecha"]);
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

  function cargaCursosFuncionarios4($matriz){
    global $conexion;
    $error = 0;
    $correcto =  0;
    var_dump($matriz);
    foreach ($matriz as $key) {
      //primreo obtengo la unidad del funcionario.
      global $conexion;

      $sql = "INSERT INTO tbl_ausentismo_jueces (rut, desde, hasta, tipoPermiso, estado) VALUES (:rut, :desde, :hasta, 5, 3)";
      $stmt = $conexion->prepare($sql);
      $stmt->bindParam(':rut', $key["rut"]);
      $stmt->bindParam(':desde', $key["fechaInicio"]);
      $stmt->bindParam(':hasta', $key["fechaTermino"]);
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

  function cargaLibroAudienciaSitfa($matriz){
  global $conexion;
  $error = 0;
  $correcto =  0;
  // antes de insertar elimina todo el contenido de la tabla tbl_libroaudienciassitfa
  $sql = "DELETE FROM tbl_libroaudienciassitfa";
  $stmt = $conexion->prepare($sql);
  $stmt->execute();
  foreach ($matriz as $key) {
    $sql = "INSERT INTO tbl_libroaudienciassitfa (
      fechaFirma, fechaAudiencia, sala, horaInicio, horaTermino, rit, caj, estadoCausa, ruc, caratulado, tipoAudiencia, juez, materia, tipoNotificacion, estadoNotificacion, enteNotificador, consolidada, videoconferencia
    ) VALUES (
      :fechaFirma, :fechaAudiencia, :sala, :horaInicio, :horaTermino, :rit, :caj, :estadoCausa, :ruc, :caratulado, :tipoAudiencia, :juez, :materia, :tipoNotificacion, :estadoNotificacion, :enteNotificador, :consolidada, :videoconferencia
    )";
    $stmt = $conexion->prepare($sql);
    $stmt->bindParam(':fechaFirma', $key['fechaFirma']);
    $stmt->bindParam(':fechaAudiencia', $key['fechaAudiencia']);
    $stmt->bindParam(':sala', $key['sala']);
    $stmt->bindParam(':horaInicio', $key['horaInicio']);
    $stmt->bindParam(':horaTermino', $key['horaTermino']);
    $stmt->bindParam(':rit', $key['rit']);
    $stmt->bindParam(':caj', $key['caj']);
    $stmt->bindParam(':estadoCausa', $key['estadoCausa']);
    $stmt->bindParam(':ruc', $key['ruc']);
    $stmt->bindParam(':caratulado', $key['caratulado']);
    $stmt->bindParam(':tipoAudiencia', $key['tipoAudiencia']);
    $stmt->bindParam(':juez', $key['juez']);
    $stmt->bindParam(':materia', $key['materia']);
    $stmt->bindParam(':tipoNotificacion', $key['tipoNotificacion']);
    $stmt->bindParam(':estadoNotificacion', $key['estadoNotificacion']);
    $stmt->bindParam(':enteNotificador', $key['enteNotificador']);
    $stmt->bindParam(':consolidada', $key['consolidada']);
    $stmt->bindParam(':videoconferencia', $key['videoconferencia']);
    if (!$stmt->execute()) {
      $error++;
    } else {
      $correcto++;
    }
  }
  return array('correctos' => $correcto, 'errores' => $error);
}