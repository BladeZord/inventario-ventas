<?php

namespace App\Config;
class Ambiente
{
    private static bool $loaded = false;

    /**
     * Carga las variables del archivo .env (solo la primera vez).
     */
    public static function load(string $path): void
    {
        if (!self::$loaded) {
            self::loadEnv($path);
            self::$loaded = true;
        }
    }

    public function __construct(string $path)
    {
        self::load($path);
    }

    private static function loadEnv(string $path): void
    {
        if (!is_file($path) || !is_readable($path)) {
            return;
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }
            if (str_starts_with($line, 'export ')) {
                $line = substr($line, 7);
            }

            $pos = strpos($line, '=');
            if ($pos === false) {
                continue;
            }

            $key = trim(substr($line, 0, $pos));
            $val = trim(substr($line, $pos + 1));

            if ($val !== '' && (
                ($val[0] === '"' && str_ends_with($val, '"')) ||
                ($val[0] === "'" && str_ends_with($val, "'"))
            )) {
                $val = substr($val, 1, -1);
            }

            $_ENV[$key] = $val;
            $_SERVER[$key] = $val;
            putenv("$key=$val");
        }
    }

    public static function get(string $key, $default = null)
    {
        $v = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);
        return ($v === false || $v === null || $v === '') ? $default : $v;
    }
}
