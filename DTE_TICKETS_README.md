# Implementación de DTE en Tickets y PDF

## Resumen de Cambios

Se han implementado cambios en el sistema para mostrar formatos específicos cuando un documento tiene DTE (Documento Tributario Electrónico). Los cambios incluyen:

### 1. Modelo Sale - Relación con DTE

**Archivo:** `app/Models/Sale.php`

- ✅ Agregada relación `hasOne` con el modelo `Dte`
- ✅ Método `hasDte()` para verificar si una venta tiene DTE
- ✅ Método `getDteInfo()` para obtener información del DTE

```php
public function dte()
{
    return $this->hasOne(Dte::class, 'sale_id');
}

public function hasDte()
{
    return $this->dte()->exists();
}

public function getDteInfo()
{
    if ($this->hasDte()) {
        return $this->dte;
    }
    return null;
}
```

### 2. Controlador SaleController - Detección Automática

**Archivo:** `app/Http/Controllers/SaleController.php`

#### Método `printTicket()` Modificado:
- ✅ Incluye relación `dte` en la consulta
- ✅ Detecta automáticamente si tiene DTE
- ✅ Usa vistas específicas para DTE (`ticket-dte`, `ticket-minimal-dte`)
- ✅ Fallback a vistas normales si las específicas no existen

#### Método `genera_pdflocal()` Modificado:
- ✅ Detecta si tiene DTE basado en la presencia de `json` en la tabla DTE
- ✅ Usa vistas de PDF con DTE (`pdf.fac`, `pdf.crf`) cuando corresponde
- ✅ Usa vistas locales (`pdf.faclocal`, `pdf.crflocal`) cuando no tiene DTE

#### Nuevo Método `checkDte()`:
- ✅ Endpoint API para verificar si una venta tiene DTE
- ✅ Retorna información detallada del DTE si existe

#### Nuevo Método Helper `getPdfViewByType()`:
- ✅ Centraliza la lógica de selección de vistas PDF
- ✅ Maneja diferentes tipos de documentos (FAC, CRF, FEX, NCR)
- ✅ Considera si tiene DTE o no

### 3. Nuevas Vistas de Tickets con DTE

#### `resources/views/sales/ticket-dte.blade.php`
- ✅ Ticket completo con sección DTE destacada
- ✅ **Logo de la empresa** en el header
- ✅ Información del DTE: Código de Generación, Número de Control, Estado
- ✅ Fecha de recepción y sello de recepción
- ✅ Diseño optimizado para 80mm

#### `resources/views/sales/ticket-minimal-dte.blade.php`
- ✅ Versión minimal del ticket con DTE
- ✅ **Logo de la empresa** en el header (tamaño reducido)
- ✅ Información condensada del DTE
- ✅ Ideal para impresoras térmicas con espacio limitado

### 4. Rutas Agregadas

**Archivo:** `routes/web.php`

```php
Route::get('check-dte/{id}', [SaleController::class, 'checkDte'])->name('check-dte');
```

### 5. JavaScript Modificado

**Archivo:** `resources/views/sales/index.blade.php`

#### Función `imprimirTicketAutomatico()` Mejorada:
- ✅ Verifica si tiene DTE antes de imprimir
- ✅ Muestra notificaciones específicas para DTE
- ✅ Usa la ruta de ticket inteligente que detecta DTE automáticamente

## Funcionalidades Implementadas

### 🎫 Tickets Inteligentes
- **Detección Automática:** El sistema detecta automáticamente si un documento tiene DTE
- **Formatos Específicos:** Muestra información del DTE cuando está disponible
- **Fallback Seguro:** Si no hay DTE, usa el formato normal
- **Impresión Automática:** Mantiene la funcionalidad de impresión automática
- **Logo de Empresa:** Todos los tickets incluyen el logo de la empresa en el header

### 🖼️ Logos en Tickets
- **Logo Principal:** `public/assets/img/logo.png`
- **Posicionamiento:** Centrado en el header, antes del nombre de la empresa
- **Tamaños Optimizados:**
  - Tickets normales: 60x60px máximo
  - Tickets minimal: 50x50px máximo
