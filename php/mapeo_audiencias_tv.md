# Mapeo de Campos - Audiencias TV a tbl_programada

## Archivo Excel (Columnas A-L) → Base de Datos

### Campos del Excel:
- **A**: F. Audiencia (fecha)
- **B**: Sala (número)  
- **C**: H. Inicio (hora)
- **D**: RIT (código caso)
- **E**: CAJ (sí/no)
- **F**: Caratulado
- **G**: Tipo Audiencia
- **H**: Materia
- **I**: Juez
- **J**: Acta
- **K**: CT
- **L**: Cuenta Zoom

### Mapeo a tbl_programada:

| Campo Base de Datos | Valor Insertado | Fuente |
|---------------------|----------------|--------|
| `id_programada` | AUTO_INCREMENT | Automático |
| `pro_sa_num_sala` | Sala | Excel Columna B |
| `pro_nombre_juez` | Juez | Excel Columna I |
| `pro_adm_nombre` | Acta | Excel Columna J |
| `pro_ct_nombre` | CT | Excel Columna K |
| `id_tribunal` | 4 | Valor fijo |
| `pro_cod_tribunal` | '4' | Valor fijo |
| `pro_fn_descripcion` | NULL | No se inserta |
| `pro_origen` | NULL | No se inserta |
| `pro_rit` | RIT | Excel Columna D |
| `pro_motivo` | 'Carga Masiva' | Valor fijo |
| `pro_juez_solicitante` | NULL | No se inserta |
| `pro_etapa` | Tipo Audiencia | Excel Columna G |
| `fecha_programada` | F. Audiencia | Excel Columna A |
| `pro_hora_inicio` | H. Inicio | Excel Columna C |
| `pro_hora_termino` | Calculado | Tabla de horarios |
| `pro_bloque_inicio` | Calculado | Tabla de horarios |
| `pro_bloque_termino` | Calculado | Tabla de horarios |
| `pro_cantidad_bloques` | Calculado | Tabla de horarios |
| `pro_res_descripcion` | NULL | No se inserta |
| `pro_caratula` | Caratulado | Excel Columna F |
| `pro_responsable_agenda` | 'Cuenta Genérica' | Valor fijo |
| `pro_au_descripcion` | Tipo Audiencia | Excel Columna G |
| `pro_estado` | 'P' | Valor fijo |
| `id_solicitud` | 0 | Valor fijo |
| `pro_curador` | CAJ | Excel Columna E |
| `pro_radicada` | 'NO RADICADA' | Valor fijo |
| `pro_actualizacion` | NULL | No se inserta |
| `pro_usu_actualizacion` | 'Cuenta Genérica' | Valor fijo |
| `flag_reprogramacion` | NULL | No se inserta |
| `pro_motivo_repro` | NULL | No se inserta |
| `pro_check` | 'En espera' | Valor fijo |
| `observacion` | Cuenta Zoom | Excel Columna L |
| `infoPublico` | NULL | No se inserta |
| `pro_check_inicio` | NULL | No se inserta |
| `pro_check_termino` | NULL | No se inserta |

## Tabla de Horarios para Cálculo de Bloques

| H. Inicio | H. Término | Bloque Inicio | Bloque Término | Cantidad Bloques |
|-----------|------------|---------------|----------------|------------------|
| 08:30:00  | 09:00:00   | 3             | 5              | 3                |
| 09:15:00  | 09:45:00   | 6             | 8              | 3                |
| 10:00:00  | 10:30:00   | 9             | 11             | 3                |
| 11:00:00  | 11:30:00   | 13            | 15             | 3                |
| 11:45:00  | 12:15:00   | 16            | 18             | 3                |
| 12:30:00  | 13:00:00   | 19            | 21             | 3                |

## Validaciones Implementadas

1. **Hora de Inicio**: Debe coincidir exactamente con uno de los horarios de la tabla
2. **Formato de Fecha**: Se convierte automáticamente de Excel a formato Y-m-d
3. **Formato de Hora**: Se agrega ':00' segundos automáticamente
4. **Campos Obligatorios**: Se valida que los campos requeridos no estén vacíos

## Notas Importantes

- Si la hora de inicio no se encuentra en la tabla de horarios, el registro se omite y se genera un error
- Los campos con NULL explícito no se insertan (valor NULL en base de datos)
- Los valores fijos se asignan automáticamente según especificación
- Se mantiene registro de errores para auditoría

## Configuración de Base de Datos

Antes de usar, actualizar en el archivo PHP:
```php
$servidor = "localhost";        // Tu servidor MySQL
$usuario = "root";             // Tu usuario MySQL
$password = "";                // Tu contraseña MySQL
$base_datos = "tu_base_datos"; // Nombre de tu base de datos
```
