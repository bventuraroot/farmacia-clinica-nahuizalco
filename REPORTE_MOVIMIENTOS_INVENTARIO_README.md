# Reporte de Movimientos de Inventario

## Descripción
Este reporte permite analizar el flujo de productos en el inventario, mostrando cuánto se ha comprado (entradas) y cuánto se ha vendido (salidas) para cada producto. Es una herramienta esencial para detectar discrepancias, productos con stock negativo y mantener el control del inventario.

## Ubicación
El reporte está disponible en el menú principal:
**Reportes > Movimientos de Inventario**

O directamente en la URL: `/report/inventory-movements`

> **Nota**: Este reporte muestra un resumen de múltiples productos. Para ver el detalle cronológico de UN producto específico (tipo Kardex), usa el botón "Kardex" en cada producto o accede a **Reportes > Kardex (por producto)**.

## Características Principales

### 1. Filtros de Búsqueda
El reporte incluye varios filtros para personalizar el análisis:

- **Empresa** (obligatorio): Selecciona la empresa de la cual deseas ver los movimientos
- **Proveedor**: Filtra productos por proveedor específico
- **Categoría**: Filtra por categoría de productos
- **Producto Específico**: Busca movimientos de un producto en particular
- **Fecha Desde**: Define el inicio del periodo de análisis
- **Fecha Hasta**: Define el fin del periodo de análisis
- **Negativos Primero**: Checkbox para priorizar productos con stock negativo en los resultados

### 2. Estadísticas Generales
El reporte muestra estadísticas clave en tarjetas visuales:

- **Total Productos**: Cantidad de productos con movimientos
- **Stock Negativo**: Número de productos con inventario negativo (resaltado en rojo)
- **Total Compras**: Suma total de unidades compradas en el periodo
- **Total Ventas**: Suma total de unidades vendidas en el periodo

### 3. Tabla de Movimientos
La tabla principal muestra para cada producto:

| Columna | Descripción |
|---------|-------------|
| Código | Código del producto |
| Producto | Nombre del producto |
| Proveedor | Proveedor del producto |
| Categoría | Categoría o tipo del producto |
| Compras | Total de unidades compradas (con contador de movimientos) |
| Ventas | Total de unidades vendidas (con contador de movimientos) |
| Balance | Diferencia calculada entre compras y ventas |
| Stock Actual | Cantidad actual en el inventario del sistema |
| Diferencia | Diferencia entre el balance calculado y el stock real |
| Estado | Indicador visual del estado del producto |
| Acciones | Botón para ver detalles expandidos |

### 4. Estados del Producto

- 🔴 **Negativo**: El stock actual es menor a cero (producto en rojo)
- ⚠️ **Diferencia**: Hay discrepancia entre el balance calculado y el stock real
- ✅ **OK**: El inventario es correcto y positivo

### 5. Detalles Expandibles
Al hacer clic en "Ver Detalles", se despliega información adicional:

#### Compras (Entradas)
- Listado de todas las compras del producto
- Fecha de cada compra
- Número de documento
- Cantidad comprada

#### Ventas (Salidas)
- Listado de todas las ventas del producto
- Fecha de cada venta
- Número de documento
- Cantidad vendida

#### Análisis
Resumen que muestra:
- Total de entradas (compras)
- Total de salidas (ventas)
- Balance calculado
- Stock actual en sistema
- Diferencia y evaluación si hay errores de inventario

### 6. Exportación de Datos
El reporte incluye botones para exportar:
- **Excel**: Para análisis en hojas de cálculo
- **PDF**: Para impresión o archivo
- **Imprimir**: Para imprimir directamente

### 7. Acceso al Kardex Detallado
Cada producto en la tabla tiene un botón **"Kardex"** que permite:
- Ver el detalle cronológico completo del producto
- Visualizar entradas y salidas fecha por fecha
- Ver el saldo acumulado después de cada movimiento
- Formato tipo Kardex tradicional (sin valores monetarios)

## Casos de Uso

### 1. Detectar Productos con Stock Negativo
1. Accede al reporte
2. Selecciona la empresa
3. Marca el checkbox "Negativos Primero"
4. Haz clic en "Buscar"
5. Los productos con stock negativo aparecerán resaltados en rojo al principio

### 2. Analizar Movimientos de un Producto Específico
1. Accede al reporte
2. Selecciona la empresa
3. En "Producto Específico", busca y selecciona el producto
4. Opcionalmente, define un rango de fechas
5. Haz clic en "Buscar"
6. Haz clic en "Ver Detalles" para ver todas las compras y ventas

