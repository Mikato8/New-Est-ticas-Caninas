<?php

declare(strict_types=1);

use App\Core\Application;
use App\Core\Config;
use App\Core\Env;
use App\Core\Session;
use App\Core\View;

if (!function_exists('env')) {
    function env(string $clave, ?string $porDefecto = null): ?string
    {
        return Env::get($clave, $porDefecto);
    }
}

if (!function_exists('env_bool')) {
    function env_bool(string $clave, bool $porDefecto = false): bool
    {
        $valor = Env::get($clave);
        return $valor === null || $valor === '' ? $porDefecto : filter_var($valor, FILTER_VALIDATE_BOOLEAN);
    }
}

if (!function_exists('env_int')) {
    function env_int(string $clave, int $porDefecto = 0): int
    {
        $valor = Env::get($clave);
        return $valor === null || $valor === '' ? $porDefecto : (int) $valor;
    }
}

if (!function_exists('config')) {
    function config(string $clave, mixed $porDefecto = null): mixed
    {
        return Config::get($clave, $porDefecto);
    }
}

if (!function_exists('app')) {
    function app(): Application
    {
        return Application::instancia();
    }
}

if (!function_exists('e')) {
    function e(mixed $valor): string
    {
        return htmlspecialchars((string) $valor, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

if (!function_exists('url')) {
    function url(string $ruta = ''): string
    {
        $base = rtrim((string) config('url', ''), '/');
        $ruta = '/' . ltrim($ruta, '/');
        return $base . ($ruta === '/' ? '' : $ruta);
    }
}

if (!function_exists('asset')) {
    function asset(string $ruta): string
    {
        $ruta = '/' . ltrim($ruta, '/');
        $archivo = BASE_PATH . '/public' . $ruta;
        $version = is_file($archivo) ? (string) filemtime($archivo) : '1';
        return url($ruta) . '?v=' . $version;
    }
}

if (!function_exists('activo')) {
    function activo(string $prefijo): string
    {
        $ruta = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
        $ruta = '/' . trim($ruta, '/');
        $prefijo = '/' . trim($prefijo, '/');
        if ($prefijo === '/') {
            return $ruta === '/' ? 'activo' : '';
        }
        return str_starts_with($ruta, $prefijo) ? 'activo' : '';
    }
}

if (!function_exists('csrf_token')) {
    function csrf_token(): string
    {
        return Session::csrfToken();
    }
}

if (!function_exists('csrf_field')) {
    function csrf_field(): string
    {
        return '<input type="hidden" name="_token" value="' . e(csrf_token()) . '">';
    }
}

if (!function_exists('viejo')) {
    function viejo(string $clave, string $porDefecto = ''): string
    {
        $datos = Session::get('_viejo', []);
        $valor = is_array($datos) ? ($datos[$clave] ?? $porDefecto) : $porDefecto;
        return is_scalar($valor) ? (string) $valor : $porDefecto;
    }
}

if (!function_exists('redirigir')) {
    function redirigir(string $ruta, int $estado = 303): \App\Core\Response
    {
        return (new \App\Core\Response('', $estado))->conCabecera('Location', url($ruta));
    }
}

if (!function_exists('flash')) {
    function flash(string $tipo, string $mensaje): void
    {
        \App\Core\Session::flash($tipo, $mensaje);
    }
}

if (!function_exists('limitar_texto')) {
    function limitar_texto(string $texto, int $limite = 160): string
    {
        return mb_strlen($texto) > $limite ? mb_substr($texto, 0, $limite - 1) . '…' : $texto;
    }
}

if (!function_exists('fecha')) {
    function fecha(?string $iso, string $formato = 'd M Y'): string
    {
        if ($iso === null || $iso === '') {
            return '';
        }
        $marca = strtotime($iso);
        return $marca === false ? '' : date($formato, $marca);
    }
}

if (!function_exists('fecha_larga')) {
    function fecha_larga(?string $iso): string
    {
        if ($iso === null || $iso === '') {
            return '';
        }
        $meses = ['', 'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
        $marca = strtotime($iso);
        if ($marca === false) {
            return '';
        }
        return (int) date('j', $marca) . ' de ' . $meses[(int) date('n', $marca)] . ' de ' . date('Y', $marca);
    }
}

if (!function_exists('dia_semana')) {
    function dia_semana(?string $iso, bool $corto = false): string
    {
        if ($iso === null || $iso === '') {
            return '';
        }
        $marca = strtotime($iso);
        if ($marca === false) {
            return '';
        }
        $dias = $corto
            ? ['dom', 'lun', 'mar', 'mié', 'jue', 'vie', 'sáb']
            : ['domingo', 'lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado'];
        return $dias[(int) date('w', $marca)];
    }
}

if (!function_exists('hora_ampm')) {
    function hora_ampm(?string $hora): string
    {
        if ($hora === null || $hora === '') {
            return '';
        }
        $marca = strtotime($hora);
        if ($marca === false) {
            return (string) $hora;
        }
        return mb_strtolower(date('g:i a', $marca));
    }
}

if (!function_exists('dinero')) {
    function dinero(float|int|string|null $monto, bool $decimales = true): string
    {
        $numero = (float) ($monto ?? 0);
        return '$' . number_format($numero, $decimales ? 2 : 0, '.', ',');
    }
}

if (!function_exists('precio')) {
    function precio(float|int|string|null $monto): string
    {
        $numero = round((float) ($monto ?? 0), 2);
        return '$' . number_format($numero, fmod($numero, 1.0) === 0.0 ? 0 : 2, '.', ',');
    }
}

if (!function_exists('iniciales')) {
    function iniciales(string $nombre): string
    {
        $partes = preg_split('/\s+/', trim($nombre)) ?: [];
        $letras = '';
        foreach ($partes as $parte) {
            if ($parte !== '' && mb_strlen($letras) < 2) {
                $letras .= mb_strtoupper(mb_substr($parte, 0, 1));
            }
        }
        return $letras === '' ? 'M' : $letras;
    }
}

if (!function_exists('whatsapp_link')) {
    function whatsapp_link(string $texto = 'Hola, quiero información sobre la estética canina.'): string
    {
        $numero = preg_replace('/\D+/', '', (string) config('contacto.whatsapp', '')) ?: '';
        if ($numero === '') {
            return '';
        }
        if (strlen($numero) === 10) {
            $numero = '52' . $numero;
        }
        return 'https://wa.me/' . $numero . '?text=' . rawurlencode($texto);
    }
}

if (!function_exists('icono')) {
    function icono(string $nombre, string $clase = ''): string
    {
        static $trazos = [
            'pata' => '<circle cx="7" cy="9" r="2" fill="currentColor"/><circle cx="12" cy="6.5" r="2" fill="currentColor"/><circle cx="17" cy="9" r="2" fill="currentColor"/><path d="M7.5 15.5c0-2.5 2-4.5 4.5-4.5s4.5 2 4.5 4.5c0 2.2-1.8 3.5-4.5 3.5s-4.5-1.3-4.5-3.5Z" fill="currentColor"/>',
            'perro' => '<path d="M6 9c0-2.8 2.4-5 5.5-5h1C15.6 4 18 6.2 18 9v5.5c0 2.5-2 4.5-4.5 4.5H10a4 4 0 0 1-4-4V9Z" fill="none" stroke="currentColor" stroke-width="1.6"/><path d="M6 8 3.5 5.5v5L6 9M18 8l2.5-2.5v5L18 9" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/><circle cx="9.5" cy="10" r=".9" fill="currentColor"/><circle cx="14.5" cy="10" r=".9" fill="currentColor"/><path d="M10.5 14.5c1-.8 2-.8 3 0" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/><path d="M12 12v1.5" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/>',
            'gato' => '<path d="M5 10 6 4l4 3h4l4-3 1 6v5a5 5 0 0 1-5 5h-4a5 5 0 0 1-5-5v-5Z" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/><circle cx="9.5" cy="11" r=".9" fill="currentColor"/><circle cx="14.5" cy="11" r=".9" fill="currentColor"/><path d="M12 14v1.2M10.5 16.2c1 .7 2 .7 3 0M4 13H2M20 13h2" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/>',
            'bano' => '<path d="M4 12h16v3a5 5 0 0 1-5 5H9a5 5 0 0 1-5-5v-3Z" fill="none" stroke="currentColor" stroke-width="1.6"/><path d="M6 12V6a3 3 0 0 1 6 0M5 20l-1 1M19 20l1 1" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/><circle cx="15" cy="7" r="1.4" fill="none" stroke="currentColor" stroke-width="1.4"/><circle cx="18.5" cy="4.5" r="1" fill="none" stroke="currentColor" stroke-width="1.3"/>',
            'tijeras' => '<circle cx="6.5" cy="17.5" r="2.5" fill="none" stroke="currentColor" stroke-width="1.6"/><circle cx="17.5" cy="17.5" r="2.5" fill="none" stroke="currentColor" stroke-width="1.6"/><path d="M8.5 15.5 18 4M15.5 15.5 6 4" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>',
            'una' => '<path d="M4 17c3.5 0 4.5-3 7-3s3.5 3 7 3" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/><path d="M8 10V6.5a2 2 0 0 1 4 0V10M12 10V5.5a2 2 0 0 1 4 0V10" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/><path d="M6 10h12l-1 4H7l-1-4Z" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>',
            'oido' => '<path d="M8 8a5 5 0 0 1 10 0c0 4-5 4.5-5 8a3 3 0 0 0 5 2.2" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><path d="M11 8.5a2.5 2.5 0 0 1 5 0c0 2-2.5 2.5-2.5 4.5" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>',
            'cepillo' => '<path d="M5 19c2-1 2-3 3.5-4.5L17 6l2 2-8.5 8.5C9 18 7 18 5 19Z" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/><path d="m15 4 1.5-1.5M18 7l1.5-1.5M19 11l2-1" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>',
            'secadora' => '<path d="M4 9a5 5 0 0 1 5-5h5.5a3 3 0 0 1 0 6H9a5 5 0 0 1-5-5v4Z" fill="none" stroke="currentColor" stroke-width="1.6"/><path d="M4 9v4h6V9M14 6l5 3-5 3M8 13v5a2 2 0 0 0 4 0v-2" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/><path d="M18 13c1 2 1 4 0 6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>',
            'mono' => '<path d="M12 12c-2-4-5-5-7-3s0 6 3 5c2-.6 3-2 4-2Zm0 0c2-4 5-5 7-3s0 6-3 5c-2-.6-3-2-4-2Z" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/><circle cx="12" cy="12" r="2" fill="none" stroke="currentColor" stroke-width="1.5"/>',
            'agenda' => '<rect x="4" y="5" width="16" height="15" rx="2" fill="none" stroke="currentColor" stroke-width="1.6"/><path d="M4 9h16M8 3v4M16 3v4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/><path d="m9 14 2 2 4-4" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>',
            'clientes' => '<circle cx="9" cy="8" r="3.2" fill="none" stroke="currentColor" stroke-width="1.6"/><path d="M3 20c0-3.3 2.7-6 6-6s6 2.7 6 6M16 5.5a3 3 0 0 1 0 5.8M17 14c2.4.5 4 2.5 4 5" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>',
            'caja' => '<path d="m4 8 8-4 8 4v8l-8 4-8-4V8Z" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/><path d="m4 8 8 4 8-4M12 12v8" fill="none" stroke="currentColor" stroke-width="1.5"/>',
            'grafica' => '<path d="M4 20V4M4 20h16" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/><path d="M8 16v-4M12 16V8M16 16v-6M20 16V6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>',
            'dinero' => '<rect x="3" y="6" width="18" height="12" rx="2" fill="none" stroke="currentColor" stroke-width="1.6"/><circle cx="12" cy="12" r="2.5" fill="none" stroke="currentColor" stroke-width="1.6"/><path d="M6 9v6M18 9v6" stroke="currentColor" stroke-width="1.4"/>',
            'escudo' => '<path d="M12 3 5 6v6c0 4.4 3 7.6 7 9 4-1.4 7-4.6 7-9V6l-7-3Z" fill="none" stroke="currentColor" stroke-width="1.6"/><path d="m9 12 2 2 4-4" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>',
            'libro' => '<path d="M4 5h7v15H4zM13 5h7v15h-7z" fill="none" stroke="currentColor" stroke-width="1.6"/><path d="M4 9h7M13 9h7" stroke="currentColor" stroke-width="1.2"/>',
            'chispa' => '<path d="M12 3l1.8 5.2L19 10l-5.2 1.8L12 17l-1.8-5.2L5 10l5.2-1.8L12 3Z" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/><path d="M18 15l.8 2.2L21 18l-2.2.8L18 21l-.8-2.2L15 18l2.2-.8L18 15Z" fill="currentColor"/>',
            'whatsapp' => '<path d="M20 11.5a8 8 0 0 1-11.8 7L4 20l1.5-4.1A8 8 0 1 1 20 11.5Z" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><path d="M9 8.5c0 4 2.5 6.5 6.3 6.8l1-1.6-2.1-1-.9.8c-1-.3-1.9-1.2-2.2-2.2l.8-.9-1-2.1-1.9.2Z" fill="currentColor"/>',
            'telefono' => '<path d="M7 3h3l1.5 4.5-2 1.5a12 12 0 0 0 5.5 5.5l1.5-2L21 14v3a3 3 0 0 1-3 3A15 15 0 0 1 4 6a3 3 0 0 1 3-3Z" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>',
            'correo' => '<rect x="3" y="5" width="18" height="14" rx="2" fill="none" stroke="currentColor" stroke-width="1.6"/><path d="m3.5 7 8.5 6 8.5-6" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>',
            'pin' => '<path d="M12 21s7-6.2 7-11a7 7 0 1 0-14 0c0 4.8 7 11 7 11Z" fill="none" stroke="currentColor" stroke-width="1.6"/><circle cx="12" cy="10" r="2.5" fill="none" stroke="currentColor" stroke-width="1.6"/>',
            'reloj' => '<circle cx="12" cy="12" r="9" fill="none" stroke="currentColor" stroke-width="1.6"/><path d="M12 7v5l3.5 2" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>',
            'check' => '<path d="m4 12 5 5L20 6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>',
            'alerta' => '<path d="M12 3 2 21h20L12 3Z" fill="none" stroke="currentColor" stroke-width="1.7"/><path d="M12 9v6M12 18v.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>',
            'estrella' => '<path d="m12 3 2.7 5.6 6.1.9-4.4 4.3 1 6.1-5.4-2.9-5.4 2.9 1-6.1L3.2 9.5l6.1-.9L12 3Z" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>',
            'menu' => '<path d="M4 7h16M4 12h16M4 17h16" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>',
            'cerrar' => '<path d="M6 6l12 12M18 6 6 18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>',
            'buscar' => '<circle cx="11" cy="11" r="7" fill="none" stroke="currentColor" stroke-width="1.8"/><path d="m16.5 16.5 4 4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>',
            'flecha' => '<path d="M4 12h15M13 6l6 6-6 6" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>',
            'calculadora' => '<rect x="5" y="3" width="14" height="18" rx="2" fill="none" stroke="currentColor" stroke-width="1.6"/><path d="M8 7h8M8 12h2M12 12h2M16 12h.5M8 16h2M12 16h2M16 16h.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>',
            'comunidad' => '<path d="M4 6h12v8H9l-5 4V6Z" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/><path d="M16 9h4v9l-3-2h-5" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>',
            'directorio' => '<path d="M4 21V6l8-3 8 3v15M2 21h20" fill="none" stroke="currentColor" stroke-width="1.6"/><path d="M8 9h2M14 9h2M8 13h2M14 13h2M10 21v-4h4v4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>',
            'producto' => '<path d="M9 3h6v3l2 3v10a2 2 0 0 1-2 2H9a2 2 0 0 1-2-2V9l2-3V3Z" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/><path d="M7 11h10M10 15h4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>',
            'salir' => '<path d="M14 4H6a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8M10 12h11M17 8l4 4-4 4" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>',
            'config' => '<circle cx="12" cy="12" r="3" fill="none" stroke="currentColor" stroke-width="1.6"/><path d="M12 2v3M12 19v3M2 12h3M19 12h3M4.9 4.9 7 7M17 17l2.1 2.1M19.1 4.9 17 7M7 17l-2.1 2.1" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>',
            'editar' => '<path d="m4 20 4-1 10-10-3-3L5 16l-1 4Z" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/><path d="m13 6 3 3" stroke="currentColor" stroke-width="1.5"/>',
            'basura' => '<path d="M4 7h16M9 7V4h6v3M6 7l1 14h10l1-14M10 11v6M14 11v6" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>',
            'mas' => '<path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"/>',
            'descargar' => '<path d="M12 3v12M7 10l5 5 5-5M4 20h16" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>',
            'ojo' => '<path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6-10-6-10-6Z" fill="none" stroke="currentColor" stroke-width="1.6"/><circle cx="12" cy="12" r="2.5" fill="none" stroke="currentColor" stroke-width="1.6"/>',
            'corazon' => '<path d="M12 20s-8-4.6-8-10a4.5 4.5 0 0 1 8-2.8A4.5 4.5 0 0 1 20 10c0 5.4-8 10-8 10Z" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>',
            'termometro' => '<path d="M10 4a2 2 0 0 1 4 0v9.3a4.5 4.5 0 1 1-4 0V4Z" fill="none" stroke="currentColor" stroke-width="1.6"/><path d="M12 9v7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>',
            'agua' => '<path d="M12 3s6 7 6 11a6 6 0 0 1-12 0c0-4 6-11 6-11Z" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>',
            'viento' => '<path d="M3 8h10a3 3 0 1 0-3-3M3 12h14a3 3 0 1 1-3 3M3 16h8" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>',
            'premio' => '<circle cx="12" cy="9" r="5" fill="none" stroke="currentColor" stroke-width="1.6"/><path d="m8.5 13-2 8 5.5-3 5.5 3-2-8" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>',
            'camara' => '<path d="M4 8h3l2-2h6l2 2h3v11H4V8Z" fill="none" stroke="currentColor" stroke-width="1.6"/><circle cx="12" cy="13" r="3.5" fill="none" stroke="currentColor" stroke-width="1.6"/>',
            'subir' => '<path d="M12 20V8M7 11l5-5 5 5M4 4h16" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>',
            'tarjeta' => '<rect x="3" y="5" width="18" height="14" rx="2" fill="none" stroke="currentColor" stroke-width="1.6"/><path d="M3 10h18M7 15h4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>',
            'ubicacion' => '<path d="M12 21s7-6.2 7-11a7 7 0 1 0-14 0c0 4.8 7 11 7 11Z" fill="none" stroke="currentColor" stroke-width="1.6"/><circle cx="12" cy="10" r="2.5" fill="none" stroke="currentColor" stroke-width="1.6"/>',
        ];
        $svg = $trazos[$nombre] ?? $trazos['pata'];
        return '<svg class="' . e($clase) . '" viewBox="0 0 24 24" aria-hidden="true" focusable="false">' . $svg . '</svg>';
    }
}

if (!function_exists('parcial')) {
    function parcial(string $nombre, array $datos = []): void
    {
        View::parcial($nombre, $datos);
    }
}

if (!function_exists('ruta')) {
    function ruta(string $ruta): string
    {
        return url('/' . ltrim($ruta, '/'));
    }
}

if (!function_exists('tamano_label')) {
    function tamano_label(?string $clave): string
    {
        $mapa = [
            'chico' => 'Chico', 'mediano' => 'Mediano', 'grande' => 'Grande', 'gigante' => 'Gigante',
            'todos' => 'Todos', 'pequeno' => 'Chico', 'small' => 'Chico', 'medium' => 'Mediano',
            'large' => 'Grande', 'xl' => 'Gigante',
        ];
        return $mapa[strtolower((string) $clave)] ?? 'Todos';
    }
}

if (!function_exists('estado_cita_label')) {
    function estado_cita_label(?string $clave): string
    {
        $mapa = [
            'pendiente' => 'Pendiente', 'confirmada' => 'Confirmada', 'en_proceso' => 'En proceso',
            'completada' => 'Completada', 'cancelada' => 'Cancelada', 'no_asistio' => 'No asistió',
            'publicado' => 'Publicado', 'aprobado' => 'Aprobado', 'rechazado' => 'Rechazado',
            'activo' => 'Activo', 'suspendido' => 'Suspendido', 'pagada' => 'Pagada', 'pagado' => 'Pagado',
        ];
        return $mapa[(string) $clave] ?? ucfirst(str_replace('_', ' ', (string) $clave));
    }
}

if (!function_exists('rol_label')) {
    function rol_label(?string $clave): string
    {
        $mapa = ['admin' => 'Administrador', 'editor' => 'Editor', 'estilista' => 'Estilista', 'cliente' => 'Cliente'];
        return $mapa[(string) $clave] ?? 'Cliente';
    }
}

if (!function_exists('estado_badge')) {
    function estado_badge(?string $clave): string
    {
        $clave = (string) $clave;
        $permitidos = ['pendiente', 'confirmada', 'en_proceso', 'completada', 'cancelada', 'no_asistio', 'publicado', 'aprobado', 'rechazado', 'activo', 'suspendido', 'pagada', 'pagado'];
        $clase = in_array($clave, $permitidos, true) ? ' badge--' . str_replace('_', '-', $clave) : '';
        return '<span class="badge' . $clase . '">' . e(estado_cita_label($clave)) . '</span>';
    }
}

if (!function_exists('estrellas')) {
    function estrellas(float|int $valor, int $total = 5): string
    {
        $html = '';
        for ($i = 1; $i <= $total; $i++) {
            $html .= '<span class="estrella' . ($i <= $valor ? ' llena' : '') . '">★</span>';
        }
        return $html;
    }
}

if (!function_exists('es_verdadero')) {
    function es_verdadero(mixed $valor): bool
    {
        return $valor === true || $valor === 1 || $valor === '1' || $valor === 't' || $valor === 'true';
    }
}


if (!function_exists('view')) {
    /** @param array<string, mixed> $datos */
    function view(string $plantilla, array $datos = [], string $layout = 'layout'): string
    {
        return View::render($plantilla, $datos, $layout);
    }
}

if (!function_exists('errores')) {
    /** @return array<string, string> */
    function errores(): array
    {
        $datos = Session::get('_errores', []);
        return is_array($datos) ? $datos : [];
    }
}


if (!function_exists('view_panel')) {
    /** @param array<string, mixed> $datos */
    function view_panel(string $plantilla, array $datos = []): string
    {
        return View::render($plantilla, $datos, 'panel/layout');
    }
}

if (!function_exists('view_admin')) {
    /** @param array<string, mixed> $datos */
    function view_admin(string $plantilla, array $datos = []): string
    {
        return View::render($plantilla, $datos, 'admin/layout');
    }
}
