# Reporte Kardex de Inventario

## Descripción
El **Kardex** es un sistema de registro detallado que muestra cronológicamente todos los movimientos de un producto específico, incluyendo entradas (compras), salidas (ventas) y el saldo acumulado después de cada movimiento. Este reporte NO incluye valores monetarios, solo cantidades físicas, permitiendo un control preciso del inventario.

## Ubicación
El reporte está disponible en dos ubicaciones:

1. **Menú Principal**: **Reportes > Kardex (por producto)**
2. **Desde Movimientos de Inventario**: Al hacer clic en el botón "Kardex" de cualquier producto en el reporte de movimientos

## Formato del Reporte

El Kardex muestra la información en un formato tabular similar al control de inventario tradicional:

```
┌─────────────────────────────────────────────────────────────────┐
│                    KARDEX DE INVENTARIO                         │
├─────────┬──────────────────┬──────────┬──────────┬─────────────┤
│  Fecha  │     DETALLE      │ ENTRADAS │ SALIDAS  │   SALDO     │
│         ├─────────┬────────┼──────────┼──────────┼─────────────┤
│         │  Tipo   │  Doc   │ Cantidad │ Cantidad │  Cantidad   │
├─────────┼─────────┼────────┼──────────┼──────────┼─────────────┤
│01/01/24 │ COMPRA  │ C-001  │   100    │    -     │     100     │
│05/01/24 │ VENTA   │ V-001  │    -     │    20    │      80     │
│10/01/24 │ COMPRA  │ C-002  │    50    │    -     │     130     │
│15/01/24 │ VENTA   │ V-002  │    -     │    30    │     100     │
└─────────┴─────────┴────────┴──────────┴──────────┴─────────────┘
```

## Características Principales

### 1. Información del Producto
Encabezado con datos completos del producto:
- **Nombre del producto**
- **Código**
- **Proveedor**
- **Categoría**
- **Marca**
- **Estado** (Activo/Inactivo)
- **Stock Actual** (destacado en tarjeta)
- **Stock Mínimo**

### 2. Estadísticas Resumidas
Tarjetas visuales con información clave:
- **Total Entradas**: Suma de todas las compras
- **Total Salidas**: Suma de todas las ventas
- **Balance Calculado**: Entradas - Salidas
- **Diferencia**: Comparación entre balance calculado y stock actual

### 3. Tabla Kardex Detallada

Columnas del reporte:

| Columna | Descripción |
|---------|-------------|
| **Fecha** | Fecha del movimiento |
| **Tipo** | COMPRA o VENTA (con badge de color) |
| **Documento** | Número de documento de la transacción |
| **ENTRADAS - Cantidad** | Cantidad comprada (fondo verde) |
| **SALIDAS - Cantidad** | Cantidad vendida (fondo rojo) |
| **SALDO - Cantidad** | Saldo acumulado después del movimiento (fondo azul) |

### 4. Totales y Verificación
Al final de la tabla:
- **Totales**: Suma de entradas, salidas y balance final
- **Stock Actual en Sistema**: Cantidad real en inventario
- **Diferencia**: Si existe discrepancia, se muestra en amarillo

### 5. Alertas Visuales
- ✅ **Verde**: Entradas (compras)
- 🔴 **Rojo**: Salidas (ventas) y saldos negativos
- 🔵 **Azul**: Saldos positivos
- ⚠️ **Amarillo**: Diferencias o alertas

## Filtros Disponibles

### Obligatorios
- **Empresa**: Selecciona la empresa para generar el Kardex
- **Producto**: Selecciona el producto específico

### Opcionales
- **Fecha Desde**: Inicio del periodo a analizar
- **Fecha Hasta**: Fin del periodo a analizar

> **Nota**: Si no se especifican fechas, se mostrarán TODOS los movimientos históricos del producto.

## Casos de Uso

### 1. Generar Kardex Completo de un Producto
1. Accede al reporte desde el menú
2. Selecciona la empresa
3. Selecciona el producto
4. NO especifiques fechas (para ver todo el historial)
5. Haz clic en "Generar Kardex"

