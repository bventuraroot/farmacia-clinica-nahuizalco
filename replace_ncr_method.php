<?php

// Script para reemplazar el método ncr en SaleController.php

$saleControllerPath = 'app/Http/Controllers/SaleController.php';
$tempNcrPath = 'temp_ncr_method.php';

// Leer el archivo original
$content = file_get_contents($saleControllerPath);

// Leer el nuevo método
$newMethod = file_get_contents($tempNcrPath);

// Encontrar el inicio del método ncr
$startPattern = '/public function ncr\(\$id_sale\)/';
$startMatch = preg_match($startPattern, $content, $matches, PREG_OFFSET_CAPTURE);

if (!$startMatch) {
    echo "No se encontró el método ncr\n";
    exit(1);
}

$startPos = $matches[0][1];

// Encontrar el final del método (próximo método público)
$remainingContent = substr($content, $startPos);
$endPattern = '/\n    public function [a-zA-Z_]+\(/';
$endMatch = preg_match($endPattern, $remainingContent, $endMatches, PREG_OFFSET_CAPTURE);

if (!$endMatch) {
    echo "No se encontró el final del método ncr\n";
    exit(1);
}

$endPos = $startPos + $endMatches[0][1];

// Reemplazar el método
$beforeMethod = substr($content, 0, $startPos);
$afterMethod = substr($content, $endPos);

$newContent = $beforeMethod . $newMethod . "\n" . $afterMethod;

// Escribir el archivo modificado
file_put_contents($saleControllerPath, $newContent);

echo "Método ncr reemplazado exitosamente\n";

// Limpiar archivos temporales
unlink($tempNcrPath);
unlink('replace_ncr_method.php');

echo "Archivos temporales eliminados\n";
