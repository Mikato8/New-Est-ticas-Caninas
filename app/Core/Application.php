<?php

declare(strict_types=1);

namespace App\Core;

final class Application
{
    private static ?self $instancia = null;

    public function __construct(private readonly Router $router)
    {
        self::$instancia = $this;
    }

    public static function instancia(): self
    {
        if (self::$instancia === null) {
            throw new \RuntimeException('La aplicación no se ha inicializado.');
        }
        return self::$instancia;
    }

    public function router(): Router
    {
        return $this->router;
    }

    public function run(): void
    {
        Session::start();
        header_remove('X-Powered-By');

        $peticion = Request::desdeGlobales();

        try {
            $respuesta = $this->router->dispatch($peticion);
        } catch (HttpException $e) {
            $respuesta = Response::html(
                View::render('paginas/error', [
                    'titulo' => $e->estado() === 404 ? 'Página no encontrada' : 'Solicitud inválida',
                    'descripcion' => $e->getMessage(),
                    'codigo' => $e->estado(),
                    'mensaje' => $e->getMessage(),
                ]),
                $e->estado()
            );
        } catch (\Throwable $e) {
            Logger::error('Error no controlado', [
                'mensaje' => $e->getMessage(),
                'archivo' => $e->getFile(),
                'linea' => $e->getLine(),
                'ruta' => $peticion->ruta(),
            ]);

            $debug = (bool) config('debug', false);
            $respuesta = Response::html(
                View::render('paginas/error', [
                    'titulo' => 'Algo salió mal',
                    'descripcion' => 'No fue posible procesar la solicitud.',
                    'codigo' => 500,
                    'mensaje' => $debug ? $e->getMessage() : 'Intenta de nuevo en unos segundos.',
                ]),
                500
            );
        }

        if ($peticion->metodo() === 'GET') {
            Session::forget('_errores');
        }

        $this->cabecerasSeguridad();
        $respuesta->enviar();
    }

    private function cabecerasSeguridad(): void
    {
        if (headers_sent()) {
            return;
        }

        $segura = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');

        header('X-Content-Type-Options: nosniff');
        header('Referrer-Policy: strict-origin-when-cross-origin');
        header('X-Frame-Options: DENY');
        header('Permissions-Policy: geolocation=(), microphone=(), camera=()');
        header(
            "Content-Security-Policy: default-src 'self'; " .
            "script-src 'none'; " .
            "style-src 'self' 'unsafe-inline'; " .
            "img-src 'self' data:; font-src 'self'; " .
            "connect-src 'self'; " .
            "worker-src 'none'; " .
            "form-action 'self'; frame-ancestors 'none'; base-uri 'self'"
        );

        if ($segura) {
            header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
        }
    }
}
