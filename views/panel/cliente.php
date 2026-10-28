<?php
declare(strict_types=1);
?>
<header class="panel__cabecera">
    <div>
        <span class="mono texto-oro">Cliente</span>
        <h1><?= e($cliente['nombre']) ?></h1>
        <p><?= e((string) ($cliente['telefono'] ?: 'Sin teléfono')) ?> · <?= e((string) ($cliente['correo'] ?: 'Sin correo')) ?> · <?= e((string) ($cliente['direccion'] ?: 'Sin dirección')) ?></p>
    </div>
    <div class="panel__acciones">
        <a class="boton boton--pequeno boton--contorno" href="<?= url('/panel/clientes?editar=' . $cliente['id']) ?>"><?= icono('editar') ?> Editar</a>
        <a class="boton boton--pequeno boton--oro" href="<?= url('/panel/mascotas?cliente=' . $cliente['id']) ?>"><?= icono('pata') ?> Agregar mascota</a>
        <a class="boton boton--pequeno" href="<?= url('/agenda') ?>" target="_blank"><?= icono('agenda') ?> Agenda pública</a>
    </div>
</header>

<div class="panel__rejilla panel__rejilla--3 mb-3">
    <div class="panel-caja"><span class="mono">Mascotas</span><strong class="dato" style="font-size:1.8rem;"><?= count($mascotas) ?></strong></div>
    <div class="panel-caja"><span class="mono">Citas</span><strong class="dato" style="font-size:1.8rem;"><?= count($citas) ?></strong></div>
    <div class="panel-caja"><span class="mono">Compras</span><strong class="dato" style="font-size:1.5rem;"><?= dinero(array_sum(array_map(static fn (array $v): float => (float) $v['total'], $ventas))) ?></strong></div>
</div>

<div class="panel__rejilla panel__rejilla--2 mb-3">
    <section class="panel-caja">
        <div class="panel__seccion-titulo"><h2>Mascotas</h2><a class="enlace-flecha" href="<?= url('/panel/mascotas') ?>">Ver todas</a></div>
        <div class="grid" style="grid-template-columns:1fr;gap:10px;">
            <?php foreach ($mascotas as $mascota): ?>
                <div class="mascota-tarjeta">
                    <?php if ($mascota['foto']): ?><img src="<?= e($mascota['foto']) ?>" alt=""><?php else: ?><span class="avatar avatar--oro"><?= e(iniciales((string) $mascota['nombre'])) ?></span><?php endif; ?>
                    <div style="flex:1;"><strong><?= e($mascota['nombre']) ?></strong><small><?= e((string) ($mascota['raza'] ?: 'Mestizo')) ?> · <?= e(tamano_label((string) $mascota['tamano'])) ?></small></div>
                    <a class="boton boton--pequeno boton--contorno" href="<?= url('/panel/mascotas/' . $mascota['id']) ?>">Ficha</a>
                </div>
            <?php endforeach; ?>
            <?php if ($mascotas === []): ?><p class="texto-suave" style="font-size:.88rem;">Este cliente aún no tiene mascotas registradas.</p><?php endif; ?>
        </div>
    </section>

    <section class="panel-caja">
        <div class="panel__seccion-titulo"><h2>Historial reciente</h2></div>
        <?php $hay = false; foreach ($historial as $items): foreach ($items as $item): $hay = true; ?>
            <div class="linea-tiempo__item"><strong><?= e($item['servicio']) ?></strong><small><?= e(fecha($item['fecha'])) ?></small><?php if ($item['observaciones']): ?><p><?= e($item['observaciones']) ?></p><?php endif; ?></div>
        <?php endforeach; endforeach; ?>
        <?php if (!$hay): ?><p class="texto-suave" style="font-size:.88rem;">Sin servicios registrados.</p><?php endif; ?>
    </section>
</div>

<section class="panel-caja mb-3">
    <div class="panel__seccion-titulo"><h2>Citas</h2><span class="etiqueta"><?= count($citas) ?></span></div>
    <div class="tabla-scroll">
        <table class="tabla">
            <thead><tr><th>Fecha</th><th>Mascota</th><th>Servicio</th><th>Estado</th><th class="num">Precio</th></tr></thead>
            <tbody>
                <?php foreach ($citas as $cita): ?>
                    <tr><td><?= e(fecha($cita['fecha'], 'd M Y')) ?> · <?= e(substr((string) $cita['hora'], 0, 5)) ?></td><td><?= e($cita['mascota_nombre']) ?></td><td><?= e($cita['servicio_nombre']) ?></td><td><?= estado_badge($cita['estado']) ?></td><td class="num"><?= precio($cita['precio']) ?></td></tr>
                <?php endforeach; ?>
                <?php if ($citas === []): ?><tr><td colspan="5" class="tabla-vacia">Sin citas.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<?php if ($ventas !== []): ?>
<section class="panel-caja">
    <div class="panel__seccion-titulo"><h2>Compras</h2></div>
    <div class="tabla-scroll">
        <table class="tabla">
            <thead><tr><th>Folio</th><th>Fecha</th><th>Estado</th><th class="num">Total</th></tr></thead>
            <tbody>
                <?php foreach ($ventas as $venta): ?><tr><td class="mono"><?= e($venta['folio']) ?></td><td><?= e(fecha($venta['fecha'])) ?></td><td><?= estado_badge($venta['estado']) ?></td><td class="num"><?= dinero($venta['total']) ?></td></tr><?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
<?php endif; ?>
