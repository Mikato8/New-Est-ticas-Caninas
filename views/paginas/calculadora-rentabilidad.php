<?php
declare(strict_types=1);
$v = static fn (string $k, string $d = ''): string => e((string) ($entrada[$k] ?? $d));
$err = static fn (string $k): string => isset($errores[$k]) ? ' campo--error' : '';
$colores = ['verde' => '#9ec9a8', 'ambar' => '#e4c37a', 'rojo' => '#e89a88'];
?>
<div class="contenedor">
    <nav class="migas" aria-label="Ruta"><a href="<?= url('/') ?>">Inicio</a><span>/</span><a href="<?= url('/calculadoras') ?>">Calculadoras</a><span>/</span>Rentabilidad</nav>
    <header class="portada-interior">
        <span class="mono texto-oro">Calculadora</span>
        <h1>Rentabilidad por servicio.</h1>
        <p>Cuánto ganas de verdad por hora, cuál es tu margen real y cuántos servicios necesitas para cubrir los costos fijos. Sin contar solo lo que entra.</p>
    </header>
</div>

<section class="seccion seccion--compacta">
    <div class="contenedor">
        <div class="calculadora">
            <form class="tarjeta calculadora__formulario formulario" method="get" action="<?= url('/calculadoras/rentabilidad') ?>">
                <div class="rejilla-campos">
                    <div class="campo campo--4<?= $err('precio') ?>"><label for="precio">Precio cobrado (MXN)</label><input id="precio" name="precio" type="number" min="1" step="10" value="<?= $v('precio', '550') ?>"></div>
                    <div class="campo campo--4<?= $err('insumos') ?>"><label for="insumos">Insumos (MXN)</label><input id="insumos" name="insumos" type="number" min="0" step="1" value="<?= $v('insumos', '45') ?>"></div>
                    <div class="campo campo--4<?= $err('minutos') ?>"><label for="minutos">Tiempo (min)</label><input id="minutos" name="minutos" type="number" min="10" max="600" step="5" value="<?= $v('minutos', '90') ?>"></div>
                    <div class="campo campo--4<?= $err('comision_pct') ?>"><label for="comision_pct">Comisión (%)</label><input id="comision_pct" name="comision_pct" type="number" min="0" max="60" step="1" value="<?= $v('comision_pct', '0') ?>"></div>
                    <div class="campo campo--4<?= $err('costo_fijo_mensual') ?>"><label for="costo_fijo_mensual">Costos fijos al mes (MXN)</label><input id="costo_fijo_mensual" name="costo_fijo_mensual" type="number" min="0" step="100" value="<?= $v('costo_fijo_mensual', '15000') ?>"></div>
                    <div class="campo campo--4<?= $err('meta_hora') ?>"><label for="meta_hora">Meta por hora (MXN)</label><input id="meta_hora" name="meta_hora" type="number" min="0" step="10" value="<?= $v('meta_hora', '180') ?>"><span class="ayuda">Lo que quieres ganar por hora trabajada.</span></div>
                </div>
                <div class="formulario__pie">
                    <button class="boton" type="submit"><?= icono('calculadora') ?> Calcular rentabilidad</button>
                    <a class="boton boton--contorno" href="<?= url('/calculadoras') ?>">Otras calculadoras</a>
                </div>
            </form>

            <aside class="calculadora__resultado">
                <?php if ($resultado !== null): ?>
                    <span class="mono" style="color:<?= $colores[$resultado['semaforo']] ?? 'var(--oro-claro)' ?>;"><?= e(mb_strtoupper($resultado['mensaje'])) ?></span>
                    <div class="dato-grande"><?= dinero($resultado['utilidad_hora']) ?> <small>/ hora</small></div>
                    <p style="font-size:.9rem;color:rgba(251,247,239,.8);">Margen real <?= e((string) $resultado['margen']) ?> % · neto por servicio <?= dinero($resultado['neto']) ?> · meta <?= dinero($resultado['meta_hora']) ?>/h</p>
                    <div class="calculadora__desglose">
                        <div><span>Precio</span><span><?= dinero($resultado['precio']) ?></span></div>
                        <div><span>Insumos</span><span>−<?= dinero($resultado['insumos']) ?></span></div>
                        <div><span>Comisión</span><span>−<?= dinero($resultado['comision_monto']) ?></span></div>
                        <div><span>Neto por servicio</span><span><?= dinero($resultado['neto']) ?></span></div>
                        <div><span>Punto de equilibrio</span><span><?= (int) ceil((float) $resultado['punto_equilibrio']) ?> servicios/mes</span></div>
                        <div><span>Precio para tu meta</span><span><?= dinero($resultado['precio_meta']) ?></span></div>
                    </div>
                    <?php if (!empty($resultado['recomendaciones'])): ?>
                        <ul class="calculadora__notas">
                            <?php foreach ($resultado['recomendaciones'] as $recomendacion): ?><li><?= e($recomendacion) ?></li><?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                <?php else: ?>
                    <span class="mono">Resultado</span>
                    <h3>Tu rentabilidad aparecerá aquí</h3>
                    <p style="font-size:.9rem;color:rgba(251,247,239,.75);">No es lo mismo facturar que ganar. Esta calculadora descuenta insumos y comisión, y compara tu utilidad por hora con la meta que definas.</p>
                    <ul class="calculadora__notas">
                        <li>Un servicio con precio alto pero mucho tiempo puede pagar menos por hora.</li>
                        <li>El punto de equilibrio no incluye tu sueldo si no lo separas como costo.</li>
                        <li>Revisa la calculadora de precio para construir el precio desde cero.</li>
                    </ul>
                <?php endif; ?>
            </aside>
        </div>
    </div>
</section>

<section class="seccion seccion--compacta">
    <div class="contenedor">
        <div class="grid grid--3">
            <div class="tarjeta tarjeta__cuerpo"><h3>Separa tu sueldo</h3><p class="texto-suave" style="font-size:.9rem;">Si no te pagas, el negocio parece rentable hasta que necesitas reponer equipo o tomarte vacaciones.</p></div>
            <div class="tarjeta tarjeta__cuerpo"><h3>Mide tiempos reales</h3><p class="texto-suave" style="font-size:.9rem;">Incluye recepción, baño, secado, acabado y limpieza. El tiempo que no cobras también cuesta.</p></div>
            <div class="tarjeta tarjeta__cuerpo"><h3>Sube el ticket</h3><p class="texto-suave" style="font-size:.9rem;">Paquetes, deslanado, tratamiento de piel y productos para casa. No siempre es subir el precio base.</p></div>
        </div>
    </div>
</section>
