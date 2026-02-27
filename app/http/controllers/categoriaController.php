<?php

namespace App\Http\Controllers;

use App\Services\Contract\ICategoriaService;
use App\Utils\Logger;

class CategoriaController
{
    private $ICategoriaService;

    public function __construct(ICategoriaService $ICategoriaService)
    {
        $this->ICategoriaService = $ICategoriaService;
    }

    public function obtenerCategorias()
    {
        Logger::logInfo('Controller: Inicia obtenerCategorias');
        try {
            $resultado = $this->ICategoriaService->obtenerCategorias();
            Logger::logInfo('Controller: Finaliza obtenerCategorias', ['total' => $resultado['total_data'] ?? 0]);
            return $resultado;
        } catch (\Throwable $e) {
            Logger::logError('Controller: Error en obtenerCategorias - ' . $e->getMessage(), ['metodo' => __METHOD__]);
            throw $e;
        }
    }

    public function obtenerCategoriaPorId($id)
    {
        Logger::logInfo('Controller: Inicia obtenerCategoriaPorId', ['id' => $id]);
        try {
            $resultado = $this->ICategoriaService->obtenerCategoriaPorId($id);
            Logger::logInfo('Controller: Finaliza obtenerCategoriaPorId', ['id' => $id]);
            return $resultado;
        } catch (\Throwable $e) {
            Logger::logError('Controller: Error en obtenerCategoriaPorId - ' . $e->getMessage(), ['id' => $id, 'metodo' => __METHOD__]);
            throw $e;
        }
    }

    public function crearCategoria($categoria)
    {
        Logger::logInfo('Controller: Inicia crearCategoria', ['nombre' => $categoria['nombre'] ?? null]);
        try {
            $resultado = $this->ICategoriaService->crearCategoria($categoria);
            Logger::logInfo('Controller: Finaliza crearCategoria', ['codigo' => $resultado['codigo'] ?? null]);
            return $resultado;
        } catch (\Throwable $e) {
            Logger::logError('Controller: Error en crearCategoria - ' . $e->getMessage(), ['metodo' => __METHOD__, 'nombre' => $categoria['nombre'] ?? null]);
            throw $e;
        }
    }

    public function actualizarCategoria($categoria)
    {
        $id = $categoria['id'] ?? null;
        Logger::logInfo('Controller: Inicia actualizarCategoria', ['id' => $id]);
        try {
            $resultado = $this->ICategoriaService->actualizarCategoria($categoria);
            Logger::logInfo('Controller: Finaliza actualizarCategoria', ['id' => $id, 'codigo' => $resultado['codigo'] ?? null]);
            return $resultado;
        } catch (\Throwable $e) {
            Logger::logError('Controller: Error en actualizarCategoria - ' . $e->getMessage(), ['id' => $id, 'metodo' => __METHOD__]);
            throw $e;
        }
    }

    public function eliminarCategoria($id)
    {
        Logger::logInfo('Controller: Inicia eliminarCategoria', ['id' => $id]);
        try {
            $resultado = $this->ICategoriaService->eliminarCategoria($id);
            Logger::logInfo('Controller: Finaliza eliminarCategoria', ['id' => $id]);
            return $resultado;
        } catch (\Throwable $e) {
            Logger::logError('Controller: Error en eliminarCategoria - ' . $e->getMessage(), ['id' => $id, 'metodo' => __METHOD__]);
            throw $e;
        }
    }
}
