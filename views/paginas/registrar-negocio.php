<?php
declare(strict_types=1);
$errores = errores();
$estados = ['Aguascalientes','Baja California','Baja California Sur','Campeche','Chiapas','Chihuahua','Ciudad de México','Coahuila','Colima','Durango','Estado de México','Guanajuato','Guerrero','Hidalgo','Jalisco','Michoacán','Morelos','Nayarit','Nuevo León','Oaxaca','Puebla','Querétaro','Quintana Roo','San Luis Potosí','Sinaloa','Sonora','Tabasco','Tamaulipas','Tlaxcala','Veracruz','Yucatán','Zacatecas'];
?>
<div class="contenedor">
    <nav class="migas" aria-label="Ruta"><a href="<?= url('/') ?>">Inicio</a><span>/</span><a href="<?= url('/directorio') ?>">Directorio</a><span>/</span>Registrar</nav>
    <header class="portada-interior">
        <span class="mono texto-oro">Directorio Mikato</span>
        <h1>Registra tu estética.</h1>
        <p>Publica tus servicios, horario y contacto. Es gratis. Revisamos cada ficha para mantener la confianza del directorio.</p>
    </header>
</div>

<section class="seccion seccion--compacta">
    <div class="contenedor">
        <?php if ($existente !== null): ?>
            <div class="tarjeta tarjeta__cuerpo" style="max-width:720px;margin-inline:auto;">
                <h2 style="font-size:1.6rem;">Ya tienes una ficha registrada</h2>
                <p class="texto-suave">La ficha <strong><?= e($existente['nombre']) ?></strong> está en estado <strong><?= e($existente['estado']) ?></strong>. Si necesitas actualizarla, escríbenos y la revisamos contigo.</p>
                <div class="flex-centro">
                    <a class="boton" href="<?= url('/directorio') ?>">Ir al directorio</a>
                    <a class="boton boton--contorno" href="<?= url('/contacto') ?>">Solicitar cambio</a>
                </div>
            </div>
        <?php else: ?>
        <div class="grid grid--2" style="align-items:start;">
            <form class="tarjeta tarjeta__cuerpo formulario" method="post" action="<?= url('/directorio') ?>" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div style="position:absolute;left:-9999px;" aria-hidden="true"><label for="sitio_web">No llenar</label><input id="sitio_web" type="text" name="sitio_web" tabindex="-1" autocomplete="off"></div>
                <div class="rejilla-campos">
                    <div class="campo campo--6<?= isset($errores['nombre']) ? ' campo--error' : '' ?>"><label for="nombre">Nombre de la estética *</label><input id="nombre" name="nombre" type="text" required maxlength="120" value="<?= e(viejo('nombre')) ?>"><?php if (isset($errores['nombre'])): ?><span class="error"><?= e($errores['nombre']) ?></span><?php endif; ?></div>
                    <div class="campo campo--6"><label for="ciudad">Ciudad *</label><input id="ciudad" name="ciudad" type="text" required maxlength="80" value="<?= e(viejo('ciudad')) ?>"></div>
                    <div class="campo campo--6"><label for="estado_geo">Estado *</label><select id="estado_geo" name="estado_geo" required><option value="">Elige</option><?php foreach ($estados as $estado): ?><option value="<?= e($estado) ?>" <?= viejo('estado_geo') === $estado ? 'selected' : '' ?>><?= e($estado) ?></option><?php endforeach; ?></select></div>
                    <div class="campo campo--6"><label for="telefono">Teléfono</label><input id="telefono" name="telefono" type="tel" maxlength="30" value="<?= e(viejo('telefono')) ?>"></div>
                    <div class="campo campo--6"><label for="whatsapp">WhatsApp</label><input id="whatsapp" name="whatsapp" type="tel" maxlength="30" value="<?= e(viejo('whatsapp')) ?>"></div>
                    <div class="campo campo--6"><label for="correo">Correo</label><input id="correo" name="correo" type="email" maxlength="160" value="<?= e(viejo('correo')) ?>"></div>
                    <div class="campo campo--6"><label for="web">Sitio web o redes</label><input id="web" name="web" type="url" maxlength="200" placeholder="https://" value="<?= e(viejo('web')) ?>"></div>
                    <div class="campo campo--6"><label for="direccion">Dirección</label><input id="direccion" name="direccion" type="text" maxlength="200" value="<?= e(viejo('direccion')) ?>"></div>
                    <div class="campo"><label for="servicios">Servicios (separados por coma)</label><input id="servicios" name="servicios" type="text" maxlength="300" placeholder="Baño, corte, uñas, oídos, spa" value="<?= e(viejo('servicios')) ?>"></div>
                    <div class="campo"><label for="horario">Horario</label><textarea id="horario" name="horario" maxlength="200" style="min-height:80px;" placeholder="Lun a sáb 9:00–19:00"><?= e(viejo('horario')) ?></textarea></div>
                    <div class="campo"><label for="descripcion">Descripción *</label><textarea id="descripcion" name="descripcion" required minlength="40" maxlength="1200" placeholder="Qué haces, cómo trabajas, qué te distingue."><?= e(viejo('descripcion')) ?></textarea><?php if (isset($errores['descripcion'])): ?><span class="error"><?= e($errores['descripcion']) ?></span><?php endif; ?></div>
                    <div class="campo"><label for="logo">Logo o foto (opcional)</label><input id="logo" name="logo" type="file" accept="image/jpeg,image/png,image/webp" class="archivo"><span class="ayuda">JPG, PNG o WebP. Máximo 3 MB.</span></div>
                </div>
                <div class="formulario__pie">
                    <button class="boton" type="submit">Enviar a revisión</button>
                    <a class="boton boton--contorno" href="<?= url('/directorio') ?>">Cancelar</a>
                </div>
            </form>

            <aside class="aside-sticky">
                <div class="aside-caja">
                    <h3>Qué revisamos</h3>
                    <ul>
                        <li>Que el negocio exista y los datos sean verificables.</li>
                        <li>Que la descripción sea clara y sin promesas médicas.</li>
                        <li>Que el contacto funcione.</li>
                        <li>Que no haya duplicados.</li>
                    </ul>
                </div>
                <div class="aside-caja">
                    <h3>Tu privacidad</h3>
                    <p style="font-size:.87rem;color:var(--tinta-suave);">Publicar tu teléfono es opcional. Puedes usar solo WhatsApp. No vendemos datos ni cobramos por aparecer.</p>
                    <a class="enlace-flecha" href="<?= url('/privacidad') ?>">Aviso de privacidad <?= icono('flecha') ?></a>
                </div>
                <div class="aside-caja" style="background:var(--oro-tenue);border-color:rgba(184,137,47,.3);">
                    <h3>Tiempo de revisión</h3>
                    <p style="font-size:.87rem;color:#6b4c12;">Hasta 48 horas hábiles. Si falta información, te escribimos al correo o WhatsApp que registres.</p>
                </div>
            </aside>
        </div>
        <?php endif; ?>
    </div>
</section>
