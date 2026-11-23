<?php
declare(strict_types=1);
$whatsapp = whatsapp_link();
$docs = [
    [
        'titulo' => 'Ficha de estilo y acuerdo de corte',
        'descripcion' => 'Define el largo por zona, la técnica, el precio, el tiempo y la autorización del cliente antes de cortar.',
        'archivo' => '/descargas/formatos/Mikato-Ficha-de-estilo.pdf',
        'paginas' => 2,
        'etiqueta' => 'Corte',
    ],
    [
        'titulo' => 'Checklist de ingreso y autorización',
        'descripcion' => 'Documenta el estado físico, la salud, la conducta y las autorizaciones al recibir al perro.',
        'archivo' => '/descargas/formatos/Mikato-Checklist-de-ingreso.pdf',
        'paginas' => 2,
        'etiqueta' => 'Ingreso',
    ],
    [
        'titulo' => 'Guía del prestador de servicios',
        'descripcion' => 'Obligaciones, responsabilidades, protocolo de incidentes, reclamaciones y marco legal explicado.',
        'archivo' => '/descargas/formatos/Mikato-Guia-prestadores.pdf',
        'paginas' => 6,
        'etiqueta' => 'Guía',
    ],
    [
        'titulo' => 'Carta de servicio',
        'descripcion' => 'Una hoja para el cliente: qué incluye, qué necesitamos de ti, salud, fotos y reclamaciones.',
        'archivo' => '/descargas/formatos/Mikato-Carta-de-servicio.pdf',
        'paginas' => 1,
        'etiqueta' => 'Cliente',
    ],
];
?>
<div class="contenedor">
    <nav class="migas" aria-label="Ruta"><a href="<?= url('/') ?>">Inicio</a><span>/</span>Prestadores</nav>
    <header class="portada-interior">
        <span class="kicker mono texto-oro">Prestadores de servicios</span>
        <h1>Guías para operar una estética canina con orden.</h1>
        <p>Cuatro documentos originales de Mikato para dejar por escrito el corte, revisar el ingreso, informar al cliente y responder cuando algo no salió como se acordó. No son copias de formatos de otros salones: están armados para que cualquier estética los adapte a su operación. Mikato no atiende perros; estos documentos son para la estética que los usa.</p>
        <div class="flex-centro mt-3">
            <a class="t-btn t-btn--azul" href="<?= url('/descargas/formatos/Mikato-Guia-prestadores.pdf') ?>?v=1.1" download><?= icono('descargar') ?> Descargar la guía</a>
            <a class="t-btn t-btn--linea" href="#documentos">Ver los cuatro documentos</a>
        </div>
    </header>
</div>

<section class="t-seccion t-seccion--ajustada" id="documentos">
    <div class="contenedor">
        <div class="t-seccion__cabeza">
            <div>
                <span class="t-kicker">El kit</span>
                <h2>Cuatro documentos, una operación trazable.</h2>
                <p>Se imprimen en tamaño carta. Cada uno tiene campos para el negocio, el cliente y el groomer.</p>
            </div>
            <span style="font-size:13px;color:var(--t-tinta-3);">PDF · listos para imprimir</span>
        </div>
        <div class="t-guias t-guias--dos">
            <?php foreach ($docs as $doc): ?>
                <article class="t-guia">
                    <div class="t-guia__franja">
                        <?= icono('libro') ?>
                        <span><?= e($doc['etiqueta']) ?></span>
                    </div>
                    <div class="t-guia__cuerpo">
                        <h3><?= e($doc['titulo']) ?></h3>
                        <p><?= e($doc['descripcion']) ?></p>
                        <div class="t-guia__meta"><span><?= (int) $doc['paginas'] ?> páginas</span><span>PDF</span></div>
                        <a class="t-btn t-btn--linea t-btn--chico" style="align-self:flex-start;margin-top:6px;" href="<?= url($doc['archivo']) ?>?v=1.1" download>Descargar</a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="t-seccion t-seccion--fondo">
    <div class="contenedor">
        <div class="t-seccion__cabeza">
            <div>
                <span class="t-kicker">Obligaciones y responsabilidades</span>
                <h2>Lo que la ley exige y lo que el oficio pide.</h2>
                <p>La guía separa las dos cosas para que no se confundan: una obligación se documenta; una responsabilidad se ejerce.</p>
            </div>
        </div>
        <div class="t-guias t-guias--dos">
            <article class="t-guia">
                <div class="t-guia__franja"><?= icono('escudo') ?><span>Prestador</span></div>
                <div class="t-guia__cuerpo">
                    <h3>Obligaciones que se documentan</h3>
                    <ul class="lista" style="font-size:13.5px;line-height:1.55;">
                        <li>Cotizar por escrito qué incluye, precio, tiempo y extras.</li>
                        <li>Entregar factura, recibo o comprobante con el detalle del servicio (LFPC, artículo 12).</li>
                        <li>Informar y publicitar de forma veraz y comprobable (LFPC, artículo 32).</li>
                        <li>Pedir consentimiento para el corte, las fotos y la atención veterinaria de urgencia.</li>
                        <li>Resguardar datos personales y poner a disposición el aviso de privacidad.</li>
                        <li>Recibir y responder reclamaciones sin limitar derechos del consumidor.</li>
                    </ul>
                </div>
            </article>
            <article class="t-guia">
                <div class="t-guia__franja"><?= icono('pata') ?><span>Cliente</span></div>
                <div class="t-guia__cuerpo">
                    <h3>Responsabilidades que se conversan</h3>
                    <ul class="lista" style="font-size:13.5px;line-height:1.55;">
                        <li>Declarar vacunas, medicamentos, alergias, condiciones y conducta.</li>
                        <li>Aceptar que el resultado depende del manto, la genética y el estado del perro.</li>
                        <li>Autorizar el rasurado cuando el desenredo no es seguro.</li>
                        <li>Llegar y recoger a la hora; avisar si no puede.</li>
                        <li>Pagar el precio acordado y los extras autorizados.</li>
                        <li>Decidir sobre fotos y presentar cualquier reclamación con datos y evidencia.</li>
                    </ul>
                </div>
            </article>
        </div>
    </div>
