<?php

namespace App\Models;

use App\Config\Conexion;
use App\Utils\Logger;
use PDO;

class ProductoModel extends Conexion
{
    public function __construct()
    {
        $this->conectar();
    }

    public function consultaProductos()
    {
        try {
            Logger::logInfo("Inicia consultaProductos");

            $stmt = $this->dbh->prepare("
                SELECT p.*
                FROM productos p
                WHERE p.estado <> :estado
                -- ORDER BY p.id DESC
            ");
            $stmt->execute(['estado' => 'ELIMINADO']);

            Logger::logInfo("Finaliza consultaProductos", [
                'count' => $stmt->rowCount()
            ]);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (\Throwable $e) {
            Logger::logError("Error en consultaProductos: " . $e->getMessage(), [
                'metodo' => __METHOD__
            ]);
            throw $e;
        }
    }

    public function consultaProductoPorId($id)
    {
        try {
            Logger::logInfo("Inicia consultaProductoPorId", ['id' => (int) $id]);

            $stmt = $this->dbh->prepare("
                SELECT p.*
                FROM productos p
                WHERE p.id = :id
                  AND p.estado <> :estado_eliminado
                LIMIT 1
            ");

            $stmt->execute([
                'id' => (int) $id,
                'estado_eliminado' => 'ELIMINADO'
            ]);

            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            Logger::logInfo("Finaliza consultaProductoPorId", [
                'id' => (int) $id,
                'encontrado' => $row ? true : false
            ]);

            return $row;

        } catch (\Throwable $e) {
            Logger::logError("Error en consultaProductoPorId: " . $e->getMessage(), [
                'metodo' => __METHOD__,
                'id' => (int) $id
            ]);
            throw $e;
        }
    }

    public function insertarProducto($producto)
    {
        Logger::logInfo("Inicia insertarProducto", [
            'codigo' => $producto['codigo'] ?? null,
            'nombre' => $producto['nombre'] ?? null,
            'id_categoria' => $producto['id_categoria'] ?? null,
            'estado' => $producto['estado'] ?? 'ACTIVO'
        ]);

        try {
            $id_categoria = isset($producto['id_categoria']) ? (int) $producto['id_categoria'] : null;
            $codigo = trim($producto['codigo'] ?? '');
            $nombre = trim($producto['nombre'] ?? '');
            $descripcion = $producto['descripcion'] ?? null;

            $precio_compra = isset($producto['precio_compra']) ? (float) $producto['precio_compra'] : 0;
            $precio_venta = isset($producto['precio_venta']) ? (float) $producto['precio_venta'] : 0;

            $stock = isset($producto['stock']) ? (int) $producto['stock'] : 0;
            $stock_minimo = isset($producto['stock_minimo']) ? (int) $producto['stock_minimo'] : 0;

            $unidad_medida = trim($producto['unidad_medida'] ?? 'UNIDAD');
            $estado = $producto['estado'] ?? 'ACTIVO';

            $stmt = $this->dbh->prepare("
                INSERT INTO productos (
                    id_categoria,
                    codigo,
                    nombre,
                    descripcion,
                    precio_compra,
                    precio_venta,
                    stock,
                    stock_minimo,
                    unidad_medida,
                    estado,
                    fecha_creacion
                )
                VALUES (
                    :id_categoria,
                    :codigo,
                    :nombre,
                    :descripcion,
                    :precio_compra,
                    :precio_venta,
                    :stock,
                    :stock_minimo,
                    :unidad_medida,
                    :estado,
                    NOW()
                )
                RETURNING id
            ");

            $stmt->execute([
                'id_categoria' => $id_categoria,
                'codigo' => $codigo,
                'nombre' => $nombre,
                'descripcion' => $descripcion,
                'precio_compra' => $precio_compra,
                'precio_venta' => $precio_venta,
                'stock' => $stock,
                'stock_minimo' => $stock_minimo,
                'unidad_medida' => $unidad_medida,
                'estado' => $estado
            ]);

            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $id = $row ? (int) $row['id'] : (int) $this->dbh->lastInsertId();

            Logger::logInfo("Finaliza insertarProducto", [
                'id' => $id,
                'affected' => $stmt->rowCount()
            ]);

            return [
                "ok" => true,
                "id" => $id
            ];

        } catch (\Throwable $e) {
            Logger::logError("Error en insertarProducto: " . $e->getMessage(), [
                'metodo' => __METHOD__,
                'producto' => [
                    'codigo' => $producto['codigo'] ?? null,
                    'nombre' => $producto['nombre'] ?? null,
                    'id_categoria' => $producto['id_categoria'] ?? null
                ]
            ]);
            throw $e;
        }
    }

    public function actualizarProducto($producto)
    {
        $id = (int) ($producto['id'] ?? 0);

        Logger::logInfo("Inicia actualizarProducto", [
            'id' => $id,
            'codigo' => $producto['codigo'] ?? null,
            'estado' => $producto['estado'] ?? null
        ]);

        try {
            $id_categoria = array_key_exists('id_categoria', $producto) ? (int) $producto['id_categoria'] : null;
            $codigo = trim($producto['codigo'] ?? '');
            $nombre = trim($producto['nombre'] ?? '');
            $descripcion = $producto['descripcion'] ?? null;

            $precio_compra = isset($producto['precio_compra']) ? (float) $producto['precio_compra'] : 0;
            $precio_venta = isset($producto['precio_venta']) ? (float) $producto['precio_venta'] : 0;

            $stock = isset($producto['stock']) ? (int) $producto['stock'] : 0;
            $stock_minimo = isset($producto['stock_minimo']) ? (int) $producto['stock_minimo'] : 0;

            $unidad_medida = trim($producto['unidad_medida'] ?? 'UNIDAD');

            // Si no viene estado, no lo actualizamos
            $estado = $producto['estado'] ?? null;

            if ($estado === null) {
                $stmt = $this->dbh->prepare("
                    UPDATE productos
                    SET id_categoria = :id_categoria,
                        codigo = :codigo,
                        nombre = :nombre,
                        descripcion = :descripcion,
                        precio_compra = :precio_compra,
                        precio_venta = :precio_venta,
                        stock = :stock,
                        stock_minimo = :stock_minimo,
                        unidad_medida = :unidad_medida,
                        fecha_actualizacion = NOW()
                    WHERE id = :id
                      AND estado <> :estado_eliminado
                ");

                $stmt->execute([
                    'id_categoria' => $id_categoria,
                    'codigo' => $codigo,
                    'nombre' => $nombre,
                    'descripcion' => $descripcion,
                    'precio_compra' => $precio_compra,
                    'precio_venta' => $precio_venta,
                    'stock' => $stock,
                    'stock_minimo' => $stock_minimo,
                    'unidad_medida' => $unidad_medida,
                    'id' => $id,
                    'estado_eliminado' => 'ELIMINADO'
                ]);
            } else {
                $stmt = $this->dbh->prepare("
                    UPDATE productos
                    SET id_categoria = :id_categoria,
                        codigo = :codigo,
                        nombre = :nombre,
                        descripcion = :descripcion,
                        precio_compra = :precio_compra,
                        precio_venta = :precio_venta,
                        stock = :stock,
                        stock_minimo = :stock_minimo,
                        unidad_medida = :unidad_medida,
                        estado = :estado,
                        fecha_actualizacion = NOW()
                    WHERE id = :id
                      AND estado <> :estado_eliminado
                ");

                $stmt->execute([
                    'id_categoria' => $id_categoria,
                    'codigo' => $codigo,
                    'nombre' => $nombre,
                    'descripcion' => $descripcion,
                    'precio_compra' => $precio_compra,
                    'precio_venta' => $precio_venta,
                    'stock' => $stock,
                    'stock_minimo' => $stock_minimo,
                    'unidad_medida' => $unidad_medida,
                    'estado' => $estado,
                    'id' => $id,
                    'estado_eliminado' => 'ELIMINADO'
                ]);
            }

            $affected = $stmt->rowCount();

            Logger::logInfo("Finaliza actualizarProducto", [
                'id' => $id,
                'affected' => $affected
            ]);

            return [
                "ok" => $affected > 0,
                "id" => $id,
                "affected" => $affected
            ];

        } catch (\Throwable $e) {
            Logger::logError("Error en actualizarProducto: " . $e->getMessage(), [
                'metodo' => __METHOD__,
                'id' => $id
            ]);
            throw $e;
        }
    }

    public function eliminarProducto($id)
    {
        $id = (int) $id;

        Logger::logInfo("Inicia eliminarProducto", ['id' => $id]);

        try {
            $stmt = $this->dbh->prepare("
                UPDATE productos
                SET estado = :estado,
                    fecha_actualizacion = NOW()
                WHERE id = :id
                  AND estado <> :estado_eliminado
            ");

            $stmt->execute([
                'estado' => 'ELIMINADO',
                'id' => $id,
                'estado_eliminado' => 'ELIMINADO'
            ]);

            $affected = $stmt->rowCount();

            Logger::logInfo("Finaliza eliminarProducto", [
                'id' => $id,
                'affected' => $affected
            ]);

            return [
                "ok" => $affected > 0,
                "id" => $id,
                "affected" => $affected
            ];

        } catch (\Throwable $e) {
            Logger::logError("Error en eliminarProducto: " . $e->getMessage(), [
                'metodo' => __METHOD__,
                'id' => $id
            ]);
            throw $e;
        }
    }
}
