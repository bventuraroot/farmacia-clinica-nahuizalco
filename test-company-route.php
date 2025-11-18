<?php
// Test simple para verificar la ruta de empresas
echo "=== Test de Ruta de Empresas ===\n";

// Simular una petición HTTP a la ruta
$url = 'http://localhost:8001/company/getcompanies';
echo "Probando URL: $url\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);

curl_close($ch);

echo "Código HTTP: $httpCode\n";
if ($error) {
    echo "Error cURL: $error\n";
} else {
    echo "Respuesta: $response\n";
}

echo "\n=== Fin del Test ===\n";
?>


