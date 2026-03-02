<?php

namespace App\Http\Controllers;

use App\Services\Contract\IVentaService;
use App\Utils\Logger;

class VentaController
{
    private $IVentaService;

    public function __construct(IVentaService $IVentaService)
    {
        $this->IVentaService = $IVentaService;
    }

    public function obtenerVentas()
    {
        Logger::logInfo('Controller: Inicia obtenerVentas');
        try {
            $resultado = $this->IVentaService->obtenerVentas();
            Logger::logInfo('Controller: Finaliza obtenerVentas', ['total' => $resultado['total_data'] ?? 0]);
            return $resultado;
        } catch (\Throwable $e) {
            Logger::logError('Controller: Error en obtenerVentas - ' . $e->getMessage(), ['metodo' => __METHOD__]);
            throw $e;
        }
    }

    public function obtenerVentaPorId($id)
    {
        Logger::logInfo('Controller: Inicia obtenerVentaPorId', ['id' => $id]);
        try {
            $resultado = $this->IVentaService->obtenerVentaPorId($id);
            Logger::logInfo('Controller: Finaliza obtenerVentaPorId', ['id' => $id]);
            return $resultado;
        } catch (\Throwable $e) {
            Logger::logError('Controller: Error en obtenerVentaPorId - ' . $e->getMessage(), ['id' => $id, 'metodo' => __METHOD__]);
            throw $e;
        }
    }

    public function crearVenta(array $data)
    {
        Logger::logInfo('Controller: Inicia crearVenta', ['numero_factura' => $data['numero_factura'] ?? null]);
        try {
            $resultado = $this->IVentaService->crearVenta($data);
            Logger::logInfo('Controller: Finaliza crearVenta');
            return $resultado;
        } catch (\Throwable $e) {
            Logger::logError('Controller: Error en crearVenta - ' . $e->getMessage(), ['metodo' => __METHOD__]);
            throw $e;
        }
    }

    public function actualizarVenta(array $venta)
    {
        $id = $venta['id'] ?? null;
        Logger::logInfo('Controller: Inicia actualizarVenta', ['id' => $id]);
        try {
            $resultado = $this->IVentaService->actualizarVenta($venta);
            Logger::logInfo('Controller: Finaliza actualizarVenta', ['id' => $id]);
            return $resultado;
        } catch (\Throwable $e) {
            Logger::logError('Controller: Error en actualizarVenta - ' . $e->getMessage(), ['id' => $id, 'metodo' => __METHOD__]);
            throw $e;
        }
    }

    public function anularVenta($id)
    {
        Logger::logInfo('Controller: Inicia anularVenta', ['id' => $id]);
        try {
            $resultado = $this->IVentaService->anularVenta($id);
            Logger::logInfo('Controller: Finaliza anularVenta', ['id' => $id]);
            return $resultado;
        } catch (\Throwable $e) {
            Logger::logError('Controller: Error en anularVenta - ' . $e->getMessage(), ['id' => $id, 'metodo' => __METHOD__]);
            throw $e;
        }
    }
}
