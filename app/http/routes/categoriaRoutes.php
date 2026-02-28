<?php

use App\Http\Controllers\CategoriaController;

return [
    ['GET',    '/api/categoria',        [CategoriaController::class, 'obtenerCategorias']],
    ['GET',    '/api/categoria/{:id}',  [CategoriaController::class, 'obtenerCategoriaPorId']],
    ['POST',   '/api/categoria',        [CategoriaController::class, 'crearCategoria']],
    ['PUT',    '/api/categoria/{:id}',  [CategoriaController::class, 'actualizarCategoria']],
    ['DELETE', '/api/categoria/{:id}',  [CategoriaController::class, 'eliminarCategoria']],
];
