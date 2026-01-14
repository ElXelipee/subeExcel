# 🚀 QUICK START - Analizar Tipos de Datos

## Iniciar en 3 pasos

### 1. Accede a la herramienta
```
URL: http://localhost/subeExcel/pages/analizar-tipos-datos.html
O desde el menú: Inicio → Cargar Archivos → Analizar Tipos de Datos
```

### 2. Carga tu archivo Excel
```
✅ Haz clic en "Seleccionar archivo Excel"
✅ Elige tu archivo (.xls, .xlsx o .csv)
✅ (Opcional) Especifica la hoja si tienes múltiples
✅ Haz clic en "Analizar Tipos de Datos"
```

### 3. Visualiza y exporta resultados
```
✅ Ve la tabla con el análisis de tipos
✅ Revisa estadísticas (celdas, filas, columnas)
✅ Descarga resultados en CSV si quieres
```

---

## Tipos de Datos Detectados (con ejemplos)

```
📝 STRING (Texto)
   Ejemplos: "Juan", "Descripción", "ABC123"

🔢 INTEGER (Número entero)
   Ejemplos: 123, 456, 789

📊 FLOAT (Número decimal)
   Ejemplos: 123.45, 67.89, 1000.5

📅 DATE (Fecha)
   Ejemplos: 01/01/2020, 2020-01-01, January 01, 2020

✓ BOOLEAN (Verdadero/Falso)
   Ejemplos: TRUE, FALSE, SI, NO, 1, 0

⬜ EMPTY (Vacío)
   Ejemplos: (celda sin contenido)

❌ MIXED (Inconsistente)
   Ejemplos: (datos con tipo no determinable)
```

---

## Requisitos Mínimos

✅ Archivo Excel válido (.xls, .xlsx, .csv)
✅ Menos de 50MB
✅ Datos comenzando en A1
✅ Máximo 20 columnas

---

## Tabla de Resultados

Cada fila muestra:

| Campo | Qué es |
|-------|---------|
| **Fila** | Número de fila (1, 2, 3...) |
| **Columna** | Letra (A, B, C..., hasta T) |
| **Valor** | Contenido de la celda |
| **Tipo de Dato** | Tipo detectado (String, Integer, etc.) |
| **Detalles** | Info adicional (ej: "14 caracteres", "Formato DD/MM/YYYY") |

---

## Descarga de Resultados

Haz clic en **"Descargar Resultados"** para obtener un CSV con:

```csv
Fila,Columna,Valor,Tipo de Dato,Detalles
1,A,Nombre,String,Texto de 6 caracteres
1,B,Edad,String,Texto de 4 caracteres
2,A,Juan,String,Texto de 4 caracteres
2,B,30,Integer (como texto),Número guardado como texto
...
```

---

## Problemas Comunes

### ❌ "Tipo de archivo no permitido"
✅ Solución: Usa .xls, .xlsx o .csv

### ❌ "El archivo es demasiado grande"
✅ Solución: Reduce el tamaño (máximo 50MB)

### ❌ Los datos no aparecen
✅ Solución: Verifica que los datos comiencen en A1

### ❌ No ve la hoja que espera
✅ Solución: Especifica el nombre exacto de la hoja

---

## Tips Útiles

💡 **Tip 1:** Los primeros 20 caracteres se muestran en la tabla
💡 **Tip 2:** Puedes descargar los resultados y abrirlos en Excel
💡 **Tip 3:** Usa esto para validar datos antes de importar
💡 **Tip 4:** Perfecta para análisis de calidad de datos

---

## Estadísticas que Verás

```
┌──────────────────────┐
│ 150 Total de Celdas  │ ← Celdas analizadas
│ 10 Filas             │ ← Número de filas
│ 15 Columnas          │ ← Número de columnas usadas
│ 5 Celdas Vacías      │ ← Sin contenido
└──────────────────────┘
```

---

## Colores en la Tabla

```
🔵 Azul claro   → String (texto)
🟢 Verde        → Integer (número entero)
🔷 Azul oscuro  → Float (decimal)
🟡 Amarillo     → Date (fecha)
🟣 Púrpura      → Boolean (sí/no)
⚫ Gris          → Empty (vacío)
🔴 Rojo         → Mixed (tipo mixto)
```

---

## Caso de Uso Ejemplo

**Situación:** Tienes un Excel con datos de empleados y quieres verificar que todos los datos estén en el formato correcto.

**Pasos:**
1. Carga el archivo
2. Mira la tabla resultante
3. Verifica que las edades sean Integer, no String
4. Verifica que las fechas sean Date, no String
5. Descarga el CSV si necesitas registrarlo

**Resultado:** Sabrás exactamente cuáles datos están mal formateados.

---

## Datos Analizados

Desde A1 hasta T∞ (20 columnas máximo):

```
A  B  C  D  E  F  G  H  I  J  K  L  M  N  O  P  Q  R  S  T
1
2
3
4
5
...
```

---

## Atributos Detectados Automáticamente

Para cada tipo de dato, se detecta:

✅ **String:**
   - Longitud en caracteres
   
✅ **Integer:**
   - Tipo (si es número puro o texto)
   
✅ **Float:**
   - Decimales
   - Tipo (si es número puro o texto)
   
✅ **Date:**
   - Formato exacto (DD/MM/YYYY, etc.)
   
✅ **Boolean:**
   - Valor (TRUE/FALSE, SI/NO, etc.)

---

## Exportar a Excel

Los datos descargados como CSV pueden abrirse en Excel:

1. Descarga el archivo
2. Abre Excel
3. Abre el archivo CSV
4. Elige codificación UTF-8 si se pide
5. ¡Listo! Ahora tienes el análisis en Excel

---

## Privacidad

✅ Los datos del archivo se procesan temporalmente
✅ Se descartan después del análisis
✅ No se almacenan en la base de datos
✅ No se registran detalles sensitivos

---

## ¿Necesitas más ayuda?

Consulta:
- **GUIA_ANALIZAR_TIPOS_DATOS.md** - Guía completa
- **CAMBIOS_ANALIZAR_TIPOS_DATOS.md** - Detalles técnicos
- Contacta al administrador

---

**¡Listo para empezar!** 🎉

Accede ahora: `pages/analizar-tipos-datos.html`
