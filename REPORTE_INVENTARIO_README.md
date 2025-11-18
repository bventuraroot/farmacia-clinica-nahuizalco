# Reporte de Inventario

## Descripción

Este sistema de reportes de inventario permite analizar y gestionar el stock de productos de manera integral, proporcionando información detallada sobre el estado del inventario, agrupaciones por categoría y proveedor, y análisis estadísticos avanzados.

## Características

### Funcionalidades Principales

- **Reporte Principal de Inventario:**
  - Vista completa de todos los productos en inventario
  - Filtros avanzados por empresa, categoría, proveedor, marca y ubicación
  - Estado del stock (bajo, sin stock, por vencer, activos)
  - Información detallada de unidades de medida y precios
  - Control de fechas de vencimiento y lotes

- **Reporte por Categoría:**
  - Agrupación de productos por tipo/categoría
  - Estadísticas por categoría (total productos, stock, valor)
  - Identificación de categorías con stock bajo o sin stock
  - Gráficos de distribución (dona y barras)
  - Análisis de porcentajes y promedios

- **Reporte por Proveedor:**
  - Agrupación de productos por proveedor
  - Análisis de valor total por proveedor
  - Top 5 proveedores por valor y cantidad de productos
  - Gráficos comparativos
  - Estadísticas de precios promedios

### Características Técnicas

- **Interfaz Responsiva:** Diseño adaptativo para diferentes dispositivos
- **DataTables:** Tablas con funcionalidades de búsqueda, ordenamiento y paginación
- **Gráficos Interactivos:** Chart.js para visualizaciones estadísticas
- **Exportación:** Funcionalidad para exportar a Excel (CSV)
- **Filtros Dinámicos:** Carga automática de datos según empresa seleccionada
- **Sistema de Unidades:** Soporte completo para unidades de medida y conversiones

## Acceso a los Reportes

### Rutas Principales
```
/report/inventory                    - Reporte principal de inventario
/report/inventory-by-category       - Reporte por categoría
/report/inventory-by-provider       - Reporte por proveedor
```

### Menú
Los reportes están disponibles en el menú principal bajo:
```
Reportes > Inventario
Reportes > Inventario por Categoría
Reportes > Inventario por Proveedor
```

## Uso de los Reportes

### 1. Reporte Principal de Inventario

#### Configuración de Filtros
1. **Empresa:** Seleccionar la empresa para la cual se desea generar el reporte
2. **Categoría:** Filtrar por tipo de producto (directo, tercero, etc.)
3. **Proveedor:** Filtrar por proveedor específico
4. **Estado del Stock:** 
   - Stock Bajo: Productos con cantidad ≤ stock mínimo
   - Sin Stock: Productos con cantidad = 0
   - Por Vencer: Productos que vencen en los próximos 30 días
   - Activos: Productos con estado activo
5. **Ubicación:** Filtrar por ubicación específica en el almacén
6. **Ordenamiento:** Por nombre, cantidad, precio o fecha de vencimiento

#### Interpretación de Resultados
- **Tarjetas de Resumen:** Estadísticas generales del inventario
- **Tabla Principal:** Lista detallada de productos con información completa
- **Indicadores Visuales:** Colores para identificar stock bajo (amarillo) y sin stock (rojo)
- **Información de Unidades:** Soporte para unidades base y conversiones
- **Control de Vencimiento:** Alertas para productos próximos a vencer

### 2. Reporte por Categoría

#### Características
- **Agrupación Automática:** Los productos se agrupan automáticamente por categoría
- **Estadísticas por Categoría:** Total productos, stock, valor y porcentajes
- **Gráficos de Distribución:** Visualización de la distribución por categoría
- **Análisis de Stock:** Identificación de categorías problemáticas

#### Interpretación
- **Tabla de Categorías:** Resumen estadístico por cada tipo de producto
- **Gráfico de Productos:** Distribución circular de productos por categoría
- **Gráfico de Valores:** Comparación de valores totales por categoría
- **Porcentajes:** Análisis de la participación de cada categoría en el total

### 3. Reporte por Proveedor

#### Características
- **Agrupación por Proveedor:** Análisis del inventario por proveedor
- **Top 5 Proveedores:** Ranking por valor y cantidad de productos
- **Gráficos Comparativos:** Visualización de la distribución por proveedor
- **Análisis de Precios:** Promedios y totales por proveedor

#### Interpretación
- **Tabla de Proveedores:** Resumen estadístico por cada proveedor
- **Gráficos de Distribución:** Comparación visual entre proveedores
- **Análisis de Valor:** Identificación de proveedores principales
- **Estadísticas de Productos:** Cantidad y valor promedio por proveedor

## Estructura de Datos

