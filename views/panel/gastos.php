<?php
declare(strict_types=1);
?>
<header class="panel__cabecera">
    <div><span class="mono texto-oro">Gastos</span><h1>Gastos</h1><p>Renta, insumos, servicios, nómina y equipo. Sin gastos registrados, la utilidad es un espejismo.</p></div>
    <div class="panel__acciones">
        <form class="flex-centro" method="get" action="<?= url('/panel/gastos') ?>" style="gap:6px;">
            <input type="date" name="desde" value="<?= e($desde) ?>" style="padding:8px 10px;border:1px solid var(--borde-fuerte);border-radius:8px;">
            <input type="date" name="hasta" value="<?= e($hasta) ?>" style="padding:8px 10px;border:1px solid var(--borde-fuerte);border-radius:8px;">
            <button class="boton boton--pequeno boton--contorno" type="submit">Filtrar</button>
        </form>
    </div>
</header>

<div class="panel__rejilla panel__rejilla--3 mb-3">
    <div class="panel-caja"><span class="mono">Total del periodo</span><strong class="dato" style="font-size:1.8rem;color:var(--coral);"><?= dinero($total) ?></strong></div>
    <div class="panel-caja"><span class="mono">Registros</span><strong class="dato" style="font-size:1.8rem;"><?= count($gastos) ?></strong></div>
    <div class="panel-caja"><span class="mono">Promedio</span><strong class="dato" style="font-size:1.6rem;"><?= dinero(count($gastos) > 0 ? $total / count($gastos) : 0) ?></strong></div>
</div>

<div class="panel__rejilla panel__rejilla--2" style="align-items:start;">
    <section class="panel-caja">
        <div class="panel__seccion-titulo"><h2>Movimientos</h2><span class="etiqueta"><?= e(fecha($desde, 'd/m')) ?> — <?= e(fecha($hasta, 'd/m')) ?></span></div>
        <div class="tabla-scroll">
            <table class="tabla">
                <thead><tr><th>Fecha</th><th>Concepto</th><th>Categoría</th><th class="num">Monto</th><th></th></tr></thead>
                <tbody>
                    <?php foreach ($gastos as $gasto): ?>
                        <tr>
                            <td><?= e(fecha($gasto['fecha'], 'd M')) ?></td>
                            <td><?= e($gasto['concepto']) ?><br><small class="texto-tenue"><?= e(ucfirst((string) $gasto['metodo_pago'])) ?></small></td>
                            <td><span class="etiqueta"><?= e($categorias[$gasto['categoria']] ?? $gasto['categoria']) ?></span></td>
                            <td class="num"><?= dinero($gasto['monto']) ?></td>
                            <td class="acciones"><form method="post" action="<?= url('/panel/gastos/eliminar') ?>"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $gasto['id'] ?>"><button class="boton boton--pequeno boton--contorno" type="submit" style="color:var(--coral);">×</button></form></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if ($gastos === []): ?><tr><td colspan="5" class="tabla-vacia">Sin gastos en el periodo.</td></tr><?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>

    <form class="panel-caja formulario" method="post" action="<?= url('/panel/gastos/guardar') ?>">
        <?= csrf_field() ?>
        <div class="panel__seccion-titulo"><h2>Registrar gasto</h2></div>
        <div class="campo"><label for="concepto">Concepto *</label><input id="concepto" name="concepto" type="text" required maxlength="160" placeholder="Shampoo, renta, luz…"></div>
        <div class="rejilla-campos">
            <div class="campo campo--6"><label for="categoria">Categoría</label><select id="categoria" name="categoria"><?php foreach ($categorias as $clave => $etiqueta): ?><option value="<?= e($clave) ?>"><?= e($etiqueta) ?></option><?php endforeach; ?></select></div>
            <div class="campo campo--6"><label for="monto">Monto *</label><input id="monto" name="monto" type="number" min="0.01" step="0.01" required></div>
            <div class="campo campo--6"><label for="fecha">Fecha</label><input id="fecha" name="fecha" type="date" value="<?= date('Y-m-d') ?>"></div>
            <div class="campo campo--6"><label for="metodo_pago">Método</label><select id="metodo_pago" name="metodo_pago"><option value="efectivo">Efectivo</option><option value="transferencia">Transferencia</option><option value="tarjeta">Tarjeta</option><option value="otro">Otro</option></select></div>
        </div>
        <div class="campo"><label for="notas">Notas</label><textarea id="notas" name="notas" maxlength="1000" style="min-height:80px;"></textarea></div>
        <button class="boton" type="submit">Guardar gasto</button>
    </form>
</div>
