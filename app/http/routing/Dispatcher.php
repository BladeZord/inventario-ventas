<?php
namespace App\Http\Routing;

use ReflectionMethod;

final class Dispatcher
{
    public function __construct(private ControllerFactory $factory) {}

    /**
     * @param array{0:string,1:string} $handler [ControllerClass, method]
     */
    public function dispatch(array $handler, array $params, array $body): mixed
    {
        [$controllerClass, $method] = $handler;

        $controller = $this->factory->make($controllerClass);

        if (!method_exists($controller, $method)) {
            throw new \RuntimeException("Método no existe: {$controllerClass}::{$method}");
        }

        $ref = new ReflectionMethod($controller, $method);
        $args = [];

        foreach ($ref->getParameters() as $p) {
            $name = $p->getName();
            $type = $p->getType();

            // 1) Si el parámetro se llama "body" o es array: pasa $body
            if ($name === 'body' || ($type && $type->getName() === 'array')) {
                $args[] = $body;
                continue;
            }

            // 2) Si existe en params (id, id_categoria, etc)
            if (array_key_exists($name, $params)) {
                $args[] = $params[$name];
                continue;
            }

            // 3) Fallback: null/default
            if ($p->isDefaultValueAvailable()) {
                $args[] = $p->getDefaultValue();
                continue;
            }

            $args[] = null;
        }

        return $ref->invokeArgs($controller, $args);
    }
}
