<?php
declare(strict_types=1);
?>
<header class="panel__cabecera">
    <div><span class="mono texto-oro">Ventas</span><h1>Ventas</h1><p>Tickets de servicios y productos. Los totales solo incluyen ventas pagadas.</p></div>
    <div class="panel__acciones">
        <form class="flex-centro" method="get" action="<?= url('/panel/ventas') ?>" style="gap:6px;">
            <input type="date" name="desde" value="<?= e($desde) ?>" style="padding:8px 10px;border:1px solid var(--borde-fuerte);border-radius:8px;">
            <input type="date" name="hasta" value="<?= e($hasta) ?>" style="padding:8px 10px;border:1px solid var(--borde-fuerte);border-radius:8px;">
            <button class="boton boton--pequeno boton--contorno" type="submit">Filtrar</button>
        </form>
        <a class="boton boton--pequeno" href="<?= url('/panel/ventas/nueva') ?>"><?= icono('mas') ?> Nueva venta</a>
    </div>
</header>

<div class="panel__rejilla panel__rejilla--3 mb-3">
    <div class="panel-caja"><span class="mono">Total del periodo</span><strong class="dato" style="font-size:1.8rem;"><?= dinero($resumen['total']) ?></strong></div>
    <div class="panel-caja"><span class="mono">Tickets</span><strong class="dato" style="font-size:1.8rem;"><?= (int) $resumen['tickets'] ?></strong></div>
    <div class="panel-caja"><span class="mono">Ticket promedio</span><strong class="dato" style="font-size:1.8rem;"><?= dinero($resumen['ticket']) ?></strong></div>
</div>

<section class="panel-caja">
    <div class="panel__seccion-titulo"><h2>Movimientos</h2><span class="etiqueta"><?= e(fecha($desde, 'd/m/Y')) ?> — <?= e(fecha($hasta, 'd/m/Y')) ?></span></div>
    <div class="tabla-scroll">
        <table class="tabla">
            <thead><tr><th>Folio</th><th>Fecha</th><th>Cliente</th><th>Conceptos</th><th>Método</th><th>Estado</th><th class="num">Total</th><th></th></tr></thead>
            <tbody>
                <?php foreach ($ventas as $venta): ?>
                    <tr>
                        <td class="mono"><?= e($venta['folio']) ?></td>
                        <td><?= e(fecha($venta['fecha'], 'd M Y')) ?></td>
                        <td><?= e((string) ($venta['cliente_nombre'] ?? 'Público general')) ?></td>
                        <td><?= (int) $venta['items'] ?></td>
                        <td><?= e(ucfirst((string) $venta['metodo_pago'])) ?></td>
                        <td><?= estado_badge($venta['estado']) ?></td>
                        <td class="num"><?= dinero($venta['total']) ?></td>
                        <td class="acciones"><a class="boton boton--pequeno boton--contorno" href="<?= url('/panel/ventas/' . $venta['id']) ?>">Ver</a></td>
                    </tr>
                <?php endforeach; ?>
                <?php if ($ventas === []): ?><tr><td colspan="8" class="tabla-vacia">Sin ventas en el periodo.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
