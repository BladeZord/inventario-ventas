<?php

namespace App\Services\Contract;

interface IProductoService {
    public function obtenerProductos();
    public function obtenerProductoPorId($id);
    public function obtenerProductosPorCategoria($id_categoria);
    public function crearProducto($producto);
    public function actualizarProducto($producto);
    public function eliminarProducto($id);
}
