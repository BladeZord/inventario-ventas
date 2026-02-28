<?php

namespace App\Services\Impl;

use App\Services\Contract\IProductoService;
use App\Models\ProductoModel;
use App\Utils\Logger;

class ProductoServiceImpl implements IProductoService {
    private $productoModel;

    public function __construct(ProductoModel $productoModel){
        $this->productoModel = $productoModel;
    }

    public function obtenerProductos(){
        try{
            Logger::logInfo("Inicia obtenerProductos");
            $productos = $this->productoModel->consultaProductos();
            Logger::logInfo("Finaliza obtenerProductos", ['productos' => $productos]);
            return [
                "codigo" => 200,
                "total_data" => count($productos),
                "data" => $productos
            ];
        } catch (\Throwable $e){
            Logger::logError("Error en obtenerProductos: " . $e->getMessage(), ['metodo' => __METHOD__]);
            throw $e;
        }
    }
    public function obtenerProductoPorId($id){
        try{
            Logger::logInfo("Inicia obtenerProductoPorId", ['id' => $id]);
            $producto = $this->productoModel->consultaProductoPorId($id);
            Logger::logInfo("Finaliza obtenerProductoPorId", ['producto' => $producto]);
            return [
                "codigo" => 200,
                "data" => $producto
            ];
        } catch (\Throwable $e){
            Logger::logError("Error en obtenerProductoPorId: " . $e->getMessage(), ['metodo' => __METHOD__]);
            throw $e;
        }
    }
    public function obtenerProductosPorCategoria($id_categoria){
        try{
            Logger::logInfo("Inicia obtenerProductosPorCategoria", ['id_categoria' => $id_categoria]);
            $productos = $this->productoModel->consultaProductosPorCategoria($id_categoria);
            Logger::logInfo("Finaliza obtenerProductosPorCategoria", ['productos' => $productos]);
            return [
                "codigo" => 200,
                "total_data" => count($productos),
                "data" => $productos
            ];
        } catch (\Throwable $e){
            Logger::logError("Error en obtenerProductosPorCategoria: " . $e->getMessage(), ['metodo' => __METHOD__]);
            throw $e;
        }
    }
    public function crearProducto($producto){
        try{
            Logger::logInfo("Inicia crearProducto", ['producto' => $producto]);
            $producto = $this->productoModel->insertarProducto($producto);
            Logger::logInfo("Finaliza crearProducto", ['producto' => $producto]);
            return [
                "codigo" => 200,
                "data" => $producto
            ];
        } catch (\Throwable $e){
            Logger::logError("Error en crearProducto: " . $e->getMessage(), ['metodo' => __METHOD__]);
            throw $e;
        }
    }
    public function actualizarProducto($producto){
        try{
            Logger::logInfo("Inicia actualizarProducto", ['producto' => $producto]);
            $producto = $this->productoModel->actualizarProducto($producto);
            Logger::logInfo("Finaliza actualizarProducto", ['producto' => $producto]);
            return [
                "codigo" => 200,
                "data" => $producto
            ];
        } catch (\Throwable $e){
            Logger::logError("Error en actualizarProducto: " . $e->getMessage(), ['metodo' => __METHOD__]);
            throw $e;
        }
    }
    public function eliminarProducto($id){
        try{
            Logger::logInfo("Inicia eliminarProducto", ['id' => $id]);
            $producto = $this->productoModel->eliminarProducto($id);
            Logger::logInfo("Finaliza eliminarProducto", ['producto' => $producto]);
            return [
                "codigo" => 200,
                "data" => $producto
            ];
        } catch (\Throwable $e){
            Logger::logError("Error en eliminarProducto: " . $e->getMessage(), ['metodo' => __METHOD__]);
            throw $e;
        }
    }
}
