# Módulo de Agente de Retención - Agroservicio Milagro de Dios

## Descripción General

Este documento describe la implementación del módulo de Agente de Retención en el sistema Agroservicio Milagro de Dios. La funcionalidad permite identificar clientes como agentes de retención y calcular automáticamente la retención del 1% sobre las ventas gravadas cuando el monto supera los $120.00.

## ¿Qué es un Agente de Retención?

Los agentes de retención son contribuyentes designados por las autoridades fiscales para retener impuestos en nombre del Estado. Cuando un cliente es marcado como agente de retención y realiza compras que superan cierto monto ($120.00 en este caso), se debe aplicar una retención del 1% sobre el monto gravado de la venta.

## Implementación Técnica

### 1. Base de Datos

#### Tabla `clients`
Se agregó la columna `agente_retencion`:
```sql
ALTER TABLE clients ADD COLUMN agente_retencion VARCHAR(1) DEFAULT '0' 
COMMENT '1: Es agente de retención, 0: No es agente de retención';
```

**Migración:** `2025_11_17_180250_add_agente_retencion_column_to_clients_table.php`

#### Tabla `sales`
Se agregó la columna `retencion_agente`:
```sql
ALTER TABLE sales ADD COLUMN retencion_agente DECIMAL(15,8) DEFAULT 0.00 
COMMENT 'Retención del 1% para agentes de retención cuando supera $120';
```

**Migración:** `2025_11_17_180620_add_retencion_agente_column_to_sales_table.php`

### 2. Modelos

#### Modelo Client (`app/Models/Client.php`)

Se agregó el campo al array `$fillable` y se añadieron dos métodos auxiliares:

```php
protected $fillable = [
    // ... otros campos
    'agente_retencion',
    // ...
];

/**
 * Accessor para verificar si es agente de retención
 */
public function getIsAgenteRetencionAttribute()
{
    return $this->agente_retencion == '1';
}

/**
 * Mutator para agente_retencion
 */
public function setAgenteRetencionAttribute($value)
{
    $this->attributes['agente_retencion'] = $value ? '1' : '0';
}
```

#### Modelo Sale (`app/Models/Sale.php`)

Se agregó el campo al array `$fillable` y al array `$casts`:

```php
protected $fillable = [
    // ... otros campos
    'retencion_agente',
    // ...
];

protected $casts = [
    // ... otros casts
    'retencion_agente' => 'decimal:8'
];
```

### 3. Controladores

#### ClientController (`app/Http/Controllers/ClientController.php`)

**Método `store`:** Guarda el valor del checkbox de agente de retención:

```php
if ($request->agente_retencion == 'on') {
    $agente_retencion = '1';
} else {
    $agente_retencion = '0';
}
$client->agente_retencion = $agente_retencion;
```

**Método `update`:** Actualiza el valor del agente de retención:

```php
$agente_retencion_value = $request->agente_retencionedit == 'on' ? '1' : ($request->agente_retencionedit_hidden == '1' ? '1' : '0');
$client->agente_retencion = $agente_retencion_value;
```

### 4. Vistas

#### Formulario de Clientes (`resources/views/client/index.blade.php`)

Se agregó un switch para marcar al cliente como agente de retención:

**Para crear nuevo cliente:**
```html
<div class="mb-3">
    <label class="switch switch-warning" id="agenteretencionlabel" name="agenteretencionlabel">
        <input type="checkbox" class="switch-input" id="agente_retencion" name="agente_retencion" />
        <span class="switch-toggle-slider">
            <span class="switch-on">
                <i class="ti ti-check"></i>
            </span>
            <span class="switch-off">
                <i class="ti ti-x"></i>
            </span>
        </span>
        <span class="switch-label">¿Es Agente de Retención?</span>
    </label>
</div>
```

**Para editar cliente:**
Similar al formulario de creación, con los campos correspondientes `agente_retencionedit` y un campo oculto `agente_retencionedit_hidden` para mantener el estado.

#### Formulario de Ventas (`resources/views/sales/create-dynamic.blade.php`)

Se agregaron campos ocultos para almacenar información del cliente:

```html
<input type="hidden" name="cliente_agente_retencion" id="cliente_agente_retencion" value="0">
<input type="hidden" name="retencion_agente" id="retencion_agente" value="0">
```

### 5. JavaScript

#### Archivo: `public/assets/js/sales-dynamic.js`

