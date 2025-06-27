<?php
// Leer la clave secreta desde las variables de entorno
$api_key = getenv("DEEPSEEK_API_KEY");

// Leer el cuerpo enviado por el navegador
$body = file_get_contents('php://input');

// Preparar contexto HTTP para enviar solicitud POST
$options = [
  'http' => [
    'method'  => 'POST',
    'header'  => [
      "Content-Type: application/json",
      "Authorization: Bearer $api_key"
    ],
    'content' => $body,
    'ignore_errors' => true // Captura errores HTTP también
  ]
];

$context  = stream_context_create($options);
$response = file_get_contents("https://api.deepseek.com/v1/chat/completions", false, $context);

// Obtener el código HTTP devuelto
$httpCode = 200;
if (isset($http_response_header)) {
  foreach ($http_response_header as $header) {
    if (preg_match('#HTTP/\d+\.\d+ (\d+)#', $header, $matches)) {
      $httpCode = intval($matches[1]);
      break;
    }
  }
}

// Devolver la respuesta al navegador
http_response_code($httpCode);
header("Content-Type: application/json");
echo $response;
