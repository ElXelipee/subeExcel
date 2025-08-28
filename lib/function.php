<?php

  // Función para convertir fecha d/m/Y a Y-m-d
 function convertirFecha($fechaString) {
  if (empty($fechaString)) {
   return null;
  }
  try {
   $fecha = DateTime::createFromFormat('d/m/Y', trim($fechaString));
   return $fecha ? $fecha->format('Y-m-d') : null;
  } catch (Exception $e) {
   error_log("Error convirtiendo fecha: " . $fechaString . " - " . $e->getMessage());
   return null;
  }
 }

            // Función para convertir hora
 function convertirHora($horaString) {
   if (empty($horaString)) {
    return null;
   }
   try {
    $hora = DateTime::createFromFormat('H:i', trim($horaString));
    if (!$hora) {
        $hora = DateTime::createFromFormat('H:i:s', trim($horaString));
    }
    return $hora ? $hora->format('H:i:s') : null;
   } catch (Exception $e) {
    error_log("Error convirtiendo hora: " . $horaString . " - " . $e->getMessage());
    return null;
   }
 }