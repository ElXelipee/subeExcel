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
            
            $i = 2;
            while ($a = $objPHPExcel->getActiveSheet()->getCell('A' . $i)->getCalculatedValue()) {
              if ($a != "") {
                  // Columna A: Fecha (convertir de d/m/Y a Y-m-d)
                  $fechaA = trim($objPHPExcel->getActiveSheet()->getCell('A' . $i)->getCalculatedValue());
                  $_DATOS_EXCEL[$i]['fechaA'] = convertirFecha($fechaA);
                  
                  // Columna B: Fecha (convertir de d/m/Y a Y-m-d)
                  $fechaB = trim($objPHPExcel->getActiveSheet()->getCell('B' . $i)->getCalculatedValue());
                  $_DATOS_EXCEL[$i]['fechaB'] = convertirFecha($fechaB);
                  
                  // Columna C: Texto normal
                  $_DATOS_EXCEL[$i]['textoC'] = trim($objPHPExcel->getActiveSheet()->getCell('C' . $i)->getCalculatedValue());
                  
                  // Columna D: Hora (convertir a H:i:s)
                  $horaD = trim($objPHPExcel->getActiveSheet()->getCell('D' . $i)->getCalculatedValue());
                  $_DATOS_EXCEL[$i]['horaD'] = convertirHora($horaD);
                  
                  // Columna E: Texto normal
                  $_DATOS_EXCEL[$i]['textoE'] = trim($objPHPExcel->getActiveSheet()->getCell('E' . $i)->getCalculatedValue());
                  
                  // Columna F: Hora (convertir a H:i:s)
                  $horaF = trim($objPHPExcel->getActiveSheet()->getCell('F' . $i)->getCalculatedValue());
                  $_DATOS_EXCEL[$i]['horaF'] = convertirHora($horaF);                  
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