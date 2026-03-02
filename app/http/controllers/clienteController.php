<?php

namespace App\Http\Controllers;

use App\Services\Contract\IClienteService;
use App\Utils\Logger;

class ClienteController
{
    private $IClienteService;

    public function __construct(IClienteService $IClienteService)
    {
        $this->IClienteService = $IClienteService;
    }

    public function obtenerClientes()
    {
        Logger::logInfo('Controller: Inicia obtenerClientes');
        try {
            $resultado = $this->IClienteService->obtenerClientes();
            Logger::logInfo('Controller: Finaliza obtenerClientes', ['total' => $resultado['total_data'] ?? 0]);
            return $resultado;
        } catch (\Throwable $e) {
            Logger::logError('Controller: Error en obtenerClientes - ' . $e->getMessage(), ['metodo' => __METHOD__]);
            throw $e;
        }
    }

    public function obtenerClientePorId($id)
    {
        Logger::logInfo('Controller: Inicia obtenerClientePorId', ['id' => $id]);
        try {
            $resultado = $this->IClienteService->obtenerClientePorId($id);
            Logger::logInfo('Controller: Finaliza obtenerClientePorId', ['id' => $id]);
            return $resultado;
        } catch (\Throwable $e) {
            Logger::logError('Controller: Error en obtenerClientePorId - ' . $e->getMessage(), ['id' => $id, 'metodo' => __METHOD__]);
            throw $e;
        }
    }

    public function crearCliente(array $cliente)
    {
        Logger::logInfo('Controller: Inicia crearCliente', ['identificacion' => $cliente['identificacion'] ?? null]);
        try {
            $resultado = $this->IClienteService->crearCliente($cliente);
            Logger::logInfo('Controller: Finaliza crearCliente');
            return $resultado;
        } catch (\Throwable $e) {
            Logger::logError('Controller: Error en crearCliente - ' . $e->getMessage(), ['metodo' => __METHOD__]);
            throw $e;
        }
    }

    public function actualizarCliente(array $cliente)
    {
        $id = $cliente['id'] ?? null;
        Logger::logInfo('Controller: Inicia actualizarCliente', ['id' => $id]);
        try {
            $resultado = $this->IClienteService->actualizarCliente($cliente);
            Logger::logInfo('Controller: Finaliza actualizarCliente', ['id' => $id]);
            return $resultado;
        } catch (\Throwable $e) {
            Logger::logError('Controller: Error en actualizarCliente - ' . $e->getMessage(), ['id' => $id, 'metodo' => __METHOD__]);
            throw $e;
        }
    }

    public function eliminarCliente($id)
    {
        Logger::logInfo('Controller: Inicia eliminarCliente', ['id' => $id]);
        try {
            $resultado = $this->IClienteService->eliminarCliente($id);
            Logger::logInfo('Controller: Finaliza eliminarCliente', ['id' => $id]);
            return $resultado;
        } catch (\Throwable $e) {
            Logger::logError('Controller: Error en eliminarCliente - ' . $e->getMessage(), ['id' => $id, 'metodo' => __METHOD__]);
            throw $e;
        }
    }
}
