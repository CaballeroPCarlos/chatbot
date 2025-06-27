<?php
// Leer JSON del cuerpo de la solicitud
$body = file_get_contents('php://input');

// Lista de claves desde variables de entorno
$api_keys = [
  getenv('API_KEY_1'),
  getenv('API_KEY_2'),
  getenv('API_KEY_3'),
  getenv('API_KEY_4'),
  getenv('API_KEY_5'),
  getenv('API_KEY_6'),
];

$response = null;
$httpcode = 500;

// Probar cada clave hasta que una funcione
foreach ($api_keys as $key) {
  if (!$key) continue;

  $ch = curl_init("https://openrouter.ai/api/v1/chat/completions");
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_POST, true);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
  curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json",
    "Authorization: Bearer $key"
  ]);

  $response = curl_exec($ch);
  $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  $curl_error = curl_error($ch);
  curl_close($ch);

  // Si hay éxito (código 2xx) y respuesta válida, salir del bucle
  if ($httpcode >= 200 && $httpcode < 300 && $response) {
    break;
  }
}

// Respuesta al cliente (JS)
header("Access-Control-Allow-Origin: *"); // opcional para CORS
header("Content-Type: application/json");
http_response_code($httpcode);

echo $response ?: json_encode([
  "error" => "No se pudo procesar la solicitud.",
  "detalle" => $curl_error ?? "Todas las claves fallaron"
]);
