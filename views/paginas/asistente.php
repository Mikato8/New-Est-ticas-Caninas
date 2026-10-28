<?php
declare(strict_types=1);
?>
<div class="contenedor">
    <nav class="migas" aria-label="Ruta"><a href="<?= url('/') ?>">Inicio</a><span>/</span>Asistente</nav>
    <header class="portada-interior">
        <span class="mono texto-oro">Asistente local</span>
        <h1>Pregunta sobre estética canina.</h1>
        <p>Busca en las guías, calculadoras y glosario de Mikato. La búsqueda corre en tu navegador y no envía tus preguntas a ningún servidor. El modelo generativo es opcional.</p>
    </header>
</div>

<section class="seccion seccion--compacta">
    <div class="contenedor">
        <div class="grid grid--2" style="align-items:start;">
            <div class="tarjeta ia-chat" data-ia-chat data-corpus="<?= url('/assets/ia/corpus.json') ?>">
                <div class="ia-chat__cabecera">
                    <div class="flex-centro"><span class="avatar avatar--teal"><?= icono('chispa') ?></span><div><strong>Asistente Mikato</strong><br><small class="texto-tenue" data-ia-estado>Preparando índice…</small></div></div>
                    <span class="badge">Local</span>
                </div>
                <div class="ia-log" data-ia-log aria-live="polite">
                    <div class="ia-burbuja ia-burbuja--sistema">
                        Hola. Puedo buscar en las <?= (int) \App\Domain\Contenido\Guias::total() ?> guías, el glosario y las calculadoras. Pregúntame por baño, corte, uñas, secado, manejo o precios.
                        <div class="ia-burbuja__nota">No sustituyo a un veterinario. Si hay dolor, heridas o una urgencia, acude a uno.</div>
                    </div>
                </div>
                <div class="ia-ejemplos">
                    <button type="button" class="chip ia-ejemplo">¿Cómo diluyo el shampoo?</button>
                    <button type="button" class="chip ia-ejemplo">¿Hasta dónde corto la uña?</button>
                    <button type="button" class="chip ia-ejemplo">¿Cuánto cobro por un baño?</button>
                    <button type="button" class="chip ia-ejemplo">¿Cómo secar un manto doble?</button>
                </div>
                <form class="ia-form" data-ia-form>
                    <label class="sr-only" for="ia-pregunta">Tu pregunta</label>
                    <input id="ia-pregunta" type="text" data-ia-pregunta placeholder="Escribe tu pregunta…" autocomplete="off" maxlength="300">
                    <button class="boton" type="submit">Preguntar</button>
                </form>
            </div>

            <aside class="aside-sticky">
                <div class="aside-caja">
                    <h3>Búsqueda guiada</h3>
                    <p style="font-size:.88rem;color:var(--tinta-suave);">La búsqueda BM25 encuentra los fragmentos más relevantes del sitio y te muestra la fuente. No inventa: si no hay información, lo dice.</p>
                    <div class="flex-centro" style="gap:7px;flex-wrap:wrap;">
                        <a class="chip" href="<?= url('/guias') ?>">Guías</a>
                        <a class="chip" href="<?= url('/calculadoras') ?>">Calculadoras</a>
                        <a class="chip" href="<?= url('/glosario') ?>">Glosario</a>
                    </div>
                </div>
                <div class="aside-caja">
                    <h3>Modelo generativo opcional</h3>
                    <p style="font-size:.88rem;color:var(--tinta-suave);">Puedes descargar un modelo pequeño de Hugging Face para redactar respuestas con las fuentes del sitio. Se ejecuta en tu navegador y requiere WebGPU y memoria. Es experimental.</p>
                    <button class="boton boton--pequeno boton--bloque" type="button" data-ia-cargar>Activar modelo (~500 MB)</button>
                    <div class="ia-progreso"><span data-ia-progreso></span></div>
                    <p class="texto-tenue" style="font-size:.76rem;margin-top:8px;" data-ia-modelo-estado>Sin cargar. La búsqueda funciona igual.</p>
                </div>
                <div class="aside-caja" style="background:var(--teal-claro);border-color:#b9d4c9;">
                    <h3>Privacidad</h3>
                    <p style="font-size:.88rem;color:var(--teal);">Tus preguntas no salen del dispositivo. No usamos datos de clientes para entrenar modelos. Los pesos quedan en la caché del navegador.</p>
                    <a class="enlace-flecha" href="<?= url('/privacidad') ?>">Aviso de privacidad <?= icono('flecha') ?></a>
                </div>
            </aside>
        </div>
    </div>
</section>

<script type="module" src="<?= asset('/assets/js/ia/asistente.js') ?>"></script>
