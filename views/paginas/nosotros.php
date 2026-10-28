<?php
declare(strict_types=1);
$whatsapp = whatsapp_link();
?>
<div class="contenedor">
    <nav class="migas" aria-label="Ruta"><a href="<?= url('/') ?>">Inicio</a><span>/</span>Nosotros</nav>
    <header class="portada-interior">
        <span class="mono texto-oro">Mikato · Hecho en Guadalajara, Jalisco</span>
        <h1>Nacimos en el salón, no en una oficina.</h1>
        <p>Somos una marca mexicana que fabrica productos para estilistas caninos y opera su propia estética. Conocemos el olor del shampoo diluido, el ruido de la secadora y la cara del perro que por fin se relaja.</p>
    </header>
</div>

<section class="seccion seccion--compacta">
    <div class="contenedor">
        <div class="split">
            <div>
                <h2>Lo que nos mueve</h2>
                <p class="texto-suave">Mikato empezó como una necesidad del día a día: productos que funcionaran en el salón, con diluciones claras, rendimiento real y precios justos para estilistas mexicanos. Con los años nos volvimos también un punto de encuentro: clientes que preguntan cómo cuidar a su perro y estilistas que comparten técnicas, dudas y casos.</p>
                <p class="texto-suave">Este portal es la extensión de ese oficio. No es un catálogo con un blog pegado: es una herramienta para agendar, aprender, calcular, conectar y encontrar a otros profesionales que trabajan con el mismo cuidado.</p>
                <div class="grid grid--3 mt-3">
                    <div class="estadistica"><strong>12</strong><span>Guías de oficio</span></div>
                    <div class="estadistica"><strong>4</strong><span>Calculadoras</span></div>
                    <div class="estadistica"><strong>100%</strong><span>Hecho en Guadalajara</span></div>
                </div>
            </div>
            <div class="tarjeta tarjeta__cuerpo" style="background:linear-gradient(160deg,var(--teal-oscuro),var(--teal));color:var(--crema);">
                <span class="mono" style="color:var(--oro-claro);">Nuestra promesa</span>
                <h3 style="color:var(--crema);font-size:1.7rem;">Trato digno, técnica y transparencia.</h3>
                <ul class="sin-lista" style="display:grid;gap:14px;margin-top:18px;">
                    <li class="flex-centro" style="align-items:flex-start;"><?= icono('escudo') ?><span>No forzamos a un perro que no tolera el manejo. Si hay riesgo, se detiene y se deriva.</span></li>
                    <li class="flex-centro" style="align-items:flex-start;"><?= icono('corazon') ?><span>No prometemos "anestesia para estética" ni rasurados que dañan el manto.</span></li>
                    <li class="flex-centro" style="align-items:flex-start;"><?= icono('libro') ?><span>Explicamos lo que hacemos y documentamos cada servicio.</span></li>
                    <li class="flex-centro" style="align-items:flex-start;"><?= icono('pin') ?><span>Elaboramos en Guadalajara, Jalisco y apoyamos a estilistas locales.</span></li>
                </ul>
            </div>
        </div>
    </div>
</section>

<section class="seccion seccion--crema">
    <div class="contenedor">
        <div class="encabezado-seccion">
            <span class="mono">Cómo fabricamos</span>
            <h2>Del salón a la fórmula, y de regreso.</h2>
            <p>No inventamos productos para llenar un catálogo. Los probamos donde se usan: con perros reales, mantos difíciles y estilistas con prisa.</p>
        </div>
        <div class="grid grid--4">
            <div class="tarjeta tarjeta__cuerpo"><div class="tarjeta-servicio__icono"><?= icono('buscar') ?></div><h3>Detectamos</h3><p class="texto-suave">Escuchamos a estilistas y clientes: qué irrita, qué no rinde, qué se batalla en el baño.</p></div>
            <div class="tarjeta tarjeta__cuerpo"><div class="tarjeta-servicio__icono"><?= icono('agua') ?></div><h3>Formulamos</h3><p class="texto-suave">Probamos diluciones, aromas y consistencias en el salón, no solo en laboratorio.</p></div>
            <div class="tarjeta tarjeta__cuerpo"><div class="tarjeta-servicio__icono"><?= icono('check') ?></div><h3>Validamos</h3><p class="texto-suave">Revisamos rendimiento, seguridad y compatibilidad con distintos tipos de manto.</p></div>
            <div class="tarjeta tarjeta__cuerpo"><div class="tarjeta-servicio__icono"><?= icono('caja') ?></div><h3>Producimos</h3><p class="texto-suave">Elaboramos en Guadalajara, Jalisco y cuidamos el abasto para que no te falte producto en temporada alta.</p></div>
        </div>
    </div>
</section>

<section class="seccion">
    <div class="contenedor">
        <div class="split">
            <div class="tarjeta tarjeta__cuerpo">
                <h2 style="font-size:1.8rem;">Lo que puedes esperar de este portal</h2>
                <ul class="sin-lista" style="display:grid;gap:12px;margin-top:16px;">
                    <li class="flex-centro"><?= icono('check', 'texto-teal') ?> Una agenda que respeta tu tiempo y el del perro.</li>
                    <li class="flex-centro"><?= icono('check', 'texto-teal') ?> Guías honestas, con fuentes y sin mitos.</li>
                    <li class="flex-centro"><?= icono('check', 'texto-teal') ?> Calculadoras que usan tus números, no promesas.</li>
                    <li class="flex-centro"><?= icono('check', 'texto-teal') ?> Un directorio revisado, sin fichas inventadas.</li>
                    <li class="flex-centro"><?= icono('check', 'texto-teal') ?> Una comunidad moderada, sin gritos ni descalificaciones.</li>
                </ul>
            </div>
            <div>
                <span class="mono texto-oro">Para estilistas</span>
                <h2>Si trabajas en una estética, este espacio también es tuyo.</h2>
                <p class="texto-suave">Publica un caso, pregunta por una técnica, presume un antes y después o encuentra proveedores. La comunidad crece cuando compartes lo que sabes.</p>
                <div class="flex-centro">
                    <a class="boton" href="<?= url('/comunidad/publicar') ?>">Publicar un caso</a>
                    <a class="boton boton--contorno" href="<?= url('/directorio/registrar') ?>">Registrar mi estética</a>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="seccion seccion--compacta">
    <div class="contenedor">
        <div class="cta">
            <div>
                <span class="mono texto-oro">Hablemos</span>
                <h2>¿Tienes un perro, una estética o una idea?</h2>
                <p>Escríbenos para agendar, distribuir productos o sumarte a la comunidad. Respondemos en horario de oficina.</p>
            </div>
            <div class="cta__acciones">
                <a class="boton boton--oro boton--grande" href="<?= url('/contacto') ?>">Escribir a Mikato</a>
                <?php if ($whatsapp !== ''): ?><a class="boton boton--fantasma boton--grande" href="<?= e($whatsapp) ?>" target="_blank" rel="noopener"><?= icono('whatsapp') ?> WhatsApp</a><?php endif; ?>
            </div>
        </div>
    </div>
</section>
