<?php

namespace App\Models;

use App\Config\Conexion;
use App\Utils\Logger;
use PDO;

class UnidadMedidaModel extends Conexion
{
    public function __construct()
    {
        $this->conectar();
    }

    public function consultaUnidadesMedida()
    {
        try {
            Logger::logInfo("Inicia consultaUnidadesMedida");

            $stmt = $this->dbh->prepare("
                SELECT *
                FROM unidades_medida
                WHERE estado <> :estado
                ORDER BY nombre
            ");
            $stmt->execute(['estado' => 'ELIMINADO']);

            Logger::logInfo("Finaliza consultaUnidadesMedida", ['count' => $stmt->rowCount()]);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Throwable $e) {
            Logger::logError("Error en consultaUnidadesMedida: " . $e->getMessage(), ['metodo' => __METHOD__]);
            throw $e;
        }
    }

    public function consultaUnidadMedidaPorId($id)
    {
        try {
            $stmt = $this->dbh->prepare("
                SELECT *
                FROM unidades_medida
                WHERE id = :id
                  AND estado <> :estado_eliminado
                LIMIT 1
            ");
            $stmt->execute([
                'id' => (int) $id,
                'estado_eliminado' => 'ELIMINADO'
            ]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\Throwable $e) {
            Logger::logError("Error en consultaUnidadMedidaPorId: " . $e->getMessage(), ['metodo' => __METHOD__, 'id' => (int) $id]);
            throw $e;
        }
    }

    public function insertarUnidadMedida(array $unidad)
    {
        Logger::logInfo("Inicia insertarUnidadMedida", ['nombre' => $unidad['nombre'] ?? null]);

        try {
            $nombre = trim($unidad['nombre'] ?? '');
            $descripcion = trim($unidad['descripcion'] ?? '') ?: null;
            $estado = $unidad['estado'] ?? 'ACTIVO';

            $stmt = $this->dbh->prepare("
                INSERT INTO unidades_medida (nombre, descripcion, estado, fecha_creacion)
                VALUES (:nombre, :descripcion, :estado, NOW())
            ");
            $stmt->execute([
                'nombre' => $nombre,
                'descripcion' => $descripcion,
                'estado' => $estado
            ]);

            $id = (int) $this->dbh->lastInsertId();
            Logger::logInfo("Finaliza insertarUnidadMedida", ['id' => $id]);

            return ['ok' => true, 'id' => $id];
        } catch (\Throwable $e) {
            Logger::logError("Error en insertarUnidadMedida: " . $e->getMessage(), ['metodo' => __METHOD__]);
            throw $e;
        }
    }

    public function actualizarUnidadMedida(array $unidad)
    {
        $id = (int) ($unidad['id'] ?? 0);
        Logger::logInfo("Inicia actualizarUnidadMedida", ['id' => $id]);

        try {
            $nombre = trim($unidad['nombre'] ?? '');
            $descripcion = trim($unidad['descripcion'] ?? '') ?: null;
            $estado = $unidad['estado'] ?? null;

            if ($estado === null) {
                $stmt = $this->dbh->prepare("
                    UPDATE unidades_medida
                    SET nombre = :nombre, descripcion = :descripcion, fecha_actualizacion = NOW()
                    WHERE id = :id AND estado <> 'ELIMINADO'
                ");
                $stmt->execute(['nombre' => $nombre, 'descripcion' => $descripcion, 'id' => $id]);
            } else {
                $stmt = $this->dbh->prepare("
                    UPDATE unidades_medida
                    SET nombre = :nombre, descripcion = :descripcion, estado = :estado, fecha_actualizacion = NOW()
                    WHERE id = :id AND estado <> 'ELIMINADO'
                ");
                $stmt->execute(['nombre' => $nombre, 'descripcion' => $descripcion, 'estado' => $estado, 'id' => $id]);
            }

            $affected = $stmt->rowCount();
            Logger::logInfo("Finaliza actualizarUnidadMedida", ['id' => $id, 'affected' => $affected]);
            return ['ok' => $affected > 0, 'id' => $id, 'affected' => $affected];
        } catch (\Throwable $e) {
            Logger::logError("Error en actualizarUnidadMedida: " . $e->getMessage(), ['metodo' => __METHOD__, 'id' => $id]);
            throw $e;
        }
    }

    public function eliminarUnidadMedida($id)
    {
        $id = (int) $id;
        Logger::logInfo("Inicia eliminarUnidadMedida", ['id' => $id]);

        try {
            $stmt = $this->dbh->prepare("
                UPDATE unidades_medida
                SET estado = 'ELIMINADO', fecha_actualizacion = NOW()
                WHERE id = :id AND estado <> 'ELIMINADO'
            ");
            $stmt->execute(['id' => $id]);
            $affected = $stmt->rowCount();
            Logger::logInfo("Finaliza eliminarUnidadMedida", ['id' => $id, 'affected' => $affected]);
            return ['ok' => $affected > 0, 'id' => $id, 'affected' => $affected];
        } catch (\Throwable $e) {
            Logger::logError("Error en eliminarUnidadMedida: " . $e->getMessage(), ['metodo' => __METHOD__, 'id' => $id]);
            throw $e;
        }
    }
}
