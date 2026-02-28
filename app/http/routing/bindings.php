<?php

use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ProductoController;

use App\Models\CategoriaModel;
use App\Models\ProductoModel;

use App\Services\Impl\CategoriaServiceImpl;
use App\Services\Impl\ProductoServiceImpl;

return [
    CategoriaController::class => function () {
        $model = new CategoriaModel();
        $service = new CategoriaServiceImpl($model);
        return new CategoriaController($service);
    },

    ProductoController::class => function () {
        $model = new ProductoModel();
        $service = new ProductoServiceImpl($model);
        return new ProductoController($service);
    },
];
