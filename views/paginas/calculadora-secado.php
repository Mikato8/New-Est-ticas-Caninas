<?php
declare(strict_types=1);
use App\Domain\Estetica\CalculadoraSecado;
$v = static fn (string $k, string $d = ''): string => e((string) ($entrada[$k] ?? $d));
$err = static fn (string $k): string => isset($errores[$k]) ? ' campo--error' : '';
?>
<div class="contenedor">
    <nav class="migas" aria-label="Ruta"><a href="<?= url('/') ?>">Inicio</a><span>/</span><a href="<?= url('/calculadoras') ?>">Calculadoras</a><span>/</span>Secado</nav>
    <header class="portada-interior">
        <span class="mono texto-oro">Calculadora</span>
        <h1>Tiempo y costo de secado.</h1>
        <p>Cuánto puede tardar el secado según peso, manto y equipo, cuánta energía consume y cuántas pausas necesita el perro. La seguridad manda sobre el reloj.</p>
    </header>
</div>

<section class="seccion seccion--compacta">
    <div class="contenedor">
        <div class="calculadora">
            <form class="tarjeta calculadora__formulario formulario" method="get" action="<?= url('/calculadoras/secado') ?>">
                <div class="rejilla-campos">
                    <div class="campo campo--6<?= $err('peso_kg') ?>"><label for="peso_kg">Peso del perro (kg)</label><input id="peso_kg" name="peso_kg" type="number" min="0.5" max="90" step="0.5" value="<?= $v('peso_kg', '10') ?>"><span class="ayuda">Si no lo sabes, usa un peso aproximado.</span></div>
                    <div class="campo campo--6<?= $err('pelo') ?>"><label for="pelo">Tipo de pelo</label>
                        <select id="pelo" name="pelo">
                            <?php foreach (CalculadoraSecado::pelos() as $clave => $etiqueta): ?><option value="<?= e($clave) ?>" <?= $v('pelo') === $clave ? 'selected' : '' ?>><?= e($etiqueta) ?></option><?php endforeach; ?>
                        </select>
                    </div>
                    <div class="campo campo--6<?= $err('equipo') ?>"><label for="equipo">Equipo</label>
                        <select id="equipo" name="equipo">
                            <?php foreach (CalculadoraSecado::equipos() as $clave => $etiqueta): ?><option value="<?= e($clave) ?>" <?= $v('equipo') === $clave ? 'selected' : '' ?>><?= e($etiqueta) ?></option><?php endforeach; ?>
                        </select>
                    </div>
                    <div class="campo campo--6<?= $err('tarifa_kwh') ?>"><label for="tarifa_kwh">Tarifa de luz (MXN / kWh)</label><input id="tarifa_kwh" name="tarifa_kwh" type="number" min="0" max="20" step="0.1" value="<?= $v('tarifa_kwh', '3.5') ?>"><span class="ayuda">Revísala en tu recibo de CFE.</span></div>
                </div>
                <div class="formulario__pie">
                    <button class="boton" type="submit"><?= icono('calculadora') ?> Calcular secado</button>
                    <a class="boton boton--contorno" href="<?= url('/calculadoras') ?>">Otras calculadoras</a>
                </div>
            </form>

            <aside class="calculadora__resultado">
                <?php if ($resultado !== null): ?>
                    <span class="mono">Tiempo estimado</span>
                    <div class="dato-grande"><?= (int) $resultado['minutos'] ?> <small>min</small></div>
                    <p style="font-size:.9rem;color:rgba(251,247,239,.8);">Rango probable: <?= e($resultado['rango']) ?> · <?= e($resultado['equipo']) ?> · <?= e($resultado['pelo']) ?></p>
                    <div class="calculadora__desglose">
                        <div><span>Peso</span><span><?= e((string) $resultado['peso_kg']) ?> kg</span></div>
                        <div><span>Potencia del equipo</span><span><?= (int) $resultado['watts'] ?> W</span></div>
                        <div><span>Consumo estimado</span><span><?= e((string) $resultado['kwh']) ?> kWh</span></div>
                        <div><span>Costo de energía</span><span><?= dinero($resultado['costo_energia']) ?></span></div>
                        <div><span>Pausas sugeridas</span><span><?= (int) $resultado['min_pausas'] ?></span></div>
                    </div>
                    <?php if (!empty($resultado['alertas'])): ?>
                        <ul class="calculadora__notas">
                            <?php foreach ($resultado['alertas'] as $alerta): ?><li><?= e($alerta) ?></li><?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                    <p class="mono" style="color:var(--oro-claro);">Checklist de seguridad</p>
                    <ul class="calculadora__notas">
                        <?php foreach ($resultado['checklist'] as $item): ?><li><?= e($item) ?></li><?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <span class="mono">Resultado</span>
                    <h3>Tu estimación aparecerá aquí</h3>
                    <p style="font-size:.9rem;color:rgba(251,247,239,.75);">El cálculo usa el peso, el manto y el equipo. No sustituye la observación del perro: si jadea, se echa o se estresa, para.</p>
                    <ul class="calculadora__notas">
                        <li>Prueba el aire en tu muñeca: tibio, no caliente.</li>
                        <li>Boquilla a 20–30 cm y siempre en movimiento.</li>
                        <li>Nunca dejes a un perro solo en jaula de secado.</li>
                    </ul>
                <?php endif; ?>
            </aside>
        </div>
    </div>
</section>

<section class="seccion seccion--compacta">
    <div class="contenedor">
        <div class="grid grid--3">
            <div class="tarjeta tarjeta__cuerpo"><h3>Golpe de calor</h3><p class="texto-suave" style="font-size:.9rem;">Jadeo intenso, saliva espesa, encías rojas, tambaleo. Enfría con agua templada, ventila y acude al veterinario de inmediato.</p></div>
            <div class="tarjeta tarjeta__cuerpo"><h3>Doble capa</h3><p class="texto-suave" style="font-size:.9rem;">Seca primero la capa interna. No uses jaula con el perro mojado: el calor queda atrapado.</p></div>
            <div class="tarjeta tarjeta__cuerpo"><h3>Mantenimiento</h3><p class="texto-suave" style="font-size:.9rem;">Limpia el filtro de la secadora cada semana. Un filtro sucio recalienta el aire y consume más.</p></div>
        </div>
    </div>
</section>
