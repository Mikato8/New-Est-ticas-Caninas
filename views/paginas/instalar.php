<?php
declare(strict_types=1);
$pasos = [
    'windows' => [
        'titulo' => 'Windows 10 u 11',
        'nota' => 'Funciona en Edge y Chrome. No se descarga ningún archivo.',
        'pasos' => [
            ['img' => 'win1', 'titulo' => 'Abre la app web', 'texto' => 'Entra a mikatoestilistascaninos.com/crm/ en Edge o Chrome. Pulsa el icono Instalar de la barra de direcciones.'],
            ['img' => 'win2', 'titulo' => 'Si no aparece el icono', 'texto' => 'Abre el menú ⋮ y elige Aplicaciones → Instalar este sitio como una aplicación.'],
            ['img' => 'win3', 'titulo' => 'Queda instalada', 'texto' => 'La app se abre en su propia ventana. Fíjala al menú de inicio o a la barra de tareas.'],
        ],
    ],
    'android' => [
        'titulo' => 'Android 8 o superior',
        'nota' => 'Funciona en Chrome. No hace falta activar “fuentes desconocidas”.',
        'pasos' => [
            ['img' => 'and1', 'titulo' => 'Abre la app web', 'texto' => 'Entra a mikatoestilistascaninos.com/crm/ en Chrome y toca el menú ⋮.'],
            ['img' => 'and2', 'titulo' => 'Elige Instalar', 'texto' => 'Toca Instalar aplicación o Añadir a pantalla de inicio.'],
            ['img' => 'and3', 'titulo' => 'Queda en tu inicio', 'texto' => 'El icono de Mikato CRM aparece junto a tus demás apps. Ábrelo como cualquiera.'],
        ],
    ],
    'macos' => [
        'titulo' => 'macOS Sonoma o posterior',
        'nota' => 'En Safari usa Agregar al Dock; en Chrome o Edge, el icono Instalar.',
        'pasos' => [
            ['img' => 'mac1', 'titulo' => 'Abre la app web', 'texto' => 'Entra a mikatoestilistascaninos.com/crm/ en Safari, Chrome o Edge.'],
            ['img' => 'mac2', 'titulo' => 'Agrega al Dock', 'texto' => 'En Safari: menú Archivo → Agregar al Dock. En Chrome o Edge: icono Instalar de la barra de direcciones.'],
            ['img' => 'mac3', 'titulo' => 'Queda en el Dock', 'texto' => 'Haz clic en el icono de Mikato CRM del Dock. La app abre en su propia ventana.'],
        ],
    ],
];
?>
<div class="contenedor">
    <nav class="migas" aria-label="Ruta"><a href="<?= url('/') ?>">Inicio</a><span>/</span><a href="<?= url('/descargar') ?>">Instalar</a><span>/</span>Guía</nav>
</div>

<section class="t-portada" style="padding-bottom:0;">
    <div class="contenedor">
        <div class="t-portada__rejilla">
            <div>
                <span class="t-eyebrow">Guía de instalación</span>
                <h1>Instálalo en tres pasos.</h1>
                <p class="t-portada__lead">La app web se instala desde el navegador, sin descargar <strong>.exe</strong>, <strong>.apk</strong> ni <strong>.dmg</strong>. Elige tu sistema y sigue las capturas. Haz clic en una captura para verla en grande.</p>
                <div class="t-portada__acciones">
                    <a class="t-btn t-btn--azul t-btn--grande" href="<?= url('/crm/') ?>" target="_blank" rel="noopener"><?= icono('descargar') ?> Abrir app web</a>
                    <a class="t-btn t-btn--linea t-btn--grande" href="<?= url('/descargas/formatos/Mikato-Guia-de-instalacion.pdf') ?>" download>Descargar guía PDF</a>
                </div>
                <div class="t-instala__sistemas">
                    <a href="#windows" class="t-btn t-btn--linea t-btn--chico">Windows</a>
                    <a href="#android" class="t-btn t-btn--linea t-btn--chico">Android</a>
                    <a href="#macos" class="t-btn t-btn--linea t-btn--chico">macOS</a>
                </div>
            </div>
            <div class="t-app-marco">
                <div class="t-app">
                    <div class="t-app__barra">
                        <span class="t-app__punto"></span><span class="t-app__punto"></span><span class="t-app__punto"></span>
                        <span class="t-app__titulo">Mikato CRM · Instalación</span>
                    </div>
                    <div style="padding:22px;">
                        <span class="t-kicker">Sin instaladores</span>
                        <h3 style="margin:0 0 8px;font-size:20px;">Tu CRM, en el navegador.</h3>
                        <p style="font-size:13.5px;margin:0 0 14px;">Los datos se guardan en tu equipo. La licencia se activa una vez y la app funciona sin internet.</p>
                        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:8px;font-size:11px;text-align:center;">
                            <div style="border:1px solid var(--t-linea);border-radius:8px;padding:10px 6px;"><strong>Windows</strong><br><span style="color:var(--t-tinta-3);">Edge / Chrome</span></div>
                            <div style="border:1px solid var(--t-linea);border-radius:8px;padding:10px 6px;"><strong>Android</strong><br><span style="color:var(--t-tinta-3);">Chrome</span></div>
                            <div style="border:1px solid var(--t-linea);border-radius:8px;padding:10px 6px;"><strong>macOS</strong><br><span style="color:var(--t-tinta-3);">Safari / Chrome</span></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php foreach ($pasos as $clave => $bloque): ?>