### Tabla Principal (inventory)
```sql
SELECT 
    inventory.id as inventory_id,
    products.id as product_id,
    products.code as product_code,
    products.name as product_name,
    products.description as product_description,
    products.type as product_type,
    products.cfiscal as fiscal_type,
    products.price as product_price,
    products.state as product_state,
    providers.razonsocial as provider_name,
    marcas.name as marca_name,
    units.unit_name as base_unit_name,
    units.unit_code as base_unit_code,
    inventory.quantity,
    inventory.base_quantity,
    inventory.base_unit_price,
    inventory.minimum_stock,
    inventory.location,
    inventory.expiration_date,
    inventory.batch_number,
    inventory.expiring_quantity
FROM inventory
JOIN products ON inventory.product_id = products.id
LEFT JOIN providers ON products.provider_id = providers.id
LEFT JOIN marcas ON products.marca_id = marcas.id
LEFT JOIN units ON inventory.base_unit_id = units.id
WHERE products.company_id = ?
```

### Agrupación por Categoría
```sql
SELECT 
    products.type as category,
    COUNT(*) as total_products,
    SUM(inventory.quantity) as total_quantity,
    SUM(CASE WHEN inventory.quantity <= inventory.minimum_stock THEN 1 ELSE 0 END) as low_stock_products,
    SUM(CASE WHEN inventory.quantity = 0 THEN 1 ELSE 0 END) as out_of_stock_products,
    SUM(inventory.quantity * COALESCE(inventory.base_unit_price, products.price)) as total_value
FROM inventory
JOIN products ON inventory.product_id = products.id
WHERE products.company_id = ?
GROUP BY products.type
ORDER BY total_value DESC
```

### Agrupación por Proveedor
```sql
SELECT 
    providers.id as provider_id,
    providers.razonsocial as provider_name,
    providers.nit as provider_nit,
    COUNT(*) as total_products,
    SUM(inventory.quantity) as total_quantity,
    SUM(inventory.quantity * COALESCE(inventory.base_unit_price, products.price)) as total_value,
    AVG(COALESCE(inventory.base_unit_price, products.price)) as average_price
FROM inventory
JOIN products ON inventory.product_id = products.id
JOIN providers ON products.provider_id = providers.id
WHERE products.company_id = ?
GROUP BY providers.id, providers.razonsocial, providers.nit
ORDER BY total_value DESC
```

## Filtros y Búsquedas

### Filtros Disponibles

#### Filtros Básicos
- **Empresa:** Filtro obligatorio para todos los reportes
- **Categoría:** Tipo de producto (directo, tercero, etc.)
- **Proveedor:** Proveedor específico del producto
- **Marca:** Marca del producto
- **Ubicación:** Ubicación física en el almacén

#### Filtros de Estado
- **Stock Bajo:** `quantity <= minimum_stock`
- **Sin Stock:** `quantity = 0`
- **Por Vencer:** `expiration_date <= now() + 30 days`
- **Activos:** `products.state = 1`

#### Ordenamiento
- **Nombre:** Orden alfabético por nombre de producto
- **Cantidad:** Orden por cantidad en stock
- **Precio:** Orden por precio del producto
- **Vencimiento:** Orden por fecha de vencimiento

### Búsquedas Avanzadas
- **Filtros Combinados:** Múltiples filtros aplicados simultáneamente
- **Búsqueda por Texto:** Filtrado en tiempo real
- **Paginación:** Navegación por páginas para grandes volúmenes de datos
- **Exportación:** Filtros aplicados se mantienen en la exportación

## Exportación de Datos

### Exportar a Excel
1. Aplicar los filtros deseados
2. Hacer clic en "Exportar Excel"
3. Se descarga automáticamente un archivo CSV con los datos filtrados
4. El archivo incluye todos los campos visibles en la tabla

### Preparado para PDF
- La funcionalidad de exportación a PDF está preparada para implementación futura
- Las vistas están optimizadas para impresión
- Se incluyen estilos específicos para impresión

## Gráficos y Visualizaciones

### Chart.js Integration
- **Gráficos de Dona:** Para distribución de productos y valores
- **Gráficos de Barras:** Para comparaciones entre categorías/proveedores
- **Responsive:** Los gráficos se adaptan al tamaño de la pantalla
- **Interactivos:** Hover effects y tooltips informativos

### Tipos de Gráficos

#### Distribución por Categoría
- **Gráfico de Productos:** Muestra la cantidad de productos por categoría
- **Gráfico de Valores:** Muestra el valor total por categoría
- **Colores Distintivos:** Cada categoría tiene un color único

#### Distribución por Proveedor
- **Gráfico de Productos:** Cantidad de productos por proveedor
- **Gráfico de Valores:** Valor total por proveedor
- **Top 5:** Resaltado de los principales proveedores

## Consideraciones Técnicas

### Rendimiento
- **Consultas Optimizadas:** Uso de índices apropiados en las tablas relacionadas
- **Paginación:** Implementación de paginación para grandes volúmenes de datos
- **Filtros a Nivel DB:** Los filtros se aplican en la base de datos para optimizar rendimiento
- **Carga Lazy:** Los gráficos se cargan solo cuando hay datos disponibles

