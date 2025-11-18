# Reporte de Ventas por Clientes

## Descripción

Este reporte permite analizar las ventas agrupadas por cliente, proporcionando información detallada sobre el comportamiento de compra de cada cliente, incluyendo estadísticas de ventas, montos totales, promedios y fechas de primera y última compra.

## Características

### Funcionalidades Principales

- **Filtros de Búsqueda Avanzados:**
  - Selección de empresa
  - Filtro por año
  - Filtro por mes específico
  - Filtro por cliente específico

- **Información del Cliente:**
  - Nombre completo (persona natural) o razón social (persona jurídica)
  - Tipo de persona (Natural/Jurídica)
  - NIT
  - Email (si está disponible)

- **Estadísticas de Ventas:**
  - Total de ventas realizadas
  - Ventas completadas vs canceladas
  - Monto total de ventas
  - Promedio por venta
  - Fecha de primera venta
  - Fecha de última venta

- **Vista de Detalles:**
  - Desglose completo de todas las ventas del cliente
  - Información de productos por venta
  - Montos desglosados (gravado, exento, IVA)
  - Resumen por tipo de documento

### Características Técnicas

- **Interfaz Responsiva:** Diseño adaptativo para diferentes dispositivos
- **DataTables:** Tabla con funcionalidades de búsqueda, ordenamiento y paginación
- **Exportación:** Funcionalidad para exportar a Excel (CSV)
- **Impresión:** Vista optimizada para impresión de reportes
- **Filtros Dinámicos:** Carga automática de empresas y clientes según selección

## Acceso al Reporte

### Ruta
```
/report/sales-by-client
```

### Menú
El reporte está disponible en el menú principal bajo:
```
Reportes > Ventas por Clientes
```

## Uso del Reporte

### 1. Configuración de Filtros

1. **Seleccionar Empresa:** Elegir la empresa para la cual se desea generar el reporte
2. **Año (Opcional):** Filtrar por año específico o dejar en "Todos" para ver todos los años
3. **Mes (Opcional):** Filtrar por mes específico o dejar en "Todos" para ver todos los meses
4. **Cliente Específico (Opcional):** Seleccionar un cliente específico o dejar en "Todos los clientes"

### 2. Generación del Reporte

1. Hacer clic en el botón "Buscar"
2. El sistema procesará los datos y mostrará los resultados
3. Los resultados se ordenan por monto total de ventas (descendente)

### 3. Interpretación de Resultados

#### Tarjetas de Resumen
- **Total Clientes:** Número de clientes que realizaron ventas en el período
- **Total Ventas:** Número total de transacciones
- **Monto Total:** Suma de todos los montos de ventas
- **Promedio por Cliente:** Promedio del monto total por cliente

#### Tabla de Resultados
- **Cliente:** Nombre o razón social del cliente
- **Tipo:** Indicador si es persona natural o jurídica
- **NIT:** Número de identificación tributaria
- **Total Ventas:** Número de ventas realizadas
- **Ventas Completadas:** Ventas con estado activo
- **Ventas Canceladas:** Ventas con estado cancelado
- **Monto Total:** Suma de todas las ventas del cliente
- **Promedio por Venta:** Promedio del monto por transacción
- **Primera Venta:** Fecha de la primera compra del cliente
- **Última Venta:** Fecha de la compra más reciente

### 4. Vista de Detalles

Para ver el desglose completo de ventas de un cliente específico:

1. Hacer clic en el botón "Ver Detalles" en la fila del cliente
2. Se abrirá una nueva ventana con el detalle completo
3. La vista incluye:
   - Información del cliente
   - Estadísticas resumidas
   - Tabla detallada de todas las ventas
   - Resumen por tipo de documento

## Exportación de Datos

### Exportar a Excel
1. Hacer clic en el botón "Exportar Excel"
2. Se descargará automáticamente un archivo CSV con los datos del reporte

### Imprimir Reporte
1. En la vista de detalles, hacer clic en el botón "Imprimir"
2. Se abrirá el diálogo de impresión del navegador
3. La vista está optimizada para impresión

## Estructura de Datos

### Tabla Principal (sales_by_client)
```sql
SELECT 
    clients.id as client_id,
    clients.firstname,
    clients.firstlastname,
    clients.comercial_name,
    clients.tpersona,
    clients.nit,
    clients.email,
    COUNT(sales.id) as total_sales,
    SUM(CASE WHEN sales.state = 1 THEN 1 ELSE 0 END) as completed_sales,
    SUM(CASE WHEN sales.state = 0 THEN 1 ELSE 0 END) as cancelled_sales,
    SUM(CASE WHEN sales.state = 1 THEN sales.totalamount ELSE 0 END) as total_amount,
    AVG(CASE WHEN sales.state = 1 THEN sales.totalamount ELSE NULL END) as average_amount,
    MIN(sales.date) as first_sale_date,
    MAX(sales.date) as last_sale_date
FROM sales
JOIN clients ON sales.client_id = clients.id
WHERE sales.company_id = ?
GROUP BY clients.id
ORDER BY total_amount DESC
```

### Tabla de Detalles (sales_details)
```sql
SELECT 
    sales.id as sale_id,
    sales.date,
    sales.totalamount,
    sales.state,
    typedocuments.description as document_type,
    products.name as product_name,
    salesdetails.quantity,
    salesdetails.pricesale,
    salesdetails.exempt,
    salesdetails.detained13,
    DATE_FORMAT(sales.date, '%d/%m/%Y') AS formatted_date
FROM sales
JOIN clients ON sales.client_id = clients.id
JOIN typedocuments ON typedocuments.id = sales.typedocument_id
JOIN salesdetails ON sales.id = salesdetails.sale_id
JOIN products ON salesdetails.product_id = products.id
WHERE clients.id = ? AND sales.company_id = ?
ORDER BY sales.date DESC
```

## Permisos y Seguridad

- El reporte respeta los permisos de usuario del sistema
- Los usuarios no administradores solo ven los clientes que han ingresado
- Los datos se filtran automáticamente por la empresa del usuario

## Archivos Relacionados

### Controladores
- `app/Http/Controllers/ReportsController.php`
  - `salesByClient()`: Muestra la vista principal del reporte
  - `salesByClientSearch()`: Procesa la búsqueda y genera los resultados

### Vistas
- `resources/views/reports/sales-by-client.blade.php`: Vista principal del reporte
- `resources/views/reports/sales-by-client-details.blade.php`: Vista de detalles del cliente

### Rutas
- `routes/web.php`: Definición de rutas del reporte
  - `GET /report/sales-by-client`: Vista principal
  - `POST /report/sales-by-client-search`: Procesamiento de búsqueda

### Menús
- `resources/menu/verticalMenu.json`: Menú principal
- `app/Http/Controllers/PermissionController.php`: Gestión de permisos

## Consideraciones Técnicas

### Rendimiento
- Las consultas utilizan índices apropiados en las tablas relacionadas
- Se implementa paginación para grandes volúmenes de datos
- Los filtros se aplican a nivel de base de datos para optimizar el rendimiento

### Compatibilidad
- Compatible con navegadores modernos
- Diseño responsivo para dispositivos móviles
- Funciona con el sistema de permisos existente

### Mantenimiento
- Código documentado y estructurado
- Separación clara entre lógica de negocio y presentación
- Fácil extensión para nuevas funcionalidades

## Futuras Mejoras

- [ ] Exportación a PDF
- [ ] Gráficos estadísticos
- [ ] Comparación entre períodos
- [ ] Alertas de clientes inactivos
- [ ] Análisis de tendencias de compra
- [ ] Integración con sistema de notificaciones
