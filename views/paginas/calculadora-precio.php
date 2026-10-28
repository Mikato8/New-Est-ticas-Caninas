<?php
declare(strict_types=1);
$v = static fn (string $k, string $d = ''): string => e((string) ($entrada[$k] ?? $d));
$err = static fn (string $k): string => isset($errores[$k]) ? ' campo--error' : '';
?>
<div class="contenedor">
    <nav class="migas" aria-label="Ruta"><a href="<?= url('/') ?>">Inicio</a><span>/</span><a href="<?= url('/calculadoras') ?>">Calculadoras</a><span>/</span>Precio</nav>
    <header class="portada-interior">
        <span class="mono texto-oro">Calculadora</span>
        <h1>Precio de un servicio.</h1>
        <p>Del costo real al precio que necesitas: insumos, costo fijo, tu mano de obra, comisión, margen e IVA. Sin adivinar con la estética de la competencia.</p>
    </header>
</div>

<section class="seccion seccion--compacta">
    <div class="contenedor">
        <div class="calculadora">
            <form class="tarjeta calculadora__formulario formulario" method="get" action="<?= url('/calculadoras/precio') ?>">
                <div class="rejilla-campos">
                    <div class="campo campo--4<?= $err('minutos') ?>"><label for="minutos">Tiempo del servicio (min)</label><input id="minutos" name="minutos" type="number" min="10" max="600" step="5" value="<?= $v('minutos', '90') ?>"><span class="ayuda">Incluye recepción, baño, secado y acabado.</span></div>
                    <div class="campo campo--4<?= $err('insumos') ?>"><label for="insumos">Insumos por servicio (MXN)</label><input id="insumos" name="insumos" type="number" min="0" step="1" value="<?= $v('insumos', '45') ?>"></div>
                    <div class="campo campo--4<?= $err('tarifa_hora') ?>"><label for="tarifa_hora">Tu hora de trabajo (MXN)</label><input id="tarifa_hora" name="tarifa_hora" type="number" min="0" step="10" value="<?= $v('tarifa_hora', '120') ?>"><span class="ayuda">Lo que vale tu tiempo, no lo que sobra.</span></div>
                    <div class="campo campo--4<?= $err('costo_fijo_mensual') ?>"><label for="costo_fijo_mensual">Costos fijos al mes (MXN)</label><input id="costo_fijo_mensual" name="costo_fijo_mensual" type="number" min="0" step="100" value="<?= $v('costo_fijo_mensual', '15000') ?>"><span class="ayuda">Renta, luz, agua, sueldos, software, publicidad.</span></div>
                    <div class="campo campo--4<?= $err('servicios_mes') ?>"><label for="servicios_mes">Servicios al mes</label><input id="servicios_mes" name="servicios_mes" type="number" min="1" max="5000" step="1" value="<?= $v('servicios_mes', '120') ?>"></div>
                    <div class="campo campo--4<?= $err('margen_pct') ?>"><label for="margen_pct">Margen deseado (%)</label><input id="margen_pct" name="margen_pct" type="number" min="0" max="80" step="1" value="<?= $v('margen_pct', '35') ?>"></div>
                    <div class="campo campo--4<?= $err('comision_pct') ?>"><label for="comision_pct">Comisión de quien atiende (%)</label><input id="comision_pct" name="comision_pct" type="number" min="0" max="60" step="1" value="<?= $v('comision_pct', '0') ?>"></div>
                    <div class="campo campo--4<?= $err('iva_pct') ?>"><label for="iva_pct">IVA (%)</label><input id="iva_pct" name="iva_pct" type="number" min="0" max="30" step="1" value="<?= $v('iva_pct', '0') ?>"><span class="ayuda">Si no facturas IVA, déjalo en 0.</span></div>
                </div>
                <div class="formulario__pie">
                    <button class="boton" type="submit"><?= icono('calculadora') ?> Calcular precio</button>
                    <a class="boton boton--contorno" href="<?= url('/calculadoras') ?>">Otras calculadoras</a>
                </div>
            </form>

            <aside class="calculadora__resultado">
                <?php if ($resultado !== null): ?>
                    <span class="mono">Precio sugerido</span>
                    <div class="dato-grande"><?= dinero($resultado['precio_sugerido'], false) ?> <small>MXN</small></div>
                    <p style="font-size:.9rem;color:rgba(251,247,239,.8);">Costo directo <?= dinero($resultado['costo_directo']) ?> · margen real <?= e((string) $resultado['margen_real']) ?> % · <?= dinero($resultado['precio_hora']) ?>/hora</p>
                    <div class="calculadora__desglose">
                        <?php foreach ($resultado['desglose'] as $linea): ?>
                            <div><span><?= e($linea['concepto']) ?></span><span><?= dinero($linea['monto']) ?></span></div>
                        <?php endforeach; ?>
                        <div><span>Precio sin IVA</span><span><?= dinero($resultado['precio_sin_iva']) ?></span></div>
                        <div><span>Precio con IVA</span><span><?= dinero($resultado['precio_con_iva']) ?></span></div>
                    </div>
                    <p class="mono" style="color:var(--oro-claro);">Punto de equilibrio: <?= (int) ceil((float) $resultado['punto_equilibrio']) ?> servicios/mes</p>
                    <?php if (!empty($resultado['alertas'])): ?>
                        <ul class="calculadora__notas">
                            <?php foreach ($resultado['alertas'] as $alerta): ?><li><?= e($alerta) ?></li><?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                <?php else: ?>
                    <span class="mono">Resultado</span>
                    <h3>Tu precio aparecerá aquí</h3>
                    <p style="font-size:.9rem;color:rgba(251,247,239,.75);">La fórmula: costo directo ÷ (1 − margen − comisión). Después se suma IVA si aplica y se redondea hacia arriba.</p>
                    <ul class="calculadora__notas">
                        <li>Si el margen real es menor al objetivo, revisa comisión o costo fijo.</li>
                        <li>Un precio bajo no se compensa con volumen si el margen es negativo.</li>
                        <li>Cobra aparte nudos, deslanado y tratamientos especiales.</li>
                    </ul>
                <?php endif; ?>
            </aside>
        </div>
    </div>
</section>

<section class="seccion seccion--compacta">
    <div class="contenedor">
        <div class="grid grid--3">
            <div class="tarjeta tarjeta__cuerpo"><h3>No olvides</h3><p class="texto-suave" style="font-size:.9rem;">El costo fijo por servicio baja cuando llenas la agenda, pero no desaparece. Calcula con un mes realista, no con el mejor mes del año.</p></div>
            <div class="tarjeta tarjeta__cuerpo"><h3>Revisa cada trimestre</h3><p class="texto-suave" style="font-size:.9rem;">Renta, luz e insumos cambian. Un precio que funcionaba en enero puede no funcionar en julio.</p></div>
            <div class="tarjeta tarjeta__cuerpo"><h3>Comunica el valor</h3><p class="texto-suave" style="font-size:.9rem;">Explica qué incluye, cuánto dura y qué productos usas. El cliente paga por confianza, no solo por agua y jabón.</p></div>
        </div>
    </div>
</section>