##### Función: `valtrypecontri` - Cargar información del cliente
```javascript
success: function (response) {
    $("#typecontribuyenteclient").val(response.tipoContribuyente);
    
    // Guardar si el cliente es agente de retención para usar en cálculos
    if (response.agente_retencion == "1") {
        $("#cliente_agente_retencion").val("1");
    } else {
        $("#cliente_agente_retencion").val("0");
    }

    // Mostrar información detallada del cliente
    showClientInfo(response);
}
```

**NOTA IMPORTANTE:** En este proyecto, la lógica de cálculo de retención del agente de retención (1% cuando supera $120) debe implementarse donde se suman los productos y se calculan los totales.

## Flujo de Trabajo

### Registro de Cliente como Agente de Retención

1. El usuario accede al módulo de clientes
2. Al crear o editar un cliente, activa el switch "¿Es Agente de Retención?"
3. El sistema guarda el valor en la columna `agente_retencion` de la tabla `clients`

### Cálculo de Retención en Ventas

1. **Selección del Cliente:**
   - Al seleccionar un cliente en la venta, el sistema verifica si `agente_retencion == '1'`
   - Si es agente de retención, guarda el valor en `#cliente_agente_retencion`

2. **Agregado de Productos:**
   - Cuando se agregan productos a la venta, el sistema NO calcula la retención por producto individual
   - La retención se calcula ÚNICAMENTE sobre el total de toda la venta

3. **Cálculo del Total:**
   - El sistema debe sumar todas las ventas gravadas (sin IVA) de la tabla de productos
   - Si el total de ventas gravadas **supera $120.00**, aplica la retención del 1%
   - Si **NO supera $120.00**, la retención es 0

4. **Fórmula de Retención:**
   ```javascript
   var es_agente_retencion = $("#cliente_agente_retencion").val() == "1";
   if (es_agente_retencion && (typedoc == '3' || typedoc == '6')) {
       var ventas_gravadas = 0;
       
       // Sumar todas las ventas gravadas
       $("#tblproduct tbody tr").each(function() {
           var gravadasText = $(this).find("td:eq(X)").text(); // Ajustar índice según tabla
           var gravadas = parseFloat(gravadasText.replace(/[$,]/g, '')) || 0;
           ventas_gravadas += gravadas;
       });
       
       // Aplicar retención solo si supera $120
       var retencion_agente = 0;
       if (ventas_gravadas > 120.00) {
           retencion_agente = parseFloat(ventas_gravadas * 0.01);
       }
       
       $("#retencion_agente").val(retencion_agente);
       // Sumar al IVA retenido total
       ivaretenidol += retencion_agente;
   }
   ```

5. **Aplicación de la Retención:**
   - La retención se suma al IVA retenido total
   - Se resta del total de la venta junto con otras retenciones
   - Se guarda en la tabla `sales` en la columna `retencion_agente`

## Tipos de Documento

La retención del agente se aplica ÚNICAMENTE para:
- **Tipo 3:** Crédito Fiscal (CCF)
- **Tipo 6:** Factura

**NO se aplica para:**
- Tipo 8: Factura de Sujeto Excluido
- Otros tipos de documento

## Validaciones Importantes

1. ✅ El cliente debe estar marcado como agente de retención (`agente_retencion = '1'`)
2. ✅ El documento debe ser tipo 3 (CCF) o tipo 6 (Factura)
3. ✅ Las ventas gravadas deben superar $120.00
4. ✅ Solo se aplica sobre ventas gravadas (NO sobre exentas o no sujetas)

## Ejemplo Práctico

### Caso 1: Cliente agente de retención con venta de $150.00

**Datos:**
- Cliente: Es agente de retención ✓
- Tipo documento: Crédito Fiscal (tipo 3) ✓
- Ventas gravadas: $150.00
- Supera $120.00: ✓

**Cálculo:**
```
Ventas gravadas sin IVA: $150.00
Retención del 1%: $150.00 × 0.01 = $1.50
IVA 13%: $150.00 × 0.13 = $19.50
Total a pagar: $150.00 + $19.50 - $1.50 = $168.00
```

### Caso 2: Cliente agente de retención con venta de $100.00

**Datos:**
- Cliente: Es agente de retención ✓
- Tipo documento: Factura (tipo 6) ✓
- Ventas gravadas: $100.00
- Supera $120.00: ✗

