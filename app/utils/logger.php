<?php

namespace App\Utils;

use App\Config\Ambiente;
class Logger
{
    private static ?string $logPath = null;

    /**
     * Directorio raíz del proyecto (donde está app/, public/, .env).
     */
    private static function getProjectRoot(): string
    {
        return realpath(__DIR__ . '/../..') ?: __DIR__ . '/../..';
    }

    /**
     * Obtener ruta de logs desde .env. Si DIR_LOG es relativa, se resuelve desde la raíz del proyecto.
     * Si la ruta no es escribible, se usa project_root/logs.
     */
    private static function getLogPath(): string
    {
        if (self::$logPath !== null) {
            return self::$logPath;
        }

        Ambiente::load(__DIR__ . '/../../.env');

        $root = self::getProjectRoot();
        $defaultLogPath = $root . DIRECTORY_SEPARATOR . 'logs';
        $path = Ambiente::get('DIR_LOG', $defaultLogPath);
        $path = trim($path, '/\\');

        // Ruta relativa (ej: "logs" o "logs/categoria") → resolver desde raíz del proyecto
        if ($path !== '' && $path[0] !== '/' && (strlen($path) < 2 || $path[1] !== ':')) {
            $path = $root . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);
        }

        self::$logPath = rtrim($path, '/\\');

        $existeOSeCreo = is_dir(self::$logPath) || @mkdir(self::$logPath, 0777, true);
        if (!$existeOSeCreo || (is_dir(self::$logPath) && !is_writable(self::$logPath))) {
            self::$logPath = $defaultLogPath;
            if (!is_dir(self::$logPath)) {
                @mkdir(self::$logPath, 0777, true);
            }
        }

        return self::$logPath;
    }

    public static function logInfo(string $message, array $context = []): void
    {
        self::write('INFO', $message, $context);
    }

    public static function logError(string $message, array $context = []): void
    {
        self::write('ERROR', $message, $context);
    }

    public static function logWarning(string $message, array $context = []): void
    {
        self::write('WARNING', $message, $context);
    }

    public static function logDebug(string $message, array $context = []): void
    {
        self::write('DEBUG', $message, $context);
    }

    private static function write(string $level, string $message, array $context): void
    {
        try {
            $logPath = self::getLogPath();

            if (!is_dir($logPath)) {
                @mkdir($logPath, 0777, true);
            }

            $file = $logPath . DIRECTORY_SEPARATOR . 'app-' . date('Y-m-d') . '.log';
            $date = date('Y-m-d H:i:s');

            $contextJson = !empty($context)
                ? json_encode($context, JSON_UNESCAPED_UNICODE)
                : '';

            $line = "[{$date}] {$level}: {$message} {$contextJson}" . PHP_EOL;

            @file_put_contents($file, $line, FILE_APPEND | LOCK_EX);
        } catch (\Throwable $e) {
            // Fallback a error_log para no romper la aplicación si falla el archivo
            error_log("Logger: no se pudo escribir en {$logPath}: " . $e->getMessage());
        }
    }
}
