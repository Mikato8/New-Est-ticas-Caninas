<?php
declare(strict_types=1);
$whatsapp = $whatsapp ?? whatsapp_link();
?>
<div class="contenedor">
    <nav class="migas" aria-label="Ruta"><a href="<?= url('/') ?>">Inicio</a><span>/</span>Instalar</nav>
</div>

<section class="t-portada" style="padding-bottom:0;">
    <div class="contenedor">
        <div class="t-portada__rejilla">
            <div>
                <span class="t-eyebrow">Mikato CRM · Instalación</span>
                <h1>Tu CRM, sin instaladores.</h1>
                <p class="t-portada__lead">Instala Mikato CRM en Windows, Android y macOS <strong>como app web, sin descargar archivos</strong>. No hay <strong>.exe</strong>, <strong>.apk</strong> ni <strong>.dmg</strong>, así que Windows no muestra “virus detectado” y macOS no bloquea la app. La agenda, los clientes, las mascotas y las ventas viven en tu equipo.</p>
                <div class="t-portada__acciones">
                    <a class="t-btn t-btn--azul t-btn--grande" href="<?= url('/crm/') ?>" target="_blank" rel="noopener"><?= icono('descargar') ?> Abrir app web</a>
                    <a class="t-btn t-btn--linea t-btn--grande" href="#app-web">Cómo instalar</a>
                </div>
                <ul class="t-portada__pruebas">
                    <li><?= icono('check') ?> Windows, Android y macOS sin SmartScreen ni Gatekeeper.</li>
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


<section class="t-seccion" id="app-web" style="padding-block:42px 0;">
    <div class="contenedor">
        <div class="t-seccion__cabeza">
            <div>
                <span class="t-kicker">Windows, Android y macOS · sin avisos</span>
                <h2>Instálalo como app web.</h2>
                <p>Se abre en el navegador y se instala como una aplicación. No hay <strong>.exe</strong>, <strong>.apk</strong> ni <strong>.dmg</strong>: Windows no muestra “virus detectado”, Android no pide fuente desconocida y macOS no bloquea la app.</p>
            </div>
            <div style="display:flex;gap:10px;flex-wrap:wrap;">
                <a class="t-btn t-btn--azul" href="<?= url('/crm/') ?>" target="_blank" rel="noopener">Abrir app web</a>
                <a class="t-btn t-btn--linea" href="<?= url('/instalar') ?>">Ver guía con capturas</a>
            </div>
        </div>
        <div class="t-guias">
            <article class="t-guia">
                <div class="t-guia__franja"><?= icono('descargar') ?><span>Windows</span></div>
                <div class="t-guia__cuerpo">
                    <h3>Edge o Chrome</h3>
                    <p>Abre la app web, pulsa el icono <strong>Instalar</strong> de la barra de direcciones o entra al menú <strong>Aplicaciones → Instalar este sitio</strong>. Queda en el menú de inicio y se abre en su propia ventana.</p>
                    <div class="t-guia__meta"><span>Sin .exe</span><span>Sin SmartScreen</span></div>
                </div>
            </article>
            <article class="t-guia">
                <div class="t-guia__franja"><?= icono('descargar') ?><span>Android</span></div>
                <div class="t-guia__cuerpo">
                    <h3>Chrome</h3>
                    <p>Abre la app web y usa el menú <strong>Agregar a pantalla de inicio</strong> o <strong>Instalar aplicación</strong>. Funciona como una app y guarda los datos en este dispositivo.</p>
                    <div class="t-guia__meta"><span>Sin APK</span><span>Sin fuente desconocida</span></div>
                </div>
            </article>

            <article class="t-guia">
                <div class="t-guia__franja"><?= icono('descargar') ?><span>macOS</span></div>
                <div class="t-guia__cuerpo">
                    <h3>Safari, Chrome o Edge</h3>
                    <p>Abre la app web. En Safari usa <strong>Archivo → Agregar al Dock</strong>; en Chrome o Edge, el icono <strong>Instalar</strong> de la barra de direcciones. Queda como una app y no pasa por Gatekeeper.</p>
                    <div class="t-guia__meta"><span>Sin .dmg</span><span>Sin Gatekeeper</span></div>
                </div>
            </article>
        </div>
        <div class="nota" style="margin-top:16px;font-size:13.5px;line-height:1.55;">
            <strong>Datos y licencia.</strong> La app web guarda la base en IndexedDB de este navegador; exporta un respaldo desde <em>Ajustes → Respaldo</em>. Funciona sin internet después de la primera carga. La licencia se activa con la misma clave y se verifica sin conexión. 
            <span style="display:block;margin-top:6px;">¿No tienes clave? <a href="<?= url('/contacto') ?>">Pídela aquí</a> · <a href="<?= url('/instalar') ?>">Guía paso a paso con capturas</a>.</span>
        </div>
    </div>
