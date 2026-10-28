<?php
declare(strict_types=1);
?>
<div class="contenedor">
    <nav class="migas" aria-label="Ruta"><a href="<?= url('/') ?>">Inicio</a><span>/</span>Asistente</nav>
    <header class="portada-interior">
        <span class="mono texto-oro">Búsqueda en el sitio</span>
        <h1>Pregunta sobre estética canina.</h1>
        <p>Busca en las 12 guías, el glosario, las calculadoras y el catálogo. La búsqueda se ejecuta en el servidor de Mikato: no usa JavaScript ni envía tus preguntas a modelos externos.</p>
        <form class="buscador mt-3" method="get" action="<?= url('/asistente') ?>" style="max-width:640px;">
            <?= icono('buscar') ?>
            <label class="sr-only" for="q-asistente">Tu pregunta</label>
            <input id="q-asistente" type="search" name="q" value="<?= e($q) ?>" placeholder="Ej. ¿cómo diluyo el shampoo?, ¿hasta dónde corto la uña?" maxlength="300" required>
            <button type="submit">Buscar</button>
        </form>
    </header>
</div>

<section class="seccion seccion--compacta">
    <div class="contenedor">
        <div class="grid grid--2" style="align-items:start;">
            <div>
                <?php if ($q === ''): ?>
                    <div class="tarjeta tarjeta__cuerpo">
                        <h2 style="font-size:1.5rem;">¿Qué puedes preguntar?</h2>
                        <div class="filtros mt-2">
                            <a class="chip" href="<?= url('/asistente?q=' . urlencode('dilución de shampoo')) ?>">Dilución de shampoo</a>
                            <a class="chip" href="<?= url('/asistente?q=' . urlencode('corte de uñas')) ?>">Corte de uñas</a>
                            <a class="chip" href="<?= url('/asistente?q=' . urlencode('cómo secar un perro')) ?>">Secado seguro</a>
                            <a class="chip" href="<?= url('/asistente?q=' . urlencode('precio de un baño')) ?>">Precio de un baño</a>
                            <a class="chip" href="<?= url('/asistente?q=' . urlencode('manto doble')) ?>">Manto doble</a>
                            <a class="chip" href="<?= url('/asistente?q=' . urlencode('marco legal')) ?>">Marco legal</a>
                        </div>
                        <p class="texto-suave mt-3" style="font-size:.9rem;">La búsqueda compara palabras clave con 164 fragmentos públicos del portal y muestra de dónde sale cada respuesta. Si no encuentra algo, te lo dice.</p>
                    </div>
                <?php elseif ($resultados === []): ?>
                    <div class="tarjeta tarjeta__cuerpo">
                        <h2 style="font-size:1.5rem;">No encontré una respuesta documentada</h2>
                        <p class="texto-suave">Prueba con otra palabra: “baño”, “uñas”, “secado”, “precio”, “doble capa” o “legal”. Si el tema no está en el sitio, <a href="<?= url('/contacto') ?>">escríbenos</a> y lo evaluamos.</p>
                    </div>
                <?php else: ?>
                    <p class="texto-tenue" style="font-size:.85rem;"><?= count($resultados) ?> resultado<?= count($resultados) === 1 ? '' : 's' ?> para «<?= e($q) ?>».</p>
                    <div style="display:grid;gap:14px;">
                        <?php foreach ($resultados as $i => $resultado): ?>
                            <article class="tarjeta tarjeta__cuerpo">
                                <div class="flex-entre" style="align-items:flex-start;gap:12px;">
                                    <div>
                                        <span class="etiqueta etiqueta--teal"><?= e($resultado['categoria']) ?></span>
                                        <h2 style="font-size:1.15rem;margin:8px 0 4px;"><?= e($resultado['titulo']) ?></h2>
                                    </div>
                                    <span class="mono texto-tenue" style="font-size:.62rem;"><?= (int) $i + 1 ?></span>
                                </div>
                                <p class="texto-suave" style="font-size:.9rem;"><?= e($resultado['extracto']) ?></p>
                                <div class="flex-centro">
                                    <a class="boton boton--pequeno" href="<?= e($resultado['url']) ?>">Abrir fuente</a>
                                    <?php if ($resultado['titulo_guia'] !== ''): ?><span class="texto-tenue" style="font-size:.78rem;"><?= e($resultado['titulo_guia']) ?></span><?php endif; ?>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <aside class="aside-sticky">
                <div class="aside-caja">
                    <h3>Cómo funciona</h3>
                    <p style="font-size:.88rem;color:var(--tinta-suave);">El servidor lee el corpus público, normaliza acentos, descarta palabras vacías y puntúa coincidencias en títulos y texto. No hay modelos externos ni llamadas a Hugging Face.</p>
                    <ul style="font-size:.85rem;">
                        <li>Sin JavaScript.</li>
                        <li>Sin datos de clientes.</li>
                        <li>Con enlace a la fuente original.</li>
                    </ul>
                </div>
                <div class="aside-caja" style="background:var(--teal-claro);border-color:#b9d4c9;">
                    <h3>Más herramientas</h3>
                    <div style="display:grid;gap:7px;">
                        <a class="chip" href="<?= url('/calculadoras') ?>">Calculadoras</a>
                        <a class="chip" href="<?= url('/glosario') ?>">Glosario</a>
                        <a class="chip" href="<?= url('/guias') ?>">Todas las guías</a>
                    </div>
                </div>
                <div class="aside-caja">
                    <h3>Importante</h3>
                    <p style="font-size:.85rem;color:var(--tinta-suave);">Las respuestas son extractos del contenido del sitio. No sustituyen la valoración de un médico veterinario. Ante dolor, heridas, fiebre o dificultad para respirar, acude a uno.</p>
                </div>
            </aside>
        </div>
    </div>
</section>
