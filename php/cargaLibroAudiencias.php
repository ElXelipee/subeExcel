<?php 
  // require("../config/database/conexion.php");
  require_once('funcionesBD.php');
  require_once ('../lib/function.php');


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
           
           // Mostrar algunos ejemplos de conversión
           if (!empty($_DATOS_EXCEL)) {
            $resultado = cargaLibroAudienciaSitfa($_DATOS_EXCEL);
            $correctos = $resultado['correctos'];
            $errores = $resultado['errores'];
            
            // Redirigir a la página con los parámetros de resultado
            header("Location: ../pages/libro-audiencias.html?correctos=" . $correctos . "&errores=" . $errores . "&carga=completada");
            exit();
           }
           

          }
          //si por algo no cargo el archivo bak_ 
          else {
            header("Location: ../pages/libro-audiencias.html?error=archivo_no_cargado");
            exit();
          }
        } else {
          header("Location: ../pages/libro-audiencias.html?error=archivo_no_recibido");
          exit();
        }