</section>

<section class="t-seccion">
    <div class="contenedor">
        <div class="t-seccion__cabeza">
            <div>
                <span class="t-kicker">Cómo usarlos</span>
                <h2>El orden importa.</h2>
                <p>No se trata de llenar papel por llenar. Cada documento cubre un momento del servicio.</p>
            </div>
        </div>
        <div class="t-modulos t-modulos--4">
            <article class="t-modulo">
                <div class="t-modulo__icono"><?= icono('agenda') ?></div>
                <h3>1. Antes de cortar</h3>
                <p>Ficha de estilo: largo por zona, técnica, precio y autorización. Si no hay acuerdo, no se empieza.</p>
            </article>
            <article class="t-modulo">
                <div class="t-modulo__icono"><?= icono('escudo') ?></div>
                <h3>2. Al ingresar</h3>
                <p>Checklist: ojos, oídos, piel, uñas, patas, salud y conducta. Se firma y se guarda.</p>
            </article>
            <article class="t-modulo">
                <div class="t-modulo__icono"><?= icono('dinero') ?></div>
                <h3>3. Al entregar</h3>
                <p>Carta de servicio y comprobante. Se explican cuidados, precio y cómo reclamar.</p>
            </article>
            <article class="t-modulo">
                <div class="t-modulo__icono"><?= icono('libro') ?></div>
                <h3>4. Si hay un problema</h3>
                <p>La guía trae el protocolo de incidentes y los cuatro pasos para responder una reclamación.</p>
            </article>
        </div>
    </div>
</section>

<section class="t-seccion t-seccion--fondo">
    <div class="contenedor">
        <div class="t-descarga">
            <div>
                <span class="t-kicker" style="color:var(--t-oro-claro);">Base y límites</span>
                <h2>Con fuentes, sin inventar obligaciones.</h2>
                <p>La guía se apoya en la Ley Federal de Protección al Consumidor y en la información pública de PROFECO. No sustituye la asesoría de un abogado ni la valoración de un médico veterinario.</p>
                <ul class="t-descarga__puntos">
                    <li>Artículo 12 LFPC: comprobante con los datos específicos del servicio.</li>
                    <li>Artículo 32 LFPC: información y publicidad veraces y comprobables.</li>
                    <li>LFPDPPP: aviso de privacidad y derechos ARCO.</li>
                    <li>No hay una NOM federal específica de estética canina; revisa reglas estatales y municipales.</li>
                </ul>
                <p class="t-descarga__nota">Fuente: <a href="https://www.gob.mx/profeco/documentos/servicios-esteticos-para-mascotas-una-cuestion-de-pelos?state=published" target="_blank" rel="noopener">PROFECO · Servicios estéticos para mascotas</a>.</p>
            </div>
            <div class="t-sistemas">
                <a class="t-sistema" href="<?= url('/descargas/formatos/Mikato-Guia-prestadores.pdf') ?>?v=1.1" download>
                    <span class="t-sistema__icono"><?= icono('descargar') ?></span>
                    <span class="t-sistema__texto"><strong>Guía completa</strong><small>6 páginas · obligaciones y responsabilidades</small></span>
                    <span class="t-sistema__flecha">PDF</span>
                </a>
                <a class="t-sistema" href="<?= url('/descargas/formatos/Mikato-Ficha-de-estilo.pdf') ?>?v=1.1" download>
                    <span class="t-sistema__icono"><?= icono('tijeras') ?></span>
                    <span class="t-sistema__texto"><strong>Ficha de estilo</strong><small>2 páginas · acuerdo de corte</small></span>
                    <span class="t-sistema__flecha">PDF</span>
                </a>
                <a class="t-sistema" href="<?= url('/descargas/formatos/Mikato-Checklist-de-ingreso.pdf') ?>?v=1.1" download>
                    <span class="t-sistema__icono"><?= icono('check') ?></span>
                    <span class="t-sistema__texto"><strong>Checklist de ingreso</strong><small>2 páginas · revisión y autorización</small></span>
                    <span class="t-sistema__flecha">PDF</span>
                </a>
                <a class="t-sistema" href="<?= url('/descargas/formatos/Mikato-Carta-de-servicio.pdf') ?>?v=1.1" download>
                    <span class="t-sistema__icono"><?= icono('correo') ?></span>
                    <span class="t-sistema__texto"><strong>Carta de servicio</strong><small>1 página · para el cliente</small></span>
                    <span class="t-sistema__flecha">PDF</span>
                </a>
            </div>
        </div>
    </div>
</section>

<section class="t-seccion t-seccion--ajustada">
    <div class="contenedor">
        <div class="t-banda-final">
            <div>
                <h2>¿Quieres que tu operación quede dentro del CRM?</h2>
                <p>Los formatos se pueden imprimir y archivar; el CRM guarda la ficha, el historial y el respaldo de cada mascota.</p>
            </div>
            <div class="t-banda-final__acciones">
                <a class="t-btn t-btn--azul" href="<?= url('/descargar') ?>">Descargar el CRM</a>
                <?php if ($whatsapp !== ''): ?>
                    <a class="t-btn t-btn--linea" href="<?= e($whatsapp) ?>" target="_blank" rel="noopener"><?= icono('whatsapp') ?> WhatsApp</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
