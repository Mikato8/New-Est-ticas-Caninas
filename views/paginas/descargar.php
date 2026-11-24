<?php
declare(strict_types=1);
$whatsapp = $whatsapp ?? whatsapp_link();
?>
<div class="contenedor">
    <nav class="migas" aria-label="Ruta"><a href="<?= url('/') ?>">Inicio</a><span>/</span>CRM en línea</nav>
</div>

<section class="t-portada" style="padding-bottom:0;">
    <div class="contenedor">
        <div class="t-portada__rejilla">
            <div>
                <span class="t-eyebrow">Mikato CRM · Software en línea</span>
                <h1>Tu CRM, en línea.</h1>
                <p class="t-portada__lead">Mikato CRM es <strong>software en línea</strong>: se abre desde el navegador, se activa con tu clave y se usa desde cualquier dispositivo con navegador moderno. <strong>No se descarga ningún archivo</strong>, así que no hay avisos de virus ni instaladores.</p>
                <div class="t-portada__acciones">
                    <a class="t-btn t-btn--azul t-btn--grande" href="<?= url('/crm/') ?>" target="_blank" rel="noopener"><?= icono('flecha') ?> Abrir CRM</a>
                    <a class="t-btn t-btn--linea t-btn--grande" href="#como-usar">Cómo funciona</a>
                </div>
                <ul class="t-portada__pruebas">
                    <li><?= icono('check') ?> Sin instaladores, sin descargas y sin avisos del sistema.</li>
                    <li><?= icono('check') ?> Datos locales en IndexedDB, con respaldo exportable.</li>
                    <li><?= icono('check') ?> Licencia por clave y actualizaciones automáticas.</li>
                </ul>
            </div>
            <div class="t-app-marco">
                <div class="t-app">
                    <div class="t-app__barra">
                        <span class="t-app__punto"></span><span class="t-app__punto"></span><span class="t-app__punto"></span>
                        <span class="t-app__titulo">Mikato CRM · Activación</span>
                    </div>
                    <div style="padding:20px;">
                        <span class="t-kicker">Licencia</span>
                        <h3 style="margin:0 0 6px;font-size:18px;">Activa este equipo</h3>
                        <p style="font-size:13px;margin:0 0 14px;">Escribe la clave que te entregó Mikato. Se valida una vez y el equipo queda activado.</p>
                        <div style="border:1px solid var(--t-linea);border-radius:8px;padding:10px 12px;font-family:var(--fuente-mono);font-size:14px;letter-spacing:.16em;color:var(--t-tinta-3);margin-bottom:12px;">MKTO-XXXX-XXXX-XXXX</div>
                        <div style="display:flex;gap:8px;">
                            <span class="t-btn t-btn--azul t-btn--chico" style="flex:1;">Activar</span>
                            <span class="t-btn t-btn--linea t-btn--chico">Comprar clave</span>
                        </div>
                        <p style="font-size:11.5px;color:var(--t-tinta-3);margin:12px 0 0;">Sin conexión permanente. La app funciona después de activar.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<section class="t-seccion" id="como-usar" style="padding-block:42px 0;">
    <div class="contenedor">
        <div class="t-seccion__cabeza">
            <div>
                <span class="t-kicker">Software en línea</span>
                <h2>Entra, activa y trabaja.</h2>
                <p>No hay programa que instalar ni archivo que descargar. El CRM vive en el navegador y tus datos se guardan en tu equipo.</p>
            </div>
            <a class="t-btn t-btn--azul" href="<?= url('/crm/') ?>" target="_blank" rel="noopener">Abrir CRM</a>
        </div>
        <div class="t-guias">
            <article class="t-guia">
                <div class="t-guia__franja"><?= icono('flecha') ?><span>1 · Abre</span></div>
                <div class="t-guia__cuerpo">
                    <h3>Entra desde el navegador</h3>
                    <p>Abre <strong>mikatoestilistascaninos.com/crm/</strong> en Edge, Chrome, Safari o el navegador de tu teléfono. No instales nada.</p>
                    <div class="t-guia__meta"><span>Sin .exe</span><span>Sin .apk</span><span>Sin .dmg</span></div>
                </div>
            </article>
            <article class="t-guia">
                <div class="t-guia__franja"><?= icono('escudo') ?><span>2 · Activa</span></div>
                <div class="t-guia__cuerpo">
                    <h3>Escribe tu clave</h3>
                    <p>En <strong>Activación</strong>, pega la clave <strong>MKTO-XXXX-XXXX-XXXX</strong> que te entregó Mikato y pulsa <strong>Activar licencia</strong>.</p>
                    <div class="t-guia__meta"><span>Una vez</span><span>Queda guardada</span></div>
                </div>
            </article>
            <article class="t-guia">
                <div class="t-guia__franja"><?= icono('agenda') ?><span>3 · Trabaja</span></div>
                <div class="t-guia__cuerpo">
                    <h3>Usa tu CRM</h3>
                    <p>Agenda, clientes, mascotas, ventas y reportes. Todo se guarda en tu equipo y puedes exportar un respaldo cuando quieras.</p>
                    <div class="t-guia__meta"><span>Datos locales</span><span>Respaldo JSON</span></div>
                </div>
            </article>
        </div>
        <div class="nota" style="margin-top:16px;font-size:13.5px;line-height:1.55;">
            <strong>Datos y licencia.</strong> El CRM guarda la base en el almacenamiento del navegador; exporta un respaldo desde <em>Ajustes → Respaldo</em>. La licencia se verifica al activar. 
            <span style="display:block;margin-top:6px;">¿No tienes clave? <a href="<?= url('/contacto') ?>">Pídela aquí</a>.</span>
        </div>
    </div>
