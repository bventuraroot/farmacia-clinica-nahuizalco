-- Script para actualizar la precisión decimal en la tabla inventory
-- Ejecutar este script en la base de datos cuando sea posible

-- Actualizar tabla inventory para usar 4 decimales
ALTER TABLE inventory
MODIFY COLUMN quantity DECIMAL(12,4) DEFAULT 0.0000,
MODIFY COLUMN minimum_stock DECIMAL(12,4) DEFAULT 0.0000;

-- Verificar los cambios
SELECT
    COLUMN_NAME,
    DATA_TYPE,
    NUMERIC_PRECISION,
    NUMERIC_SCALE,
    COLUMN_DEFAULT
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'inventory'
    AND COLUMN_NAME IN ('quantity', 'minimum_stock', 'base_quantity')
ORDER BY COLUMN_NAME;
