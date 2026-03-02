<?php

use App\Http\Controllers\UnidadMedidaController;

return [
    ['GET',    '/api/unidad-medida',        [UnidadMedidaController::class, 'obtenerUnidadesMedida']],
    ['GET',    '/api/unidad-medida/{:id}',  [UnidadMedidaController::class, 'obtenerUnidadMedidaPorId']],
    ['POST',   '/api/unidad-medida',        [UnidadMedidaController::class, 'crearUnidadMedida']],
    ['PUT',    '/api/unidad-medida/{:id}',  [UnidadMedidaController::class, 'actualizarUnidadMedida']],
    ['DELETE', '/api/unidad-medida/{:id}',  [UnidadMedidaController::class, 'eliminarUnidadMedida']],
];
