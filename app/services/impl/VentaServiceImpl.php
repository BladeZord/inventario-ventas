<?php

namespace App\Services\Impl;

use App\Services\Contract\IVentaService;
use App\Models\VentaModel;
use App\Utils\Logger;

class VentaServiceImpl implements IVentaService
{
    private $ventaModel;

    public function __construct(VentaModel $ventaModel)
    {
        $this->ventaModel = $ventaModel;
    }

    public function obtenerVentas()
    {
        try {
            Logger::logInfo("Inicia obtenerVentas");
            $ventas = $this->ventaModel->consultaVentas();
            Logger::logInfo("Finaliza obtenerVentas", ['total' => count($ventas)]);
            return [
                'codigo' => 200,
                'total_data' => count($ventas),
                'data' => $ventas,
            ];
        } catch (\Throwable $e) {
            Logger::logError("Error en obtenerVentas: " . $e->getMessage(), ['metodo' => __METHOD__]);
            throw $e;
        }
    }

    public function obtenerVentaPorId($id)
    {
        try {
            Logger::logInfo("Inicia obtenerVentaPorId", ['id' => $id]);
            $venta = $this->ventaModel->consultaVentaPorId($id);
            Logger::logInfo("Finaliza obtenerVentaPorId", ['id' => $id]);
            return [
                'codigo' => 200,
                'data' => $venta,
            ];
        } catch (\Throwable $e) {
            Logger::logError("Error en obtenerVentaPorId: " . $e->getMessage(), ['metodo' => __METHOD__]);
            throw $e;
        }
    }

    public function crearVenta(array $data)
    {
        try {
            Logger::logInfo("Inicia crearVenta", ['numero_factura' => $data['numero_factura'] ?? null]);

            $detalles = $data['detalles'] ?? [];
            unset($data['detalles']);
            $resultado = $this->ventaModel->insertarVenta($data, $detalles);

            if ($resultado['ok']) {
                $venta = $this->obtenerVentaPorId($resultado['id']);
                return [
                    'codigo' => 200,
                    'data' => $venta['data'],
                ];
            }

            return [
                'codigo' => 500,
                'data' => null,
                'error' => 'Error al crear la venta',
            ];
        } catch (\Throwable $e) {
            Logger::logError("Error en crearVenta: " . $e->getMessage(), ['metodo' => __METHOD__]);
            throw $e;
        }
    }

    public function actualizarVenta(array $venta)
    {
        try {
            Logger::logInfo("Inicia actualizarVenta", ['id' => $venta['id'] ?? null]);
            $resultado = $this->ventaModel->actualizarVenta($venta);

            if ($resultado['ok']) {
                $v = $this->obtenerVentaPorId($resultado['id']);
                return [
                    'codigo' => 200,
                    'data' => $v['data'],
                ];
            }

            return [
                'codigo' => 404,
                'data' => null,
                'error' => 'Venta no encontrada o no actualizable',
            ];
        } catch (\Throwable $e) {
            Logger::logError("Error en actualizarVenta: " . $e->getMessage(), ['metodo' => __METHOD__]);
            throw $e;
        }
    }

    public function anularVenta($id)
    {
        try {
            Logger::logInfo("Inicia anularVenta", ['id' => $id]);
            $resultado = $this->ventaModel->anularVenta($id);
            Logger::logInfo("Finaliza anularVenta", ['id' => $id]);
            return [
                'codigo' => 200,
                'data' => $resultado,
                'mensaje' => 'Venta anulada correctamente',
            ];
        } catch (\Throwable $e) {
            Logger::logError("Error en anularVenta: " . $e->getMessage(), ['metodo' => __METHOD__]);
            throw $e;
        }
    }
}