<section class="t-seccion" id="<?= e($clave) ?>" style="padding-block:38px 0;">
    <div class="contenedor">
        <div class="t-seccion__cabeza">
            <div>
                <span class="t-kicker"><?= e($bloque['titulo']) ?></span>
                <h2><?= $clave === 'windows' ? 'Windows' : ($clave === 'android' ? 'Android' : 'macOS') ?></h2>
                <p><?= e($bloque['nota']) ?></p>
            </div>
        </div>
        <div class="t-instala">
            <?php foreach ($bloque['pasos'] as $i => $paso): ?>
            <article class="t-instala__paso">
                <a class="t-instala__zoom" href="<?= url('/assets/img/instalacion/' . $paso['img'] . '.png') ?>" target="_blank" rel="noopener" title="Abrir captura en grande">
                    <img src="<?= url('/assets/img/instalacion/' . $paso['img'] . '.png') ?>" alt="<?= e($bloque['titulo'] . ': ' . $paso['titulo']) ?>" loading="<?= $i === 0 ? 'eager' : 'lazy' ?>" width="900" height="560">
                </a>
                <div class="t-instala__cuerpo">
                    <span class="t-instala__num"><?= $i + 1 ?></span>
                    <h3><?= e($paso['titulo']) ?></h3>
                    <p><?= e($paso['texto']) ?></p>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endforeach; ?>

<section class="t-seccion" style="padding-block:38px;">
    <div class="contenedor">
        <div class="t-descarga">
            <div>
                <span class="t-kicker" style="color:var(--t-oro-claro);">Antes de empezar</span>
                <h2>Datos, licencia y respaldo.</h2>
                <p>La app web guarda la base en IndexedDB de este navegador. Si cambias de equipo o borras los datos del navegador, necesitas tu respaldo.</p>
                <ul class="t-descarga__puntos">
                    <li>Activa la licencia con la clave que te entregó Mikato.</li>
                    <li>Exporta un respaldo desde Ajustes → Respaldo.</li>
                    <li>Funciona sin internet después de la primera carga.</li>
                </ul>
                <p class="t-descarga__nota">¿No tienes clave? <a href="<?= url('/contacto') ?>">Pídela aquí</a>.</p>
            </div>
            <div class="t-sistemas">
                <div class="t-sistema" style="cursor:default;">
                    <span class="t-sistema__icono"><?= icono('escudo') ?></span>
                    <span class="t-sistema__texto"><strong>Sin avisos del sistema</strong><small>No hay instalador que antivirus o Gatekeeper puedan bloquear.</small></span>
                </div>
                <div class="t-sistema" style="cursor:default;">
                    <span class="t-sistema__icono"><?= icono('caja') ?></span>
                    <span class="t-sistema__texto"><strong>Respaldo portable</strong><small>Exporta e importa tu base cuando cambies de equipo.</small></span>
                </div>
                <div class="t-sistema" style="cursor:default;">
                    <span class="t-sistema__icono"><?= icono('reloj') ?></span>
                    <span class="t-sistema__texto"><strong>Actualizaciones automáticas</strong><small>La app se actualiza cuando Mikato publica una versión.</small></span>
                </div>
            </div>
        </div>
    </div>
</section>
