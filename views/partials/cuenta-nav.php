<?php
declare(strict_types=1);
use App\Core\Auth;
$u = Auth::usuario();
$ruta = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$activo = static function (string $p) use ($ruta): string {
    return $ruta === $p || ($p !== '/mi-cuenta' && str_starts_with($ruta, $p)) ? 'activo' : '';
};
?>
<nav class="cuenta__nav" aria-label="Mi cuenta">
    <div class="cuenta__perfil">
        <?php if (!empty($u['avatar'])): ?>
            <img src="<?= e($u['avatar']) ?>" alt="" class="avatar avatar--grande" style="object-fit:cover;">
        <?php else: ?>
            <span class="avatar avatar--grande avatar--oro"><?= e(iniciales((string) ($u['nombre'] ?? 'M'))) ?></span>
        <?php endif; ?>
        <div><strong><?= e($u['nombre'] ?? '') ?></strong><small><?= e(rol_label((string) ($u['rol'] ?? 'cliente'))) ?></small></div>
    </div>
    <a class="<?= $activo('/mi-cuenta') ?>" href="<?= url('/mi-cuenta') ?>"><?= icono('grafica') ?> Resumen</a>
    <a class="<?= $activo('/mi-cuenta/mascotas') ?>" href="<?= url('/mi-cuenta/mascotas') ?>"><?= icono('pata') ?> Mis mascotas</a>
    <a class="<?= $activo('/mi-cuenta/citas') ?>" href="<?= url('/mi-cuenta/citas') ?>"><?= icono('agenda') ?> Mis citas</a>
    <a class="<?= $activo('/perfil') ?>" href="<?= url('/perfil') ?>"><?= icono('perfil') ?> Mi perfil</a>
    <a href="<?= url('/agenda') ?>"><?= icono('mas') ?> Agendar cita</a>
    <a href="<?= url('/guias') ?>"><?= icono('libro') ?> Guías</a>
    <form method="post" action="<?= url('/salir') ?>" style="margin-top:8px;border-top:1px solid var(--borde);padding-top:8px;">
        <?= csrf_field() ?>
        <button type="submit" class="boton boton--contorno boton--pequeno boton--bloque"><?= icono('salir') ?> Salir</button>
    </form>
</nav>
