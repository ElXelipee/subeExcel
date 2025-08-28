<?php 
  // require("../config/database/conexion.php");
  require_once('funcionesBD.php');
  //var_dump($_REQUEST);
  // Desactivar todos los reportes de error
  // error_reporting(0);
  // // Desactivar la visualización de errores
  // ini_set('display_errors', 0);
  // // Evitar que los errores se muestren en la salida
  // ini_set('display_startup_errors', 0);
  

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
              echo 'i = ' . $i . ' y j = ' . $j . '<br>';
              if ($a =! "") {
                $_DATOS_EXCEL[$i]['rut'] =trim($objPHPExcel->getActiveSheet()->getCell('A' . $i)->getCalculatedValue());
                $fechaExcel = $objPHPExcel->getActiveSheet()->getCell('B' . $i)->getCalculatedValue();
                if (PHPExcel_Shared_Date::isDateTime($objPHPExcel->getActiveSheet()->getCell('B' . $i))) {
                  // Convertir el valor de Excel a un objeto DateTime
                  $fechaDateTime = PHPExcel_Shared_Date::ExcelToPHPObject($fechaExcel);
                  $_DATOS_EXCEL[$i]['fechaInicio'] = $fechaDateTime->format('Y-m-d');
                }
                $fechaExcel = $objPHPExcel->getActiveSheet()->getCell('C' . $i)->getCalculatedValue();
                if (PHPExcel_Shared_Date::isDateTime($objPHPExcel->getActiveSheet()->getCell('C' . $i))) {
                  // Convertir el valor de Excel a un objeto DateTime
                  $fechaDateTime = PHPExcel_Shared_Date::ExcelToPHPObject($fechaExcel);
                  $_DATOS_EXCEL[$i]['fechaTermino'] = $fechaDateTime->format('Y-m-d');
                }
                $i ++;
              } else {
                break;
              }
            }
            var_dump($_DATOS_EXCEL);
            echo '<h1>aquíiii</h1><br><br>';
            //con el array completado, lo subo a la base de dato, por lo que necesitaré la nueva conexión.
            if(cargaCursosFuncionarios2($_DATOS_EXCEL)){
              // $alerta = [
              //   'Alerta' => 'simple',
              //   'Titulo' => 'CARGA CORRECTA ✔️',
              //   'Texto' => 'Las audiencias fueroncargadas de manera correcta.',
              //   'Tipo' => 'success',
              //   'Timer' => '1500',
              //   'Tabla' => 'no'
              // ];
              // echo json_encode($alerta);
              // exit();
            }else {
              echo 'error';
            }
            //var_dump($_DATOS_EXCEL);
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