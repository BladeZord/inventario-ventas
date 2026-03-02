<?php

namespace App\Http\Controllers;

use App\Services\Contract\IUnidadMedidaService;
use App\Utils\Logger;

class UnidadMedidaController
{
    private $IUnidadMedidaService;

    public function __construct(IUnidadMedidaService $IUnidadMedidaService)
    {
        $this->IUnidadMedidaService = $IUnidadMedidaService;
    }

    public function obtenerUnidadesMedida()
    {
        Logger::logInfo('Controller: Inicia obtenerUnidadesMedida');
        try {
            $resultado = $this->IUnidadMedidaService->obtenerUnidadesMedida();
            Logger::logInfo('Controller: Finaliza obtenerUnidadesMedida', ['total' => $resultado['total_data'] ?? 0]);
            return $resultado;
        } catch (\Throwable $e) {
            Logger::logError('Controller: Error en obtenerUnidadesMedida - ' . $e->getMessage(), ['metodo' => __METHOD__]);
            throw $e;
        }
    }

    public function obtenerUnidadMedidaPorId($id)
    {
        Logger::logInfo('Controller: Inicia obtenerUnidadMedidaPorId', ['id' => $id]);
        try {
            $resultado = $this->IUnidadMedidaService->obtenerUnidadMedidaPorId($id);
            Logger::logInfo('Controller: Finaliza obtenerUnidadMedidaPorId', ['id' => $id]);
            return $resultado;
        } catch (\Throwable $e) {
            Logger::logError('Controller: Error en obtenerUnidadMedidaPorId - ' . $e->getMessage(), ['id' => $id, 'metodo' => __METHOD__]);
            throw $e;
        }
    }

    public function crearUnidadMedida(array $unidad)
    {
        Logger::logInfo('Controller: Inicia crearUnidadMedida', ['nombre' => $unidad['nombre'] ?? null]);
        try {
            $resultado = $this->IUnidadMedidaService->crearUnidadMedida($unidad);
            Logger::logInfo('Controller: Finaliza crearUnidadMedida');
            return $resultado;
        } catch (\Throwable $e) {
            Logger::logError('Controller: Error en crearUnidadMedida - ' . $e->getMessage(), ['metodo' => __METHOD__]);
            throw $e;
        }
    }

    public function actualizarUnidadMedida(array $unidad)
    {
        $id = $unidad['id'] ?? null;
        Logger::logInfo('Controller: Inicia actualizarUnidadMedida', ['id' => $id]);
        try {
            $resultado = $this->IUnidadMedidaService->actualizarUnidadMedida($unidad);
            Logger::logInfo('Controller: Finaliza actualizarUnidadMedida', ['id' => $id]);
            return $resultado;
        } catch (\Throwable $e) {
            Logger::logError('Controller: Error en actualizarUnidadMedida - ' . $e->getMessage(), ['id' => $id, 'metodo' => __METHOD__]);
            throw $e;
        }
    }

    public function eliminarUnidadMedida($id)
    {
        Logger::logInfo('Controller: Inicia eliminarUnidadMedida', ['id' => $id]);
        try {
            $resultado = $this->IUnidadMedidaService->eliminarUnidadMedida($id);
            Logger::logInfo('Controller: Finaliza eliminarUnidadMedida', ['id' => $id]);
            return $resultado;
        } catch (\Throwable $e) {
            Logger::logError('Controller: Error en eliminarUnidadMedida - ' . $e->getMessage(), ['id' => $id, 'metodo' => __METHOD__]);
            throw $e;
        }
    }
}
