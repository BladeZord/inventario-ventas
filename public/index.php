<?php

/**
 * Entry point del API REST (sin framework).
 * Enruta por método HTTP y path; delega en el controlador de categoría y devuelve JSON.
 */

header('Content-Type: application/json; charset=utf-8');

// CORS: restringir Access-Control-Allow-Origin a tu dominio en producción
if (isset($_SERVER['HTTP_ORIGIN'])) {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');
}
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

require_once __DIR__ . '/../vendor/autoload.php';

$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$path = '/' . trim($path, '/');
$method = $_SERVER['REQUEST_METHOD'];

// Cuerpo JSON para POST/PUT
$rawInput = file_get_contents('php://input');
$body = $rawInput !== false && $rawInput !== '' ? json_decode($rawInput, true) : [];

$categoriaRoutes = require_once __DIR__ . '/../app/http/routes/categoriaRoutes.php';

$matched = null;
$capturedId = null;

foreach ($categoriaRoutes as $route) {
    [$routeMethod, $pattern, $handler] = $route;
    if ($routeMethod !== $method) {
        continue;
    }
    $regex = '#^' . preg_quote($pattern, '#') . '$#u';
    $regex = str_replace(preg_quote('{id}', '#'), '([0-9]+)', $regex);
    if (preg_match($regex, $path, $m)) {
        $matched = $handler;
        $capturedId = isset($m[1]) ? (int) $m[1] : null;
        break;
    }
}

if ($matched === null) {
    http_response_code(404);
    echo json_encode([
        'codigo' => 404,
        'error' => 'Ruta no encontrada',
        'path' => $path,
        'method' => $method,
    ], JSON_UNESCAPED_UNICODE);
    return;
}

try {
    $categoriaModel = new \App\Models\CategoriaModel();
    $categoriaService = new \App\Services\Impl\CategoriaServiceImpl($categoriaModel);
    $controller = new \App\Http\Controllers\CategoriaController($categoriaService);

    $args = [];
    if ($capturedId !== null) {
        $args[] = $capturedId;
    }
    if (in_array($matched, ['crearCategoria'], true)) {
        $args = [$body];
    } elseif (in_array($matched, ['actualizarCategoria'], true)) {
        $body['id'] = $capturedId;
        $args = [$body];
    }

    $response = $controller->$matched(...$args);

    $codigo = (int) ($response['codigo'] ?? 200);
    http_response_code($codigo);
    echo json_encode($response, JSON_UNESCAPED_UNICODE);

} catch (Throwable $e) {
    \App\Utils\Logger::logError('API categoria: ' . $e->getMessage(), [
        'path' => $path,
        'method' => $method,
        'trace' => $e->getTraceAsString(),
    ]);
    http_response_code(500);
    echo json_encode([
        'codigo' => 500,
        'error' => 'Error interno del servidor',
        'mensaje' => $e->getMessage(),
    ], JSON_UNESCAPED_UNICODE);
}