</section>

<section class="t-seccion t-seccion--fondo">
    <div class="contenedor">
        <div class="t-seccion__cabeza">
            <div>
                <span class="t-kicker">Cómo funciona</span>
                <h2>Tres pasos y trabajas.</h2>
            </div>
        </div>
        <div class="t-modulos">
            <article class="t-modulo">
                <div class="t-modulo__icono"><?= icono('descargar') ?></div>
                <h3>1. Abre e instala</h3>
                <p>Abre la app web y usa <strong>Instalar aplicación</strong> del navegador. Queda en el menú de inicio o en el Dock.</p>
            </article>
            <article class="t-modulo">
                <div class="t-modulo__icono"><?= icono('escudo') ?></div>
                <h3>2. Activa</h3>
                <p>Escribe tu clave de acceso. Se valida una vez contra el servidor de licencias y queda guardada en tu equipo.</p>
            </article>
            <article class="t-modulo">
                <div class="t-modulo__icono"><?= icono('agenda') ?></div>
                <h3>3. Trabaja</h3>
                <p>Captura servicios, clientes y citas. Todo se guarda en la base local; puedes exportar un respaldo cuando quieras.</p>
            </article>
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
                    <span class="t-sistema__texto"><strong>Solo Mikato publica versiones</strong><small>La app se actualiza cuando Mikato publica una versión.</small></span>
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
                <h2>Antes de instalar.</h2>
            </div>
        </div>
        <div class="t-guias t-guias--dos">
            <article class="t-guia">
                <div class="t-guia__cuerpo">
                    <h3>¿Necesito internet?</h3>
                    <p>Para descargar y activar, sí. Para agenda, clientes, mascotas, inventario y ventas del día, no: la base vive en tu equipo.</p>
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
                    <p>Exporta el respaldo antes. Al reinstalar, activas la clave y restauras el archivo. Tus datos quedan igual.</p>
                </div>
            </article>
            <article class="t-guia">
                <div class="t-guia__cuerpo">
                    <h3>¿Cómo sé que la actualización es de Mikato?</h3>
                    <p>La app verifica la firma del paquete con la llave pública de Mikato. Si no coincide, no se instala.</p>
                </div>
            </article>
        </div>
    </div>
</section>

<section class="t-seccion t-seccion--ajustada">
    <div class="contenedor">
        <div class="t-banda-final">
            <div>
                <h2>¿Te ayudamos a instalar?</h2>
                <p>Te damos la clave, revisamos los requisitos de tu equipo y te acompañamos en la primera carga de servicios y clientes.</p>
            </div>
            <div class="t-banda-final__acciones">
                <a class="t-btn t-btn--azul" href="<?= url('/contacto') ?>">Solicitar instalación</a>
                <?php if ($whatsapp !== ''): ?>
                    <a class="t-btn t-btn--linea" href="<?= e($whatsapp) ?>" target="_blank" rel="noopener"><?= icono('whatsapp') ?> WhatsApp</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
