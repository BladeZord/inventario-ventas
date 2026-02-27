<?php
namespace App\Models;

use App\Config\Conexion;
use App\Utils\Logger;
use PDO;

class CategoriaModel extends Conexion
{
    public function __construct()
    {
        $this->conectar();
    }

    public function consultaCategoria()
    {
        try {
            Logger::logInfo("Inicia consultaCategoria");

            $stmt = $this->dbh->prepare("
            SELECT *
            FROM public.categorias
            WHERE estado <> :estado
            --ORDER BY id DESC
        ");
            $stmt->execute(['estado' => 'ELIMINADO']);

            Logger::logInfo("Finaliza consultaCategoria");

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (\Throwable $e) {
            Logger::logError('Error en consultaCategoria: ' . $e->getMessage(), [
                'metodo' => __METHOD__,
            ]);
            throw $e;
        }
    }
    public function consultaCategoriaPorId($id)
    {
        $stmt = $this->dbh->prepare("
        SELECT *
        FROM public.categorias
        WHERE id = :id
          AND estado <> :estado_eliminado
        LIMIT 1
    ");

        $stmt->execute([
            'id' => (int) $id,
            'estado_eliminado' => 'ELIMINADO'
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function insertarCategoria($categoria)
    {
        Logger::logInfo("Inicia insertarCategoria", [
            'nombre' => $categoria['nombre'] ?? null,
            'estado' => $categoria['estado'] ?? 'ACTIVO',
        ]);

        try {
            $nombre = trim($categoria['nombre'] ?? '');
            $descripcion = $categoria['descripcion'] ?? null;
            $estado = $categoria['estado'] ?? 'ACTIVO';

            $stmt = $this->dbh->prepare("
            INSERT INTO public.categorias (
                nombre,
                descripcion,
                estado,
                fecha_creacion
            )
            VALUES (:nombre, :descripcion, :estado, NOW())
            RETURNING id
        ");

            $stmt->execute([
                'nombre' => $nombre,
                'descripcion' => $descripcion,
                'estado' => $estado
            ]);

            $id = (int) $stmt->fetchColumn();

            Logger::logInfo("Finaliza insertarCategoria", [
                'id' => $id,
                'affected' => $stmt->rowCount()
            ]);

            return [
                "ok" => true,
                "id" => $id
            ];
        } catch (\Throwable $e) {
            Logger::logError("Error en insertarCategoria: " . $e->getMessage(), [
                'metodo' => __METHOD__,
                'categoria' => [
                    'nombre' => $categoria['nombre'] ?? null,
                    'estado' => $categoria['estado'] ?? null,
                ]
            ]);
            throw $e;
        }
    }

    public function actualizarCategoria($categoria)
    {
        $id = (int) ($categoria['id'] ?? 0);

        Logger::logInfo("Inicia actualizarCategoria", [
            'id' => $id,
            'estado' => $categoria['estado'] ?? null
        ]);

        try {
            $nombre = trim($categoria['nombre'] ?? '');
            $descripcion = $categoria['descripcion'] ?? null;
            $estado = $categoria['estado'] ?? null;

            if ($estado === null) {
                $stmt = $this->dbh->prepare("
                UPDATE public.categorias
                SET nombre = :nombre,
                    descripcion = :descripcion,
                    fecha_actualizacion = NOW()
                WHERE id = :id
                  AND estado <> :estado_eliminado
            ");

                $stmt->execute([
                    'nombre' => $nombre,
                    'descripcion' => $descripcion,
                    'id' => $id,
                    'estado_eliminado' => 'ELIMINADO'
                ]);
            } else {
                $stmt = $this->dbh->prepare("
                UPDATE public.categorias
                SET nombre = :nombre,
                    descripcion = :descripcion,
                    estado = :estado,
                    fecha_actualizacion = NOW()
                WHERE id = :id
                  AND estado <> :estado_eliminado
            ");

                $stmt->execute([
                    'nombre' => $nombre,
                    'descripcion' => $descripcion,
                    'estado' => $estado,
                    'id' => $id,
                    'estado_eliminado' => 'ELIMINADO'
                ]);
            }

            $affected = $stmt->rowCount();

            Logger::logInfo("Finaliza actualizarCategoria", [
                'id' => $id,
                'affected' => $affected
            ]);

            return [
                "ok" => $affected > 0,
                "id" => $id,
                "affected" => $affected
            ];
        } catch (\Throwable $e) {
            Logger::logError("Error en actualizarCategoria: " . $e->getMessage(), [
                'metodo' => __METHOD__,
                'id' => $id,
                'categoria' => [
                    'nombre' => $categoria['nombre'] ?? null,
                    'estado' => $categoria['estado'] ?? null,
                ]
            ]);
            throw $e;
        }
    }

    public function eliminarCategoria($id)
    {
        $id = (int) $id;

        Logger::logInfo("Inicia eliminarCategoria", [
            'id' => $id
        ]);

        try {
            $stmt = $this->dbh->prepare("
            UPDATE public.categorias
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

            Logger::logInfo("Finaliza eliminarCategoria", [
                'id' => $id,
                'affected' => $affected
            ]);

            return [
                "ok" => $affected > 0,
                "id" => $id,
                "affected" => $affected
            ];
        } catch (\Throwable $e) {
            Logger::logError("Error en eliminarCategoria: " . $e->getMessage(), [
                'metodo' => __METHOD__,
                'id' => $id
            ]);
            throw $e;
        }
    }
}
