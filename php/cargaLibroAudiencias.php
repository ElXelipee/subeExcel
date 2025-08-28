<?php 
  // require("../config/database/conexion.php");
  require_once('funcionesBD.php');
  require_once ('../lib/function.php');
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
                  // Columna A: Fecha (convertir de d/m/Y a Y-m-d)
                  $fechaA = trim($objPHPExcel->getActiveSheet()->getCell('A' . $i)->getCalculatedValue());
                  $_DATOS_EXCEL[$i]['fechaFirma'] = convertirFecha($fechaA);
                  
                  // Columna B: Fecha (convertir de d/m/Y a Y-m-d)
                  $fechaB = trim($objPHPExcel->getActiveSheet()->getCell('B' . $i)->getCalculatedValue());
                  $_DATOS_EXCEL[$i]['fechaAudiencia'] = convertirFecha($fechaB);
                  
                  // Columna C: Texto normal
                  //En el siguiente caracter dejar solo el número

                  $textoC = trim($objPHPExcel->getActiveSheet()->getCell('C' . $i)->getCalculatedValue());
                  $_DATOS_EXCEL[$i]['sala'] = trim(str_replace('Sala N°', '', $textoC));
                  
                  // Columna D: Hora (convertir a H:i:s)
                  $horaD = trim($objPHPExcel->getActiveSheet()->getCell('D' . $i)->getCalculatedValue());
                  $_DATOS_EXCEL[$i]['horaInicio'] = convertirHora($horaD);
                  
                  // Columna F: Hora (convertir a H:i:s)
                  $horaF = trim($objPHPExcel->getActiveSheet()->getCell('F' . $i)->getCalculatedValue());
                  $_DATOS_EXCEL[$i]['horaTermino'] = convertirHora($horaF);      
                  // Columna E: Texto normal
                  $_DATOS_EXCEL[$i]['rit'] = trim($objPHPExcel->getActiveSheet()->getCell('H' . $i)->getCalculatedValue());
                  $_DATOS_EXCEL[$i]['caj'] = trim($objPHPExcel->getActiveSheet()->getCell('I' . $i)->getCalculatedValue());
                  $_DATOS_EXCEL[$i]['estadoCausa'] = trim($objPHPExcel->getActiveSheet()->getCell('J' . $i)->getCalculatedValue());
                  $_DATOS_EXCEL[$i]['ruc'] = trim($objPHPExcel->getActiveSheet()->getCell('K' . $i)->getCalculatedValue());   
                  $_DATOS_EXCEL[$i]['caratulado'] = trim($objPHPExcel->getActiveSheet()->getCell('M' . $i)->getCalculatedValue());
                  $_DATOS_EXCEL[$i]['tipoAudiencia'] = trim($objPHPExcel->getActiveSheet()->getCell('N' . $i)->getCalculatedValue());
                  $_DATOS_EXCEL[$i]['juez'] = trim($objPHPExcel->getActiveSheet()->getCell('O' . $i)->getCalculatedValue());
                  $_DATOS_EXCEL[$i]['materia'] = trim($objPHPExcel->getActiveSheet()->getCell('P' . $i)->getCalculatedValue());
                  $_DATOS_EXCEL[$i]['tipoNotificacion'] = trim($objPHPExcel->getActiveSheet()->getCell('Q' . $i)->getCalculatedValue());
                  $_DATOS_EXCEL[$i]['estadoNotificacion'] = trim($objPHPExcel->getActiveSheet()->getCell('R' . $i)->getCalculatedValue());
                  $_DATOS_EXCEL[$i]['enteNotificador'] = trim($objPHPExcel->getActiveSheet()->getCell('S' . $i)->getCalculatedValue());
                  $_DATOS_EXCEL[$i]['consolidada'] = trim($objPHPExcel->getActiveSheet()->getCell('U' . $i)->getCalculatedValue());
                  $_DATOS_EXCEL[$i]['videoconferencia'] = trim($objPHPExcel->getActiveSheet()->getCell('V' . $i)->getCalculatedValue());
                $i++;
              } else {
                break;
              }
            }
           
           // Mostrar los datos procesados para verificación
           echo "<h3>Datos procesados del Excel:</h3>";
           echo "<pre>";
           var_dump($_DATOS_EXCEL);
           echo "</pre>";
           
           echo "<h3>Resumen de registros procesados:</h3>";
           echo "Total de filas procesadas: " . count($_DATOS_EXCEL) . "<br>";
           
           // Mostrar algunos ejemplos de conversión
           if (!empty($_DATOS_EXCEL)) {
               echo "<h4>Ejemplos de conversión:</h4>";
               $contador = 0;
               foreach ($_DATOS_EXCEL as $indice => $fila) {
                   if ($contador < 3) { // Mostrar solo los primeros 3 ejemplos
                       echo "<strong>Fila $indice:</strong><br>";
                       echo "- Fecha A: " . ($fila['fechaA'] ?? 'NULL') . "<br>";
                       echo "- Fecha B: " . ($fila['fechaB'] ?? 'NULL') . "<br>";
                       echo "- Texto C: " . ($fila['textoC'] ?? 'NULL') . "<br>";
                       echo "- Hora D: " . ($fila['horaD'] ?? 'NULL') . "<br>";
                       echo "- Texto E: " . ($fila['textoE'] ?? 'NULL') . "<br>";
                       echo "- Hora F: " . ($fila['horaF'] ?? 'NULL') . "<br><br>";
                       $contador++;
                   }
               }
           }
           
            //con el array completado, lo subo a la base de dato, por lo que necesitaré la nueva conexión.
//   Este se encarga de insertar         if(insertaAudiencias($_DATOS_EXCEL)){
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
//            }else {
//              echo 'error';
//            }
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