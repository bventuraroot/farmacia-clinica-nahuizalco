# Sistema de Unidades en Compras e Inventario

## Descripción

El sistema de compras ahora incluye la funcionalidad completa de **selección de unidades de medida** para determinar cómo se agregarán los productos al inventario.

## Estructura de la Tabla de Compras (8 Columnas)

### Vista Actual
```
1. Producto         - Nombre del producto a comprar
2. Unidad          - Unidad de medida para agregar al inventario
3. Cantidad        - Cantidad a comprar en la unidad seleccionada
4. Costo Unitario  - Precio de compra por unidad
5. Subtotal        - Cantidad × Costo Unitario
6. Fecha Caducidad - Fecha de vencimiento del producto
7. Lote            - Número de lote (opcional)
8. Acciones        - Eliminar producto
```

## Flujo de Trabajo

### 1. Selección de Producto
Cuando se selecciona un producto:
- Se carga el nombre del producto
- Se cargan automáticamente las unidades disponibles para ese producto
- Se muestra un selector con las unidades configuradas

### 2. Selección de Unidad
El usuario puede elegir la unidad en la que desea registrar la compra:
- **Unidad** (59) - Por pieza
- **Libra** (36) - Por peso
- **Litro** (23) - Por volumen
- Otras unidades configuradas en el producto

### 3. Registro de Cantidad y Precio
- **Cantidad**: En la unidad seleccionada
- **Costo Unitario**: Precio de compra (con 4 decimales para mayor precisión)
- **Subtotal**: Se calcula automáticamente

### 4. Agregado al Inventario
Al guardar la compra:
```php
// Los datos enviados al servidor incluyen:
{
    product_id: 123,
    quantity: 10,
    unit_code: "36",           // Código de unidad (Libra)
    unit_id: 2,                 // ID de la unidad
    conversion_factor: 1.0000,  // Factor de conversión
    unit_price: 2.5000,         // Precio con 4 decimales
    expiration_date: "2025-12-31",
    batch_number: "LOT-2025-001"
}
```

### 5. Cálculo en Inventario
El servicio `PurchaseInventoryService` procesa la compra:
```php
// Calcula la cantidad en unidad base
$baseQuantity = $this->calculateBaseQuantity($detail, $product);

// Actualiza o crea el registro de inventario
Inventory::create([
    'product_id' => $product->id,
    'quantity' => $detail->quantity,        // Cantidad en unidad de compra
    'base_unit_id' => $baseUnitId,
    'base_quantity' => $baseQuantity,       // Cantidad convertida a unidad base
    'expiration_date' => $detail->expiration_date,
    'batch_number' => $detail->batch_number,
    // ...
]);
```

## Campos de la Tabla

### Producto
- **Tipo**: Input readonly
- **Función**: Muestra el producto seleccionado
- **Acción**: Click para cambiar producto

### Unidad
- **Tipo**: Select dinámico
- **Función**: Seleccionar unidad de medida para el inventario
- **Carga**: Automática desde las unidades del producto
- **Datos almacenados**:
  - `unit_code`: Código de la unidad (36, 23, 59, etc.)
  - `unit_id`: ID de la unidad en la base de datos
  - `conversion_factor`: Factor de conversión a unidad base

### Cantidad
- **Tipo**: Number input
- **Función**: Cantidad a comprar
- **Validación**: Mínimo 1
- **Relación**: Se multiplica con el precio para el subtotal

### Costo Unitario
- **Tipo**: Number input (4 decimales)
- **Función**: Precio de compra por unidad
- **Precisión**: `step="0.0001"`
- **Nota**: Es el costo de compra, NO el precio de venta

### Subtotal
- **Tipo**: Span (solo lectura)
- **Cálculo**: `Cantidad × Costo Unitario`
- **Formato**: Con 4 decimales ($0.0000)

### Fecha Caducidad
- **Tipo**: Date input
- **Función**: Fecha de vencimiento del producto
- **Uso**: Para control de inventario y productos próximos a vencer

### Lote
- **Tipo**: Text input
- **Función**: Número de lote del producto
- **Estado**: Opcional
- **Generación**: Automática si no se proporciona

