<?php

namespace App\Services\Impl;

use App\Services\Contract\ICategoriaService;
use App\Models\CategoriaModel;
use App\Utils\Logger;

class CategoriaServiceImpl implements ICategoriaService
{
    private $categoriaModel;

    public function __construct(CategoriaModel $categoriaModel)
    {
        $this->categoriaModel = $categoriaModel;
    }

    public function obtenerCategorias()
    {

        try {
            Logger::logInfo("Inicia obtenerCategorias");
            $categorias = $this->categoriaModel->consultaCategoria();
            Logger::logInfo("Finaliza obtenerCategorias", ['categorias' => $categorias]);
            return [
                "codigo" => 200,
                "total_data" => count($categorias),
                "data" => $categorias
            ];
        } catch (\Throwable $e) {
            Logger::logError("Error en obtenerCategorias: " . $e->getMessage(), ['metodo' => __METHOD__]);
            throw $e;
        }
    }

    public function obtenerCategoriaPorId($id)
    {
        try {
            Logger::logInfo("Inicia obtenerCategoriaPorId", ['id' => $id]);
            $categoria = $this->categoriaModel->consultaCategoriaPorId($id);
            Logger::logInfo("Finaliza obtenerCategoriaPorId", ['categoria' => $categoria]);
            return [
                "codigo" => 200,
                "data" => $categoria
            ];
        } catch (\Throwable $e) {
            Logger::logError("Error en obtenerCategoriaPorId: " . $e->getMessage(), ['metodo' => __METHOD__]);
            throw $e;
        }
    }

    public function crearCategoria($categoria)
    {
        try {
            Logger::logInfo("Inicia crearCategoria", ['categoria' => $categoria]);
            $categoria = $this->categoriaModel->insertarCategoria($categoria);

            if ($categoria['ok']) {
                $resultado = $this->obtenerCategoriaPorId($categoria['id']);
                return [
                    "codigo" => 200,
                    "data" => $resultado['data']
                ];
            } else {
                return [
                    "codigo" => 500,
                    "data" => null,
                    "error" => "Error al crear la categoria",
                    "mensaje" => $categoria['mensaje']
                ];
            }
        } catch (\Throwable $e) {
            Logger::logError("Error en crearCategoria: " . $e->getMessage(), ['metodo' => __METHOD__]);
            throw $e;
        }
    }

    public function actualizarCategoria($categoria)
    {
        try {
            Logger::logInfo("Inicia actualizarCategoria", ['categoria' => $categoria]);
            $categoria = $this->categoriaModel->actualizarCategoria($categoria);

            if ($categoria['ok']) {
                $resultado = $this->obtenerCategoriaPorId($categoria['id']);
                return [
                    "codigo" => 200,
                    "data" => $resultado['data']
                ];
            } else {
                return [
                    "codigo" => 500,
                    "data" => null,
                    "error" => "Error al actualizar la categoria",
                    "mensaje" => $categoria['mensaje']
                ];
            }
        } catch (\Throwable $e) {
            Logger::logError("Error en actualizarCategoria: " . $e->getMessage(), ['metodo' => __METHOD__]);
            throw $e;
        }
    }

    public function eliminarCategoria($id)
    {
        try {
            Logger::logInfo("Inicia eliminarCategoria", ['id' => $id]);
            $categoria = $this->categoriaModel->eliminarCategoria($id);
            Logger::logInfo("Finaliza eliminarCategoria", ['categoria' => $categoria]);
            return [
                "codigo" => 200,
                "data" => $categoria,
                "mensaje" => "Categoria eliminada correctamente"
            ];
        } catch (\Throwable $e) {
            Logger::logError("Error en eliminarCategoria: " . $e->getMessage(), ['metodo' => __METHOD__]);
            throw $e;
        }
    }
}
