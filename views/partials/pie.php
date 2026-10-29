<?php
declare(strict_types=1);
$whatsapp = whatsapp_link();
?>
<footer class="pie">
    <div class="contenedor">
        <div class="pie__rejilla">
            <div class="pie__marca">
                <img src="<?= asset('/assets/img/logo-mikato.png') ?>" alt="Mikato">
                <p>CRM y productos para estéticas caninas. Elaboramos en Guadalajara, Jalisco y sostenemos una comunidad de profesionales que cuida a los perros como se debe.</p>
                <p class="pie__hecho">Mikato · Hecho en Guadalajara, Jalisco</p>
            </div>
            <div>
                <h4>Explora</h4>
                <ul>
                    <li><a href="<?= url('/servicios') ?>">Servicios y precios</a></li>
                    <li><a href="<?= url('/guias') ?>">Guías de oficio</a></li>
                    <li><a href="<?= url('/comunidad') ?>">Comunidad</a></li>
                    <li><a href="<?= url('/directorio') ?>">Directorio</a></li>
                    <li><a href="<?= url('/productos') ?>">Productos Mikato</a></li>
                </ul>
            </div>
            <div>
                <h4>Herramientas</h4>
                <ul>
                    <li><a href="<?= url('/agenda') ?>">Demo de agenda</a></li>
                    <li><a href="<?= url('/calculadoras') ?>">Calculadoras</a></li>
                    <li><a href="<?= url('/asistente') ?>">Asistente local</a></li>
                    <li><a href="<?= url('/glosario') ?>">Glosario</a></li>
                    <li><a href="<?= url('/buenas-practicas') ?>">Buenas prácticas</a></li>
                </ul>
            </div>
            <div>
                <h4>Contacto</h4>
                <ul>
                    <li><a href="<?= url('/contacto') ?>">Escríbenos</a></li>
                    <?php if ($whatsapp !== ''): ?><li><a href="<?= e($whatsapp) ?>" target="_blank" rel="noopener">WhatsApp</a></li><?php endif; ?>
                    <?php if ((string) config('contacto.email') !== ''): ?><li><a href="mailto:<?= e(config('contacto.email')) ?>"><?= e(config('contacto.email')) ?></a></li><?php endif; ?>
                    <li><span class="texto-tenue"><?= e(config('contacto.horario')) ?></span></li>
                </ul>
                <form class="pie__boletin" method="post" action="<?= url('/boletin') ?>">
                    <?= csrf_field() ?>
                    <label class="sr-only" for="boletin-correo">Tu correo</label>
                    <input id="boletin-correo" type="email" name="correo" placeholder="Tu correo" required>
                    <button type="submit" aria-label="Suscribirme"><?= icono('flecha') ?></button>
                </form>
                <p class="texto-tenue" style="font-size:.72rem;margin-top:8px;">Nuevas guías y productos. Sin spam.</p>
            </div>
        </div>
        <div class="pie__legal">
            <span>© <?= date('Y') ?> Mikato Estilistas Caninos. Todos los derechos reservados.</span>
            <span>
                <a href="<?= url('/privacidad') ?>">Aviso de privacidad</a> ·
                <a href="<?= url('/derechos/rectificacion') ?>">Derechos ARCO</a> ·
                <a href="<?= url('/terminos') ?>">Términos</a> ·
                <a href="<?= url('/herramientas') ?>">Fuentes y herramientas</a> ·
                <a href="<?= url('/sitemap.xml') ?>">Mapa del sitio</a>
            </span>
        </div>
        <p class="texto-tenue" style="font-size:.72rem;padding-bottom:22px;margin:0;">El contenido de este portal es educativo y no sustituye la valoración de un médico veterinario. Ante una urgencia, acude a tu veterinario de confianza.</p>
    </div>
</footer>
