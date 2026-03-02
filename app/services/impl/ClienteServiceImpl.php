<?php

namespace App\Services\Impl;

use App\Services\Contract\IClienteService;
use App\Models\ClienteModel;
use App\Utils\Logger;

class ClienteServiceImpl implements IClienteService
{
    private $clienteModel;

    public function __construct(ClienteModel $clienteModel)
    {
        $this->clienteModel = $clienteModel;
    }

    public function obtenerClientes()
    {
        try {
            Logger::logInfo("Inicia obtenerClientes");
            $clientes = $this->clienteModel->consultaClientes();
            Logger::logInfo("Finaliza obtenerClientes", ['total' => count($clientes)]);
            return [
                'codigo' => 200,
                'total_data' => count($clientes),
                'data' => $clientes,
            ];
        } catch (\Throwable $e) {
            Logger::logError("Error en obtenerClientes: " . $e->getMessage(), ['metodo' => __METHOD__]);
            throw $e;
        }
    }

    public function obtenerClientePorId($id)
    {
        try {
            Logger::logInfo("Inicia obtenerClientePorId", ['id' => $id]);
            $cliente = $this->clienteModel->consultaClientePorId($id);
            Logger::logInfo("Finaliza obtenerClientePorId", ['id' => $id]);
            return [
                'codigo' => 200,
                'data' => $cliente,
            ];
        } catch (\Throwable $e) {
            Logger::logError("Error en obtenerClientePorId: " . $e->getMessage(), ['metodo' => __METHOD__]);
            throw $e;
        }
    }

    public function crearCliente(array $cliente)
    {
        try {
            Logger::logInfo("Inicia crearCliente", ['identificacion' => $cliente['identificacion'] ?? null]);
            $resultado = $this->clienteModel->insertarCliente($cliente);

            if ($resultado['ok']) {
                $c = $this->obtenerClientePorId($resultado['id']);
                return [
                    'codigo' => 200,
                    'data' => $c['data'],
                ];
            }

            return [
                'codigo' => 500,
                'data' => null,
                'error' => 'Error al crear el cliente',
            ];
        } catch (\Throwable $e) {
            Logger::logError("Error en crearCliente: " . $e->getMessage(), ['metodo' => __METHOD__]);
            throw $e;
        }
    }

    public function actualizarCliente(array $cliente)
    {
        try {
            Logger::logInfo("Inicia actualizarCliente", ['id' => $cliente['id'] ?? null]);
            $resultado = $this->clienteModel->actualizarCliente($cliente);

            if ($resultado['ok']) {
                $c = $this->obtenerClientePorId($resultado['id']);
                return [
                    'codigo' => 200,
                    'data' => $c['data'],
                ];
            }

            return [
                'codigo' => 404,
                'data' => null,
                'error' => 'Cliente no encontrado o no actualizable',
            ];
        } catch (\Throwable $e) {
            Logger::logError("Error en actualizarCliente: " . $e->getMessage(), ['metodo' => __METHOD__]);
            throw $e;
        }
    }

    public function eliminarCliente($id)
    {
        try {
            Logger::logInfo("Inicia eliminarCliente", ['id' => $id]);
            $resultado = $this->clienteModel->eliminarCliente($id);
            Logger::logInfo("Finaliza eliminarCliente", ['id' => $id]);
            return [
                'codigo' => 200,
                'data' => $resultado,
                'mensaje' => 'Cliente eliminado correctamente',
            ];
        } catch (\Throwable $e) {
            Logger::logError("Error en eliminarCliente: " . $e->getMessage(), ['metodo' => __METHOD__]);
            throw $e;
        }
    }
}
