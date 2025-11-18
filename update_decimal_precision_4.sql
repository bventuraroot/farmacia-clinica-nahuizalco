-- Script para actualizar la precisión decimal a 4 decimales en el sistema de compras
-- Ejecutar este script en la base de datos cuando sea posible

-- Actualizar tabla purchase_details
ALTER TABLE purchase_details
MODIFY COLUMN unit_price DECIMAL(12,4),
MODIFY COLUMN subtotal DECIMAL(12,4),
MODIFY COLUMN tax_amount DECIMAL(12,4),
MODIFY COLUMN total_amount DECIMAL(12,4);

-- Actualizar tabla purchases
ALTER TABLE purchases
MODIFY COLUMN exenta DECIMAL(12,4),
MODIFY COLUMN gravada DECIMAL(12,4),
MODIFY COLUMN iva DECIMAL(12,4),
MODIFY COLUMN contrns DECIMAL(12,4),
MODIFY COLUMN fovial DECIMAL(12,4),
MODIFY COLUMN iretenido DECIMAL(12,4),
MODIFY COLUMN otros DECIMAL(12,4),
MODIFY COLUMN total DECIMAL(12,4);

-- Verificar los cambios
SELECT
    TABLE_NAME,
    COLUMN_NAME,
    DATA_TYPE,
    NUMERIC_PRECISION,
    NUMERIC_SCALE
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME IN ('purchases', 'purchase_details')
    AND DATA_TYPE = 'decimal'
ORDER BY TABLE_NAME, COLUMN_NAME;
