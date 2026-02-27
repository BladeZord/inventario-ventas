<?php

namespace App\Services\Contract;

interface ICategoriaService {
    public function obtenerCategorias();
    public function obtenerCategoriaPorId($id);
    public function crearCategoria($categoria);
    public function actualizarCategoria($categoria);
    public function eliminarCategoria($id);
}