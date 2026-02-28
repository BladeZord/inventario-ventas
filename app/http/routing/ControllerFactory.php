<?php
namespace App\Http\Routing;

final class ControllerFactory
{
    /** @var array<string, callable():object> */
    private array $bindings;

    /** @var array<string, object> */
    private array $instances = [];

    /**
     * @param array<string, callable():object> $bindings
     */
    public function __construct(array $bindings)
    {
        $this->bindings = $bindings;
    }

    public function make(string $controllerClass): object
    {
        // Cache (opcional)
        if (isset($this->instances[$controllerClass])) {
            return $this->instances[$controllerClass];
        }

        if (!isset($this->bindings[$controllerClass])) {
            throw new \RuntimeException("No hay binding para el controlador: {$controllerClass}");
        }

        $controller = ($this->bindings[$controllerClass])();
        $this->instances[$controllerClass] = $controller;

        return $controller;
    }
}
