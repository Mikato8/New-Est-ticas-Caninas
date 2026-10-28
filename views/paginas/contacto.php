<?php
declare(strict_types=1);
$errores = errores();
$whatsapp = whatsapp_link();
$correo = (string) config('contacto.email');
$telefono = (string) config('contacto.telefono');
?>
<div class="contenedor">
    <nav class="migas" aria-label="Ruta"><a href="<?= url('/') ?>">Inicio</a><span>/</span>Contacto</nav>
    <header class="portada-interior">
        <span class="mono texto-oro">Contacto</span>
        <h1>Hablemos de tu perro o de tu estética.</h1>
        <p>Agenda, mayoreo, dudas de cuidado o registro en el directorio. Escríbenos y te respondemos en horario de oficina.</p>
    </header>
</div>

<section class="seccion seccion--compacta">
    <div class="contenedor">
        <div class="split" style="align-items:start;">
            <div class="tarjeta tarjeta__cuerpo">
                <h2 style="font-size:1.7rem;">Escríbenos</h2>
                <p class="texto-suave">Los campos con * son obligatorios. No compartas datos médicos sensibles en este formulario.</p>
                <form class="formulario" method="post" action="<?= url('/contacto') ?>" novalidate>
                    <?= csrf_field() ?>
                    <div style="position:absolute;left:-9999px;" aria-hidden="true"><label for="sitio_web">No llenar</label><input id="sitio_web" type="text" name="sitio_web" tabindex="-1" autocomplete="off"></div>
                    <div class="rejilla-campos">
                        <div class="campo campo--6<?= isset($errores['nombre']) ? ' campo--error' : '' ?>">
                            <label for="nombre">Nombre *</label>
                            <input id="nombre" name="nombre" type="text" value="<?= e(viejo('nombre')) ?>" required maxlength="120" autocomplete="name">
                            <?php if (isset($errores['nombre'])): ?><span class="error"><?= e($errores['nombre']) ?></span><?php endif; ?>
                        </div>
                        <div class="campo campo--6<?= isset($errores['correo']) ? ' campo--error' : '' ?>">
                            <label for="correo">Correo *</label>
                            <input id="correo" name="correo" type="email" value="<?= e(viejo('correo')) ?>" required maxlength="160" autocomplete="email">
                            <?php if (isset($errores['correo'])): ?><span class="error"><?= e($errores['correo']) ?></span><?php endif; ?>
                        </div>
                        <div class="campo campo--6">
                            <label for="telefono">Teléfono / WhatsApp</label>
                            <input id="telefono" name="telefono" type="tel" value="<?= e(viejo('telefono')) ?>" maxlength="30" autocomplete="tel">
                        </div>
                        <div class="campo campo--6">
                            <label for="asunto">Asunto</label>
                            <select id="asunto" name="asunto">
                                <option value="servicio">Quiero agendar un servicio</option>
                                <option value="mayoreo">Mayoreo / distribución</option>
                                <option value="directorio">Registrar mi estética</option>
                                <option value="comunidad">Comunidad y contenido</option>
                                <option value="otro">Otro</option>
                            </select>
                        </div>
                        <div class="campo<?= isset($errores['mensaje']) ? ' campo--error' : '' ?>">
                            <label for="mensaje">Mensaje *</label>
                            <textarea id="mensaje" name="mensaje" required minlength="15" maxlength="3000" placeholder="Cuéntanos qué necesitas, el tamaño y la raza de tu perro, o el volumen de producto que buscas."><?= e(viejo('mensaje')) ?></textarea>
                            <?php if (isset($errores['mensaje'])): ?><span class="error"><?= e($errores['mensaje']) ?></span><?php endif; ?>
                        </div>
                    </div>
                    <div class="formulario__pie">
                        <button class="boton" type="submit"><?= icono('correo') ?> Enviar mensaje</button>
                        <span class="texto-tenue" style="font-size:.78rem;">Al enviar aceptas el <a href="<?= url('/privacidad') ?>">aviso de privacidad</a>.</span>
                    </div>
                </form>
            </div>

            <aside class="aside-sticky">
                <div class="aside-caja">
                    <h3>Datos de contacto</h3>
                    <div class="datos-lista">
                        <?php if ($correo !== ''): ?>
                            <div><dt>Correo</dt><dd><a href="mailto:<?= e($correo) ?>"><?= e($correo) ?></a></dd></div>
                        <?php endif; ?>
                        <?php if ($telefono !== ''): ?>
                            <div><dt>Teléfono</dt><dd><a href="tel:<?= e(preg_replace('/\s+/', '', $telefono)) ?>"><?= e($telefono) ?></a></dd></div>
                        <?php endif; ?>
                        <div><dt>Horario</dt><dd><?= e(config('contacto.horario')) ?></dd></div>
                        <div><dt>Ciudad</dt><dd><?= e(config('contacto.ciudad')) ?></dd></div>
                    </div>
                    <?php if ($whatsapp !== ''): ?>
                        <a class="boton boton--bloque mt-3" href="<?= e($whatsapp) ?>" target="_blank" rel="noopener"><?= icono('whatsapp') ?> Escribir por WhatsApp</a>
                    <?php else: ?>
                        <a class="boton boton--bloque mt-3" href="<?= url('/agenda') ?>"><?= icono('agenda') ?> Agendar en línea</a>
                    <?php endif; ?>
                </div>
                <div class="aside-caja">
                    <h3>Antes de escribir</h3>
                    <ul>
                        <li>Para una urgencia médica, llama a tu veterinario. No atendemos emergencias por este medio.</li>
                        <li>Si tu perro tiene heridas, hongos o dolor, lo valoramos antes de bañarlo.</li>
                        <li>Para mayoreo, dinos ciudad, volumen mensual y tipo de productos.</li>
                    </ul>
                </div>
            </aside>
        </div>
    </div>
</section>
