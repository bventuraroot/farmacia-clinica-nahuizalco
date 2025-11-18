# Corrección de Casts en Modelo Inventory

## Problema Identificado

Los decimales se estaban convirtiendo a 2 lugares porque en el modelo `Inventory.php` los campos `quantity` y `minimum_stock` estaban configurados como `integer` en lugar de `decimal:4`.

## Causa del Problema

En el archivo `app/Models/Inventory.php`, líneas 32-39, los casts estaban configurados así:

```php
// ANTES - PROBLEMÁTICO:
protected $casts = [
    'quantity' => 'integer',        // ❌ Convertía a entero
    'minimum_stock' => 'integer',   // ❌ Convertía a entero
    'price' => 'decimal:2',
    'active' => 'boolean'
];
```

### Resultado:
- Los valores `25.5000` se convertían a `25` (entero)
- Los valores `10.0000` se convertían a `10` (entero)
- Se perdían los decimales en la base de datos

## Solución Implementada

Se corrigieron los casts para usar `decimal:4`:

```php
// DESPUÉS - CORREGIDO:
protected $casts = [
    'quantity' => 'decimal:4',      // ✅ Mantiene 4 decimales
    'minimum_stock' => 'decimal:4', // ✅ Mantiene 4 decimales
    'base_quantity' => 'decimal:4', // ✅ Agregado para consistencia
    'base_unit_price' => 'decimal:4', // ✅ Agregado para consistencia
    'price' => 'decimal:2',         // ✅ Mantiene 2 decimales (precio)
    'active' => 'boolean'
];
```

## Campos Corregidos

### 1. `quantity`
- **Antes**: `integer` → `25.5000` se convertía a `25`
- **Después**: `decimal:4` → `25.5000` se mantiene como `25.5000`

### 2. `minimum_stock`
- **Antes**: `integer` → `10.0000` se convertía a `10`
- **Después**: `decimal:4` → `10.0000` se mantiene como `10.0000`

### 3. `base_quantity` (Agregado)
- **Antes**: Sin cast específico
- **Después**: `decimal:4` → Mantiene 4 decimales para cantidad base

### 4. `base_unit_price` (Agregado)
- **Antes**: Sin cast específico
- **Después**: `decimal:4` → Mantiene 4 decimales para precio base

## Flujo de Datos Corregido

### 1. Base de Datos
```
Tabla: inventory
- quantity: DECIMAL(12,4) → 25.5000
- minimum_stock: DECIMAL(12,4) → 10.0000
- base_quantity: DECIMAL(12,4) → 25.5000
- base_unit_price: DECIMAL(12,4) → 15.7500
```

### 2. Modelo Laravel
```php
// Ahora los casts mantienen los decimales:
$inventory->quantity;        // 25.5000 (no 25)
$inventory->minimum_stock;   // 10.0000 (no 10)
$inventory->base_quantity;   // 25.5000
$inventory->base_unit_price; // 15.7500
```

### 3. Frontend JavaScript
```javascript
// El JavaScript ya formatea correctamente:
parseFloat(data).toFixed(4); // 25.5000
```

## Resultado Visual

### Antes de la Corrección:
```
Cantidad: 25      (perdía los decimales)
Stock Mínimo: 10  (perdía los decimales)
```

### Después de la Corrección:
```
Cantidad: 25.5000    (mantiene 4 decimales)
Stock Mínimo: 10.0000 (mantiene 4 decimales)
```

## Archivos Modificados

### 1. Modelo
**Archivo**: `app/Models/Inventory.php`
- ✅ Líneas 32-39: Casts corregidos de `integer` a `decimal:4`
- ✅ Agregados casts para `base_quantity` y `base_unit_price`

### 2. JavaScript (Ya corregido anteriormente)
**Archivo**: `public/assets/js/app-inventory-list.js`
- ✅ Ya tenía el formateo con `toFixed(4)`

## Beneficios de la Corrección

### 1. Precisión Completa
- ✅ Los valores se mantienen exactos desde la BD hasta el frontend
- ✅ No se pierden decimales en ninguna parte del flujo
- ✅ Consistencia total con el sistema de compras (4 decimales)

### 2. Compatibilidad con Unidades Múltiples
- ✅ Los factores de conversión se manejan correctamente
- ✅ Las cantidades base se mantienen precisas
- ✅ Los precios base mantienen su precisión

### 3. Experiencia de Usuario Mejorada
- ✅ Visualización exacta de cantidades
- ✅ Mejor control de inventario
- ✅ Datos precisos para toma de decisiones

## Verificación

### 1. Base de Datos
- Verificar que los campos tengan tipo `DECIMAL(12,4)`
- Confirmar que los valores se almacenen con 4 decimales

### 2. Modelo Laravel
- Los casts `decimal:4` mantienen la precisión
- Los accessors/mutators funcionan correctamente

### 3. Frontend
- JavaScript muestra 4 decimales con `toFixed(4)`
- La tabla se actualiza correctamente

## Conclusión

El problema estaba en los casts del modelo `Inventory.php` que convertían los campos de cantidad a enteros. Al cambiar los casts a `decimal:4`, ahora el sistema mantiene la precisión completa de 4 decimales desde la base de datos hasta la interfaz de usuario, proporcionando una experiencia consistente y precisa para el control de inventario.