### Acciones
- **Tipo**: Button
- **Función**: Eliminar producto de la compra

## Integración con Inventario

### Proceso de Agregado
1. Usuario selecciona producto y unidad
2. Ingresa cantidad y precio
3. Al guardar, el sistema envía la información completa
4. `PurchaseController` procesa los datos con 4 decimales
5. `PurchaseInventoryService` agrega al inventario
6. Se calcula la cantidad en unidad base
7. Se actualiza el registro de inventario

### Datos Almacenados en `purchase_details`
```sql
CREATE TABLE purchase_details (
    id BIGINT PRIMARY KEY,
    purchase_id BIGINT,
    product_id BIGINT,
    quantity INT,                    -- Cantidad en unidad de compra
    unit_price DECIMAL(12,4),        -- Precio con 4 decimales
    unit_code VARCHAR(10),           -- Código de unidad
    unit_id BIGINT,                  -- ID de unidad
    conversion_factor DECIMAL(10,4), -- Factor de conversión
    subtotal DECIMAL(12,4),
    tax_amount DECIMAL(12,4),
    total_amount DECIMAL(12,4),
    expiration_date DATE,
    batch_number VARCHAR(255),
    -- ...
);
```

### Datos Almacenados en `inventory`
```sql
CREATE TABLE inventory (
    id BIGINT PRIMARY KEY,
    product_id BIGINT,
    quantity DECIMAL(10,2),          -- Cantidad en unidad de compra
    base_unit_id BIGINT,             -- Unidad base del producto
    base_quantity DECIMAL(10,4),     -- Cantidad en unidad base
    expiration_date DATE,
    batch_number VARCHAR(255),
    -- ...
);
```

## Cálculo de Utilidad

El sistema compara automáticamente:
- **Costo de Compra**: Ingresado en el formulario
- **Precio de Venta**: Registrado en el catálogo del producto
- **Utilidad**: `Precio Venta - Costo Compra`
- **Margen**: `(Utilidad / Precio Venta) × 100`

## Ejemplo de Uso

### Compra de 10 libras de CONCEI a $2.50/lb
```
1. Producto:        CONCEI
2. Unidad:          Libra (36)
3. Cantidad:        10
4. Costo Unitario:  $2.5000
5. Subtotal:        $25.0000
6. Fecha Caducidad: 2025-12-31
7. Lote:            LOT-2025-001
```

### Resultado en Inventario
```php
Inventory {
    product_id: 123,
    quantity: 10,              // 10 libras
    base_unit_id: 2,          // ID de Libra
    base_quantity: 10.0000,   // 10 libras en unidad base
    expiration_date: "2025-12-31",
    batch_number: "LOT-2025-001"
}
```

## Funciones JavaScript Clave

### `loadProductUnits(rowIndex, productId)`
Carga las unidades disponibles para un producto específico.

### `updateSelectedProduct(rowIndex)`
Actualiza el objeto `selectedProducts` con la información de unidad seleccionada.

### `calculateBaseQuantity(detail, product)`
Calcula la cantidad en unidad base usando el factor de conversión.

### `isIndexViewStructure()`
Detecta si la vista tiene 8 columnas (modal) o 10 columnas (página completa).

## Consideraciones Importantes

### Precisión Decimal
- Todos los cálculos usan 4 decimales
- Permite mayor exactitud en precios fraccionarios
- Reduce errores de redondeo

### Conversión de Unidades
- El factor de conversión se aplica automáticamente
- La unidad base se determina según el tipo de producto
- El inventario mantiene ambas cantidades (compra y base)

### Trazabilidad
- Cada compra registra la unidad utilizada
- Se mantiene historial de lotes y fechas
- Facilita auditorías y control de calidad

## Archivos Relacionados

- **Vista**: `resources/views/purchases/index.blade.php`
- **JavaScript**: `public/assets/js/app-purchase-list.js`
- **Controlador**: `app/Http/Controllers/PurchaseController.php`
- **Servicio**: `app/Services/PurchaseInventoryService.php`
- **Modelo**: `app/Models/PurchaseDetail.php`
