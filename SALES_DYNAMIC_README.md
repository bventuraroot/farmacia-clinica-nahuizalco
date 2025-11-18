# Módulo de Ventas Dinámicas - Agroservicio Milagro de Dios

## 📋 Descripción

El **Módulo de Ventas Dinámicas** es una versión mejorada del sistema de ventas tradicional que integra toda la lógica del módulo original en una interfaz moderna y dinámica. Este módulo permite crear ventas de manera más eficiente, mostrando toda la información relevante en una sola pantalla.

## ✨ Características Principales

### 🏢 **Gestión de Empresas**
- Selección de empresa con filtrado por usuario
- Obtención automática de correlativos por tipo de documento
- Validación de permisos de usuario

### 👥 **Información Completa del Cliente**
- Selección de cliente con información detallada
- Visualización automática de datos del cliente (NIT, email, teléfono, dirección)
- Llenado automático del campo "a cuenta de"

### 📄 **Gestión de Documentos**
- Soporte para múltiples tipos de documento:
  - 📄 **Factura** (tipo 6)
  - 📋 **Nota de Crédito** (tipo 8)
  - 📝 **Nota de Débito** (tipo 7)
  - 🏛️ **Crédito Fiscal** (tipo 3)
- Correlativos automáticos por empresa y tipo de documento
- Fecha automática del sistema

### 🛍️ **Gestión de Productos**
- Búsqueda por código de barras (escáner)
- Búsqueda por nombre de producto
- Vista previa de información del producto
- Cálculo automático de totales
- Gestión de stock

### 💰 **Cálculos Automáticos**
- Subtotal por producto
- IVA (13%) automático
- Total general
- Validaciones de stock

### 💾 **Funcionalidades Avanzadas**
- Guardado de borradores
- Impresión de tickets
- Atajos de teclado
- Validaciones completas

## 🗂️ Estructura de Archivos

```
app/Http/Controllers/
├── SaleController.php          # Controlador principal con métodos dinámicos

resources/views/sales/
├── create-dynamic.blade.php    # Vista principal del módulo dinámico

public/assets/js/
├── sales-dynamic.js           # JavaScript del módulo dinámico

public/css/
├── sales-dynamic.css          # Estilos específicos del módulo

routes/
├── web.php                    # Rutas del módulo dinámico
```

## 🛣️ Rutas del Módulo

```php
// Rutas principales
Route::get('create-dynamic', [SaleController::class, 'createDynamic'])->name('create-dynamic');
Route::get('search-product', [SaleController::class, 'searchProduct'])->name('search-product');
Route::post('add-product', [SaleController::class, 'addProduct'])->name('add-product');
Route::post('remove-product', [SaleController::class, 'removeProduct'])->name('remove-product');
Route::post('process-sale', [SaleController::class, 'processSale'])->name('process-sale');
Route::post('save-draft', [SaleController::class, 'saveDraft'])->name('save-draft');
Route::get('clients', [SaleController::class, 'getClients'])->name('clients');
Route::get('products', [SaleController::class, 'getProducts'])->name('products');
Route::get('get-correlativo', [SaleController::class, 'getCorrelativo'])->name('get-correlativo');
Route::get('get-client-info', [SaleController::class, 'getClientInfo'])->name('get-client-info');
```

## 🚀 Cómo Usar

### 1. **Acceso al Módulo**
- Navegar a **Ventas > Nueva Venta** en el menú principal
- O ir directamente a `/sale/create-dynamic`

### 2. **Selección de Tipo de Documento**
- Desde `sales.index`, hacer clic en **"Nueva Venta"**
- Seleccionar el tipo de documento deseado en el modal
- El sistema redirigirá al módulo dinámico con el tipo seleccionado

### 3. **Configuración Inicial**
1. **Seleccionar Empresa**: Elegir la empresa desde el dropdown
2. **Verificar Correlativo**: Se asignará automáticamente
3. **Seleccionar Cliente**: Elegir el cliente de la lista
4. **Completar Información**: Forma de pago y "a cuenta de"

### 4. **Agregar Productos**
- **Opción 1**: Escanear código de barras
- **Opción 2**: Buscar por nombre en el dropdown
- **Ajustar Cantidad**: Modificar la cantidad deseada
- **Agregar**: Hacer clic en "Agregar" o presionar Enter

### 5. **Finalizar Venta**
- Revisar totales en el panel derecho
- Agregar notas adicionales si es necesario
- Hacer clic en **"Generar Documento"**

## ⌨️ Atajos de Teclado

| Atajo | Función |
|-------|---------|
| `F2` | Enfocar campo de código de barras |
| `Ctrl + S` | Guardar borrador |
| `Ctrl + Enter` | Finalizar venta |

## 🔧 Configuración

