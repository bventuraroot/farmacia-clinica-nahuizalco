# Corrección: Usar base_quantity en lugar de quantity

## Problema Identificado

Aunque la base de datos ya tiene los decimales correctos en la columna `base_quantity`, el controlador estaba devolviendo la columna `quantity` (que es entero) en lugar de `base_quantity` (que tiene 4 decimales).

## Evidencia del Problema

En la imagen de la base de datos se puede ver:
- **`quantity`**: Valores enteros (`447`, `19`, `100`)
- **`base_quantity`**: Valores con 4 decimales (`446.5000`, `18.9495`, `100.0000`)

El controlador estaba usando `quantity` (entero) en lugar de `base_quantity` (decimal).

## Solución Implementada

### 1. Controlador - InventoryController.php
**Línea 379-381:**
```php
// ANTES - Usaba quantity (entero):
->addColumn('quantity', function($item) {
    return $item->quantity;  // ❌ 447 (entero)
})

// DESPUÉS - Usa base_quantity (decimal):
->addColumn('quantity', function($item) {
    return $item->base_quantity ?? $item->quantity;  // ✅ 446.5000 (decimal)
})
```

### 2. JavaScript - Modal de Edición
**Línea 350:**
```javascript
// ANTES - Usaba quantity:
$('#edit_current_stock').text(parseFloat(response.inventory.quantity).toFixed(4));

// DESPUÉS - Usa base_quantity:
$('#edit_current_stock').text(parseFloat(response.inventory.base_quantity || response.inventory.quantity).toFixed(4));
```

## Lógica de Fallback

Se usa `base_quantity ?? $item->quantity` para:
- **Prioridad 1**: Usar `base_quantity` si existe (valores decimales)
- **Prioridad 2**: Usar `quantity` como fallback si `base_quantity` es null

## Resultado Esperado

### Antes:
```
Cantidad: 447.0000  (basado en quantity entero)
```

### Después:
```
Cantidad: 446.5000  (basado en base_quantity decimal)
```

## Archivos Modificados

### 1. Controlador
**Archivo**: `app/Http/Controllers/InventoryController.php`
- ✅ Línea 380: Usa `base_quantity ?? $item->quantity`

### 2. JavaScript
**Archivo**: `public/assets/js/app-inventory-list.js`
- ✅ Línea 350: Usa `base_quantity || response.inventory.quantity`

## Beneficios

### 1. Precisión Correcta
- ✅ Muestra los valores decimales reales de la base de datos
- ✅ Usa `base_quantity` que tiene 4 decimales
- ✅ Fallback a `quantity` si `base_quantity` es null

### 2. Consistencia
- ✅ Los valores mostrados coinciden con los de la base de datos
- ✅ No hay redondeo artificial
- ✅ Mantiene la precisión de 4 decimales

### 3. Compatibilidad
- ✅ Funciona con inventarios que tienen `base_quantity`
- ✅ Funciona con inventarios que solo tienen `quantity` (fallback)
- ✅ No rompe funcionalidad existente

## Conclusión

El problema no era la estructura de la base de datos (que ya tenía los decimales correctos), sino que el controlador estaba devolviendo la columna incorrecta. Al cambiar para usar `base_quantity` en lugar de `quantity`, ahora el sistema mostrará los valores decimales exactos que están almacenados en la base de datos.
