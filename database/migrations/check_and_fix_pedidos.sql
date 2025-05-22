-- Verificar se a tabela existe e sua estrutura
SELECT 
    COLUMN_NAME, 
    DATA_TYPE, 
    IS_NULLABLE, 
    COLUMN_DEFAULT
FROM 
    INFORMATION_SCHEMA.COLUMNS 
WHERE 
    TABLE_SCHEMA = 'erp' 
    AND TABLE_NAME = 'pedidos'; 