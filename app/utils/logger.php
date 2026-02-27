<?php

namespace App\Utils;

use App\Config\Ambiente;
class Logger
{
    private static ?string $logPath = null;

    /**
     * Obtener ruta de logs desde .env
     */
    private static function getLogPath(): string
    {
        if (self::$logPath !== null) {
            return self::$logPath;
        }

        // cargar .env si no está cargado
        Ambiente::load(__DIR__ . '/../../.env');

        $path = Ambiente::get('DIR_LOG', __DIR__ . '/../../logs');

        self::$logPath = rtrim($path, '/\\');

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
        $logPath = self::getLogPath();

        if (!is_dir($logPath)) {
            mkdir($logPath, 0777, true);
        }

        $file = $logPath . '/app-' . date('Y-m-d') . '.log';
        $date = date('Y-m-d H:i:s');

        $contextJson = !empty($context)
            ? json_encode($context, JSON_UNESCAPED_UNICODE)
            : '';

        $line = "[{$date}] {$level}: {$message} {$contextJson}" . PHP_EOL;

        file_put_contents($file, $line, FILE_APPEND | LOCK_EX);
    }
}
