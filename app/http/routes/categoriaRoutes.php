<?php

/**
 * Rutas REST para el recurso Categoria.
 * Cada ruta: [ método HTTP, patrón path, nombre del método del controlador ].
 * El patrón {id} se reemplaza por el ID numérico en la URL.
 */
return [
    ['GET', '/api/categoria', 'obtenerCategorias'],
    ['GET', '/api/categoria/{id}', 'obtenerCategoriaPorId'],
    ['POST', '/api/categoria', 'crearCategoria'],
    ['PUT', '/api/categoria/{id}', 'actualizarCategoria'],
    ['DELETE', '/api/categoria/{id}', 'eliminarCategoria'],
];
