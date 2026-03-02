<?php

use App\Http\Controllers\ClienteController;

return [
    ['GET',    '/api/cliente',        [ClienteController::class, 'obtenerClientes']],
    ['GET',    '/api/cliente/{:id}',  [ClienteController::class, 'obtenerClientePorId']],
    ['POST',   '/api/cliente',        [ClienteController::class, 'crearCliente']],
    ['PUT',    '/api/cliente/{:id}',  [ClienteController::class, 'actualizarCliente']],
    ['DELETE', '/api/cliente/{:id}',  [ClienteController::class, 'eliminarCliente']],
];
