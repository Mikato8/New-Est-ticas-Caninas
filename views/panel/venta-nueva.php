<?php
declare(strict_types=1);
?>
<header class="panel__cabecera">
    <div><span class="mono texto-oro">Ventas</span><h1>Nueva venta</h1><p>Agrega servicios, productos o paquetes. El total se calcula en el servidor.</p></div>
    <div class="panel__acciones"><a class="boton boton--pequeno boton--contorno" href="<?= url('/panel/ventas') ?>">← Volver a ventas</a></div>
</header>

<form method="post" action="<?= url('/panel/ventas/guardar') ?>" data-venta>
    <?= csrf_field() ?>
    <div class="panel__rejilla panel__rejilla--2" style="align-items:start;">
        <section class="panel-caja">
            <div class="panel__seccion-titulo"><h2>Conceptos</h2><button class="boton boton--pequeno boton--contorno" type="button" data-agregar-linea><?= icono('mas') ?> Línea manual</button></div>
            <div class="flex-centro mb-2" style="gap:8px;">
                <select data-agregar-servicio style="flex:1;min-width:160px;padding:9px 11px;border:1px solid var(--borde-fuerte);border-radius:8px;">
                    <option value="">+ Agregar servicio…</option>
                    <?php foreach ($servicios as $servicio): ?><option value="<?= (int) $servicio['id'] ?>" data-nombre="<?= e($servicio['nombre']) ?>" data-precio="<?= e((string) $servicio['precio']) ?>"><?= e($servicio['nombre']) ?> · <?= precio($servicio['precio']) ?></option><?php endforeach; ?>
                </select>
                <select data-agregar-producto style="flex:1;min-width:160px;padding:9px 11px;border:1px solid var(--borde-fuerte);border-radius:8px;">
                    <option value="">+ Agregar producto…</option>
                    <?php foreach ($productos as $producto): ?><option value="<?= (int) $producto['id'] ?>" data-nombre="<?= e($producto['nombre']) ?>" data-precio="<?= e((string) $producto['precio']) ?>"><?= e($producto['nombre']) ?> · <?= precio($producto['precio']) ?> (<?= (int) $producto['stock'] ?> pzas)</option><?php endforeach; ?>
                </select>
                <select data-agregar-paquete style="flex:1;min-width:160px;padding:9px 11px;border:1px solid var(--borde-fuerte);border-radius:8px;">
                    <option value="">+ Agregar paquete…</option>
                    <?php foreach ($paquetes as $paquete): ?><option value="<?= (int) $paquete['id'] ?>" data-nombre="<?= e($paquete['nombre']) ?>" data-precio="<?= e((string) $paquete['precio']) ?>"><?= e($paquete['nombre']) ?> · <?= precio($paquete['precio']) ?></option><?php endforeach; ?>
                </select>
            </div>
            <div class="tabla-scroll">
                <table class="tabla" style="table-layout:fixed;">
                    <colgroup><col><col style="width:64px"><col style="width:90px"><col style="width:95px"><col style="width:44px"></colgroup>
                    <thead><tr><th>Descripción</th><th>Cant.</th><th>Precio</th><th class="num">Importe</th><th></th></tr></thead>
                    <tbody data-conceptos></tbody>
                </table>
            </div>
            <div class="flex-entre mt-2" style="padding-top:12px;border-top:1px solid var(--borde);">
                <span class="texto-tenue" style="font-size:.85rem;">Subtotal</span>
                <strong data-subtotal-venta>$0.00</strong>
            </div>
            <div class="flex-entre mt-1">
                <span class="mono" style="font-size:.7rem;">Total a cobrar</span>
                <strong data-total-venta style="font-family:var(--fuente-titulo);font-size:1.8rem;color:var(--teal);">$0.00</strong>
            </div>
        </section>

        <aside class="panel-caja">
            <div class="panel__seccion-titulo"><h2>Datos de la venta</h2></div>
            <div class="formulario">
                <div class="campo"><label for="cliente_id">Cliente</label><select id="cliente_id" name="cliente_id"><option value="">Público general</option><?php foreach ($clientes as $cliente): ?><option value="<?= (int) $cliente['id'] ?>"><?= e($cliente['nombre']) ?></option><?php endforeach; ?></select></div>
                <div class="rejilla-campos">
                    <div class="campo campo--6"><label for="fecha">Fecha</label><input id="fecha" name="fecha" type="date" value="<?= date('Y-m-d') ?>"></div>
                    <div class="campo campo--6"><label for="metodo_pago">Método</label><select id="metodo_pago" name="metodo_pago"><option value="efectivo">Efectivo</option><option value="transferencia">Transferencia</option><option value="tarjeta">Tarjeta</option><option value="otro">Otro</option></select></div>
                    <div class="campo campo--6"><label for="descuento">Descuento (MXN)</label><input id="descuento" name="descuento" type="number" min="0" step="0.01" value="0"></div>
                    <div class="campo campo--6"><label for="estado">Estado</label><select id="estado" name="estado"><option value="pagada">Pagada</option><option value="pendiente">Pendiente</option></select></div>
                </div>
                <div class="campo"><label for="notas">Notas</label><textarea id="notas" name="notas" maxlength="1000" style="min-height:80px;"></textarea></div>
                <button class="boton boton--bloque" type="submit"><?= icono('dinero') ?> Registrar venta</button>
                <p class="texto-tenue" style="font-size:.76rem;margin:0;">Los productos descuentan inventario al guardar. Revisa cantidades antes de cobrar.</p>
            </div>
        </aside>
    </div>
</form>

<template id="plantilla-concepto">
    <tr data-concepto>
        <td>
            <input type="hidden" name="tipo[]" value="servicio">
            <input type="hidden" name="referencia_id[]" value="">
            <input name="descripcion[]" type="text" required maxlength="200" placeholder="Descripción del concepto" style="width:100%;padding:7px 9px;border:1px solid var(--borde-fuerte);border-radius:7px;">
        </td>
        <td><input name="cantidad[]" type="number" min="1" max="50" value="1" required style="width:70px;padding:7px 9px;border:1px solid var(--borde-fuerte);border-radius:7px;"></td>
        <td><input name="precio_unitario[]" type="number" min="0" step="0.01" value="0" required style="width:100px;padding:7px 9px;border:1px solid var(--borde-fuerte);border-radius:7px;"></td>
        <td class="num" data-importe style="font-family:var(--fuente-mono);">$0.00</td>
        <td><button class="boton boton--pequeno boton--contorno" type="button" data-quitar aria-label="Quitar">×</button></td>
    </tr>
</template>