### 2. Analizar Movimientos en un Periodo Específico
1. Selecciona empresa y producto
2. Define "Fecha Desde" (ej: 01/01/2024)
3. Define "Fecha Hasta" (ej: 31/01/2024)
4. Genera el Kardex
5. Verás solo movimientos de ese mes

### 3. Verificar Discrepancias
1. Genera el Kardex del producto con problemas
2. Revisa la tarjeta "Diferencia"
3. Si hay diferencia ≠ 0, revisa la alerta al final
4. Analiza movimiento por movimiento para identificar el problema
5. Verifica que el saldo final coincida con el stock actual

### 4. Acceso Rápido desde Resumen
1. Ve a "Reportes > Movimientos de Inventario"
2. Busca productos (por empresa, proveedor, etc.)
3. En la tabla, haz clic en el botón "Kardex" del producto deseado
4. Se abrirá directamente el Kardex con los mismos filtros de fecha

### 5. Auditoría de Inventario
1. Genera el Kardex del producto a auditar
2. Imprime o exporta a PDF
3. Realiza conteo físico
4. Compara con el saldo final del Kardex
5. Documenta cualquier diferencia

## Interpretación de Resultados

### Saldo (Columna de Saldo)
Es el **saldo acumulado** después de cada movimiento:
```
Nuevo Saldo = Saldo Anterior + Entradas - Salidas
```

### Ejemplo de Lectura
```
Fecha      | Tipo   | Doc   | Entradas | Salidas | Saldo
-----------|--------|-------|----------|---------|-------
01/01/2024 | COMPRA | C-001 |   100    |    -    |  100   <- Empezamos con 100
05/01/2024 | VENTA  | V-001 |    -     |   20    |   80   <- 100 - 20 = 80
10/01/2024 | COMPRA | C-002 |    50    |    -    |  130   <- 80 + 50 = 130
```

### Balance Calculado vs Stock Actual

**Balance Calculado** = Total Entradas - Total Salidas
**Stock Actual** = Cantidad en la tabla `inventory`

Si **Balance Calculado ≠ Stock Actual**:
- ⚠️ Puede haber ajustes manuales no registrados
- ⚠️ Puede haber ventas no aplicadas al inventario
- ⚠️ Puede haber compras no agregadas al inventario
- ⚠️ Puede haber errores en las conversiones de unidades

## Exportación

El Kardex puede exportarse en varios formatos:

### Excel
- Ideal para análisis adicionales
- Mantiene el formato de columnas
- Permite cálculos personalizados

### PDF
- Orientación horizontal (landscape)
- Conserva el diseño visual
- Ideal para archivo y auditorías

### Imprimir
- Impresión directa desde el navegador
- Optimizado para papel A4/Carta
- Oculta elementos de navegación

## Ventajas del Kardex

✅ **Control cronológico preciso** de cada movimiento
✅ **Solo cantidades** (sin confusión con valores monetarios)
✅ **Saldo acumulado** visible en cada línea
✅ **Detección inmediata** de discrepancias
✅ **Trazabilidad completa** de compras y ventas
✅ **Auditable** y exportable
✅ **Cumple estándares** de control de inventario

## Diferencias con Otros Reportes

### Kardex vs Movimientos de Inventario
- **Kardex**: Vista detallada de UN producto
- **Movimientos**: Vista resumida de MÚLTIPLES productos

### Kardex vs Inventario General
- **Kardex**: Muestra MOVIMIENTOS cronológicos
- **Inventario**: Muestra STOCK actual

## Solución de Problemas

### Problema: No aparecen movimientos
**Causas posibles**:
- El producto no tiene compras agregadas al inventario
- El producto no tiene ventas en el periodo
- Las fechas filtran todos los movimientos

**Solución**:
- Verifica que las compras estén marcadas como "agregadas al inventario"
- Amplia el rango de fechas o elimina los filtros de fecha
- Verifica que el producto haya tenido actividad

### Problema: Saldo negativo en rojo
**Interpretación**:
- El producto se vendió más de lo que se tenía en existencia
- Indica posible error en el inventario