- **Responsive:** Se adapta automáticamente al ancho del ticket (80mm)

### 📄 PDF Inteligentes
- **Vistas DTE:** Usa `pdf.fac`, `pdf.crf` cuando hay DTE
- **Vistas Locales:** Usa `pdf.faclocal`, `pdf.crflocal` cuando no hay DTE
- **Compatibilidad:** Mantiene compatibilidad con documentos existentes

### 🔍 API de Verificación
- **Endpoint:** `/sale/check-dte/{id}`
- **Respuesta JSON:** Incluye estado DTE e información detallada
- **Uso:** Para verificación programática de DTE

## Tipos de Documentos Soportados

| Tipo | Código | Con DTE | Sin DTE |
|------|--------|---------|---------|
| Factura | 01 | `pdf.fac` | `pdf.faclocal` |
| Comprobante de Retención | 03 | `pdf.crf` | `pdf.crflocal` |
| Factura de Exportación | 11 | `pdf.fex` | `pdf.fex` |
| Nota de Crédito | 05 | `pdf.ncr` | `pdf.ncr` |

## Información del DTE Mostrada

### En Tickets:
- ✅ Código de Generación
- ✅ Número de Control
- ✅ Estado del Documento
- ✅ Fecha de Recepción
- ✅ Sello de Recepción (truncado)

### En PDF:
- ✅ Toda la información del DTE según el formato específico
- ✅ Código QR cuando está disponible
- ✅ Información de validación fiscal

## Uso del Sistema

### Para Usuarios:
1. **Impresión Normal:** Los tickets se generan automáticamente con el formato correcto
2. **Verificación Visual:** Los tickets con DTE muestran claramente la información fiscal
3. **Compatibilidad:** Funciona con documentos existentes y nuevos

### Para Desarrolladores:
1. **API de Verificación:** Usar `/sale/check-dte/{id}` para verificar DTE
2. **Vistas Personalizadas:** Crear vistas específicas agregando `-dte` al nombre
3. **Extensibilidad:** Fácil agregar nuevos tipos de documentos

## Consideraciones Técnicas

### Base de Datos:
- ✅ No requiere cambios en la estructura de BD
- ✅ Usa relaciones existentes entre `sales` y `dte`
- ✅ Compatible con datos existentes

### Rendimiento:
- ✅ Consultas optimizadas con `with()` para relaciones
- ✅ Fallback seguro si las vistas no existen
- ✅ Caché de verificación DTE en JavaScript

### Seguridad:
- ✅ Validación de IDs de venta
- ✅ Manejo de errores robusto
- ✅ Logs para debugging

## Próximos Pasos Sugeridos

1. **Testing:** Probar con documentos reales con y sin DTE
2. **Optimización:** Considerar caché para verificaciones DTE frecuentes
3. **UI/UX:** Agregar indicadores visuales en la lista de ventas
4. **Reportes:** Incluir información DTE en reportes de ventas

## Archivos Modificados

- `app/Models/Sale.php`
- `app/Http/Controllers/SaleController.php`
- `routes/web.php`
- `resources/views/sales/index.blade.php`
- `resources/views/sales/ticket-dte.blade.php` (nuevo)
- `resources/views/sales/ticket-minimal-dte.blade.php` (nuevo)
- `resources/views/sales/ticket.blade.php` (actualizado con logo)
- `resources/views/sales/ticket-minimal.blade.php` (actualizado con logo)
- `resources/views/sales/ticket-direct.blade.php` (actualizado con logo)

## Archivos de PDF Existentes (No Modificados)

- `resources/views/pdf/fac.blade.php`
- `resources/views/pdf/crf.blade.php`
- `resources/views/pdf/fex.blade.php`
- `resources/views/pdf/ncr.blade.php`
- `resources/views/pdf/faclocal.blade.php`
- `resources/views/pdf/crflocal.blade.php`
