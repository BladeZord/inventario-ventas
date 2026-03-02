<?php

namespace App\Services\Contract;

interface IUnidadMedidaService
{
    public function obtenerUnidadesMedida();

    public function obtenerUnidadMedidaPorId($id);

    public function crearUnidadMedida(array $unidad);

    public function actualizarUnidadMedida(array $unidad);

    public function eliminarUnidadMedida($id);
}