### Variables de Entorno
```env
# Configuración de impresión automática
TICKET_AUTO_ENABLED=true
```

### Configuración de JavaScript
```javascript
window.salesDynamicConfig = {
    baseUrl: '{{ url("/") }}',
    routes: {
        searchProduct: '{{ route("sale.search-product") }}',
        addProduct: '{{ route("sale.add-product") }}',
        removeProduct: '{{ route("sale.remove-product") }}',
        processSale: '{{ route("sale.process-sale") }}',
        saveDraft: '{{ route("sale.save-draft") }}',
        clients: '{{ route("sale.clients") }}',
        products: '{{ route("sale.products") }}',
        getCorrelativo: '{{ route("sale.get-correlativo") }}',
        getClientInfo: '{{ route("sale.get-client-info") }}'
    },
    documentType: {{ $typedocument ?? 6 }},
    documentName: '{{ $document ?? "Factura" }}'
};
```

## 🎨 Personalización

### Estilos CSS
Los estilos se pueden personalizar editando `public/css/sales-dynamic.css`:

```css
:root {
    --primary-color: #696cff;
    --success-color: #71dd37;
    --warning-color: #ffab00;
    --danger-color: #ff3e1d;
    --info-color: #03c3ec;
    --light-color: #f8f9fa;
    --dark-color: #566a7f;
    --border-color: #d9dee3;
    --text-color: #697a8d;
    --border-radius: 0.375rem;
    --box-shadow: 0 0.25rem 1.125rem rgba(75, 70, 92, 0.1);
}
```

### JavaScript
El comportamiento se puede personalizar editando `public/assets/js/sales-dynamic.js`:

```javascript
class SalesDynamicManager {
    constructor() {
        // Configuración personalizable
        this.config = {
            autoFocus: true,
            enableShortcuts: true,
            showNotifications: true
        };
    }
}
```

## 🔍 Ventajas del Módulo Dinámico

### ✅ **Ventajas**
- **Interfaz Unificada**: Toda la información en una sola pantalla
- **Flujo Optimizado**: Menos pasos para completar una venta
- **Validaciones en Tiempo Real**: Feedback inmediato al usuario
- **Compatibilidad Total**: Usa toda la lógica del módulo original
- **Diseño Responsivo**: Funciona en dispositivos móviles
- **Atajos de Teclado**: Navegación rápida para usuarios avanzados

### 🔄 **Diferencias con el Módulo Original**
- **Sin Pasos**: Elimina el proceso step-by-step
- **Información Completa**: Muestra todos los datos relevantes desde el inicio
- **Interacción Dinámica**: Respuesta inmediata a las acciones del usuario
- **Diseño Moderno**: Interfaz más limpia y profesional

## 🐛 Solución de Problemas

### Error: "Route [sale.finalize] not defined"
**Solución**: Verificar que las rutas estén correctamente definidas en `routes/web.php`

### Error: "Producto no encontrado"
**Solución**: 
1. Verificar que el producto exista en la base de datos
2. Confirmar que la empresa seleccionada tenga acceso al producto
3. Revisar el código de barras o nombre del producto

### Error: "No se encontró correlativo"
**Solución**:
1. Verificar que la empresa tenga correlativos configurados
2. Confirmar que el tipo de documento esté habilitado para la empresa

### Problemas de Rendimiento
**Solución**:
1. Optimizar consultas de base de datos
2. Implementar caché para productos y clientes
3. Reducir el número de llamadas AJAX

## 🔮 Mejoras Futuras

### Funcionalidades Planificadas
- [ ] **Descuentos Automáticos**: Sistema de descuentos por cliente/producto
- [ ] **Múltiples Formas de Pago**: Combinación de métodos de pago
- [ ] **Gestión de Stock Avanzada**: Reservas y alertas de stock
- [ ] **Integración con Impresoras**: Impresión directa de tickets
- [ ] **Modo Offline**: Funcionamiento sin conexión a internet
- [ ] **Reportes en Tiempo Real**: Estadísticas de ventas dinámicas

### Optimizaciones Técnicas
- [ ] **Lazy Loading**: Carga progresiva de productos
- [ ] **Caché Inteligente**: Caché de consultas frecuentes
- [ ] **Compresión de Datos**: Optimización de transferencia de datos
- [ ] **PWA**: Aplicación web progresiva

## 📞 Soporte

Para soporte técnico o reportar problemas:
- **Email**: soporte@agroserviciomilagro.com
- **Teléfono**: +503 XXXX-XXXX
- **Documentación**: [Enlace a documentación completa]

---

**Versión**: 2.0.0  
**Última Actualización**: Enero 2025  
**Desarrollado por**: Equipo de Desarrollo Agroservicio Milagro de Dios
