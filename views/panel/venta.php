<?php
declare(strict_types=1);
$usuario = $usuario ?? [];
$puedeAnular = in_array((string) ($usuario['rol'] ?? ''), ['admin', 'editor'], true);
?>
<header class="panel__cabecera no-imprimir">
    <div><span class="mono texto-oro">Venta</span><h1><?= e($venta['folio']) ?></h1><p><?= e(fecha_larga($venta['fecha'])) ?> · <?= e(ucfirst((string) $venta['metodo_pago'])) ?> · <?= e((string) ($venta['cliente_nombre'] ?? 'Público general')) ?></p></div>
    <div class="panel__acciones">
        <button class="boton boton--pequeno boton--contorno" type="button" onclick="window.print()"><?= icono('descargar') ?> Imprimir</button>
        <a class="boton boton--pequeno boton--contorno" href="<?= url('/panel/ventas') ?>">← Ventas</a>
        <?php if ($puedeAnular && $venta['estado'] !== 'cancelada'): ?>
            <form method="post" action="<?= url('/panel/ventas/' . $venta['id'] . '/anular') ?>" data-confirmar="¿Anular esta venta? No se puede deshacer."><?= csrf_field() ?><button class="boton boton--pequeno boton--contorno" type="submit" style="color:var(--coral);">Anular</button></form>
        <?php endif; ?>
    </div>
</header>

<div class="ticket">
    <div class="ticket__cabeza">
        <div>
            <h2><?= e(config('negocio.nombre')) ?></h2>
            <p class="texto-tenue" style="font-size:.82rem;margin:0;"><?= e(config('negocio.ciudad')) ?> · <?= e(config('negocio.estado')) ?><br><?= e((string) config('negocio.telefono')) ?></p>
        </div>
        <div style="text-align:right;">
            <span class="mono"><?= e($venta['folio']) ?></span><br>
            <span class="texto-tenue" style="font-size:.82rem;"><?= e(fecha_larga($venta['fecha'])) ?></span><br>
            <?= estado_badge($venta['estado']) ?>
        </div>
    </div>
    <p class="mt-2" style="font-size:.9rem;"><strong>Cliente:</strong> <?= e((string) ($venta['cliente_nombre'] ?? 'Público general')) ?><?= $venta['cliente_telefono'] ? ' · ' . e($venta['cliente_telefono']) : '' ?></p>

    <table class="ticket__tabla">
        <thead><tr><th>Concepto</th><th>Cant.</th><th class="num">Precio</th><th class="num">Importe</th></tr></thead>
        <tbody>
            <?php foreach ($items as $item): ?>
                <tr><td><?= e($item['descripcion']) ?><br><small class="texto-tenue"><?= e(ucfirst((string) $item['tipo'])) ?></small></td><td><?= (int) $item['cantidad'] ?></td><td class="num"><?= dinero($item['precio_unitario']) ?></td><td class="num"><?= dinero($item['importe']) ?></td></tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="ticket__totales">
        <div><span>Subtotal</span><span><?= dinero($venta['subtotal']) ?></span></div>
        <div><span>Descuento</span><span>−<?= dinero($venta['descuento']) ?></span></div>
        <div><span>Total</span><span><?= dinero($venta['total']) ?></span></div>
    </div>

    <p class="texto-tenue mt-3" style="font-size:.76rem;">Gracias por confiar en Mikato. Este comprobante no es un comprobante fiscal. Si necesitas factura, solicítala con tu RFC. Los servicios de estética no sustituyen la atención veterinaria.</p>
    <?php if ($venta['notas']): ?><p style="font-size:.82rem;"><strong>Notas:</strong> <?= e($venta['notas']) ?></p><?php endif; ?>
    <p class="texto-tenue" style="font-size:.72rem;">Registró: <?= e((string) ($venta['registrado_nombre'] ?? '—')) ?> · <?= e(fecha($venta['creado_en'], 'd/m/Y H:i')) ?></p>
</div>
