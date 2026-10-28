<?php
declare(strict_types=1);
?>
<div class="contenedor">
    <nav class="migas" aria-label="Ruta"><a href="<?= url('/') ?>">Inicio</a><span>/</span><a href="<?= url('/mi-cuenta') ?>">Mi cuenta</a><span>/</span><a href="<?= url('/mi-cuenta/mascotas') ?>">Mis mascotas</a><span>/</span><?= e($mascota['nombre']) ?></nav>
    <header class="portada-interior">
        <div class="flex-centro" style="justify-content:space-between;align-items:flex-start;">
            <div class="flex-centro" style="align-items:flex-start;gap:16px;">
                <?php if ($mascota['foto']): ?><img src="<?= e($mascota['foto']) ?>" alt="" style="width:72px;height:72px;border-radius:16px;object-fit:cover;"><?php else: ?><span class="avatar" style="width:72px;height:72px;border-radius:16px;font-size:1.4rem;background:var(--oro);"><?= e(iniciales((string) $mascota['nombre'])) ?></span><?php endif; ?>
                <div><h1 style="margin:0;font-size:clamp(2rem,4vw,3rem);"><?= e($mascota['nombre']) ?></h1><p class="texto-suave mb-0"><?= e((string) ($mascota['raza'] ?: 'Mestizo')) ?> · <?= e(tamano_label((string) $mascota['tamano'])) ?> · Tutor: <?= e($mascota['cliente_nombre']) ?></p></div>
            </div>
            <a class="boton boton--oro" href="<?= url('/agenda') ?>">Agendar cita</a>
        </div>
    </header>
</div>

<section class="seccion seccion--compacta">
    <div class="contenedor">
        <div class="cuenta">
            <?php parcial('cuenta-nav'); ?>
            <div>
                <div class="grid grid--3 mb-3">
                    <div class="tarjeta-dato"><span class="mono">Peso</span><strong class="dato" style="font-size:1.35rem;"><?= $mascota['peso'] ? e((string) $mascota['peso']) . ' kg' : '—' ?></strong></div>
                    <div class="tarjeta-dato"><span class="mono">Nacimiento</span><strong class="dato" style="font-size:1.15rem;"><?= $mascota['nacimiento'] ? e(fecha($mascota['nacimiento'])) : '—' ?></strong></div>
                    <div class="tarjeta-dato"><span class="mono">Sexo</span><strong class="dato" style="font-size:1.15rem;"><?= e((string) ($mascota['sexo'] ?: 'Sin dato')) ?></strong></div>
                </div>
                <?php if ($mascota['alergias'] || $mascota['temperamento'] || $mascota['notas']): ?>
                    <div class="nota mb-3" style="font-size:.88rem;">
                        <?php if ($mascota['alergias']): ?><div><strong>Alergias:</strong> <?= e($mascota['alergias']) ?></div><?php endif; ?>
                        <?php if ($mascota['temperamento']): ?><div><strong>Temperamento:</strong> <?= e($mascota['temperamento']) ?></div><?php endif; ?>
                        <?php if ($mascota['notas']): ?><div><strong>Notas:</strong> <?= e($mascota['notas']) ?></div><?php endif; ?>
                    </div>
                <?php endif; ?>

                <h2 style="font-size:1.35rem;">Historial de estética</h2>
                <?php if ($historial === []): ?>
                    <div class="tarjeta tarjeta__cuerpo"><p class="texto-suave mb-0">Todavía no hay servicios registrados. Cuando visiten la estética, aquí aparecerá el historial.</p></div>
                <?php else: ?>
                    <div class="linea-tiempo">
                        <?php foreach ($historial as $item): ?>
                            <div class="linea-tiempo__item">
                                <strong><?= e($item['servicio']) ?></strong>
                                <small><?= e(fecha_larga($item['fecha'])) ?><?= $item['proxima_visita'] ? ' · Próxima: ' . e(fecha($item['proxima_visita'])) : '' ?></small>
                                <?php if ($item['observaciones']): ?><p><?= e($item['observaciones']) ?></p><?php endif; ?>
                                <?php if ($item['foto']): ?><img src="<?= e($item['foto']) ?>" alt="" style="max-width:220px;border-radius:10px;margin-top:8px;" loading="lazy"><?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <h2 class="mt-4" style="font-size:1.35rem;">Citas registradas</h2>
                <div class="tarjeta">
                    <div class="tabla-scroll">
                        <table class="tabla">
                            <thead><tr><th>Fecha</th><th>Servicio</th><th>Estado</th><th class="num">Precio</th></tr></thead>
                            <tbody>
                                <?php $hay = false; foreach ($citas as $cita): if ((int) $cita['mascota_id'] !== (int) $mascota['id']) continue; $hay = true; ?>
                                    <tr><td><?= e(fecha($cita['fecha'], 'd M Y')) ?> · <?= e(substr((string) $cita['hora'], 0, 5)) ?></td><td><?= e($cita['servicio_nombre']) ?></td><td><?= estado_badge($cita['estado']) ?></td><td class="num"><?= precio($cita['precio']) ?></td></tr>
                                <?php endforeach; ?>
                                <?php if (!$hay): ?><tr><td colspan="4" class="tabla-vacia">Sin citas para esta mascota.</td></tr><?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
