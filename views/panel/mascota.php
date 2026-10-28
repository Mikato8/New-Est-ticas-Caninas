<?php
declare(strict_types=1);
?>
<header class="panel__cabecera">
    <div>
        <span class="mono texto-oro">Mascota</span>
        <h1><?= e($mascota['nombre']) ?></h1>
        <p><?= e((string) ($mascota['raza'] ?: 'Mestizo')) ?> · <?= e(tamano_label((string) $mascota['tamano'])) ?> · Tutor: <a href="<?= url('/panel/clientes/' . $mascota['cliente_id']) ?>"><?= e($mascota['cliente_nombre']) ?></a> · <?= e((string) ($mascota['cliente_telefono'] ?: 'sin teléfono')) ?></p>
    </div>
    <div class="panel__acciones">
        <a class="boton boton--pequeno boton--contorno" href="<?= url('/panel/mascotas?editar=' . $mascota['id']) ?>"><?= icono('editar') ?> Editar</a>
        <a class="boton boton--pequeno boton--oro" href="<?= url('/panel/agenda') ?>"><?= icono('agenda') ?> Agendar cita</a>
    </div>
</header>

<div class="panel__rejilla panel__rejilla--4 mb-3">
    <div class="panel-caja"><span class="mono">Peso</span><strong class="dato" style="font-size:1.5rem;"><?= $mascota['peso'] ? e((string) $mascota['peso']) . ' kg' : '—' ?></strong></div>
    <div class="panel-caja"><span class="mono">Nacimiento</span><strong class="dato" style="font-size:1.1rem;"><?= $mascota['nacimiento'] ? e(fecha($mascota['nacimiento'])) : '—' ?></strong></div>
    <div class="panel-caja"><span class="mono">Sexo</span><strong class="dato" style="font-size:1.1rem;"><?= e((string) ($mascota['sexo'] ?: 's/d')) ?></strong></div>
    <div class="panel-caja"><span class="mono">Registrada</span><strong class="dato" style="font-size:1.1rem;"><?= e(fecha($mascota['creado_en'], 'd M Y')) ?></strong></div>
</div>

<?php if ($mascota['alergias'] || $mascota['temperamento'] || $mascota['notas']): ?>
<div class="nota mb-3">
    <?php if ($mascota['alergias']): ?><div><strong>Alergias / salud:</strong> <?= e($mascota['alergias']) ?></div><?php endif; ?>
    <?php if ($mascota['temperamento']): ?><div><strong>Manejo:</strong> <?= e($mascota['temperamento']) ?></div><?php endif; ?>
    <?php if ($mascota['notas']): ?><div><strong>Notas:</strong> <?= e($mascota['notas']) ?></div><?php endif; ?>
</div>
<?php endif; ?>

<div class="panel__rejilla panel__rejilla--2" style="align-items:start;">
    <section class="panel-caja">
        <div class="panel__seccion-titulo"><h2>Historial</h2><span class="etiqueta"><?= count($historial) ?> registros</span></div>
        <?php if ($historial === []): ?><p class="texto-suave" style="font-size:.88rem;">Sin servicios registrados. Al completar una cita se agrega automáticamente.</p><?php endif; ?>
        <div class="linea-tiempo">
            <?php foreach ($historial as $item): ?>
                <div class="linea-tiempo__item">
                    <strong><?= e($item['servicio']) ?></strong>
                    <small><?= e(fecha_larga($item['fecha'])) ?><?= $item['proxima_visita'] ? ' · Próxima: ' . e(fecha($item['proxima_visita'])) : '' ?></small>
                    <?php if ($item['observaciones']): ?><p><?= e($item['observaciones']) ?></p><?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <div style="display:grid;gap:18px;">
        <form class="panel-caja formulario" method="post" action="<?= url('/panel/mascotas/historial') ?>">
            <?= csrf_field() ?>
            <input type="hidden" name="mascota_id" value="<?= (int) $mascota['id'] ?>">
            <div class="panel__seccion-titulo"><h2>Agregar al historial</h2></div>
            <div class="rejilla-campos">
                <div class="campo campo--6"><label for="fecha">Fecha</label><input id="fecha" name="fecha" type="date" value="<?= date('Y-m-d') ?>"></div>
                <div class="campo campo--6"><label for="servicio">Servicio *</label><input id="servicio" name="servicio" type="text" required maxlength="120" placeholder="Baño y corte"></div>
            </div>
            <div class="campo"><label for="observaciones">Observaciones</label><textarea id="observaciones" name="observaciones" maxlength="2000" style="min-height:80px;" placeholder="Estado de piel, nudos, comportamiento, producto usado…"></textarea></div>
            <div class="campo"><label for="proxima_visita">Próxima visita sugerida</label><input id="proxima_visita" name="proxima_visita" type="date"></div>
            <button class="boton" type="submit">Guardar historial</button>
        </form>

        <section class="panel-caja">
            <div class="panel__seccion-titulo"><h2>Citas</h2></div>
            <div class="tabla-scroll">
                <table class="tabla">
                    <thead><tr><th>Fecha</th><th>Servicio</th><th>Estado</th></tr></thead>
                    <tbody>
                        <?php foreach ($citas as $cita): if ((int) $cita['mascota_id'] !== (int) $mascota['id']) continue; ?>
                            <tr><td><?= e(fecha($cita['fecha'], 'd M')) ?> · <?= e(substr((string) $cita['hora'], 0, 5)) ?></td><td><?= e($cita['servicio_nombre']) ?></td><td><?= estado_badge($cita['estado']) ?></td></tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</div>
