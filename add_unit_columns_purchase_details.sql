-- Script para agregar columnas de unidad a la tabla purchase_details
-- Ejecutar este script en la base de datos cuando sea posible

-- Agregar columnas de unidad a purchase_details
ALTER TABLE purchase_details
ADD COLUMN unit_code VARCHAR(255) NULL AFTER quantity,
ADD COLUMN unit_id BIGINT UNSIGNED NULL AFTER unit_code,
ADD COLUMN conversion_factor DECIMAL(12,4) DEFAULT 1.0000 AFTER unit_id;

-- Agregar índices para mejorar el rendimiento
ALTER TABLE purchase_details
ADD INDEX idx_unit_id (unit_id);

-- Verificar que las columnas se agregaron correctamente
DESCRIBE purchase_details;

-- Mostrar la estructura actualizada
SELECT
    COLUMN_NAME,
    DATA_TYPE,
    IS_NULLABLE,
    COLUMN_DEFAULT,
    COLUMN_COMMENT
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'purchase_details'
    AND COLUMN_NAME IN ('unit_code', 'unit_id', 'conversion_factor')
ORDER BY ORDINAL_POSITION;
