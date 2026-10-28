<?php
declare(strict_types=1);
use App\Domain\Estetica\CalculadoraShampoo;
$v = static fn (string $k, string $d = ''): string => e((string) ($entrada[$k] ?? $d));
$err = static fn (string $k): string => isset($errores[$k]) ? ' campo--error' : '';
?>
<div class="contenedor">
    <nav class="migas" aria-label="Ruta"><a href="<?= url('/') ?>">Inicio</a><span>/</span><a href="<?= url('/calculadoras') ?>">Calculadoras</a><span>/</span>Dilución</nav>
    <header class="portada-interior">
        <span class="mono texto-oro">Calculadora</span>
        <h1>Dilución de shampoo.</h1>
        <p>Cuánta agua y cuánto concentrado necesitas, cuántos baños rinde la mezcla y cuánto cuesta por baño. La ficha del fabricante siempre manda.</p>
    </header>
</div>

<section class="seccion seccion--compacta">
    <div class="contenedor">
        <div class="calculadora">
            <form class="tarjeta calculadora__formulario formulario" method="get" action="<?= url('/calculadoras/dilucion') ?>">
                <div class="rejilla-campos">
                    <div class="campo campo--6<?= $err('concentrado_ml') ?>">
                        <label for="concentrado_ml">Shampoo concentrado (ml)</label>
                        <input id="concentrado_ml" name="concentrado_ml" type="number" min="1" max="5000" step="10" value="<?= $v('concentrado_ml', '500') ?>" required>
                        <?php if (isset($errores['concentrado_ml'])): ?><span class="error"><?= e($errores['concentrado_ml']) ?></span><?php endif; ?>
                    </div>
                    <div class="campo campo--6<?= $err('ratio') ?>">
                        <label for="ratio">Dilución (1 parte de shampoo : X de agua)</label>
                        <input id="ratio" name="ratio" type="number" min="1" max="50" step="1" value="<?= $v('ratio', '10') ?>" required>
                        <span class="ayuda">Ejemplo: 1:10 = 1 parte de shampoo por 10 de agua.</span>
                        <?php if (isset($errores['ratio'])): ?><span class="error"><?= e($errores['ratio']) ?></span><?php endif; ?>
                    </div>
                    <div class="campo campo--6<?= $err('tamano') ?>">
                        <label for="tamano">Tamaño del perro</label>
                        <select id="tamano" name="tamano">
                            <?php foreach (CalculadoraShampoo::tamanos() as $clave => $etiqueta): ?>
                                <option value="<?= e($clave) ?>" <?= $v('tamano') === $clave ? 'selected' : '' ?>><?= e($etiqueta) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <span class="ayuda">Chico hasta 8 kg · Mediano 8–15 · Grande 15–30 · Gigante +30.</span>
                    </div>
                    <div class="campo campo--6<?= $err('pelo') ?>">
                        <label for="pelo">Tipo de pelo</label>
                        <select id="pelo" name="pelo">
                            <?php foreach (CalculadoraShampoo::pelos() as $clave => $etiqueta): ?>
                                <option value="<?= e($clave) ?>" <?= $v('pelo') === $clave ? 'selected' : '' ?>><?= e($etiqueta) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="campo campo--6<?= $err('suciedad') ?>">
                        <label for="suciedad">Suciedad</label>
                        <select id="suciedad" name="suciedad">
                            <option value="normal" <?= $v('suciedad') === 'normal' ? 'selected' : '' ?>>Normal</option>
                            <option value="grasa" <?= $v('suciedad') === 'grasa' ? 'selected' : '' ?>>Pelo graso</option>
                            <option value="mucho" <?= $v('suciedad') === 'mucho' ? 'selected' : '' ?>>Suciedad pesada</option>
                        </select>
                    </div>
                    <div class="campo campo--6<?= $err('precio_litro') ?>">
                        <label for="precio_litro">Precio del shampoo por litro (MXN)</label>
                        <input id="precio_litro" name="precio_litro" type="number" min="0" max="100000" step="1" value="<?= $v('precio_litro', '450') ?>">
                        <span class="ayuda">Opcional. Sirve para calcular el costo por baño.</span>
                    </div>
                </div>
                <div class="formulario__pie">
                    <button class="boton" type="submit"><?= icono('calculadora') ?> Calcular mezcla</button>
                    <a class="boton boton--contorno" href="<?= url('/calculadoras') ?>">Otras calculadoras</a>
                </div>
            </form>

            <aside class="calculadora__resultado">
                <?php if ($resultado !== null): ?>
                    <span class="mono">Mezcla total</span>
                    <div class="dato-grande"><?= number_format((float) $resultado['total_ml'], 0) ?> <small>ml</small></div>
                    <p style="font-size:.9rem;color:rgba(251,247,239,.8);"><?= e($resultado['mezcla']) ?></p>
                    <div class="calculadora__desglose">
                        <div><span>Shampoo concentrado</span><span><?= number_format((float) $resultado['concentrado_ml'], 0) ?> ml</span></div>
                        <div><span>Agua</span><span><?= number_format((float) $resultado['agua_ml'], 0) ?> ml</span></div>
                        <div><span>Dosis por baño (<?= e($resultado['tamano_etiqueta']) ?>)</span><span><?= number_format((float) $resultado['dosis_por_bano'], 0) ?> ml</span></div>
                        <div><span>Baños que rinde</span><span><?= (int) $resultado['banos'] ?></span></div>
                        <?php if ((float) ($entrada['precio_litro'] ?? 0) > 0): ?>
                            <div><span>Costo de la mezcla</span><span><?= dinero($resultado['costo_mezcla']) ?></span></div>
                            <div><span>Costo por baño</span><span><?= dinero($resultado['costo_bano']) ?></span></div>
                        <?php endif; ?>
                    </div>
                    <p class="mono" style="color:var(--oro-claro);">Por baño: <?= number_format((float) $resultado['concentrado_por_bano'], 0) ?> ml de shampoo + <?= number_format((float) $resultado['agua_por_bano'], 0) ?> ml de agua</p>
                    <?php if (!empty($resultado['alertas'])): ?>
                        <ul class="calculadora__notas">
                            <?php foreach ($resultado['alertas'] as $alerta): ?><li><?= e($alerta) ?></li><?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                    <ul class="calculadora__notas">
                        <?php foreach ($resultado['notas'] as $nota): ?><li><?= e($nota) ?></li><?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <span class="mono">Resultado</span>
                    <h3>Tu mezcla aparecerá aquí</h3>
                    <p style="font-size:.9rem;color:rgba(251,247,239,.75);">Escribe cuánto shampoo tienes y la dilución de tu producto. Te decimos cuánta agua agregar, cuántos baños rinde y cuánto cuesta.</p>
                    <ul class="calculadora__notas">
                        <li>No hay una dilución universal: usa la ficha de tu fabricante.</li>
                        <li>Prepara solo lo del día. Una mezcla guardada pierde eficacia.</li>
                        <li>Etiqueta el bote con producto, dilución y fecha.</li>
                    </ul>
                <?php endif; ?>
            </aside>
        </div>
    </div>
</section>

<section class="seccion seccion--compacta">
    <div class="contenedor">
        <div class="grid grid--3">
            <div class="tarjeta tarjeta__cuerpo"><h3>1. Revisa la ficha</h3><p class="texto-suave" style="font-size:.9rem;">Cada shampoo tiene una dilución recomendada. Si no la encuentras, pide la ficha técnica a tu proveedor.</p></div>
            <div class="tarjeta tarjeta__cuerpo"><h3>2. Mide, no estimes</h3><p class="texto-suave" style="font-size:.9rem;">Usa jarra graduada. “Un chorrito” cambia el costo y la eficacia del baño.</p></div>
            <div class="tarjeta tarjeta__cuerpo"><h3>3. Etiqueta y usa</h3><p class="texto-suave" style="font-size:.9rem;">Anota producto, dilución y fecha. No reutilices envases de limpieza ni de alimentos.</p></div>
        </div>
    </div>
</section>
