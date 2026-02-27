<?php

namespace App\Services;

class ProductoService {
    private $productoModel;

    public function __construct(ProductoModel $productoModel){
        $this->productoModel = $productoModel;
    }
}