# Analizar Tipos de Datos - Guía de Uso

## Descripción

La funcionalidad **Analizar Tipos de Datos** es una herramienta que te permite cargar un archivo Excel y obtener un análisis detallado del tipo de dato en cada celda, desde A1 hasta un máximo de 20 columnas.

## Características

✅ **Análisis automático de tipos de datos:**
- String (Texto)
- Integer (Números enteros)
- Float (Números decimales)
- Date (Fechas)
- Boolean (Verdadero/Falso)
- Empty (Celdas vacías)
- Mixed (Tipos inconsistentes)

✅ **Soporta múltiples formatos:**
- Excel (.xls)
- Excel moderno (.xlsx)
- CSV (.csv)

✅ **Funcionalidades:**
- Lee desde celda A1
- Analiza hasta 20 columnas (A-T)
- Detecta automáticamente el tipo de dato
- Muestra detalles sobre cada dato
- Proporciona estadísticas resumen
- Permite descargar resultados en CSV

## Cómo Usar

### Paso 1: Acceder a la Herramienta
1. Ve a la página principal del Cargador Excel
2. En el menú "Cargar Archivos", selecciona "Analizar Tipos de Datos"
3. O accede directamente a: `pages/analizar-tipos-datos.html`

### Paso 2: Cargar el Archivo
1. Haz clic en "Seleccionar archivo Excel"
2. Elige tu archivo Excel (máximo 50MB)
3. (Opcional) Especifica el nombre de la hoja si tu archivo tiene múltiples hojas
4. Haz clic en "Analizar Tipos de Datos"

### Paso 3: Ver Resultados
La herramienta mostrará:
- **Estadísticas resumen**: Total de celdas, filas, columnas analizadas, celdas vacías
- **Tabla detallada** con:
  - Número de fila
  - Letra de columna
  - Valor de la celda
  - Tipo de dato detectado
  - Detalles adicionales

### Paso 4: Descargar Resultados (Opcional)
Haz clic en "Descargar Resultados" para obtener un archivo CSV con el análisis completo.

## Tipos de Datos Detectados

### String
Texto genérico. Ejemplo: "Nombre", "Descripción"

### Integer
Números enteros. Ejemplo: 123, 456, 789

### Float
Números decimales. Ejemplo: 123.45, 678.90

### Integer/Float (como texto)
Números guardados como texto. La herramienta lo detecta y lo indica.

### Date
Fechas en formato reconocido. La herramienta detecta formatos como:
- DD/MM/YYYY
- MM/DD/YYYY
- YYYY-MM-DD
- Month DD, YYYY

### Boolean
Valores lógicos. Detecta: TRUE, FALSE, SI, NO, SÍ, YES, 1, 0

### Empty
Celdas vacías o sin contenido

### Mixed
Tipo de dato inconsistente o no identificado

## Requisitos del Archivo

- ✅ Debe ser un archivo Excel válido (.xls, .xlsx) o CSV
- ✅ Debe comenzar desde celda A1
- ✅ Máximo 20 columnas (A-T)
- ✅ Tamaño máximo: 50MB
- ✅ Codificación UTF-8 recomendada

## Casos de Uso

1. **Validación de datos**: Verifica que los tipos de datos sean correctos antes de procesar
2. **Limpieza de datos**: Identifica valores que podrían estar en formato incorrecto
3. **Análisis de estructura**: Entiende la estructura de tus datos Excel
4. **Integración**: Confirma que los datos cumplen requisitos antes de importar

## Solución de Problemas

### Error: "Tipo de archivo no permitido"
- Asegúrate de usar .xls, .xlsx o .csv
- Verifica que el archivo no esté corrupto

### Error: "El archivo es demasiado grande"
- El tamaño máximo es 50MB
- Reduce el tamaño del archivo

### Error: "Error al procesar el archivo"
- Verifica que el archivo sea un Excel válido
- Asegúrate de que la hoja especificada existe
- Intenta con un archivo diferente para confirmar

### Los datos no aparecen
- Verifica que los datos comienzan en celda A1
- La hoja debe ser accesible y no estar protegida

## Ejemplos de Salida

Para un archivo Excel con estos datos:

```
A1: Nombre      B1: Edad    C1: Salario      D1: Fecha Ingreso
A2: Juan        B2: 30      C2: 2500.50      D2: 01/01/2020
A3: María       B3: 28      C3: 2300.00      D3: 15/03/2021
```

La herramienta mostrará:

| Fila | Columna | Valor | Tipo de Dato | Detalles |
|------|---------|-------|--------------|----------|
| 1 | A | Nombre | String | Texto de 6 caracteres |
| 1 | B | Edad | String | Texto de 4 caracteres |
| 1 | C | Salario | String | Texto de 7 caracteres |
| 1 | D | Fecha Ingreso | String | Texto de 14 caracteres |
| 2 | A | Juan | String | Texto de 4 caracteres |
| 2 | B | 30 | Integer (como texto) | Número entero guardado como texto |
| 2 | C | 2500.50 | Float (como texto) | Número decimal guardado como texto |
| 2 | D | 01/01/2020 | Date | Formato de fecha: DD/MM/YYYY |

## Notas Importantes

⚠️ **Límite de columnas**: Solo analiza las primeras 20 columnas (A-T). Las columnas más allá se ignoran.

⚠️ **Datos sensitivos**: No almacena los datos del archivo en el servidor. Se procesan temporalmente y se descartan.

⚠️ **Formato de hoja**: Si no especificas el nombre de la hoja, usa la primera hoja del archivo.

## Historial de Cambios

### Versión 1.0 - 14 Enero 2026
- Lanzamiento inicial
- Análisis de tipos de datos
- Descarga de resultados en CSV
- Interfaz responsiva

---

**¿Necesitas ayuda?** Contacta al administrador del sistema.
