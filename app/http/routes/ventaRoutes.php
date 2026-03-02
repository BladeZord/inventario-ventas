<?php

use App\Http\Controllers\VentaController;

return [
    ['GET',  '/api/venta',           [VentaController::class, 'obtenerVentas']],
    ['GET',  '/api/venta/{:id}',     [VentaController::class, 'obtenerVentaPorId']],
    ['POST', '/api/venta',           [VentaController::class, 'crearVenta']],
    ['PUT',  '/api/venta/{:id}',     [VentaController::class, 'actualizarVenta']],
    ['PUT',  '/api/venta/{:id}/anular', [VentaController::class, 'anularVenta']],
];