</section>

<section class="t-seccion">
    <div class="contenedor">
        <div class="t-descarga">
            <div>
                <span class="t-kicker" style="color:var(--t-oro-claro);">Datos y licencia</span>
                <h2>Tu base no viaja a un servidor ajeno.</h2>
                <p>Cada estética tiene su propia base. Mikato no necesita cargar con los datos de todos los salones para que el programa funcione, y tú no dependes de nuestra disponibilidad.</p>
                <ul class="t-descarga__puntos">
                    <li>La licencia se valida al activar; después el trabajo diario es local.</li>
                    <li>El respaldo se exporta a un archivo que tú controlas.</li>
                    <li>Las actualizaciones las publica solo Mikato.</li>
                </ul>
                <p class="t-descarga__nota">¿Necesitas una versión nativa para un equipo sin navegador moderno? <a href="<?= url('/contacto') ?>">Escríbenos</a> y lo revisamos.</p>
            </div>
            <div class="t-sistemas">
                <div class="t-sistema" style="cursor:default;">
                    <span class="t-sistema__icono"><?= icono('escudo') ?></span>
                    <span class="t-sistema__texto"><strong>Solo Mikato publica versiones</strong><small>El CRM se actualiza cuando Mikato publica una versión.</small></span>
                </div>
                <div class="t-sistema" style="cursor:default;">
                    <span class="t-sistema__icono"><?= icono('caja') ?></span>
                    <span class="t-sistema__texto"><strong>Respaldo local</strong><small>Exporta e importa tu base cuando cambies de equipo.</small></span>
                </div>
                <div class="t-sistema" style="cursor:default;">
                    <span class="t-sistema__icono"><?= icono('reloj') ?></span>
                    <span class="t-sistema__texto"><strong>Latencia bajo tu control</strong><small>Agenda y ventas responden desde tu equipo, sin esperar la red.</small></span>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="t-seccion t-seccion--fondo">
    <div class="contenedor">
        <div class="t-seccion__cabeza">
            <div>
                <span class="t-kicker">Preguntas</span>
                <h2>Antes de empezar.</h2>
            </div>
        </div>
        <div class="t-guias t-guias--dos">
            <article class="t-guia">
                <div class="t-guia__cuerpo">
                    <h3>¿Necesito internet?</h3>
                    <p>Sí: el CRM se usa en línea desde el navegador. Tus datos quedan en el almacenamiento de ese navegador y puedes exportar un respaldo.</p>
                </div>
            </article>
            <article class="t-guia">
                <div class="t-guia__cuerpo">
                    <h3>¿Puedo usar la misma clave en dos equipos?</h3>
                    <p>Cada clave cubre un equipo. Si cambias de computadora, te ayudamos a liberar la anterior y activar la nueva.</p>
                </div>
            </article>
            <article class="t-guia">
                <div class="t-guia__cuerpo">
                    <h3>¿Qué pasa si formateo mi computadora?</h3>
                    <p>Exporta el respaldo antes. Al volver a entrar desde el navegador, activas la clave y restauras el archivo. Tus datos quedan igual.</p>
                </div>
            </article>
            <article class="t-guia">
                <div class="t-guia__cuerpo">
                    <h3>¿Cómo se actualiza?</h3>
                    <p>Las actualizaciones las publica solo Mikato. Al recargar el navegador se carga la versión vigente; no tienes que instalar nada.</p>
                </div>
            </article>
        </div>
    </div>
</section>

<section class="t-seccion t-seccion--ajustada">
    <div class="contenedor">
        <div class="t-banda-final">
            <div>
                <h2>¿Te ayudamos a empezar?</h2>
                <p>Te damos la clave y te acompañamos en la primera carga de servicios y clientes.</p>
            </div>
            <div class="t-banda-final__acciones">
                <a class="t-btn t-btn--azul" href="<?= url('/contacto') ?>">Solicitar acceso</a>
                <?php if ($whatsapp !== ''): ?>
                    <a class="t-btn t-btn--linea" href="<?= e($whatsapp) ?>" target="_blank" rel="noopener"><?= icono('whatsapp') ?> WhatsApp</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