**Solución**:
- Revisa los movimientos anteriores
- Verifica si falta registrar alguna compra
- Considera un ajuste de inventario si es necesario

### Problema: Diferencia entre balance y stock actual
**Causas posibles**:
- Ajustes manuales directos al inventario
- Compras no agregadas al inventario
- Ventas no aplicadas correctamente
- Errores en conversiones de unidades

**Solución**:
1. Revisa el historial completo del producto
2. Verifica ajustes manuales en la tabla `inventory`
3. Confirma que todas las compras estén agregadas
4. Revisa conversiones de unidades de medida

## Mejores Prácticas

1. **Genera Kardex mensualmente** para cada producto crítico
2. **Exporta a PDF** para mantener registro histórico
3. **Compara con conteos físicos** regularmente
4. **Investiga inmediatamente** cualquier diferencia
5. **Documenta ajustes** cuando sean necesarios
6. **Usa filtros de fecha** para análisis de periodos específicos
7. **Archiva los reportes** para auditorías futuras

## Archivos del Sistema

### Backend
- **Controlador**: `app/Http/Controllers/ReportsController.php`
  - Método: `inventoryKardex()`
- **Servicios**: `app/Services/UnitConversionService.php`

### Frontend
- **Vista**: `resources/views/reports/inventory-kardex.blade.php`

### Rutas
- **POST**: `/report/inventory-kardex`

### Menú
- **Reportes > Kardex (por producto)**

## Consideraciones Técnicas

### Unidades de Medida
- Todas las cantidades se muestran en **unidades base**
- Las conversiones se aplican automáticamente
- Consistente con el sistema de unidades del producto

### Orden Cronológico
- Los movimientos se ordenan por fecha ascendente
- Los saldos se calculan secuencialmente
- Si hay múltiples movimientos en el mismo día, se procesan en orden de registro

### Performance
- El reporte puede tardar con productos que tienen muchos movimientos
- Se recomienda usar filtros de fecha para grandes volúmenes
- La exportación a Excel es más rápida que PDF

### Precisión
- Decimales: hasta 2 posiciones
- Redondeo: estándar matemático
- Saldos: se calculan con precisión de 4 decimales internamente

## Integración con Otros Reportes

El Kardex se complementa con:
- **Movimientos de Inventario**: Para vista general de múltiples productos
- **Inventario**: Para stock actual
- **Compras**: Para detalles de entradas
- **Ventas**: Para detalles de salidas

## Ejemplo Práctico Completo

### Escenario
Producto: "Fertilizante 25kg"
Periodo: Enero 2024

### Kardex Generado
```
Fecha      | Tipo   | Documento | Entradas | Salidas | Saldo
-----------|--------|-----------|----------|---------|-------
02/01/2024 | COMPRA | COMP-001  |   200    |    -    |  200
05/01/2024 | VENTA  | FC-0045   |    -     |   50    |  150
10/01/2024 | VENTA  | FC-0067   |    -     |   30    |  120
15/01/2024 | COMPRA | COMP-002  |   100    |    -    |  220
20/01/2024 | VENTA  | FC-0089   |    -     |   40    |  180
25/01/2024 | VENTA  | FC-0095   |    -     |   60    |  120
-----------|--------|-----------|----------|---------|-------
TOTALES                         |   300    |   180   |  120
STOCK ACTUAL EN SISTEMA:                             |  120
DIFERENCIA:                                          |    0  ✓
```

### Análisis
- ✅ Total entradas: 300 unidades (2 compras)
- ✅ Total salidas: 180 unidades (4 ventas)
- ✅ Balance calculado: 120 unidades
- ✅ Stock actual: 120 unidades
- ✅ Diferencia: 0 (inventario correcto)

## Soporte

Para problemas o sugerencias sobre el Kardex:
- Revisa los logs en `storage/logs/laravel.log`
- Verifica permisos: `report.inventory-kardex`
- Contacta al equipo de desarrollo

---

**Última actualización**: Noviembre 2024

