<?php

use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\VentaController;

use App\Models\CategoriaModel;
use App\Models\ClienteModel;
use App\Models\ProductoModel;
use App\Models\VentaModel;

use App\Services\Impl\CategoriaServiceImpl;
use App\Services\Impl\ClienteServiceImpl;
use App\Services\Impl\ProductoServiceImpl;
use App\Services\Impl\VentaServiceImpl;

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

    VentaController::class => function () {
        $model = new VentaModel();
        $service = new VentaServiceImpl($model);
        return new VentaController($service);
    },

    ClienteController::class => function () {
        $model = new ClienteModel();
        $service = new ClienteServiceImpl($model);
        return new ClienteController($service);
    },
];
