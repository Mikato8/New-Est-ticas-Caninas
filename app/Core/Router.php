<?php

declare(strict_types=1);

namespace App\Core;

final class Router
{
    /** @var list<array{metodo: string, regex: string, handler: mixed}> */
    private array $rutas = [];

    public function get(string $patron, mixed $handler): void
    {
        $this->agregar('GET', $patron, $handler);
    }

    public function post(string $patron, mixed $handler): void
    {
        $this->agregar('POST', $patron, $handler);
    }

    private function agregar(string $metodo, string $patron, mixed $handler): void
    {
        $patron = '/' . trim($patron, '/');
        $partes = preg_split('/(\{[a-zA-Z_][a-zA-Z0-9_]*\})/', $patron, -1, PREG_SPLIT_DELIM_CAPTURE) ?: [];
        $regex = '';
        foreach ($partes as $parte) {
            if (preg_match('/^\{([a-zA-Z_][a-zA-Z0-9_]*)\}$/', $parte, $m)) {
                $regex .= '(?P<' . $m[1] . '>[^/]+)';
            } else {
                $regex .= preg_quote($parte, '#');
            }
        }
        $this->rutas[] = ['metodo' => $metodo, 'regex' => '#^' . $regex . '$#', 'handler' => $handler];
    }

    public function dispatch(Request $peticion): Response
    {
        $rutaSolicitada = '/' . trim($peticion->ruta(), '/');
        $metodo = $peticion->metodo() === 'HEAD' ? 'GET' : $peticion->metodo();
        $rutaEncontrada = false;

        foreach ($this->rutas as $ruta) {
            if (!preg_match($ruta['regex'], $rutaSolicitada, $coincidencias)) {
                continue;
            }
            $rutaEncontrada = true;
            if ($ruta['metodo'] !== $metodo) {
                continue;
            }

            $parametros = [];
            foreach ($coincidencias as $clave => $valor) {
                if (is_string($clave)) {
                    $parametros[$clave] = $valor;
                }
            }

            return $this->normalizar($this->invocar($ruta['handler'], $peticion, $parametros));
        }

        if ($rutaEncontrada) {
            return Response::texto('Método no permitido', 405)->conCabecera('Allow', 'GET, POST');
        }

        throw new HttpException('La página que buscas no existe.', 404);
    }

    private function invocar(mixed $handler, Request $peticion, array $parametros): mixed
    {
        if (is_callable($handler)) {
            return $handler($peticion, $parametros);
        }
        if (is_array($handler) && count($handler) === 2) {
            [$clase, $metodo] = $handler;
            return (new $clase())->{$metodo}($peticion, $parametros);
        }
        throw new \LogicException('Manejador de ruta inválido.');
    }

    private function normalizar(mixed $resultado): Response
    {
        if ($resultado instanceof Response) {
            return $resultado;
        }
        if (is_string($resultado)) {
            return Response::html($resultado);
        }
        if (is_array($resultado)) {
            return Response::json($resultado);
        }
        throw new \LogicException('La ruta no devolvió una respuesta válida.');
    }
}
