<?php

use App\Http\Controllers\ProductoController;

return [
    ['GET',    '/api/producto',                          [ProductoController::class, 'obtenerProductos']],
    ['GET',    '/api/producto/{:id}',                    [ProductoController::class, 'obtenerProductoPorId']],
    ['GET',    '/api/producto/categoria/{:id_categoria}',[ProductoController::class, 'obtenerProductosPorCategoria']],
    ['POST',   '/api/producto',                          [ProductoController::class, 'crearProducto']],
    ['PUT',    '/api/producto/{:id}',                    [ProductoController::class, 'actualizarProducto']],
    ['DELETE', '/api/producto/{:id}',                    [ProductoController::class, 'eliminarProducto']],
];
