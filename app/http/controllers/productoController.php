<?php
namespace App\Http\Controllers;

use App\Services\Contract\IProductoService;
use App\Utils\Logger;


class ProductoController{
    private $IProductoService;

    public function __construct(IProductoService $IProductoService){
        $this->IProductoService = $IProductoService;
    }

    public function obtenerProductos(){
        Logger::logInfo('Controller: Inicia obtenerProductos');
        try{
            $resultado = $this->IProductoService->obtenerProductos();
            Logger::logInfo('Controller: Finaliza obtenerProductos', ['total' => $resultado['total_data'] ?? 0]);
            return $resultado;
        }
        catch(\Throwable $e){
            Logger::logError('Controller: Error en obtenerProductos - ' . $e->getMessage(), ['metodo' => __METHOD__]);
            throw $e;
        }
    }

    public function obtenerProductoPorId($id){
        Logger::logInfo('Controller: Inicia obtenerProductoPorId', ['id' => $id]);
        try{
            $resultado = $this->IProductoService->obtenerProductoPorId($id);
            Logger::logInfo('Controller: Finaliza obtenerProductoPorId', ['id' => $id]);
            return $resultado;
        }
        catch(\Throwable $e){
            Logger::logError('Controller: Error en obtenerProductoPorId - ' . $e->getMessage(), ['id' => $id, 'metodo' => __METHOD__]);
            throw $e;
        }
    }

    public function obtenerProductosPorCategoria($id_categoria){
        Logger::logInfo('Controller: Inicia obtenerProductosPorCategoria', ['id_categoria' => $id_categoria]);
        try{
            $resultado = $this->IProductoService->obtenerProductosPorCategoria($id_categoria);
            Logger::logInfo('Controller: Finaliza obtenerProductosPorCategoria', ['id_categoria' => $id_categoria]);
            return $resultado;
        }
        catch(\Throwable $e){
            Logger::logError('Controller: Error en obtenerProductosPorCategoria - ' . $e->getMessage(), ['id_categoria' => $id_categoria, 'metodo' => __METHOD__]);
            throw $e;
        }
    }

    public function crearProducto(array $producto){
        Logger::logInfo('Controller: Inicia crearProducto', ['producto' => $producto]);
        try{
            $resultado = $this->IProductoService->crearProducto($producto);
            Logger::logInfo('Controller: Finaliza crearProducto', ['producto' => $producto]);
            return $resultado;
        }
        catch(\Throwable $e){
            Logger::logError('Controller: Error en crearProducto - ' . $e->getMessage(), ['producto' => $producto, 'metodo' => __METHOD__]);
            throw $e;
        }
    }

    public function actualizarProducto(array $producto){
        Logger::logInfo('Controller: Inicia actualizarProducto', ['producto' => $producto]);
        try{
            $resultado = $this->IProductoService->actualizarProducto($producto);
            Logger::logInfo('Controller: Finaliza actualizarProducto', ['producto' => $producto]);
            return $resultado;
        }
        catch(\Throwable $e){
            Logger::logError('Controller: Error en actualizarProducto - ' . $e->getMessage(), ['producto' => $producto, 'metodo' => __METHOD__]);
            throw $e;
        }
    }

    public function eliminarProducto($id){
        Logger::logInfo('Controller: Inicia eliminarProducto', ['id' => $id]);
        try{
            $resultado = $this->IProductoService->eliminarProducto($id);
            Logger::logInfo('Controller: Finaliza eliminarProducto', ['id' => $id]);
            return $resultado;
        }
        catch(\Throwable $e){
            Logger::logError('Controller: Error en eliminarProducto - ' . $e->getMessage(), ['id' => $id, 'metodo' => __METHOD__]);
            throw $e;
        }
    }   

    
}