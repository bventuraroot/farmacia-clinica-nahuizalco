-- Script para actualizar la precisión decimal a 8 decimales
-- Ejecutar este script directamente en la base de datos si las migraciones fallan

-- 1. Primero, limpiar y ajustar los datos existentes en salesdetails
UPDATE salesdetails
SET
    pricesale = CASE
        WHEN pricesale > 99.99999999 THEN 99.99999999
        WHEN pricesale < -99.99999999 THEN -99.99999999
        ELSE pricesale
    END,
    priceunit = CASE
        WHEN priceunit > 99.99999999 THEN 99.99999999
        WHEN priceunit < -99.99999999 THEN -99.99999999
        ELSE priceunit
    END,
    nosujeta = CASE
        WHEN nosujeta > 99.99999999 THEN 99.99999999
        WHEN nosujeta < -99.99999999 THEN -99.99999999
        ELSE nosujeta
    END,
    exempt = CASE
        WHEN exempt > 99.99999999 THEN 99.99999999
        WHEN exempt < -99.99999999 THEN -99.99999999
        ELSE exempt
    END,
    detained = CASE
        WHEN detained IS NOT NULL THEN CASE
            WHEN detained > 99.99999999 THEN 99.99999999
            WHEN detained < -99.99999999 THEN -99.99999999
            ELSE detained
        END
        ELSE NULL
    END,
    detained13 = CASE
        WHEN detained13 > 99.99999999 THEN 99.99999999
        WHEN detained13 < -99.99999999 THEN -99.99999999
        ELSE detained13
    END,
    renta = CASE
        WHEN renta > 99.99999999 THEN 99.99999999
        WHEN renta < -99.99999999 THEN -99.99999999
        ELSE renta
    END,
    fee = CASE
        WHEN fee > 99.99999999 THEN 99.99999999
        WHEN fee < -99.99999999 THEN -99.99999999
        ELSE fee
    END,
    feeiva = CASE
        WHEN feeiva > 99.99999999 THEN 99.99999999
        WHEN feeiva < -99.99999999 THEN -99.99999999
        ELSE feeiva
    END;

-- 2. Limpiar datos en la tabla sales
UPDATE sales
SET
    totalamount = CASE
        WHEN totalamount > 99.99999999 THEN 99.99999999
        WHEN totalamount < -99.99999999 THEN -99.99999999
        ELSE totalamount
    END
WHERE totalamount IS NOT NULL;

-- 3. Cambiar los tipos de columna en salesdetails
ALTER TABLE salesdetails
MODIFY COLUMN pricesale decimal(10,8) NOT NULL,
MODIFY COLUMN priceunit decimal(10,8) NOT NULL,
MODIFY COLUMN nosujeta decimal(10,8) NOT NULL,
MODIFY COLUMN exempt decimal(10,8) NOT NULL,
MODIFY COLUMN detained decimal(10,8) NULL,
MODIFY COLUMN detained13 decimal(10,8) NOT NULL,
MODIFY COLUMN renta decimal(10,8) NOT NULL,
MODIFY COLUMN fee decimal(10,8) NOT NULL,
MODIFY COLUMN feeiva decimal(10,8) NOT NULL;

-- 4. Cambiar el tipo de columna en sales
ALTER TABLE sales
MODIFY COLUMN totalamount decimal(10,8) NULL;

-- 5. Verificar los cambios
SELECT
    COLUMN_NAME,
    DATA_TYPE,
    NUMERIC_PRECISION,
    NUMERIC_SCALE
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_NAME IN ('salesdetails', 'sales')
AND COLUMN_NAME IN ('pricesale', 'priceunit', 'nosujeta', 'exempt', 'detained', 'detained13', 'renta', 'fee', 'feeiva', 'totalamount')
ORDER BY TABLE_NAME, COLUMN_NAME;
