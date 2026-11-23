<?php
declare(strict_types=1);
$whatsapp = $whatsapp ?? whatsapp_link();
?>
<div class="contenedor">
    <nav class="migas" aria-label="Ruta"><a href="<?= url('/') ?>">Inicio</a><span>/</span>Descargar</nav>
</div>

<section class="t-portada" style="padding-bottom:0;">
    <div class="contenedor">
        <div class="t-portada__rejilla">
            <div>
                <span class="t-eyebrow">Mikato CRM · Descarga</span>
                <h1>Tu CRM, en tu computadora.</h1>
                <p class="t-portada__lead">Instala Mikato CRM en Windows, macOS o Android. La agenda, los clientes, las mascotas y las ventas viven en tu equipo: no dependes de una conexión ni de una base compartida.</p>
                <div class="t-portada__acciones">
                    <a class="t-btn t-btn--azul t-btn--grande" href="#sistemas"><?= icono('descargar') ?> Elegir mi sistema</a>
                    <a class="t-btn t-btn--linea t-btn--grande" href="<?= url('/contacto') ?>">Pedir mi clave</a>
                </div>
                <ul class="t-portada__pruebas">
                    <li><?= icono('check') ?> Instalador ligero, sin dependencias ni servidor propio.</li>
                    <li><?= icono('check') ?> Base SQLite local con respaldo exportable.</li>
                    <li><?= icono('check') ?> Actualizaciones firmadas: solo Mikato publica versiones.</li>
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

<section class="t-seccion" id="sistemas">
    <div class="contenedor">
        <div class="t-seccion__cabeza">
            <div>
                <span class="t-kicker">Instaladores</span>
                <h2>Elige tu sistema.</h2>
                <p>La misma app y la misma base para las tres plataformas. Puedes mover tu respaldo de una a otra.</p>
            </div>
        </div>
        <div class="t-modulos">
            <article class="t-modulo" id="windows">
                <div class="t-modulo__icono"><?= icono('descargar') ?></div>
                <h3>Windows</h3>
                <p>Instalador <strong>.exe</strong> para Windows 10 y 11, 64 bits. Instalación por usuario, sin permisos de administrador.</p>
                <div class="t-modulo__pie"><span>Instalador · ≈ 3 MB</span><span>v1.0</span></div>
                <a class="t-btn t-btn--azul t-btn--chico" style="align-self:flex-start;margin-top:4px;" href="<?= url('/descargas/Mikato-CRM-1.0.0-windows-x64-setup.exe') ?>?v=1.0.2" download>Descargar para Windows</a>
                <p style="font-size:12px;margin:2px 0 0;"><a href="<?= url('/descargas/Mikato-CRM-1.0.0-windows-x64.msi') ?>?v=1.0.2">Probar el .msi</a> · <a href="<?= url('/descargas/Mikato-CRM-1.0.0-windows-x64-portable.zip') ?>?v=1.0.2">Versión portable .zip</a></p>
            </article>
            <article class="t-modulo" id="macos">
                <div class="t-modulo__icono"><?= icono('descargar') ?></div>
                <h3>macOS</h3>
                <p>Instalador <strong>.dmg</strong> universal para Apple Silicon e Intel. macOS 12 o superior.</p>
                <div class="t-modulo__pie"><span>DMG universal · ≈ 7 MB</span><span>v1.0</span></div>
                <a class="t-btn t-btn--azul t-btn--chico" style="align-self:flex-start;margin-top:4px;" href="<?= url('/descargas/Mikato-CRM-1.0.0-macOS.dmg') ?>?v=1.0.2" download>Descargar para macOS</a>
            </article>
            <article class="t-modulo" id="android">
                <div class="t-modulo__icono"><?= icono('descargar') ?></div>
                <h3>Android</h3>
                <p>APK firmado para Android 8 o superior, arm64. Para tablet o teléfono del mostrador; restaura tu respaldo y sigue trabajando.</p>
                <div class="t-modulo__pie"><span>APK firmado · ≈ 9 MB</span><span>v1.0</span></div>
                <a class="t-btn t-btn--azul t-btn--chico" style="align-self:flex-start;margin-top:4px;" href="<?= url('/descargas/Mikato-CRM-1.0.0-android.apk') ?>?v=1.0.2" download>Descargar para Android</a>
            </article>
        </div>
        <div class="nota" style="margin-top:16px;font-size:14px;line-height:1.55;">
            <strong>Si Windows o macOS bloquean la instalación</strong>
            <p style="margin:6px 0 8px;">No es un virus. Los instaladores todavía no tienen un certificado comercial de plataforma, así que el sistema los marca como desconocidos. Estos son los pasos para continuar:</p>
            <ul style="margin:0 0 8px;padding-left:20px;">
                <li><strong>Windows:</strong> en SmartScreen, pulsa <em>Más información</em> y luego <em>Ejecutar de todos modos</em>. Si Defender lo pone en cuarentena, restáuralo desde <em>Protección contra virus y amenazas → Historial de protección</em> y agrega una exclusión. También puedes probar el <a href="<?= url('/descargas/Mikato-CRM-1.0.0-windows-x64.msi') ?>?v=1.0.2">instalador .msi</a> o la <a href="<?= url('/descargas/Mikato-CRM-1.0.0-windows-x64-portable.zip') ?>?v=1.0.2">versión portable .zip</a> (extrae y ejecuta; requiere WebView2).</li>
                <li><strong>macOS:</strong> haz clic derecho sobre <em>Mikato CRM</em> y elige <em>Abrir</em>; confirma <em>Abrir</em>. Si no aparece, ve a <em>Ajustes del Sistema → Privacidad y seguridad</em> y pulsa <em>Abrir igualmente</em>. Si dice que está dañada, abre Terminal y ejecuta <code>xattr -dr com.apple.quarantine "/Applications/Mikato CRM.app"</code>, luego ábrela.</li>
            </ul>
            <p style="margin:0;">Puedes comprobar que la descarga es la nuestra con <a href="<?= url('/descargas/SHA256SUMS.txt') ?>">SHA256SUMS.txt</a>.</p>
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
                <h3>1. Instala</h3>
                <p>Descarga el instalador de tu sistema y ábrelo. La app se instala como cualquier programa.</p>
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
                    <li>Las actualizaciones se firman con la llave de Mikato.</li>
                </ul>
            </div>
            <div class="t-sistemas">
                <div class="t-sistema" style="cursor:default;">
                    <span class="t-sistema__icono"><?= icono('escudo') ?></span>
                    <span class="t-sistema__texto"><strong>Solo Mikato publica versiones</strong><small>La app rechaza actualizaciones sin firma válida.</small></span>
                </div>
                <div class="t-sistema" style="cursor:default;">
                    <span class="t-sistema__icono"><?= icono('caja') ?></span>
                    <span class="t-sistema__texto"><strong>Respaldo local</strong><small>Exporta e importa tu base cuando cambies de equipo.</small></span>
                </div>
                <div class="t-sistema" style="cursor:default;">
                    <span class="t-sistema__icono"><?= icono('reloj') ?></span>
                    <span class="t-sistema__texto"><strong>Latencia bajo tu control</strong><small>Agenda y ventas responden desde tu disco, sin esperar la red.</small></span>
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
