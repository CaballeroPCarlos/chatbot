<?php
// Leer clave desde variable de entorno
$api_key = getenv("OPENROUTER_API_KEY");

// Leer cuerpo JSON del navegador
$body = file_get_contents('php://input');

// Crear contexto para solicitud POST
$options = [
  'http' => [
    'method'  => 'POST',
    'header'  => [
      "Content-Type: application/json",
      "Authorization: Bearer $api_key"
    ],
    'content' => $body,
    'ignore_errors' => true
  ]
];

$context  = stream_context_create($options);
$response = file_get_contents("https://openrouter.ai/api/v1/chat/completions", false, $context);

// Extraer código HTTP de la respuesta
$httpCode = 200;
if (isset($http_response_header)) {
  foreach ($http_response_header as $header) {
    if (preg_match('#HTTP/\d+\.\d+ (\d+)#', $header, $matches)) {
      $httpCode = intval($matches[1]);
      break;
    }
  }
}

http_response_code($httpCode);
header("Content-Type: application/json");
echo $response;
