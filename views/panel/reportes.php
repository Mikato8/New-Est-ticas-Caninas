<?php
declare(strict_types=1);
$maxSerie = 0.01;
foreach ($serie as $dia) { $maxSerie = max($maxSerie, (float) $dia['total']); }
$maxTop = 0.01;
foreach ($top as $item) { $maxTop = max($maxTop, (float) $item['total']); }
$maxGasto = 0.01;
foreach ($gastosCategoria as $item) { $maxGasto = max($maxGasto, (float) $item['total']); }
$categoriasGasto = \App\Repositories\GastoRepository::categorias();
?>
<header class="panel__cabecera">
    <div><span class="mono texto-oro">Reportes</span><h1>Reportes</h1><p>Ventas, gastos y utilidad del periodo. Cifras para tomar decisiones, no para presumir.</p></div>
    <div class="panel__acciones">
        <form class="flex-centro" method="get" action="<?= url('/panel/reportes') ?>" style="gap:6px;">
            <input type="date" name="desde" value="<?= e($desde) ?>" style="padding:8px 10px;border:1px solid var(--borde-fuerte);border-radius:8px;">
            <input type="date" name="hasta" value="<?= e($hasta) ?>" style="padding:8px 10px;border:1px solid var(--borde-fuerte);border-radius:8px;">
            <button class="boton boton--pequeno boton--contorno" type="submit">Actualizar</button>
        </form>
        <button class="boton boton--pequeno boton--contorno" type="button" onclick="window.print()"><?= icono('descargar') ?> Imprimir</button>
    </div>
</header>

<div class="panel__rejilla mb-3">
    <div class="panel-caja"><span class="mono">Ventas</span><strong class="dato" style="font-size:1.7rem;"><?= dinero($ventas['total']) ?></strong><span class="texto-tenue" style="font-size:.75rem;"><?= (int) $ventas['tickets'] ?> tickets</span></div>
    <div class="panel-caja"><span class="mono">Gastos</span><strong class="dato" style="font-size:1.7rem;color:var(--coral);"><?= dinero($gastos) ?></strong></div>
    <div class="panel-caja"><span class="mono">Utilidad</span><strong class="dato" style="font-size:1.7rem;color:<?= $utilidad >= 0 ? 'var(--teal)' : 'var(--coral)' ?>;"><?= dinero($utilidad) ?></strong></div>
    <div class="panel-caja"><span class="mono">Citas</span><strong class="dato" style="font-size:1.7rem;"><?= (int) $completadas ?>/<?= (int) $citas ?></strong><span class="texto-tenue" style="font-size:.75rem;">completadas</span></div>
</div>

<div class="panel__rejilla panel__rejilla--2 mb-3">
    <section class="panel-caja">
        <div class="panel__seccion-titulo"><h2>Ventas por día</h2><span class="etiqueta"><?= count($serie) ?> días con venta</span></div>
        <?php if ($serie === []): ?><p class="texto-suave" style="font-size:.9rem;">Sin ventas en el periodo.</p><?php else: ?>
            <div class="mini-grafica" style="height:150px;">
                <?php foreach ($serie as $dia): ?>
                    <div class="mini-grafica__barra" style="height:<?= max(4, round(((float) $dia['total'] / $maxSerie) * 100)) ?>%;background:var(--teal);" title="<?= e(fecha($dia['fecha'])) ?>: <?= dinero($dia['total']) ?>"><span><?= e(fecha($dia['fecha'], 'd')) ?></span></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>

    <section class="panel-caja">
        <div class="panel__seccion-titulo"><h2>Gastos por categoría</h2></div>
        <?php if ($gastosCategoria === []): ?><p class="texto-suave" style="font-size:.9rem;">Sin gastos en el periodo.</p><?php endif; ?>
        <div style="display:grid;gap:12px;">
            <?php foreach ($gastosCategoria as $item): ?>
                <div>
                    <div class="flex-entre" style="font-size:.84rem;"><span><?= e($categoriasGasto[$item['categoria']] ?? $item['categoria']) ?></span><strong><?= dinero($item['total']) ?></strong></div>
                    <div style="height:7px;background:var(--crema-2);border-radius:99px;margin-top:5px;overflow:hidden;"><div style="height:100%;width:<?= max(3, round(((float) $item['total'] / $maxGasto) * 100)) ?>%;background:var(--coral);border-radius:99px;"></div></div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
</div>

<div class="panel__rejilla panel__rejilla--2">
    <section class="panel-caja">
        <div class="panel__seccion-titulo"><h2>Conceptos más vendidos</h2></div>
        <div class="tabla-scroll">
            <table class="tabla">
                <thead><tr><th>Concepto</th><th class="num">Cantidad</th><th class="num">Total</th></tr></thead>
                <tbody>
                    <?php foreach ($top as $item): ?>
                        <tr><td><?= e($item['descripcion']) ?></td><td class="num"><?= (int) $item['cantidad'] ?></td><td class="num"><?= dinero($item['total']) ?></td></tr>
                    <?php endforeach; ?>
                    <?php if ($top === []): ?><tr><td colspan="3" class="tabla-vacia">Sin datos.</td></tr><?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>

    <section class="panel-caja">
        <div class="panel__seccion-titulo"><h2>Cómo leerlo</h2></div>
        <div class="prosa" style="font-size:.9rem;">
            <p><strong>Utilidad = ventas − gastos.</strong> No incluye tu sueldo si no lo registras como gasto. Si quieres medir la rentabilidad real, sepáralo.</p>
            <p>El ticket promedio del periodo es <strong><?= dinero($ventas['ticket']) ?></strong>. Si subes el ticket con paquetes o productos, la utilidad crece sin más horas de trabajo.</p>
            <p>Compara periodos completos. Un mes con muchos días festivos no se compara con un mes normal.</p>
            <a class="boton boton--pequeno boton--contorno" href="<?= url('/calculadoras/rentabilidad') ?>" target="_blank">Abrir calculadora de rentabilidad</a>
        </div>
    </section>
</div>
