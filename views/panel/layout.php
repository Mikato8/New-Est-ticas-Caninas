<?php
declare(strict_types=1);

use App\Core\Auth;
use App\Core\Session;

$u = Auth::usuario();
$ruta = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$activo = static function (string $p) use ($ruta): string {
    return $ruta === $p || ($p !== '/panel' && str_starts_with($ruta, $p . '/')) ? 'activo' : '';
};
$esAdmin = ($u['rol'] ?? '') === 'admin';
$puedeModerar = in_array((string) ($u['rol'] ?? ''), ['admin', 'editor'], true);
$flashExito = Session::flash('exito');
$flashError = Session::flash('error');
?>
<!doctype html>
<html lang="es-MX">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($titulo ?? 'Panel') ?> · <?= e(config('short')) ?></title>
    <meta name="robots" content="noindex, nofollow">
    <meta name="theme-color" content="#163a36">
    <link rel="icon" type="image/svg+xml" href="<?= asset('/assets/img/favicon.svg') ?>">
    <link rel="stylesheet" href="<?= asset('/assets/fonts/fonts.css') ?>">
    <link rel="stylesheet" href="<?= asset('/assets/css/app.css') ?>">
    <script defer src="<?= asset('/assets/js/app.js') ?>"></script>
</head>
<body>
<div class="panel">
    <aside class="panel__barra" id="panel-barra">
        <a class="panel__marca" href="<?= url('/panel') ?>">
            <img src="<?= asset('/assets/img/logo-mikato.png') ?>" alt="Mikato">
            <small>Panel</small>
        </a>
        <nav class="panel__nav" aria-label="Panel">
            <a class="<?= $activo('/panel') ?>" href="<?= url('/panel') ?>"><?= icono('grafica') ?> Resumen</a>
            <a class="<?= $activo('/panel/agenda') ?>" href="<?= url('/panel/agenda') ?>"><?= icono('agenda') ?> Agenda</a>
            <a class="<?= $activo('/panel/clientes') ?>" href="<?= url('/panel/clientes') ?>"><?= icono('clientes') ?> Clientes</a>
            <a class="<?= $activo('/panel/mascotas') ?>" href="<?= url('/panel/mascotas') ?>"><?= icono('pata') ?> Mascotas</a>
            <span class="separador-nav">Catálogo</span>
            <a class="<?= $activo('/panel/servicios') ?>" href="<?= url('/panel/servicios') ?>"><?= icono('tijeras') ?> Servicios</a>
            <a class="<?= $activo('/panel/productos') ?>" href="<?= url('/panel/productos') ?>"><?= icono('producto') ?> Productos</a>
            <a class="<?= $activo('/panel/paquetes') ?>" href="<?= url('/panel/paquetes') ?>"><?= icono('caja') ?> Paquetes</a>
            <span class="separador-nav">Dinero</span>
            <a class="<?= $activo('/panel/ventas') ?>" href="<?= url('/panel/ventas') ?>"><?= icono('dinero') ?> Ventas</a>
            <a class="<?= $activo('/panel/gastos') ?>" href="<?= url('/panel/gastos') ?>"><?= icono('tarjeta') ?> Gastos</a>
            <a class="<?= $activo('/panel/reportes') ?>" href="<?= url('/panel/reportes') ?>"><?= icono('grafica') ?> Reportes</a>
            <?php if ($esAdmin): ?>
                <span class="separador-nav">Administración</span>
                <a class="<?= $activo('/panel/usuarios') ?>" href="<?= url('/panel/usuarios') ?>"><?= icono('perfil') ?> Usuarios</a>
                <a class="<?= $activo('/panel/configuracion') ?>" href="<?= url('/panel/configuracion') ?>"><?= icono('config') ?> Configuración</a>
            <?php endif; ?>
            <?php if ($puedeModerar): ?>
                <a class="<?= $activo('/admin') ?>" href="<?= url('/admin') ?>"><?= icono('escudo') ?> Moderación</a>
            <?php endif; ?>
        </nav>
        <div class="panel__pie">
            <div class="flex-centro" style="margin-bottom:10px;">
                <span class="avatar" style="width:32px;height:32px;font-size:.75rem;background:var(--oro);color:var(--tinta);"><?= e(iniciales((string) ($u['nombre'] ?? 'M'))) ?></span>
                <div><strong style="font-size:.82rem;display:block;"><?= e($u['nombre'] ?? '') ?></strong><small style="font-size:.68rem;color:rgba(251,247,239,.55);"><?= e(rol_label((string) ($u['rol'] ?? ''))) ?></small></div>
            </div>
            <a href="<?= url('/') ?>" style="display:block;color:rgba(251,247,239,.7);text-decoration:none;font-size:.78rem;margin-bottom:8px;"><?= icono('flecha') ?> Ver el sitio</a>
            <form method="post" action="<?= url('/salir') ?>"><?= csrf_field() ?><button type="submit" class="boton boton--pequeno boton--fantasma boton--bloque"><?= icono('salir') ?> Salir</button></form>
        </div>
    </aside>

    <div>
        <div class="panel__barra-movil">
            <img src="<?= asset('/assets/img/logo-mikato.png') ?>" alt="Mikato">
            <strong style="font-size:.82rem;">Panel</strong>
            <button type="button" data-panel-menu aria-label="Abrir menú"><?= icono('menu') ?></button>
        </div>
        <main class="panel__contenido" id="contenido">
            <?php if ($flashExito !== null): ?>
                <div class="aviso-flash aviso-flash--exito" role="status"><?= icono('check') ?><div><?= e($flashExito) ?></div></div>
            <?php endif; ?>
            <?php if ($flashError !== null): ?>
                <div class="aviso-flash aviso-flash--error" role="alert"><?= icono('alerta') ?><div><?= e($flashError) ?></div></div>
            <?php endif; ?>
            <?= $contenido ?>
        </main>
    </div>
</div>
</body>
</html>