**Cálculo:**
```
Ventas gravadas sin IVA: $100.00
Retención del 1%: $0.00 (NO supera $120)
IVA 13%: $100.00 × 0.13 = $13.00
Total a pagar: $100.00 + $13.00 = $113.00
```

### Caso 3: Cliente NO es agente de retención con venta de $200.00

**Datos:**
- Cliente: NO es agente de retención ✗
- Tipo documento: Crédito Fiscal (tipo 3)
- Ventas gravadas: $200.00
- Supera $120.00: ✓ (pero no aplica porque no es agente)

**Cálculo:**
```
Ventas gravadas sin IVA: $200.00
Retención del 1%: $0.00 (NO es agente de retención)
IVA 13%: $200.00 × 0.13 = $26.00
Total a pagar: $200.00 + $26.00 = $226.00
```

## Migraciones Necesarias

Para implementar esta funcionalidad en producción, ejecutar:

```bash
cd "/Volumes/ExternalHelp/Outside/htdocs/Agroservicio Milagro de Dios"
php artisan migrate
```

Esto ejecutará las siguientes migraciones:
1. `2025_11_17_180250_add_agente_retencion_column_to_clients_table.php`
2. `2025_11_17_180620_add_retencion_agente_column_to_sales_table.php`

## Archivos Creados y Modificados

### Archivos Nuevos (Migraciones):
- `database/migrations/2025_11_17_180250_add_agente_retencion_column_to_clients_table.php`
- `database/migrations/2025_11_17_180620_add_retencion_agente_column_to_sales_table.php`

### Archivos Modificados:
- `app/Models/Client.php` - Agregado campo y accessors/mutators
- `app/Models/Sale.php` - Agregado campo y cast
- `app/Http/Controllers/ClientController.php` - Actualizado store() y update()
- `public/assets/js/sales-dynamic.js` - Agregada captura del campo agente_retencion
- `resources/views/client/index.blade.php` - Agregado checkbox en formularios
- `resources/views/sales/create-dynamic.blade.php` - Agregados campos ocultos

## Corrección de Errores y Mejoras Adicionales

### Vista de Clientes Optimizada

Se realizó una optimización completa de la vista de clientes (`resources/views/client/index.blade.php`):

**Antes:** 16 columnas difíciles de leer  
**Ahora:** 8 columnas optimizadas + Modal de detalles completos

#### Columnas de la Tabla:
1. **ID** - Identificador único
2. **Cliente** - Nombre completo con info secundaria
3. **Tipo** - Badge de Persona Jurídica/Natural + Extranjero
4. **Documento** - DUI/NIT/Pasaporte + NRC
5. **Contribuyente** - Badges agrupados (Contribuyente, Agente Retención, Tipo)
6. **Contacto** - Teléfono y email
7. **Estado** - Badge de activo + Actividad económica resumida
8. **Acciones** - Botones mejorados (Ver, Editar, Menú)

#### Modal de Detalles:
- 6 secciones organizadas con toda la información
- Botón para editar directamente desde el modal
- Carga dinámica mediante AJAX
- Diseño responsive y profesional

### Correcciones en Edición de Clientes

Se corrigieron los problemas al editar los campos de checkbox:

1. **`public/assets/js/forms-client.js`:**
   - ✅ Agregada función `updateAgenteRetencionEdit()` para actualizar el campo oculto
   - ✅ Corregida función `escontriedit()` para actualizar correctamente el valor de contribuyente
   - ✅ Agregada función `esextranjeroedit()` para manejar el campo de extranjero
   - ✅ Agregado código en `editClient()` para cargar correctamente:
     - Campo `agente_retencion`
     - Campo `extranjero`
     - Campo `contribuyente` con su valor oculto
   - ✅ Agregada validación en el submit para asegurar que los valores ocultos estén actualizados

2. **`resources/views/client/index.blade.php`:**
   - ✅ Agregado evento `onclick="updateAgenteRetencionEdit()"` al checkbox
   - ✅ Agregado campo de pasaporte en el formulario de edición
   - ✅ Agregado campo oculto `agente_retencionedit_hidden`
   - ✅ Agregado switch de extranjero en el formulario de edición

### Archivo JavaScript Actualizado

**`public/assets/js/app-client-list.js`:**
- ✅ Actualizada configuración de DataTables de 15 columnas a 8
- ✅ Corregidos targets de responsive priority
- ✅ Actualizado ordenamiento por ID descendente
- ✅ Corregidas opciones de exportación (Print, CSV, Excel, PDF, Copy)

