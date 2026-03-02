<?php

namespace App\Models;

use App\Config\Conexion;
use App\Utils\Logger;
use PDO;

class VentaModel extends Conexion
{
    public function __construct()
    {
        $this->conectar();
    }

    /**
     * Lista ventas con nombre del cliente (excluye anuladas/eliminadas si usas estado).
     */
    public function consultaVentas()
    {
        try {
            Logger::logInfo("Inicia consultaVentas");

            $stmt = $this->dbh->prepare("
                SELECT v.*,
                    c.nombres AS cliente_nombres,
                    c.apellidos AS cliente_apellidos,
                    CONCAT(COALESCE(c.nombres, ''), ' ', COALESCE(c.apellidos, '')) AS cliente_nombre
                FROM ventas v
                INNER JOIN clientes c ON c.id = v.id_cliente
                WHERE v.estado <> 'ELIMINADO'
                ORDER BY v.fecha_creacion DESC
            ");
            $stmt->execute();

            Logger::logInfo("Finaliza consultaVentas", ['count' => $stmt->rowCount()]);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Throwable $e) {
            Logger::logError("Error en consultaVentas: " . $e->getMessage(), ['metodo' => __METHOD__]);
            throw $e;
        }
    }

    /**
     * Obtiene una venta por ID con sus detalles y nombres de producto.
     */
    public function consultaVentaPorId($id)
    {
        try {
            Logger::logInfo("Inicia consultaVentaPorId", ['id' => (int) $id]);

            $stmt = $this->dbh->prepare("
                SELECT v.*,
                    c.nombres AS cliente_nombres,
                    c.apellidos AS cliente_apellidos,
                    CONCAT(COALESCE(c.nombres, ''), ' ', COALESCE(c.apellidos, '')) AS cliente_nombre
                FROM ventas v
                INNER JOIN clientes c ON c.id = v.id_cliente
                WHERE v.id = :id
                  AND v.estado <> 'ELIMINADO'
                LIMIT 1
            ");
            $stmt->execute(['id' => (int) $id]);
            $venta = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$venta) {
                Logger::logInfo("Finaliza consultaVentaPorId", ['id' => (int) $id, 'encontrado' => false]);
                return null;
            }

            $stmtDet = $this->dbh->prepare("
                SELECT d.*,
                    p.nombre AS producto_nombre
                FROM detalle_ventas d
                INNER JOIN productos p ON p.id = d.id_producto
                WHERE d.id_venta = :id_venta
                  AND d.estado <> 'ELIMINADO'
                ORDER BY d.id
            ");
            $stmtDet->execute(['id_venta' => (int) $id]);
            $venta['detalles'] = $stmtDet->fetchAll(PDO::FETCH_ASSOC);

            Logger::logInfo("Finaliza consultaVentaPorId", ['id' => (int) $id, 'encontrado' => true]);

            return $venta;
        } catch (\Throwable $e) {
            Logger::logError("Error en consultaVentaPorId: " . $e->getMessage(), ['metodo' => __METHOD__, 'id' => (int) $id]);
            throw $e;
        }
    }

    /**
     * Inserta venta y sus detalles en transacción.
     * $venta: id_cliente, numero_factura, subtotal, descuento, impuesto, total, metodo_pago, estado?
     * $detalles: array de { id_producto, cantidad, precio_unitario, descuento, total_linea }
     */
    public function insertarVenta(array $venta, array $detalles = [])
    {
        Logger::logInfo("Inicia insertarVenta", [
            'numero_factura' => $venta['numero_factura'] ?? null,
            'id_cliente' => $venta['id_cliente'] ?? null,
        ]);

        try {
            $id_cliente = (int) ($venta['id_cliente'] ?? 0);
            $numero_factura = trim($venta['numero_factura'] ?? '');
            $subtotal = isset($venta['subtotal']) ? (float) $venta['subtotal'] : 0;
            $descuento = isset($venta['descuento']) ? (float) $venta['descuento'] : 0;
            $impuesto = isset($venta['impuesto']) ? (float) $venta['impuesto'] : 0;
            $total = isset($venta['total']) ? (float) $venta['total'] : 0;
            $metodo_pago = trim($venta['metodo_pago'] ?? '') ?: null;
            $estado = $venta['estado'] ?? 'ACTIVO';

            $this->dbh->beginTransaction();

            $stmt = $this->dbh->prepare("
                INSERT INTO ventas (
                    id_cliente,
                    numero_factura,
                    subtotal,
                    descuento,
                    impuesto,
                    total,
                    metodo_pago,
                    estado,
                    fecha_creacion
                )
                VALUES (
                    :id_cliente,
                    :numero_factura,
                    :subtotal,
                    :descuento,
                    :impuesto,
                    :total,
                    :metodo_pago,
                    :estado,
                    NOW()
                )
            ");
            $stmt->execute([
                'id_cliente' => $id_cliente,
                'numero_factura' => $numero_factura,
                'subtotal' => $subtotal,
                'descuento' => $descuento,
                'impuesto' => $impuesto,
                'total' => $total,
                'metodo_pago' => $metodo_pago,
                'estado' => $estado,
            ]);

            $id_venta = (int) $this->dbh->lastInsertId();

            $stmtDet = $this->dbh->prepare("
                INSERT INTO detalle_ventas (
                    id_venta,
                    id_producto,
                    cantidad,
                    precio_unitario,
                    descuento,
                    total_linea,
                    estado,
                    fecha_creacion
                )
                VALUES (
                    :id_venta,
                    :id_producto,
                    :cantidad,
                    :precio_unitario,
                    :descuento,
                    :total_linea,
                    :estado,
                    NOW()
                )
            ");

            foreach ($detalles as $d) {
                $stmtDet->execute([
                    'id_venta' => $id_venta,
                    'id_producto' => (int) ($d['id_producto'] ?? 0),
                    'cantidad' => (int) ($d['cantidad'] ?? 0),
                    'precio_unitario' => (float) ($d['precio_unitario'] ?? 0),
                    'descuento' => (float) ($d['descuento'] ?? 0),
                    'total_linea' => (float) ($d['total_linea'] ?? 0),
                    'estado' => $d['estado'] ?? 'ACTIVO',
                ]);
            }

            $this->dbh->commit();

            Logger::logInfo("Finaliza insertarVenta", ['id' => $id_venta, 'detalles' => count($detalles)]);

            return [
                'ok' => true,
                'id' => $id_venta,
            ];
        } catch (\Throwable $e) {
            if ($this->dbh->inTransaction()) {
                $this->dbh->rollBack();
            }
            Logger::logError("Error en insertarVenta: " . $e->getMessage(), [
                'metodo' => __METHOD__,
                'numero_factura' => $venta['numero_factura'] ?? null,
            ]);
            throw $e;
        }
    }

    /**
     * Actualiza cabecera de venta (no modifica detalles en esta versión).
     */
    public function actualizarVenta(array $venta)
    {
        $id = (int) ($venta['id'] ?? 0);

        Logger::logInfo("Inicia actualizarVenta", ['id' => $id]);

        try {
            $id_cliente = (int) ($venta['id_cliente'] ?? 0);
            $numero_factura = trim($venta['numero_factura'] ?? '');
            $subtotal = (float) ($venta['subtotal'] ?? 0);
            $descuento = (float) ($venta['descuento'] ?? 0);
            $impuesto = (float) ($venta['impuesto'] ?? 0);
            $total = (float) ($venta['total'] ?? 0);
            $metodo_pago = trim($venta['metodo_pago'] ?? '') ?: null;
            $estado = $venta['estado'] ?? 'ACTIVO';

            $stmt = $this->dbh->prepare("
                UPDATE ventas
                SET id_cliente = :id_cliente,
                    numero_factura = :numero_factura,
                    subtotal = :subtotal,
                    descuento = :descuento,
                    impuesto = :impuesto,
                    total = :total,
                    metodo_pago = :metodo_pago,
                    estado = :estado,
                    fecha_actualizacion = NOW()
                WHERE id = :id
                  AND estado <> 'ELIMINADO'
            ");
            $stmt->execute([
                'id_cliente' => $id_cliente,
                'numero_factura' => $numero_factura,
                'subtotal' => $subtotal,
                'descuento' => $descuento,
                'impuesto' => $impuesto,
                'total' => $total,
                'metodo_pago' => $metodo_pago,
                'estado' => $estado,
                'id' => $id,
            ]);

            $affected = $stmt->rowCount();
            Logger::logInfo("Finaliza actualizarVenta", ['id' => $id, 'affected' => $affected]);

            return [
                'ok' => $affected > 0,
                'id' => $id,
                'affected' => $affected,
            ];
        } catch (\Throwable $e) {
            Logger::logError("Error en actualizarVenta: " . $e->getMessage(), ['metodo' => __METHOD__, 'id' => $id]);
            throw $e;
        }
    }

    /**
     * Anula una venta (estado = ANULADO). No borra físicamente.
     */
    public function anularVenta($id)
    {
        $id = (int) $id;

        Logger::logInfo("Inicia anularVenta", ['id' => $id]);

        try {
            $stmt = $this->dbh->prepare("
                UPDATE ventas
                SET estado = 'ANULADO',
                    fecha_actualizacion = NOW()
                WHERE id = :id
                  AND estado <> 'ELIMINADO'
            ");
            $stmt->execute(['id' => $id]);
            $affected = $stmt->rowCount();

            Logger::logInfo("Finaliza anularVenta", ['id' => $id, 'affected' => $affected]);

            return [
                'ok' => $affected > 0,
                'id' => $id,
                'affected' => $affected,
            ];
        } catch (\Throwable $e) {
            Logger::logError("Error en anularVenta: " . $e->getMessage(), ['metodo' => __METHOD__, 'id' => $id]);
            throw $e;
        }
    }
}
