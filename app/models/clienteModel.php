<?php

namespace App\Models;

use App\Config\Conexion;
use App\Utils\Logger;
use PDO;

class ClienteModel extends Conexion
{
    public function __construct()
    {
        $this->conectar();
    }

    public function consultaClientes()
    {
        try {
            Logger::logInfo("Inicia consultaClientes");

            $stmt = $this->dbh->prepare("
                SELECT *
                FROM clientes
                WHERE estado <> :estado
                ORDER BY apellidos, nombres
            ");
            $stmt->execute(['estado' => 'ELIMINADO']);

            Logger::logInfo("Finaliza consultaClientes", ['count' => $stmt->rowCount()]);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Throwable $e) {
            Logger::logError("Error en consultaClientes: " . $e->getMessage(), ['metodo' => __METHOD__]);
            throw $e;
        }
    }

    public function consultaClientePorId($id)
    {
        try {
            Logger::logInfo("Inicia consultaClientePorId", ['id' => (int) $id]);

            $stmt = $this->dbh->prepare("
                SELECT *
                FROM clientes
                WHERE id = :id
                  AND estado <> :estado_eliminado
                LIMIT 1
            ");
            $stmt->execute([
                'id' => (int) $id,
                'estado_eliminado' => 'ELIMINADO'
            ]);

            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            Logger::logInfo("Finaliza consultaClientePorId", ['id' => (int) $id, 'encontrado' => $row ? true : false]);

            return $row;
        } catch (\Throwable $e) {
            Logger::logError("Error en consultaClientePorId: " . $e->getMessage(), ['metodo' => __METHOD__, 'id' => (int) $id]);
            throw $e;
        }
    }

    public function insertarCliente(array $cliente)
    {
        Logger::logInfo("Inicia insertarCliente", [
            'identificacion' => $cliente['identificacion'] ?? null,
            'nombres' => $cliente['nombres'] ?? null,
        ]);

        try {
            $identificacion = trim($cliente['identificacion'] ?? '');
            $nombres = trim($cliente['nombres'] ?? '');
            $apellidos = trim($cliente['apellidos'] ?? '');
            $correo = trim($cliente['correo'] ?? '') ?: null;
            $telefono = trim($cliente['telefono'] ?? '') ?: null;
            $direccion = trim($cliente['direccion'] ?? '') ?: null;
            $estado = $cliente['estado'] ?? 'ACTIVO';

            $stmt = $this->dbh->prepare("
                INSERT INTO clientes (
                    identificacion,
                    nombres,
                    apellidos,
                    correo,
                    telefono,
                    direccion,
                    estado,
                    fecha_creacion
                )
                VALUES (
                    :identificacion,
                    :nombres,
                    :apellidos,
                    :correo,
                    :telefono,
                    :direccion,
                    :estado,
                    NOW()
                )
            ");
            $stmt->execute([
                'identificacion' => $identificacion,
                'nombres' => $nombres,
                'apellidos' => $apellidos,
                'correo' => $correo,
                'telefono' => $telefono,
                'direccion' => $direccion,
                'estado' => $estado,
            ]);

            $id = (int) $this->dbh->lastInsertId();

            Logger::logInfo("Finaliza insertarCliente", ['id' => $id, 'affected' => $stmt->rowCount()]);

            return [
                'ok' => true,
                'id' => $id,
            ];
        } catch (\Throwable $e) {
            Logger::logError("Error en insertarCliente: " . $e->getMessage(), [
                'metodo' => __METHOD__,
                'identificacion' => $cliente['identificacion'] ?? null,
            ]);
            throw $e;
        }
    }

    public function actualizarCliente(array $cliente)
    {
        $id = (int) ($cliente['id'] ?? 0);

        Logger::logInfo("Inicia actualizarCliente", ['id' => $id]);

        try {
            $identificacion = trim($cliente['identificacion'] ?? '');
            $nombres = trim($cliente['nombres'] ?? '');
            $apellidos = trim($cliente['apellidos'] ?? '');
            $correo = trim($cliente['correo'] ?? '') ?: null;
            $telefono = trim($cliente['telefono'] ?? '') ?: null;
            $direccion = trim($cliente['direccion'] ?? '') ?: null;
            $estado = $cliente['estado'] ?? null;

            if ($estado === null) {
                $stmt = $this->dbh->prepare("
                    UPDATE clientes
                    SET identificacion = :identificacion,
                        nombres = :nombres,
                        apellidos = :apellidos,
                        correo = :correo,
                        telefono = :telefono,
                        direccion = :direccion,
                        fecha_actualizacion = NOW()
                    WHERE id = :id
                      AND estado <> :estado_eliminado
                ");
                $stmt->execute([
                    'identificacion' => $identificacion,
                    'nombres' => $nombres,
                    'apellidos' => $apellidos,
                    'correo' => $correo,
                    'telefono' => $telefono,
                    'direccion' => $direccion,
                    'id' => $id,
                    'estado_eliminado' => 'ELIMINADO',
                ]);
            } else {
                $stmt = $this->dbh->prepare("
                    UPDATE clientes
                    SET identificacion = :identificacion,
                        nombres = :nombres,
                        apellidos = :apellidos,
                        correo = :correo,
                        telefono = :telefono,
                        direccion = :direccion,
                        estado = :estado,
                        fecha_actualizacion = NOW()
                    WHERE id = :id
                      AND estado <> :estado_eliminado
                ");
                $stmt->execute([
                    'identificacion' => $identificacion,
                    'nombres' => $nombres,
                    'apellidos' => $apellidos,
                    'correo' => $correo,
                    'telefono' => $telefono,
                    'direccion' => $direccion,
                    'estado' => $estado,
                    'id' => $id,
                    'estado_eliminado' => 'ELIMINADO',
                ]);
            }

            $affected = $stmt->rowCount();
            Logger::logInfo("Finaliza actualizarCliente", ['id' => $id, 'affected' => $affected]);

            return [
                'ok' => $affected > 0,
                'id' => $id,
                'affected' => $affected,
            ];
        } catch (\Throwable $e) {
            Logger::logError("Error en actualizarCliente: " . $e->getMessage(), ['metodo' => __METHOD__, 'id' => $id]);
            throw $e;
        }
    }

    public function eliminarCliente($id)
    {
        $id = (int) $id;

        Logger::logInfo("Inicia eliminarCliente", ['id' => $id]);

        try {
            $stmt = $this->dbh->prepare("
                UPDATE clientes
                SET estado = :estado,
                    fecha_actualizacion = NOW()
                WHERE id = :id
                  AND estado <> :estado_eliminado
            ");
            $stmt->execute([
                'estado' => 'ELIMINADO',
                'id' => $id,
                'estado_eliminado' => 'ELIMINADO',
            ]);

            $affected = $stmt->rowCount();
            Logger::logInfo("Finaliza eliminarCliente", ['id' => $id, 'affected' => $affected]);

            return [
                'ok' => $affected > 0,
                'id' => $id,
                'affected' => $affected,
            ];
        } catch (\Throwable $e) {
            Logger::logError("Error en eliminarCliente: " . $e->getMessage(), ['metodo' => __METHOD__, 'id' => $id]);
            throw $e;
        }
    }
}
