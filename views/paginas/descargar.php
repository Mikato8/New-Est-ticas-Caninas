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
                <span class="t-eyebrow">Mikato CRM · En línea</span>
                <h1>Tu CRM, en línea y conectado.</h1>
                <p class="t-portada__lead">Entra con tu cuenta desde cualquier dispositivo. Tu equipo ve los mismos clientes, mascotas, citas y ventas, sin instalar archivos ni compartir bases por separado.</p>
                <div class="t-portada__acciones">
                    <a class="t-btn t-btn--azul t-btn--grande" href="<?= url('/panel') ?>"><?= icono('flecha') ?> Entrar al CRM</a>
                    <a class="t-btn t-btn--linea t-btn--grande" href="#como-usar">Cómo funciona</a>
                </div>
                <ul class="t-portada__pruebas">
                    <li><?= icono('check') ?> Cuentas para todo el equipo, con roles y permisos.</li>
                    <li><?= icono('check') ?> Una sola base: los cambios se ven en todos los dispositivos.</li>
                    <li><?= icono('check') ?> Sin instaladores, sin descargas y sin avisos del sistema.</li>
                </ul>
            </div>
            <div class="t-app-marco">
                <div class="t-app">
                    <div class="t-app__barra">
                        <span class="t-app__punto"></span><span class="t-app__punto"></span><span class="t-app__punto"></span>
                        <span class="t-app__titulo">Mikato CRM · Iniciar sesión</span>
                    </div>
                    <div style="padding:20px;">
                        <span class="t-kicker">Cuenta del equipo</span>
                        <h3 style="margin:0 0 12px;font-size:18px;">Entra a tu estética</h3>
                        <div style="border:1px solid var(--t-linea);border-radius:8px;padding:9px 12px;font-size:12px;color:var(--t-tinta-3);margin-bottom:8px;">Correo o usuario</div>
                        <div style="border:1px solid var(--t-linea);border-radius:8px;padding:9px 12px;font-size:12px;color:var(--t-tinta-3);margin-bottom:12px;">Contraseña</div>
                        <div class="t-btn t-btn--azul t-btn--chico" style="width:100%;justify-content:center;">Entrar al CRM</div>
                        <p style="font-size:11.5px;color:var(--t-tinta-3);margin:12px 0 0;">Si no tienes cuenta, el administrador de tu estética te la crea.</p>
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
                <span class="t-kicker">Cuentas conectadas</span>
                <h2>Entra, inicia sesión y trabaja.</h2>
                <p>El CRM vive en el servidor de Mikato. Cada persona entra con su cuenta y todos trabajan sobre la misma información.</p>
            </div>
            <a class="t-btn t-btn--azul" href="<?= url('/panel') ?>">Entrar al CRM</a>
        </div>
        <div class="t-guias">
            <article class="t-guia">
                <div class="t-guia__franja"><?= icono('flecha') ?><span>1 · Entra</span></div>
                <div class="t-guia__cuerpo">
                    <h3>Abre el CRM</h3>
                    <p>Entra a <strong>mikatoestilistascaninos.com/panel</strong> desde cualquier navegador. No hay nada que instalar.</p>
                    <div class="t-guia__meta"><span>Sin .exe</span><span>Sin .apk</span><span>Sin .dmg</span></div>
                </div>
            </article>
            <article class="t-guia">
                <div class="t-guia__franja"><?= icono('perfil') ?><span>2 · Inicia sesión</span></div>
                <div class="t-guia__cuerpo">
                    <h3>Tu cuenta del equipo</h3>
                    <p>Escribe el correo o usuario y la contraseña que te dio el administrador de tu estética. Cada persona tiene su propia cuenta.</p>
                    <div class="t-guia__meta"><span>Admin</span><span>Editor</span><span>Estilista</span></div>
                </div>
            </article>
            <article class="t-guia">
                <div class="t-guia__franja"><?= icono('agenda') ?><span>3 · Trabaja</span></div>
                <div class="t-guia__cuerpo">
                    <h3>Todo el equipo conectado</h3>
                    <p>Agenda, clientes, mascotas, ventas y reportes se guardan en una sola base. Lo que registra recepción lo ve el estilista.</p>
                    <div class="t-guia__meta"><span>Datos compartidos</span><span>Multiusuario</span></div>
                </div>
            </article>
        </div>
        <div class="nota" style="margin-top:16px;font-size:13.5px;line-height:1.55;">
            <strong>¿No tienes cuenta?</strong> El administrador de tu estética puede crearla en <strong>Panel → Usuarios</strong>. Si necesitas ayuda, <a href="<?= url('/contacto') ?>">escríbenos</a>.
        </div>
    </div>
