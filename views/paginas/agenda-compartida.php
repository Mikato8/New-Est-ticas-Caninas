<?php
declare(strict_types=1);
?>
<div class="contenedor">
    <nav class="migas" aria-label="Ruta"><a href="<?= url('/') ?>">Inicio</a><span>/</span>Agenda compartida</nav>
    <header class="portada-interior">
        <span class="mono texto-oro">Agenda compartida</span>
        <h1><?= e(dia_semana($fecha)) ?> <?= e(fecha_larga($fecha)) ?></h1>
        <p><?= count($citas) ?> cita<?= count($citas) === 1 ? '' : 's' ?> del día. Este enlace es privado y vence en 7 días.</p>
        <div class="flex-centro mt-3">
            <a class="boton" href="<?= url('/agenda/compartida/' . rawurlencode($token) . '/csv') ?>"><?= icono('descargar') ?> Descargar CSV</a>
            <a class="boton boton--contorno" href="<?= url('/panel/agenda?fecha=' . $fecha) ?>">Ir al panel</a>
        </div>
    </header>
</div>

<section class="seccion seccion--compacta">
    <div class="contenedor">
        <?php if ($citas === []): ?>
            <div class="nota nota--teal">No hay citas agendadas para este día.</div>
        <?php else: ?>
            <div class="agenda-panel">
                <?php foreach ($citas as $cita): ?>
                    <article class="agenda-item agenda-item--<?= e($cita['estado']) ?>">
                        <div class="agenda-item__hora"><?= e(substr((string) $cita['hora'], 0, 5)) ?><small><?= (int) $cita['duracion_min'] ?> min</small></div>
                        <div class="agenda-item__datos">
                            <strong><?= e($cita['mascota_nombre']) ?> · <?= e($cita['servicio_nombre']) ?></strong>
                            <span><?= e($cita['cliente_nombre']) ?> · <?= e((string) ($cita['cliente_telefono'] ?: 'sin teléfono')) ?> · <?= e($cita['codigo']) ?></span>
                            <?php if (!empty($cita['notas'])): ?><span style="display:block;margin-top:3px;color:var(--tinta-tenue);">“<?= e(limitar_texto((string) $cita['notas'], 160)) ?>”</span><?php endif; ?>
                            <div class="flex-centro" style="margin-top:5px;"><?= estado_badge($cita['estado']) ?><span class="texto-tenue" style="font-size:.75rem;"><?= precio($cita['precio']) ?></span></div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <div class="nota mt-3" style="font-size:.82rem;"><strong>Privacidad:</strong> este enlace muestra datos de contacto de clientes. Compártelo solo con el equipo del salón; vence automáticamente.</div>
    </div>
</section>
