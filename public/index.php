<?php
require_once __DIR__ . '/../vendor/autoload.php';

$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';

// Si NO es /api..., devolver HTML (vistas)
if (strpos($path, '/api') !== 0) {
    header('Content-Type: text/html; charset=utf-8');
    // Home (puedes hacer router web luego, por ahora directo)
    require __DIR__ . '/views/initial/initial.php';
    exit;
}

/** --------- API JSON (lo que ya tienes) --------- */
header('Content-Type: application/json; charset=utf-8');

if (isset($_SERVER['HTTP_ORIGIN'])) {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');
}
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'OPTIONS') {
    http_response_code(204);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

$rawInput = file_get_contents('php://input');
$body = [];
if ($rawInput !== false && trim($rawInput) !== '') {
    $body = json_decode($rawInput, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400);
        echo json_encode(['codigo'=>400,'error'=>'JSON inválido','detalle'=>json_last_error_msg()], JSON_UNESCAPED_UNICODE);
        exit;
    }
    if (!is_array($body)) $body = [];
}

$routes = require __DIR__ . '/../app/http/routes/routes.php';
$router = new \App\Http\Routing\Router($routes);

$match = $router->match($method, $path);

if ($match === null) {
    http_response_code(404);
    echo json_encode(['codigo'=>404,'error'=>'Ruta no encontrada','path'=>$path,'method'=>$method], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $bindings = require __DIR__ . '/../app/http/routing/bindings.php';
    $factory = new \App\Http\Routing\ControllerFactory($bindings);
    $dispatcher = new \App\Http\Routing\Dispatcher($factory);

    $response = $dispatcher->dispatch($match['handler'], $match['params'], $body);

    $codigo = (int)($response['codigo'] ?? 200);
    http_response_code($codigo);
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    \App\Utils\Logger::logError('API error: ' . $e->getMessage(), [
        'path' => $path,
        'method' => $method,
        'trace' => $e->getTraceAsString(),
    ]);

    http_response_code(500);
    echo json_encode(['codigo'=>500,'error'=>'Error interno','mensaje'=>$e->getMessage()], JSON_UNESCAPED_UNICODE);
}