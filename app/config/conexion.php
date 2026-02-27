<?php

namespace App\Config;

use App\Utils\Logger;
use PDO;
use Throwable;

class Conexion
{
    protected $dbh = null;

    protected function conectar()
    {
        if ($this->dbh instanceof PDO) {
            return $this->dbh;
        }

        Ambiente::load(__DIR__ . '/../../.env');

        $host = Ambiente::get('DB_HOST', '127.0.0.1');
        $port = Ambiente::get('DB_PORT', '5432');
        $db   = Ambiente::get('DB_NAME', '');
        $user = Ambiente::get('DB_USERNAME', '');
        $pass = Ambiente::get('DB_PASSWORD', '');

        try {
            $dsn = "pgsql:host={$host};port={$port};dbname={$db}";

            $this->dbh = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);

            return $this->dbh;

        } catch (Throwable $e) {

            Logger::logError('Error de conexión a BD', [
                'host' => $host,
                'db' => $db,
                'error' => $e->getMessage()
            ]);

            // mensaje genérico al usuario
            die("Error DB");
        }
    }
}
