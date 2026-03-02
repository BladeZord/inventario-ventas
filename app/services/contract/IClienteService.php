<?php

namespace App\Services\Contract;

interface IClienteService
{
    public function obtenerClientes();

    public function obtenerClientePorId($id);

    public function crearCliente(array $cliente);

    public function actualizarCliente(array $cliente);

    public function eliminarCliente($id);
}
