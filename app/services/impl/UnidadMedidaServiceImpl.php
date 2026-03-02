<?php

namespace App\Services\Impl;

use App\Services\Contract\IUnidadMedidaService;
use App\Models\UnidadMedidaModel;
use App\Utils\Logger;

class UnidadMedidaServiceImpl implements IUnidadMedidaService
{
    private $unidadMedidaModel;

    public function __construct(UnidadMedidaModel $unidadMedidaModel)
    {
        $this->unidadMedidaModel = $unidadMedidaModel;
    }

    public function obtenerUnidadesMedida()
    {
        try {
            Logger::logInfo("Inicia obtenerUnidadesMedida");
            $lista = $this->unidadMedidaModel->consultaUnidadesMedida();
            Logger::logInfo("Finaliza obtenerUnidadesMedida", ['total' => count($lista)]);
            return [
                'codigo' => 200,
                'total_data' => count($lista),
                'data' => $lista,
            ];
        } catch (\Throwable $e) {
            Logger::logError("Error en obtenerUnidadesMedida: " . $e->getMessage(), ['metodo' => __METHOD__]);
            throw $e;
        }
    }

    public function obtenerUnidadMedidaPorId($id)
    {
        try {
            Logger::logInfo("Inicia obtenerUnidadMedidaPorId", ['id' => $id]);
            $unidad = $this->unidadMedidaModel->consultaUnidadMedidaPorId($id);
            Logger::logInfo("Finaliza obtenerUnidadMedidaPorId", ['id' => $id]);
            return [
                'codigo' => 200,
                'data' => $unidad,
            ];
        } catch (\Throwable $e) {
            Logger::logError("Error en obtenerUnidadMedidaPorId: " . $e->getMessage(), ['metodo' => __METHOD__]);
            throw $e;
        }
    }

    public function crearUnidadMedida(array $unidad)
    {
        try {
            Logger::logInfo("Inicia crearUnidadMedida", ['nombre' => $unidad['nombre'] ?? null]);
            $resultado = $this->unidadMedidaModel->insertarUnidadMedida($unidad);
            if ($resultado['ok']) {
                $u = $this->obtenerUnidadMedidaPorId($resultado['id']);
                return ['codigo' => 200, 'data' => $u['data']];
            }
            return ['codigo' => 500, 'data' => null, 'error' => 'Error al crear la unidad de medida'];
        } catch (\Throwable $e) {
            Logger::logError("Error en crearUnidadMedida: " . $e->getMessage(), ['metodo' => __METHOD__]);
            throw $e;
        }
    }

    public function actualizarUnidadMedida(array $unidad)
    {
        try {
            Logger::logInfo("Inicia actualizarUnidadMedida", ['id' => $unidad['id'] ?? null]);
            $resultado = $this->unidadMedidaModel->actualizarUnidadMedida($unidad);
            if ($resultado['ok']) {
                $u = $this->obtenerUnidadMedidaPorId($resultado['id']);
                return ['codigo' => 200, 'data' => $u['data']];
            }
            return ['codigo' => 404, 'data' => null, 'error' => 'Unidad de medida no encontrada'];
        } catch (\Throwable $e) {
            Logger::logError("Error en actualizarUnidadMedida: " . $e->getMessage(), ['metodo' => __METHOD__]);
            throw $e;
        }
    }

    public function eliminarUnidadMedida($id)
    {
        try {
            Logger::logInfo("Inicia eliminarUnidadMedida", ['id' => $id]);
            $resultado = $this->unidadMedidaModel->eliminarUnidadMedida($id);
            Logger::logInfo("Finaliza eliminarUnidadMedida", ['id' => $id]);
            return [
                'codigo' => 200,
                'data' => $resultado,
                'mensaje' => 'Unidad de medida eliminada correctamente',
            ];
        } catch (\Throwable $e) {
            Logger::logError("Error en eliminarUnidadMedida: " . $e->getMessage(), ['metodo' => __METHOD__]);
            throw $e;
        }
    }
}
