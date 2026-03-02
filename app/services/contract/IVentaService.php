<?php

namespace App\Services\Contract;

interface IVentaService
{
    public function obtenerVentas();

    public function obtenerVentaPorId($id);

    public function crearVenta(array $data);

    public function actualizarVenta(array $venta);

    public function anularVenta($id);
}
