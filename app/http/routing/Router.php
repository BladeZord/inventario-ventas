<?php
namespace App\Http\Routing;

final class Router
{
    /** @var array<int, array{0:string,1:string,2:array}> */
    private array $routes;

    public function __construct(array $routes)
    {
        $this->routes = $routes;
    }

    /**
     * @return array{handler: array{0:string,1:string}, params: array<string, mixed>}|null
     */
    public function match(string $method, string $path): ?array
    {
        $method = strtoupper($method);
        $path = '/' . trim($path, '/');

        foreach ($this->routes as $route) {
            [$routeMethod, $pattern, $handler] = $route;
            if (strtoupper($routeMethod) !== $method) continue;

            [$regex, $paramNames] = $this->compile($pattern);

            if (preg_match($regex, $path, $m)) {
                $params = [];
                foreach ($paramNames as $name) {
                    $val = $m[$name] ?? null;
                    if (is_string($val) && ctype_digit($val)) $val = (int)$val;
                    $params[$name] = $val;
                }
                return ['handler' => $handler, 'params' => $params];
            }
        }

        return null;
    }

    /**
     * Soporta {id} y {:id} (dos puntos opcional).
     * @return array{0:string,1:array<int,string>} [regex, paramNames]
     */
    private function compile(string $pattern): array
    {
        $pattern = '/' . trim($pattern, '/');
        $paramNames = [];

        // Reemplaza {id} o {:id} por grupos nombrados (?P<id>[^/]+)
        $regex = preg_replace_callback(
            '#\{(:?)([a-zA-Z_][a-zA-Z0-9_]*)\}#',
            function ($matches) use (&$paramNames) {
                $name = $matches[2];
                $paramNames[] = $name;
                return '(?P<' . $name . '>[^/]+)';
            },
            $pattern
        );

        return ['#^' . $regex . '$#u', $paramNames];
    }
}