### 3. Identificar Discrepancias en el Inventario
1. Ejecuta el reporte sin filtros de producto (solo empresa)
2. Revisa la columna "Diferencia"
3. Los productos con diferencia ≠ 0 tienen discrepancias
4. Revisa los detalles para identificar el origen del problema

### 4. Análisis por Periodo
1. Selecciona la empresa
2. Define "Fecha Desde" y "Fecha Hasta"
3. Busca
4. El reporte mostrará solo movimientos dentro de ese periodo

### 5. Control por Proveedor
1. Selecciona la empresa y el proveedor
2. Busca
3. Verás movimientos solo de productos de ese proveedor

## Interpretación de la Diferencia

La columna "Diferencia" es crucial para detectar problemas:

```
Diferencia = Stock Actual - Balance Calculado
Balance Calculado = Total Compras - Total Ventas
```

### Casos:
- **Diferencia = 0**: ✅ El inventario es correcto
- **Diferencia > 0**: ⚠️ Hay más stock del que debería (posible entrada no registrada o venta no aplicada)
- **Diferencia < 0**: ⚠️ Hay menos stock del que debería (posible salida no registrada o compra no aplicada)

## Consideraciones Técnicas

### Unidades de Medida
- El reporte utiliza las unidades base del sistema
- Las conversiones se aplican automáticamente
- Para productos con peso: se muestra en libras
- Para productos con volumen: se muestra en litros
- Para productos por unidad: se muestra en unidades

### Rango de Fechas
- Si no se especifican fechas, se consideran TODOS los movimientos históricos
- Las fechas ayudan a analizar periodos específicos
- Recomendado para auditorías mensuales o trimestrales

### Performance
- El reporte puede tardar más con muchos productos
- Use filtros para mejorar el tiempo de respuesta
- La exportación a Excel es más rápida para grandes volúmenes

## Solución de Problemas Comunes

### Problema: No aparecen movimientos
**Solución**: 
- Verifica que la empresa seleccionada sea correcta
- Asegúrate de que las compras estén marcadas como "agregadas al inventario"
- Confirma que las ventas estén en estado "activo"

### Problema: Stock negativo
**Solución**:
1. Usa el reporte para identificar el producto
2. Revisa los detalles de compras y ventas
3. Verifica si hay ventas que no deberían estar aplicadas
4. Considera ajustes de inventario si es necesario

### Problema: Diferencia inexplicable
**Solución**:
1. Revisa los detalles del producto
2. Verifica ajustes manuales en el inventario
3. Confirma que todas las compras estén agregadas al inventario
4. Revisa si hay ventas canceladas que no se revirtieron

## Mantenimiento

### Permisos Requeridos
Para acceder al reporte, el usuario debe tener el permiso:
- `report.inventory-movements`

### Frecuencia Recomendada
Se recomienda ejecutar este reporte:
- **Diariamente**: Para negocios con alto movimiento
- **Semanalmente**: Para control rutinario
- **Mensualmente**: Para auditorías formales
- **Cuando sea necesario**: Para investigar discrepancias específicas

## Archivos Relacionados

### Backend
- **Controlador**: `app/Http/Controllers/ReportsController.php`
  - Métodos: `inventoryMovements()`, `inventoryMovementsSearch()`
- **Modelos**: 
  - `app/Models/Product.php`
  - `app/Models/Inventory.php`
  - `app/Models/PurchaseDetail.php`
  - `app/Models/Salesdetail.php`
- **Servicios**: `app/Services/UnitConversionService.php`

### Frontend
- **Vista**: `resources/views/reports/inventory-movements.blade.php`

### Rutas
- **Web**: `routes/web.php`
  - GET: `/report/inventory-movements`
  - POST: `/report/inventory-movements-search`

### Menú
- **Vertical**: `resources/menu/verticalMenu.json`

## Notas Importantes

1. **Compras no agregadas**: Solo se cuentan compras que están marcadas como "agregadas al inventario"
2. **Ventas canceladas**: Solo se cuentan ventas en estado activo
3. **Unidades base**: Todos los cálculos se hacen en unidades base para consistencia
4. **Tiempo real**: Los datos reflejan el estado actual del inventario al momento de ejecutar el reporte
5. **Auditoría**: Este reporte es ideal para auditorías y reconciliaciones de inventario

## Soporte

Para problemas o sugerencias sobre este reporte, contacta al equipo de desarrollo o revisa los logs del sistema en `storage/logs/laravel.log`.



