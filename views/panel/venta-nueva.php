<?php
declare(strict_types=1);
?>
<header class="panel__cabecera">
    <div><span class="mono texto-oro">Ventas</span><h1>Nueva venta</h1><p>Selecciona los conceptos del catálogo o agrega un concepto manual. El importe se calcula al guardar.</p></div>
    <div class="panel__acciones"><a class="boton boton--pequeno boton--contorno" href="<?= url('/panel/ventas') ?>">← Volver a ventas</a></div>
</header>

<form method="post" action="<?= url('/panel/ventas/guardar') ?>">
    <?= csrf_field() ?>
    <div class="panel__rejilla panel__rejilla--2" style="align-items:start;">
        <section class="panel-caja">
            <div class="panel__seccion-titulo"><h2>Conceptos del catálogo</h2><span class="etiqueta">Hasta 6</span></div>
            <div class="tabla-scroll">
                <table class="tabla">
                    <thead><tr><th>Concepto</th><th style="width:90px;">Cantidad</th></tr></thead>
                    <tbody>
                        <?php for ($i = 0; $i < 6; $i++): ?>
                            <tr>
                                <td>
                                    <select name="concepto[]" style="width:100%;padding:7px 9px;border:1px solid var(--borde-fuerte);border-radius:7px;font-size:.85rem;">
                                        <option value="">— Sin concepto —</option>
                                        <optgroup label="Servicios">
                                            <?php foreach ($servicios as $servicio): ?>
                                                <option value="servicio:<?= (int) $servicio['id'] ?>"><?= e($servicio['nombre']) ?> · <?= precio($servicio['precio']) ?></option>
                                            <?php endforeach; ?>
                                        </optgroup>
                                        <optgroup label="Productos">
                                            <?php foreach ($productos as $producto): ?>
                                                <option value="producto:<?= (int) $producto['id'] ?>"><?= e($producto['nombre']) ?> · <?= precio($producto['precio']) ?> (<?= (int) $producto['stock'] ?> pzas)</option>
                                            <?php endforeach; ?>
                                        </optgroup>
                                        <optgroup label="Paquetes">
                                            <?php foreach ($paquetes as $paquete): ?>
                                                <option value="paquete:<?= (int) $paquete['id'] ?>"><?= e($paquete['nombre']) ?> · <?= precio($paquete['precio']) ?></option>
                                            <?php endforeach; ?>
                                        </optgroup>
                                    </select>
                                </td>
                                <td><input name="cantidad[]" type="number" min="1" max="50" value="1" style="width:70px;padding:7px 9px;border:1px solid var(--borde-fuerte);border-radius:7px;"></td>
                            </tr>
                        <?php endfor; ?>
                    </tbody>
                </table>
            </div>
            <p class="texto-tenue mt-2" style="font-size:.8rem;">Los productos descuentan inventario al guardar. Revisa cantidades antes de cobrar.</p>

            <div class="panel__seccion-titulo mt-3"><h2>Conceptos manuales</h2><span class="etiqueta">Hasta 2</span></div>
            <div class="tabla-scroll">
                <table class="tabla">
                    <thead><tr><th>Descripción</th><th style="width:80px;">Cant.</th><th style="width:110px;">Precio</th></tr></thead>
                    <tbody>
                        <?php for ($i = 0; $i < 2; $i++): ?>
                            <tr>
                                <td><input name="manual_descripcion[]" type="text" maxlength="200" placeholder="Ej. Tratamiento especial" style="width:100%;padding:7px 9px;border:1px solid var(--borde-fuerte);border-radius:7px;"></td>
                                <td><input name="manual_cantidad[]" type="number" min="1" max="50" value="1" style="width:65px;padding:7px 9px;border:1px solid var(--borde-fuerte);border-radius:7px;"></td>
                                <td><input name="manual_precio[]" type="number" min="0" step="0.01" value="0" style="width:95px;padding:7px 9px;border:1px solid var(--borde-fuerte);border-radius:7px;"></td>
                            </tr>
                        <?php endfor; ?>
                    </tbody>
                </table>
            </div>
            <div class="nota nota--teal mt-3" style="font-size:.84rem;">El total y el cambio se calculan en el servidor al registrar la venta. No se muestra un total en vivo para evitar errores de redondeo.</div>
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
            </div>
        </aside>
    </div>
</form>
