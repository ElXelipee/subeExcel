<?php 
  // require("../config/database/conexion.php");
  require_once('funcionesBD.php');
  var_dump($_REQUEST);


          if (isset($_FILES['archivoExcel'])) {

          set_time_limit(2000000);
          ini_set('memory_limit', '-1');
          $encontrado = 0;
          $fallido = 0;

          //extract($_POST);
          $archivo = $_FILES['archivoExcel']['name'];
          $tipo = $_FILES['archivoExcel']['type'];
          $destino = "bak_" . $archivo;
          if (copy($_FILES['archivoExcel']['tmp_name'], $destino)) {
            //echo "Archivo Cargado Con Éxito";
          } else {
            echo "Error Al Cargar el Archivo";
          }
          if (file_exists("bak_" . $archivo)) {
            /** Clases necesarias */
            require_once('../lib/PHPExcel/PHPExcel.php');
            require_once('../lib/PHPExcel/PHPExcel/Reader/Excel2007.php');
            // Cargando la hoja de cálculo
$objReader = new PHPExcel_Reader_Excel2007();
$objPHPExcel = $objReader->load("bak_" . $archivo);
$objFecha = new PHPExcel_Shared_Date();

$objPHPExcel->setActiveSheetIndex(0);
$i = 2;

while ($a = $objPHPExcel->getActiveSheet()->getCell('A' . $i)->getCalculatedValue()) {
    if ($a != "") {
        // Obtener el valor numérico de la fecha en formato Excel
        $fechaExcel = $objPHPExcel->getActiveSheet()->getCell('A' . $i)->getCalculatedValue();

        // Verificar si la fecha es válida (en Excel, la fecha es un número flotante)
        if (PHPExcel_Shared_Date::isDateTime($objPHPExcel->getActiveSheet()->getCell('A' . $i))) {
            // Convertir el valor de Excel a un objeto DateTime
            $fechaDateTime = PHPExcel_Shared_Date::ExcelToPHPObject($fechaExcel);
        } else {
            // Manejar el caso en que la celda no contenga una fecha válida
            $fechaDateTime = new DateTime('2024-01-01');
        }

        // Obtener el valor de la celda de la hora
        $horaExcel = $objPHPExcel->getActiveSheet()->getCell('B' . $i)->getCalculatedValue();

        // Verificar si la hora es un número (fracción de día en Excel)
        if (is_numeric($horaExcel)) {
            // Convertir el valor numérico de Excel a una fracción de tiempo y obtener un objeto DateTime
            $horaDateTime = PHPExcel_Shared_Date::ExcelToPHPObject($horaExcel);
            // Extraer la hora del objeto DateTime y asignarla a la fecha
            $fechaDateTime->setTime($horaDateTime->format('H'), $horaDateTime->format('i'), $horaDateTime->format('s'));
        } else {
            // Manejar el caso en que la hora no sea válida
            $fechaDateTime->setTime(0, 0, 0);
        }

        // Formatear la fecha y hora en el formato deseado (d-m-y H:i:s)
        $fechaHoraFormateada = $fechaDateTime->format('Y-m-d H:i:s');

        // Asignar la fecha y hora formateada al array de datos
        $_DATOS_EXCEL[$i]['fechaHora'] = $fechaHoraFormateada;

        // Resto de las columnas
        $_DATOS_EXCEL[$i]['sala'] = trim($objPHPExcel->getActiveSheet()->getCell('C' . $i)->getCalculatedValue());
        $_DATOS_EXCEL[$i]['tipoBloque'] = trim($objPHPExcel->getActiveSheet()->getCell('D' . $i)->getCalculatedValue());
        $_DATOS_EXCEL[$i]['juezAudiencia'] = trim($objPHPExcel->getActiveSheet()->getCell('E' . $i)->getCalculatedValue());
        $_DATOS_EXCEL[$i]['zoom'] = trim($objPHPExcel->getActiveSheet()->getCell('F' . $i)->getCalculatedValue());
        $_DATOS_EXCEL[$i]['estado'] = trim($objPHPExcel->getActiveSheet()->getCell('O' . $i)->getCalculatedValue());

        $i++;
    } else {
        break;
    }
}


        var_dump($_DATOS_EXCEL);
        //con el array completado, lo subo a la base de dato, por lo que necesitaré la nueva conexión.
        if (insertaHorasAudiencias($_DATOS_EXCEL)) {
        //     // $alerta = [
        //     //   'Alerta' => 'simple',
        //     //   'Titulo' => 'CARGA CORRECTA ✔️',
        //     //   'Texto' => 'Las audiencias fueron cargadas de manera correcta.',
        //     //   'Tipo' => 'success',
        }else {
          echo 'fail error';
        }
    }
    //si por algo no cargo el archivo bak_ 
    else {
      //echo "Necesitas primero importar el archivo";
    }
    $i = 0;
  } else {
    //header("Location:index.html");
    echo 'noo llegó el archiv';
  }

    exit();