</section>

<section class="t-seccion t-seccion--fondo">
    <div class="contenedor">
        <div class="t-descarga">
            <div>
                <span class="t-kicker" style="color:var(--t-oro-claro);">Cuentas y datos</span>
                <h2>Una sola base para todo el equipo.</h2>
                <p>La información vive en el servidor de Mikato y se comparte entre las cuentas autorizadas de tu estética. No hay bases separadas por computadora.</p>
                <ul class="t-descarga__puntos">
                    <li>El administrador crea las cuentas y asigna el rol de cada persona.</li>
                    <li>Puede limitar el acceso de un usuario hasta una fecha.</li>
                    <li>Los cambios se ven al instante en los demás dispositivos.</li>
                </ul>
                <p class="t-descarga__nota">¿Necesitas otra cuenta o cambiar permisos? <a href="<?= url('/panel/usuarios') ?>">Administrar usuarios</a>.</p>
            </div>
            <div class="t-sistemas">
                <div class="t-sistema" style="cursor:default;">
                    <span class="t-sistema__icono"><?= icono('escudo') ?></span>
                    <span class="t-sistema__texto"><strong>Roles y permisos</strong><small>Administrador, editor y estilista.</small></span>
                </div>
                <div class="t-sistema" style="cursor:default;">
                    <span class="t-sistema__icono"><?= icono('perfil') ?></span>
                    <span class="t-sistema__texto"><strong>Cuentas conectadas</strong><small>La misma cuenta funciona en cualquier dispositivo.</small></span>
                </div>
                <div class="t-sistema" style="cursor:default;">
                    <span class="t-sistema__icono"><?= icono('reloj') ?></span>
                    <span class="t-sistema__texto"><strong>Actualizaciones automáticas</strong><small>La versión vigente se carga al entrar.</small></span>
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
                <h2>Antes de entrar.</h2>
            </div>
        </div>
        <div class="t-guias t-guias--dos">
            <article class="t-guia">
                <div class="t-guia__cuerpo">
                    <h3>¿Necesito internet?</h3>
                    <p>Sí: el CRM es un servicio en línea. Necesitas conexión para entrar y trabajar; los datos se guardan en el servidor.</p>
                </div>
            </article>
            <article class="t-guia">
                <div class="t-guia__cuerpo">
                    <h3>¿Puedo usar la misma cuenta en dos equipos?</h3>
                    <p>Sí. La cuenta es la misma en cualquier dispositivo y los datos que ves son los mismos para todo el equipo.</p>
                </div>
            </article>
            <article class="t-guia">
                <div class="t-guia__cuerpo">
                    <h3>¿Qué pasa si formateo mi computadora?</h3>
                    <p>Nada: solo vuelve a entrar desde el navegador con tu cuenta. La información está en el servidor, no en tu equipo.</p>
                </div>
            </article>
            <article class="t-guia">
                <div class="t-guia__cuerpo">
                    <h3>¿Cómo se crean las cuentas?</h3>
                    <p>El administrador las crea en <strong>Panel → Usuarios</strong>, asigna rol y contraseña temporal. Cada persona puede cambiar su acceso.</p>
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
                <p>Te acompañamos a crear la cuenta del administrador y a cargar tus primeros servicios y clientes.</p>
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