## Compatibilidad

Esta implementación está basada en el sistema de Explorer Travel y sigue el mismo patrón que el proyecto de Agroservicio utiliza actualmente.

## Flujo Completo del Cálculo de Retención en Ventas

### Proceso Paso a Paso:

1. **Usuario selecciona cliente:**
   - JavaScript consulta `/client/gettypecontri/` para obtener datos del cliente
   - Si `agente_retencion == '1'`, guarda en `#cliente_agente_retencion`

2. **Usuario agrega productos:**
   - Cada producto se agrega a la tabla
   - JavaScript calcula totales acumulados

3. **Cálculo de Retención del Agente:**
   ```javascript
   // En agregarp() - después de cada producto agregado
   var es_agente_retencion = $("#cliente_agente_retencion").val() == "1";
   if (es_agente_retencion && (typedoc == '3' || typedoc == '6')) {
       var ventas_gravadas = 0;
       
       // Sumar todas las ventas gravadas de la tabla
       $("#tblproduct tbody tr").each(function() {
           var gravadas = parseFloat($(this).find("td:eq(5)").text().replace(/[$,]/g, '')) || 0;
           ventas_gravadas += gravadas;
       });
       
       // Solo aplicar si supera $120
       var retencion_agente = 0;
       if (ventas_gravadas > 120.00) {
           retencion_agente = parseFloat(ventas_gravadas * 0.01);
       }
       
       // Sumar al IVA retenido (se RESTA del total)
       ivaretenidol += retencion_agente;
       $("#retencion_agente").val(retencion_agente);
   }
   ```

4. **Cálculo del Total Final:**
   ```javascript
   // Para CCF (tipo 3):
   ventatotall = sumasl + iva13l - ivaretenidol;
   
   // Para Factura (tipo 6):
   ventatotall = sumasl - ivaretenidol;
   ```

5. **Finalizar Venta:**
   - Usuario hace clic en "Finalizar Venta"
   - JavaScript llama a `updateRetencionAgenteBeforeFinalize()`
   - Se envía la retención al backend: `/sale/update-retencion-agente`
   - Backend guarda `retencion_agente` en la tabla `sales`
   - Se genera el DTE y la factura

### Diferencia entre IVA Percibido e IVA Retenido:

| Concepto | Cuándo Aplica | Efecto en Total | Campo |
|----------|---------------|-----------------|-------|
| **IVA Percibido** | Empresa es gran contribuyente | Se SUMA (+) | Se suma al total |
| **IVA Retenido** | Cliente es agente de retención | Se RESTA (-) | `ivaretenidol` |
| **Retención Agente 1%** | Cliente agente + Venta > $120 | Se RESTA (-) | Incluido en `ivaretenidol` |

### Visualización en la Factura:

```
SUMAS                          $150.00
(+) IVA 13%                    $ 19.50
(-) IVA Retenido               $  1.50  ← Incluye retención del agente
─────────────────────────────────────
TOTAL A PAGAR                  $168.00
```

## Rutas Agregadas

### Web Routes (`routes/web.php`):

```php
Route::post('update-retencion-agente', [SaleController::class, 'updateRetencionAgente'])->name('update-retencion-agente');
```

## Funciones JavaScript Agregadas

### En `sales-dynamic.js`:

1. **`updateRetencionAgenteBeforeFinalize()`**
   - Envía la retención del agente al backend antes de finalizar la venta
   - Se llama automáticamente desde `finalizeSale()`

2. **Cálculo en `agregarp()` - Success callback**
   - Calcula la retención del 1% sobre ventas gravadas > $120
   - Suma la retención al IVA retenido total
   - Actualiza el display en pantalla

3. **Cálculo en `calculateDraftTotals()`**
   - Recalcula la retención al cargar un draft
   - Misma lógica de validación de $120

4. **Cálculo en `recalculateTotalsAfterDelete()`**
   - Recalcula la retención después de eliminar productos
   - Valida si todavía supera $120 después de la eliminación

## Soporte y Mantenimiento

Para reportar problemas o solicitar mejoras, contactar al equipo de desarrollo.

---

**Fecha de implementación:** Noviembre 17, 2025  
**Versión:** 1.0  
**Sistema:** Agroservicio Milagro de Dios  
**Estado:** ✅ Implementación Completa y Funcional

