<?php
declare(strict_types=1);
$whatsapp = whatsapp_link();
?>
<footer class="t-pie">
    <div class="contenedor">
        <div class="t-pie__rejilla">
            <div class="t-pie__marca">
                <img src="<?= asset('/assets/img/logo-mikato.png') ?>" alt="Mikato">
                <p>CRM y productos para estéticas caninas. Elaboramos en Guadalajara, Jalisco y sostenemos una comunidad de profesionales que cuida a los perros como se debe.</p>
                <p class="mono" style="font-size:11px;letter-spacing:.08em;text-transform:uppercase;color:var(--t-tinta-3);">Mikato · Hecho en Guadalajara, Jalisco</p>
            </div>
            <div>
                <h3>CRM</h3>
                <ul>
                    <li><a href="<?= url('/descargar') ?>">Instalar el CRM</a></li>
                    <li><a href="<?= url('/instalar') ?>">Guía de instalación</a></li>
                    <li><a href="<?= url('/crm/') ?>">Abrir app web</a></li>
                    <li><a href="<?= url('/servicios') ?>">Servicios y precios</a></li>
                    <li><a href="<?= url('/agenda') ?>">Demo de agenda</a></li>
                    <li><a href="<?= url('/calculadoras') ?>">Calculadoras</a></li>
                    <li><a href="<?= url('/entrar') ?>">Entrar a mi cuenta</a></li>
                </ul>
            </div>
            <div>
                <h3>Contenido</h3>
                <ul>
                    <li><a href="<?= url('/guias') ?>">Guías de oficio</a></li>
                    <li><a href="<?= url('/prestadores') ?>">Guías para prestadores</a></li>
                    <li><a href="<?= url('/comunidad') ?>">Comunidad</a></li>
                    <li><a href="<?= url('/directorio') ?>">Directorio nacional</a></li>
                    <li><a href="<?= url('/glosario') ?>">Glosario</a></li>
                    <li><a href="<?= url('/productos') ?>">Productos Mikato</a></li>
                </ul>
            </div>
            <div>
                <h3>Contacto</h3>
                <ul>
                    <li><a href="<?= url('/contacto') ?>">Escríbenos</a></li>
                    <?php if ($whatsapp !== ''): ?><li><a href="<?= e($whatsapp) ?>" target="_blank" rel="noopener">WhatsApp</a></li><?php endif; ?>
                    <?php if ((string) config('contacto.email') !== ''): ?><li><a href="mailto:<?= e(config('contacto.email')) ?>"><?= e(config('contacto.email')) ?></a></li><?php endif; ?>
                    <li><span style="color:var(--t-tinta-3);"><?= e(config('contacto.horario')) ?></span></li>
                    <li><a href="<?= url('/buenas-practicas') ?>">Buenas prácticas</a></li>
                </ul>
            </div>
        </div>
        <div class="t-pie__legal">
            <span>© <?= date('Y') ?> Mikato Estilistas Caninos. Todos los derechos reservados.</span>
            <span>
                <a href="<?= url('/privacidad') ?>">Aviso de privacidad</a> ·
                <a href="<?= url('/derechos/rectificacion') ?>">Derechos ARCO</a> ·
                <a href="<?= url('/terminos') ?>">Términos</a> ·
                <a href="<?= url('/herramientas') ?>">Fuentes y herramientas</a> ·
                <a href="<?= url('/sitemap.xml') ?>">Mapa del sitio</a>
            </span>
        </div>
    </div>
</footer>
