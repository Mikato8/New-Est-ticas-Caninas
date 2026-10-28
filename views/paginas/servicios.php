<?php
declare(strict_types=1);
$iconos = ['bano' => 'bano', 'corte' => 'tijeras', 'unas' => 'una', 'oidos' => 'oido', 'spa' => 'chispa', 'paquete' => 'caja'];
?>
<div class="contenedor">
    <nav class="migas" aria-label="Ruta"><a href="<?= url('/') ?>">Inicio</a><span>/</span>Servicios</nav>
    <header class="portada-interior">
        <span class="mono texto-oro">Estética canina</span>
        <h1>Servicios y precios claros.</h1>
        <p>Baño, corte, uñas, oídos y spa para perros de todas las tallas. Cada servicio incluye revisión en seco, secado completo y recomendaciones para casa.</p>
        <div class="flex-centro mt-3">
            <a class="boton boton--oro" href="<?= url('/agenda') ?>"><?= icono('agenda') ?> Agendar cita</a>
            <a class="boton boton--contorno" href="<?= url('/contacto') ?>">Preguntar por mi perro</a>
        </div>
    </header>
</div>

<section class="seccion seccion--compacta">
    <div class="contenedor">
        <div class="grid grid--2">
            <div class="nota nota--teal"><strong>Antes de la cita:</strong> si tu perro tiene nudos, pulgas, heridas o piel irritada, avísanos. Ajustamos el servicio y el precio antes de empezar.</div>
            <div class="nota"><strong>Precios por tamaño:</strong> chico hasta 8 kg, mediano 8–15 kg, grande 15–30 kg y gigante más de 30 kg. El manto y el temperamento también cuentan.</div>
        </div>
    </div>
</section>

<?php foreach ($agrupados as $categoria => $lista): ?>
    <section class="seccion seccion--compacta">
        <div class="contenedor">
            <div class="encabezado-seccion encabezado-seccion--fila">
                <div>
                    <span class="mono texto-oro"><?= e($categorias[$categoria] ?? ucfirst($categoria)) ?></span>
                    <h2><?= e($categorias[$categoria] ?? ucfirst($categoria)) ?></h2>
                </div>
            </div>
            <div class="grid grid--3">
                <?php foreach ($lista as $servicio): ?>
                    <article class="tarjeta tarjeta-servicio">
                        <div class="tarjeta__cuerpo">
                            <div class="tarjeta-servicio__icono"><?= icono($iconos[$categoria] ?? 'pata') ?></div>
                            <h3><?= e($servicio['nombre']) ?></h3>
                            <p><?= e((string) $servicio['descripcion']) ?></p>
                            <div class="tarjeta-servicio__precio">
                                <strong><?= precio($servicio['precio']) ?></strong>
                                <small><?= e(tamano_label((string) $servicio['tamano'])) ?> · <?= (int) $servicio['duracion_min'] ?> min</small>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endforeach; ?>

<?php if ($paquetes !== []): ?>
<section class="seccion seccion--crema">
    <div class="contenedor">
        <div class="encabezado-seccion">
            <span class="mono">Paquetes</span>
            <h2>Más cuidado, mejor precio.</h2>
            <p>Combos pensados para mantenimiento. Pregunta por la vigencia y por las condiciones de cada paquete.</p>
        </div>
        <div class="grid grid--3">
            <?php foreach ($paquetes as $paquete): ?>
                <article class="tarjeta tarjeta__cuerpo">
                    <span class="etiqueta etiqueta--oro"><?= e(tamano_label((string) $paquete['tamano'])) ?></span>
                    <h3 class="mt-1"><?= e($paquete['nombre']) ?></h3>
                    <p class="texto-suave"><?= e((string) $paquete['descripcion']) ?></p>
                    <div class="flex-entre">
                        <strong style="font-family:var(--fuente-titulo);font-size:1.6rem;color:var(--teal);"><?= precio($paquete['precio']) ?></strong>
                        <a class="boton boton--pequeno" href="<?= url('/agenda') ?>">Agendar</a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<section class="seccion">
    <div class="contenedor">
        <div class="split">
            <div>
                <span class="mono texto-oro">Cómo trabajamos</span>
                <h2>Tres pasos, cero prisas.</h2>
                <div class="pasos mt-3">
                    <div class="paso"><div><h3>Revisión en seco</h3><p>Revisamos piel, oídos, uñas, nudos y temperamento. Te decimos qué encontramos y qué podemos hacer.</p></div></div>
                    <div class="paso"><div><h3>Servicio adaptado</h3><p>Baño con producto adecuado, secado seguro y corte según el manto. Si algo no se puede hacer sin riesgo, se detiene.</p></div></div>
                    <div class="paso"><div><h3>Entrega y recomendaciones</h3><p>Te explicamos el resultado, lo que observamos y cómo mantenerlo en casa. Todo queda en la ficha de tu perro.</p></div></div>
                </div>
            </div>
            <div class="tarjeta tarjeta__cuerpo">
                <h3>¿Qué incluye un baño?</h3>
                <ul class="sin-lista" style="display:grid;gap:9px;">
                    <li class="flex-centro"><?= icono('check', 'texto-teal') ?> Revisión de piel y parásitos</li>
                    <li class="flex-centro"><?= icono('check', 'texto-teal') ?> Cepillado previo y desenredo básico</li>
                    <li class="flex-centro"><?= icono('check', 'texto-teal') ?> Baño con shampoo diluido y dos enjuagues</li>
                    <li class="flex-centro"><?= icono('check', 'texto-teal') ?> Secado completo con control de temperatura</li>
                    <li class="flex-centro"><?= icono('check', 'texto-teal') ?> Limpieza de oídos externos y uñas</li>
                    <li class="flex-centro"><?= icono('check', 'texto-teal') ?> Perfume pet-safe y acabado</li>
                </ul>
                <p class="texto-tenue mt-2" style="font-size:.82rem;">Los nudos severos, el deslanado y los tratamientos especiales se cotizan aparte.</p>
                <a class="boton boton--bloque" href="<?= url('/agenda') ?>">Reservar ahora</a>
            </div>
        </div>
    </div>
</section>
