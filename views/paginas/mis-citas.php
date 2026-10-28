<?php
declare(strict_types=1);
$proximas = array_values(array_filter($citas, static fn (array $c): bool => $c['fecha'] >= date('Y-m-d') && in_array($c['estado'], ['pendiente', 'confirmada'], true)));
$pasadas = array_values(array_filter($citas, static fn (array $c): bool => !in_array($c, $proximas, true)));
?>
<div class="contenedor">
    <nav class="migas" aria-label="Ruta"><a href="<?= url('/') ?>">Inicio</a><span>/</span><a href="<?= url('/mi-cuenta') ?>">Mi cuenta</a><span>/</span>Mis citas</nav>
    <header class="portada-interior">
        <span class="mono texto-oro">Mis citas</span>
        <h1>Próximas citas e historial.</h1>
        <p>Consulta horarios, servicios y estados. Si necesitas cancelar con menos de 24 horas, llámanos.</p>
    </header>
</div>

<section class="seccion seccion--compacta">
    <div class="contenedor">
        <div class="cuenta">
            <?php parcial('cuenta-nav'); ?>
            <div>
                <div class="flex-entre mb-2">
                    <h2 style="font-size:1.35rem;margin:0;">Próximas</h2>
                    <a class="boton boton--pequeno boton--oro" href="<?= url('/agenda') ?>">Agendar otra</a>
                </div>
                <div class="grid grid--2 mb-4">
                    <?php foreach ($proximas as $cita): ?>
                        <div class="tarjeta tarjeta__cuerpo" style="border-left:5px solid var(--oro);">
                            <div class="flex-entre"><span class="mono texto-oro"><?= e($cita['codigo']) ?></span><?= estado_badge($cita['estado']) ?></div>
                            <h3 style="font-size:1.3rem;margin:6px 0;"><?= e($cita['mascota_nombre']) ?></h3>
                            <p class="texto-suave" style="font-size:.9rem;"><?= e($cita['servicio_nombre']) ?><br><?= e(dia_semana($cita['fecha'])) ?> <?= e(fecha_larga($cita['fecha'])) ?> · <?= e(hora_ampm($cita['hora'])) ?></p>
                            <div class="flex-entre">
                                <strong><?= precio($cita['precio']) ?></strong>
                                <form method="post" action="<?= url('/mi-cuenta/citas/cancelar') ?>">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="id" value="<?= (int) $cita['id'] ?>">
                                    <button class="boton boton--pequeno boton--contorno" type="submit">Cancelar</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <?php if ($proximas === []): ?>
                        <div class="tarjeta tarjeta__cuerpo" style="grid-column:1/-1;"><p class="texto-suave mb-2">No tienes citas próximas.</p><a class="boton boton--pequeno" href="<?= url('/agenda') ?>">Agendar</a></div>
                    <?php endif; ?>
                </div>

                <h2 style="font-size:1.35rem;">Historial</h2>
                <div class="tarjeta">
                    <div class="tabla-scroll">
                        <table class="tabla">
                            <thead><tr><th>Código</th><th>Fecha</th><th>Mascota</th><th>Servicio</th><th>Estado</th><th class="num">Precio</th></tr></thead>
                            <tbody>
                                <?php foreach ($pasadas as $cita): ?>
                                    <tr>
                                        <td class="mono"><?= e($cita['codigo']) ?></td>
                                        <td><?= e(fecha($cita['fecha'], 'd M Y')) ?></td>
                                        <td><?= e($cita['mascota_nombre']) ?></td>
                                        <td><?= e($cita['servicio_nombre']) ?></td>
                                        <td><?= estado_badge($cita['estado']) ?></td>
                                        <td class="num"><?= precio($cita['precio']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                <?php if ($pasadas === []): ?><tr><td colspan="6" class="tabla-vacia">Aún no hay citas pasadas.</td></tr><?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
