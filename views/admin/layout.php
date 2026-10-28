<?php
declare(strict_types=1);

use App\Core\Auth;
use App\Core\Session;

$u = Auth::usuario();
$ruta = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$activo = static function (string $p) use ($ruta): string {
    return $ruta === $p || ($p !== '/admin' && str_starts_with($ruta, $p . '/')) ? 'activo' : '';
};
$flashExito = Session::flash('exito');
$flashError = Session::flash('error');
?>
<!doctype html>
<html lang="es-MX">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($titulo ?? 'Administración') ?> · <?= e(config('short')) ?></title>
    <meta name="robots" content="noindex, nofollow">
    <meta name="theme-color" content="#2b1d1a">
    <link rel="icon" type="image/svg+xml" href="<?= asset('/assets/img/favicon.svg') ?>">
    <link rel="stylesheet" href="<?= asset('/assets/fonts/fonts.css') ?>">
    <link rel="stylesheet" href="<?= asset('/assets/css/app.css') ?>">
    <script defer src="<?= asset('/assets/js/app.js') ?>"></script>
</head>
<body>
<div class="panel panel--admin">
    <aside class="panel__barra" id="panel-barra">
        <a class="panel__marca" href="<?= url('/admin') ?>">
            <img src="<?= asset('/assets/img/logo-mikato.png') ?>" alt="Mikato">
            <small>Moderación</small>
        </a>
        <nav class="panel__nav" aria-label="Administración">
            <a class="<?= $activo('/admin') ?>" href="<?= url('/admin') ?>"><?= icono('grafica') ?> Resumen</a>
            <a class="<?= $activo('/admin/publicaciones') ?>" href="<?= url('/admin/publicaciones') ?>"><?= icono('comunidad') ?> Publicaciones</a>
            <a class="<?= $activo('/admin/comentarios') ?>" href="<?= url('/admin/comentarios') ?>"><?= icono('libro') ?> Comentarios</a>
            <a class="<?= $activo('/admin/negocios') ?>" href="<?= url('/admin/negocios') ?>"><?= icono('directorio') ?> Directorio</a>
            <a class="<?= $activo('/admin/mensajes') ?>" href="<?= url('/admin/mensajes') ?>"><?= icono('correo') ?> Mensajes</a>
            <a class="<?= $activo('/admin/citas') ?>" href="<?= url('/admin/citas') ?>"><?= icono('agenda') ?> Citas</a>
            <span class="separador-nav">Salón</span>
            <a href="<?= url('/panel') ?>"><?= icono('tijeras') ?> Panel de gestión</a>
            <a href="<?= url('/') ?>" target="_blank"><?= icono('flecha') ?> Ver el sitio</a>
        </nav>
        <div class="panel__pie">
            <div class="flex-centro" style="margin-bottom:10px;">
                <span class="avatar" style="width:32px;height:32px;font-size:.75rem;background:var(--coral);color:#fff;"><?= e(iniciales((string) ($u['nombre'] ?? 'M'))) ?></span>
                <div><strong style="font-size:.82rem;display:block;"><?= e($u['nombre'] ?? '') ?></strong><small style="font-size:.68rem;color:rgba(251,247,239,.55);"><?= e(rol_label((string) ($u['rol'] ?? ''))) ?></small></div>
            </div>
            <form method="post" action="<?= url('/salir') ?>"><?= csrf_field() ?><button type="submit" class="boton boton--pequeno boton--fantasma boton--bloque"><?= icono('salir') ?> Salir</button></form>
        </div>
    </aside>
    <div>
        <div class="panel__barra-movil">
            <img src="<?= asset('/assets/img/logo-mikato.png') ?>" alt="Mikato">
            <strong style="font-size:.82rem;">Moderación</strong>
            <button type="button" data-panel-menu aria-label="Abrir menú"><?= icono('menu') ?></button>
        </div>
        <main class="panel__contenido" id="contenido">
            <?php if ($flashExito !== null): ?><div class="aviso-flash aviso-flash--exito" role="status"><?= icono('check') ?><div><?= e($flashExito) ?></div></div><?php endif; ?>
            <?php if ($flashError !== null): ?><div class="aviso-flash aviso-flash--error" role="alert"><?= icono('alerta') ?><div><?= e($flashError) ?></div></div><?php endif; ?>
            <?= $contenido ?>
        </main>
    </div>
</div>
</body>
</html>