### Seguridad
- **Filtros por Empresa:** Los datos se filtran automáticamente por la empresa del usuario
- **Validación de Entrada:** Todos los parámetros de búsqueda se validan
- **Sanitización:** Los datos se sanitizan antes de ser procesados
- **Permisos:** Respeta el sistema de permisos existente

### Compatibilidad
- **Navegadores Modernos:** Compatible con Chrome, Firefox, Safari, Edge
- **Dispositivos Móviles:** Diseño responsivo para tablets y smartphones
- **Sistemas Operativos:** Funciona en Windows, macOS y Linux
- **Resoluciones:** Se adapta a diferentes resoluciones de pantalla

## Archivos Relacionados

### Controladores
- `app/Http/Controllers/ReportsController.php`
  - `inventory()`: Muestra la vista principal del reporte de inventario
  - `inventorySearch()`: Procesa la búsqueda y genera los resultados principales
  - `inventoryByCategory()`: Genera el reporte agrupado por categoría
  - `inventoryByProvider()`: Genera el reporte agrupado por proveedor

### Vistas
- `resources/views/reports/inventory.blade.php`: Vista principal del reporte de inventario
- `resources/views/reports/inventory-by-category.blade.php`: Vista del reporte por categoría
- `resources/views/reports/inventory-by-provider.blade.php`: Vista del reporte por proveedor

### Rutas
- `routes/web.php`: Definición de todas las rutas del sistema de reportes
  - `GET /report/inventory`: Vista principal
  - `POST /report/inventory-search`: Procesamiento de búsqueda
  - `GET /report/inventory-by-category`: Reporte por categoría
  - `GET /report/inventory-by-provider`: Reporte por proveedor

### Modelos
- `app/Models/Inventory.php`: Modelo principal del inventario
- `app/Models/Product.php`: Modelo de productos
- `app/Models/Provider.php`: Modelo de proveedores
- `app/Models/Marca.php`: Modelo de marcas
- `app/Models/Unit.php`: Modelo de unidades de medida

### Menús
- `resources/menu/verticalMenu.json`: Menú principal del sistema
- `app/Http/Controllers/PermissionController.php`: Gestión de permisos y menús

## Mantenimiento y Extensión

### Estructura del Código
- **Separación de Responsabilidades:** Lógica de negocio en controladores, presentación en vistas
- **Reutilización:** Componentes comunes entre diferentes reportes
- **Modularidad:** Cada reporte es independiente y puede ser modificado sin afectar otros
- **Documentación:** Código comentado y estructurado para fácil mantenimiento

### Extensibilidad
- **Nuevos Filtros:** Fácil adición de nuevos filtros de búsqueda
- **Nuevos Reportes:** Estructura preparada para agregar nuevos tipos de reportes
- **Nuevos Gráficos:** Sistema de gráficos extensible para nuevas visualizaciones
- **APIs:** Preparado para integración con sistemas externos

### Monitoreo y Logs
- **Logs de Errores:** Captura y registro de errores para debugging
- **Métricas de Rendimiento:** Tiempo de respuesta de consultas
- **Uso de Recursos:** Monitoreo de memoria y CPU
- **Auditoría:** Registro de accesos y consultas realizadas

## Futuras Mejoras

### Funcionalidades Planificadas
- [ ] Exportación a PDF con librerías como DomPDF o Snappy
- [ ] Gráficos 3D para análisis más avanzados
- [ ] Alertas automáticas por email para stock bajo
- [ ] Comparación de inventario entre períodos
- [ ] Análisis de tendencias de stock
- [ ] Integración con sistemas de códigos de barras
- [ ] Reportes programados y automáticos
- [ ] Dashboard ejecutivo con KPIs de inventario

### Mejoras Técnicas
- [ ] Caché de consultas frecuentes
- [ ] Optimización de consultas para grandes volúmenes
- [ ] API REST para integración externa
- [ ] WebSockets para actualizaciones en tiempo real
- [ ] Sistema de notificaciones push
- [ ] Backup automático de reportes generados

### Integraciones
- [ ] Sistemas ERP externos
- [ ] Plataformas de e-commerce
- [ ] Sistemas de logística
- [ ] Herramientas de análisis de datos
- [ ] Sistemas de gestión de almacenes (WMS)
- [ ] Plataformas de business intelligence

## Conclusión

El sistema de reportes de inventario proporciona una solución completa y robusta para la gestión y análisis del inventario empresarial. Con su arquitectura modular, interfaz intuitiva y funcionalidades avanzadas, permite a los usuarios tomar decisiones informadas sobre la gestión de stock, identificación de productos problemáticos y optimización de la cadena de suministro.

La implementación sigue las mejores prácticas de desarrollo web moderno, asegurando rendimiento, seguridad y escalabilidad para futuras expansiones del sistema.
