# Sistema de Reportes - Agroservicio Milagro de Dios

## Índice
1. [Reportes de Inventario](#reportes-de-inventario)
2. [Reportes de Ventas](#reportes-de-ventas)
3. [Análisis Avanzados](#análisis-avanzados)
4. [Cómo Acceder](#cómo-acceder)

---

## Reportes de Inventario

### 1. Reporte General de Inventario
**Ruta:** `/report/inventory`  
**Método:** `GET`

**Características:**
- Listado completo de productos en inventario
- Información detallada: código, nombre, categoría, proveedor, marca
- Stock actual, stock mínimo y unidad base
- Precio unitario y valor total del inventario
- Ubicación y fechas de vencimiento
- Estado del producto (activo/inactivo)

**Filtros disponibles:**
- Empresa (requerido)
- Categoría
- Proveedor
- Estado del stock (bajo stock, sin stock, por vencer, activos)
- Ubicación
- Ordenamiento (por nombre, cantidad, precio, vencimiento)

**Estadísticas mostradas:**
- Total de productos
- Valor total del inventario
- Productos con stock bajo
- Productos sin stock
- Productos por vencer (próximos 30 días)
- Productos activos

**Exportación:**
- Excel (CSV)
- PDF (en desarrollo)

### 2. Reporte de Inventario por Categoría
**Ruta:** `/report/inventory-by-category`  
**Método:** `GET`

**Características:**
- Agrupación de inventario por categoría de producto
- Total de productos por categoría
- Cantidad total en stock
- Productos con stock bajo y sin stock
- Valor total por categoría

### 3. Reporte de Inventario por Proveedor
**Ruta:** `/report/inventory-by-provider`  
**Método:** `GET`

**Características:**
- Agrupación de inventario por proveedor
- Información del proveedor (razón social, NIT)
- Total de productos por proveedor
- Cantidad total en stock
- Valor total del inventario por proveedor
- Precio promedio de productos

---

## Reportes de Ventas

### 1. Reporte de Ventas por Cliente
**Ruta:** `/report/sales-by-client`  
**Método:** `GET`

**Características:**
- Análisis de ventas agrupadas por cliente
- Información del cliente (nombre, tipo, NIT, email)
- Total de ventas, completadas y canceladas
- Monto total y promedio por venta
- Fecha de primera y última venta
- Vista de detalles por cliente (productos vendidos)

**Filtros disponibles:**
- Empresa (requerido)
- Cliente específico
- Año
- Mes
- Rango de fechas

**Exportación:**
- Excel (CSV)
- PDF con detalles

### 2. Reporte de Ventas por Proveedor
**Ruta:** `/report/sales-by-provider`  
**Método:** `GET`

**Características:**
- Análisis de ventas de productos agrupados por proveedor
- Información del proveedor (razón social, NIT, email)
- Total de ventas realizadas
- Cantidad de productos diferentes vendidos
- Cantidad total vendida
- Monto total de ventas
- Precio promedio
- Fechas de primera y última venta

**Filtros disponibles:**
- Empresa (requerido)
- Proveedor específico
- Año
- Mes
- Rango de fechas

**Estadísticas mostradas:**
- Total de proveedores
- Total de ventas
- Monto total
- Promedio por proveedor

**Visualización:**
- Gráfica de barras con Top 10 proveedores
- Tabla detallada con todos los proveedores

**Exportación:**
- Excel (CSV)
- PDF (en desarrollo)

### 3. Reporte de Ventas por Producto
**Ruta:** `/report/sales-by-product`  
**Método:** `GET`

**Características:**
- Análisis detallado de ventas por producto individual
- Código y nombre del producto
- Categoría, proveedor y marca
- Total de ventas realizadas
- Cantidad total vendida
- Monto total generado
- Precio promedio de venta
- Fechas de primera y última venta

**Filtros disponibles:**
- Empresa (requerido)
- Categoría
- Proveedor
- Año
- Mes
- Rango de fechas

**Estadísticas mostradas:**
- Total de productos vendidos
- Cantidad total vendida
- Monto total de ventas
- Promedio por producto

**Visualización:**
- Gráfica Top 10 productos por monto (barras horizontales)
- Gráfica Top 10 productos por cantidad (barras horizontales)

**Exportación:**
- Excel (CSV)

### 4. Reporte de Ventas por Categoría
**Ruta:** `/report/sales-by-category`  
**Método:** `GET`

**Características:**
- Análisis de ventas agrupadas por categoría de producto
- Total de ventas por categoría
- Total de productos diferentes vendidos
- Cantidad total vendida
- Monto total generado
- Precio promedio
- Porcentaje del total de ventas

**Filtros disponibles:**
- Empresa (requerido)
- Año
- Mes
- Rango de fechas

**Estadísticas mostradas:**
- Total de categorías
- Cantidad total vendida
- Monto total
- Promedio por categoría

**Visualización:**
- Gráfica de pastel: Distribución de ventas por categoría (monto)
- Gráfica de barras comparativa: Cantidad vs Monto por categoría
- Barra de progreso por porcentaje del total

**Exportación:**
- Excel (CSV)

---

## Análisis Avanzados

### 1. Análisis General de Ventas
**Ruta:** `/report/sales-analysis`  
**Método:** `GET`

**Características:**
- Dashboard completo con múltiples métricas
- Análisis por período (mensual)
- Análisis por tipo de documento
- Top 10 clientes
- Tendencias de ventas

**Filtros disponibles:**
- Empresa (requerido)
- Año
- Mes

**Estadísticas principales:**
- Total de ventas completadas
- Monto total generado
- Ticket promedio
- Ventas canceladas

**Secciones del análisis:**

#### a) Ventas por Período
- Gráfica de líneas con evolución mensual
- Tabla con desglose por mes
- Métricas: total ventas, monto, promedio, canceladas

#### b) Ventas por Tipo de Documento
- Gráfica de dona (doughnut) con distribución
- Tabla con cantidad y monto por tipo
- Tipos: CCF, Factura, Ticket, etc.

#### c) Top 10 Clientes
- Tabla con ranking de clientes
- Total de ventas y monto por cliente
- Barra de progreso con porcentaje del total
- Identificación de clientes más valiosos

**Visualización:**
- Gráficas interactivas con Chart.js
- Tarjetas con métricas principales
- Tablas con datos detallados

---

## Reportes Existentes Previos

### 1. Libro de Ventas (Contribuyentes)
**Ruta:** `/report/contribuyentes`

### 2. Libro de Ventas a Consumidor
**Ruta:** `/report/consumidor`

### 3. Libro de Compras
**Ruta:** `/report/bookpurchases`

### 4. Reporte Anual (IVA)
**Ruta:** `/report/reportyear`

---

## Cómo Acceder a los Reportes

### Desde el Menú Principal
Los reportes están organizados en el menú de navegación bajo la sección "Reportes":

```
Reportes
├── Inventario
│   ├── Reporte General
│   ├── Por Categoría
│   └── Por Proveedor
├── Ventas
│   ├── Por Cliente
│   ├── Por Proveedor
│   ├── Por Producto
│   ├── Por Categoría
│   └── Análisis General
└── Fiscales
    ├── Contribuyentes
    ├── Consumidor Final
    ├── Libro de Compras
    └── Reporte Anual
```

### Uso Básico

1. **Seleccionar Empresa**: Todos los reportes requieren seleccionar una empresa
2. **Aplicar Filtros**: Configurar los filtros según necesidades (año, mes, categoría, etc.)
3. **Buscar**: Hacer clic en el botón "Buscar" para generar el reporte
4. **Visualizar**: Ver resultados en tablas y gráficas
5. **Exportar**: Descargar en formato Excel o PDF según disponibilidad

### Características Comunes

**Filtros:**
- Todos los reportes requieren selección de empresa
- Filtros por período (año/mes) o rango de fechas
- Filtros específicos según tipo de reporte

**Visualización:**
- Tarjetas con estadísticas principales
- Tablas con DataTables (ordenamiento, búsqueda, paginación)
- Gráficas interactivas con Chart.js
- Responsive design para móviles y tablets

**Exportación:**
- Excel (CSV con UTF-8 BOM para caracteres especiales)
- PDF (en algunos reportes)
- Nombres de archivo con timestamp

**Tecnologías Utilizadas:**
- Laravel 10
- DataTables.js para tablas interactivas
- Chart.js para gráficas
- Select2 para selects mejorados
- Bootstrap 5 para diseño responsivo
- DomPDF para generación de PDFs

---

## Notas Importantes

### Permisos
- Asegúrate de que el usuario tenga permisos para acceder a los reportes
- Los reportes filtran datos por empresa asignada al usuario

### Rendimiento
- Los reportes con grandes volúmenes de datos pueden tomar tiempo
- Se recomienda usar filtros para limitar resultados
- Las gráficas muestran Top 10 para mejor rendimiento

### Datos
- Solo se incluyen ventas completadas (state = 1) en análisis de ventas
- Las ventas canceladas se cuentan por separado
- El inventario muestra datos en tiempo real
- Los precios y cantidades usan hasta 4 decimales de precisión

### Exportación Excel
- Los archivos CSV incluyen BOM UTF-8 para compatibilidad
- Se pueden abrir directamente en Excel
- Los decimales usan punto como separador

### Mejoras Futuras
- [ ] Exportación PDF completa para todos los reportes
- [ ] Reportes programados por email
- [ ] Gráficas adicionales y dashboards
- [ ] Comparativas entre períodos
- [ ] Análisis de rentabilidad por producto/categoría
- [ ] Reportes de compras similares a ventas
- [ ] Integración con sistema DTE

---

## Configuración de Permisos

### Permisos Requeridos

El sistema de reportes utiliza un sistema de permisos granular. Cada reporte y funcionalidad tiene permisos específicos:

#### Permisos de Visualización
- `report.sales` - Ver reporte de ventas
- `report.sales-by-client` - Ver reporte de ventas por cliente
- `report.sales-by-provider` - Ver reporte de ventas por proveedor
- `report.sales-analysis` - Ver análisis general de ventas
- `report.sales-by-product` - Ver reporte de ventas por producto
- `report.sales-by-category` - Ver reporte de ventas por categoría
- `report.inventory` - Ver reporte de inventario
- `report.inventory-by-category` - Ver reporte de inventario por categoría
- `report.inventory-by-provider` - Ver reporte de inventario por proveedor
- `report.contribuyentes` - Ver reporte de contribuyentes
- `report.consumidor` - Ver reporte de consumidor final
- `report.bookpurchases` - Ver libro de compras
- `report.reportyear` - Ver reporte anual

#### Permisos de Búsqueda
- `report.sales-by-provider-search` - Buscar en reporte de ventas por proveedor
- `report.sales-analysis-search` - Buscar en análisis general de ventas
- `report.sales-by-product-search` - Buscar en reporte de ventas por producto
- `report.sales-by-category-search` - Buscar en reporte de ventas por categoría
- `report.inventory-search` - Buscar en reporte de inventario

#### Permisos de Exportación
- `report.export-excel` - Exportar reportes a Excel
- `report.export-pdf` - Exportar reportes a PDF

### Configurar Permisos

#### Opción 1: Comando Artisan (Recomendado)
```bash
# Crear todos los permisos y asignarlos a todos los roles
php artisan reports:setup-permissions

# Listar roles disponibles
php artisan reports:setup-permissions --list-roles

# Asignar permisos solo a un rol específico
php artisan reports:setup-permissions --assign-to-role="Administrador"
```

#### Opción 2: Script PHP
```bash
php scripts/setup_report_permissions.php
```

#### Opción 3: Interfaz Web
1. Acceder a `/permission/index`
2. Usar los endpoints:
   - `POST /permission/create-reports-permissions`
   - `POST /permission/assign-reports-permissions`

#### Opción 4: Manual
1. Ir a **Administración > Permisos**
2. Crear manualmente cada permiso
3. Asignar permisos a los roles correspondientes

### Verificar Permisos

Para verificar que los permisos están configurados correctamente:

1. **Verificar en la base de datos:**
   ```sql
   SELECT * FROM permissions WHERE name LIKE 'report.%';
   ```

2. **Verificar asignación a roles:**
   ```sql
   SELECT r.name as role_name, p.name as permission_name 
   FROM roles r 
   JOIN role_has_permissions rhp ON r.id = rhp.role_id 
   JOIN permissions p ON rhp.permission_id = p.id 
   WHERE p.name LIKE 'report.%';
   ```

3. **Probar acceso:** Intentar acceder a los reportes con diferentes usuarios y roles.

### Solución de Problemas de Permisos

#### Usuario no puede ver reportes
1. Verificar que el usuario tiene un rol asignado
2. Verificar que el rol tiene los permisos necesarios
3. Verificar que el usuario está autenticado
4. Limpiar caché de permisos: `php artisan permission:cache-reset`

#### Menú no muestra opciones de reportes
1. Verificar que el permiso `report.index` existe
2. Verificar que el rol del usuario tiene este permiso
3. Verificar la configuración del menú en `PermissionController::getmenujson()`

#### Error 403 (Forbidden)
1. Verificar permisos específicos del reporte
2. Verificar middleware de permisos en las rutas
3. Revisar logs de Laravel para más detalles

---

## Soporte

Para dudas o problemas con los reportes, contactar al equipo de desarrollo o consultar la documentación técnica en:
- `app/Http/Controllers/ReportsController.php` - Controlador principal
- `app/Http/Controllers/PermissionController.php` - Gestión de permisos
- `resources/views/reports/` - Vistas de los reportes
- `routes/web.php` - Rutas configuradas
- `app/Console/Commands/SetupReportPermissions.php` - Comando de configuración

### Comandos Útiles

```bash
# Configurar permisos de reportes
php artisan reports:setup-permissions

# Limpiar caché de permisos
php artisan permission:cache-reset

# Listar todos los permisos
php artisan permission:show

# Crear rol
php artisan permission:create-role "Nombre del Rol"

# Asignar permiso a rol
php artisan permission:give-permission-to-role "permiso" "rol"
```

---

**Última actualización:** Octubre 2025  
**Versión:** 1.1

