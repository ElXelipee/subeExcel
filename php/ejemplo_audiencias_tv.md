# Archivo de ejemplo para Audiencias TV

## Estructura del Excel esperada:

**Columnas (A-L):**
- A: F. Audiencia (fecha) - Ejemplo: 22/09/2025
- B: Sala (número) - Ejemplo: 1
- C: H. Inicio (hora) - Ejemplo: 08:30
- D: RIT - Ejemplo: C-8029-2024
- E: CAJ - Ejemplo: No
- F: Caratulado - Ejemplo: GALDAMES/GALDAMES
- G: Tipo Audiencia - Ejemplo: Citación a Audiencia de Juicio
- H: Materia - Ejemplo: 1. Alimentos, Aumento 2. Alimentos, Cesacion 3. Alimentos, Rebaja
- I: Juez - Ejemplo: Vera Alejandra Garrido Crino
- J: Acta - Ejemplo: FRANCISCA
- K: CT - Ejemplo: CESAR MANDUJANO
- L: Cuenta Zoom - Ejemplo: zoom_3jfsantiago03@pjud.cl

## Datos de ejemplo:

```
F. Audiencia	Sala	H. Inicio	Rit	Caj	Caratulado	Tipo Audiencia	Materia	Juez	Acta	CT	Cuenta Zoom
22/09/2025	1	08:30	C-8029-2024	No	GALDAMES/GALDAMES	Citación a Audiencia de Juicio	1. Alimentos, Aumento 2. Alimentos, Cesacion 3. Alimentos, Rebaja	Vera Alejandra Garrido Crino	FRANCISCA	CESAR MANDUJANO	zoom_3jfsantiago03@pjud.cl
23/09/2025	2	09:00	C-8030-2024	Sí	RODRIGUEZ/MARTINEZ	Audiencia de Preparación	Divorcio por Mutuo Acuerdo	Juan Carlos Pérez Silva	MARIA JOSE	PATRICIA GONZALEZ	zoom_sala2santiago@pjud.cl
24/09/2025	1	14:30	C-8031-2024	No	LOPEZ/SANCHEZ	Audiencia de Juicio	Cuidado Personal de Menores	Ana María Torres Vega	CARLOS	LUIS MORALES	zoom_3jfsantiago03@pjud.cl
```

## Notas importantes:

1. **Fecha**: Debe estar en formato de fecha de Excel (no texto)
2. **Hora**: Debe estar en formato personalizado HH:MM
3. **Sala**: Número de sala (puede ser texto o número)
4. **RIT**: Código único del caso
5. **CAJ**: Indicador Sí/No
6. **Cuenta Zoom**: Email válido de zoom

## Para testing:

1. Crear un archivo Excel con estos datos
2. Asegurar que la primera fila contenga los encabezados
3. Los datos empiecen desde la fila 2
4. No dejar filas en blanco entre datos
5. Guardar como .xlsx para mejor compatibilidad
