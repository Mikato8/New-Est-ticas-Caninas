<?php
declare(strict_types=1);
$errores = errores();
$medio = viejo('notificacion_medio', 'correo');
?>
<div class="contenedor">
    <nav class="migas" aria-label="Ruta"><a href="<?= url('/') ?>">Inicio</a><span>/</span><a href="<?= url('/privacidad') ?>">Privacidad</a><span>/</span>Rectificación</nav>
    <header class="portada-interior">
        <span class="mono texto-oro">Derechos ARCO · LFPDPPP</span>
        <h1>Solicitud de rectificación de datos personales.</h1>
        <p>Usa este formato si algún dato personal que tenemos sobre ti es <strong>inexacto, incompleto o está desactualizado</strong>. Con fundamento en los artículos 27, 31 y 34 de la Ley Federal de Protección de Datos Personales en Posesión de los Particulares.</p>
    </header>
</div>

<section class="seccion seccion--compacta">
    <div class="contenedor">
        <form class="tarjeta tarjeta__cuerpo formulario" method="post" action="<?= url('/derechos/rectificacion') ?>" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <div style="position:absolute;left:-9999px;" aria-hidden="true"><label for="sitio_web_arco">No llenar</label><input id="sitio_web_arco" type="text" name="sitio_web" tabindex="-1" autocomplete="off"></div>

            <h2>1. Datos del titular</h2>
            <p class="texto-suave" style="font-size:.9rem;">Lugar y fecha: <strong><?= e($ciudad) ?></strong>, <?= e(fecha_larga(date('Y-m-d'))) ?>.</p>
            <div class="rejilla-campos">
                <div class="campo campo--6<?= isset($errores['nombre_titular']) ? ' campo--error' : '' ?>">
                    <label for="nombre_titular">Nombre completo *</label>
                    <input id="nombre_titular" name="nombre_titular" type="text" required maxlength="160" value="<?= e(viejo('nombre_titular')) ?>" autocomplete="name">
                    <?php if (isset($errores['nombre_titular'])): ?><span class="error"><?= e($errores['nombre_titular']) ?></span><?php endif; ?>
                </div>
                <div class="campo campo--6"><label for="numero_cliente">Número de cliente o identificador</label><input id="numero_cliente" name="numero_cliente" type="text" maxlength="80" value="<?= e(viejo('numero_cliente')) ?>"></div>
                <div class="campo campo--6<?= isset($errores['correo']) ? ' campo--error' : '' ?>"><label for="correo_arco">Correo electrónico *</label><input id="correo_arco" name="correo" type="email" required maxlength="160" value="<?= e(viejo('correo')) ?>" autocomplete="email"><?php if (isset($errores['correo'])): ?><span class="error"><?= e($errores['correo']) ?></span><?php endif; ?></div>
                <div class="campo campo--6"><label for="telefono_arco">Teléfono (opcional)</label><input id="telefono_arco" name="telefono" type="tel" maxlength="30" value="<?= e(viejo('telefono')) ?>" autocomplete="tel"></div>
                <div class="campo campo--6<?= isset($errores['ciudad']) ? ' campo--error' : '' ?>"><label for="ciudad">Ciudad *</label><input id="ciudad" name="ciudad" type="text" required maxlength="120" value="<?= e(viejo('ciudad', $ciudad)) ?>"><?php if (isset($errores['ciudad'])): ?><span class="error"><?= e($errores['ciudad']) ?></span><?php endif; ?></div>
            </div>

            <h2 class="mt-4">2. Representante legal (si aplica)</h2>
            <div class="rejilla-campos">
                <div class="campo campo--6"><label for="nombre_representante">Nombre completo del representante</label><input id="nombre_representante" name="nombre_representante" type="text" maxlength="160" value="<?= e(viejo('nombre_representante')) ?>"></div>
                <div class="campo campo--6">
                    <label for="tipo_representacion">Documento que acredita la representación</label>
                    <select id="tipo_representacion" name="tipo_representacion">
                        <option value="">No aplica</option>
                        <option value="poder" <?= viejo('tipo_representacion') === 'poder' ? 'selected' : '' ?>>Instrumento público (poder notarial)</option>
                        <option value="carta" <?= viejo('tipo_representacion') === 'carta' ? 'selected' : '' ?>>Carta poder firmada ante dos testigos</option>
                        <option value="comparecencia" <?= viejo('tipo_representacion') === 'comparecencia' ? 'selected' : '' ?>>Declaración en comparecencia del titular</option>
                    </select>
                </div>
                <div class="campo"><label for="representacion">Copia del documento de representación</label><input id="representacion" name="representacion" type="file" accept="application/pdf,image/jpeg,image/png,image/webp"><span class="ayuda">PDF, JPG, PNG o WebP. Máximo 5 MB. Opcional si no actúas por representación.</span></div>
            </div>

            <h2 class="mt-4">3. Datos que solicito corregir</h2>
            <div class="tabla-scroll">
                <table class="tabla">
                    <thead><tr><th>Dato incorrecto (actual)</th><th>Dato correcto (solicitado)</th><th>Documento acreditativo</th></tr></thead>
                    <tbody><tr>
                        <td><textarea name="dato_incorrecto" required minlength="3" maxlength="800" placeholder="Ej. Teléfono registrado incorrecto"><?= e(viejo('dato_incorrecto')) ?></textarea><?php if (isset($errores['dato_incorrecto'])): ?><span class="error"><?= e($errores['dato_incorrecto']) ?></span><?php endif; ?></td>
                        <td><textarea name="dato_correcto" required minlength="3" maxlength="800" placeholder="Ej. Teléfono correcto"><?= e(viejo('dato_correcto')) ?></textarea><?php if (isset($errores['dato_correcto'])): ?><span class="error"><?= e($errores['dato_correcto']) ?></span><?php endif; ?></td>
                        <td><textarea name="documento_acreditativo" required maxlength="300" placeholder="Ej. Comprobante de domicilio, identificación, constancia"><?= e(viejo('documento_acreditativo')) ?></textarea><?php if (isset($errores['documento_acreditativo'])): ?><span class="error"><?= e($errores['documento_acreditativo']) ?></span><?php endif; ?></td>
                    </tr></tbody>
                </table>
            </div>
            <div class="campo mt-2"><label for="acreditativo">Copia del documento que sustenta el cambio</label><input id="acreditativo" name="acreditativo" type="file" accept="application/pdf,image/jpeg,image/png,image/webp"><span class="ayuda">PDF, JPG, PNG o WebP. Máximo 5 MB. De preferencia con antigüedad no mayor a tres meses.</span></div>
            <div class="campo"><label for="identificacion">Identificación oficial del titular</label><input id="identificacion" name="identificacion" type="file" accept="application/pdf,image/jpeg,image/png,image/webp"><span class="ayuda">INE, pasaporte o identificación oficial vigente. PDF o imagen, máximo 5 MB.</span></div>

            <h2 class="mt-4">4. Medio para recibir notificaciones</h2>
            <div class="campo">
                <label class="campo--inline flex-centro" style="gap:8px;"><input type="radio" name="notificacion_medio" value="correo" <?= $medio === 'correo' ? 'checked' : '' ?>> Correo electrónico</label>
                <label class="campo--inline flex-centro" style="gap:8px;"><input type="radio" name="notificacion_medio" value="domicilio" <?= $medio === 'domicilio' ? 'checked' : '' ?>> Domicilio</label>
                <label class="campo--inline flex-centro" style="gap:8px;"><input type="radio" name="notificacion_medio" value="oficina" <?= $medio === 'oficina' ? 'checked' : '' ?>> Acudir a la oficina</label>
                <?php if (isset($errores['notificacion_medio'])): ?><span class="error"><?= e($errores['notificacion_medio']) ?></span><?php endif; ?>
            </div>
            <div class="rejilla-campos">
                <div class="campo campo--6<?= isset($errores['domicilio']) ? ' campo--error' : '' ?>"><label for="domicilio">Domicilio completo (si eliges domicilio)</label><input id="domicilio" name="domicilio" type="text" maxlength="300" value="<?= e(viejo('domicilio')) ?>"><?php if (isset($errores['domicilio'])): ?><span class="error"><?= e($errores['domicilio']) ?></span><?php endif; ?></div>
                <div class="campo campo--6"><label for="direccion_oficina">Oficina (si eliges acudir)</label><input id="direccion_oficina" name="direccion_oficina" type="text" maxlength="300" value="<?= e(viejo('direccion_oficina', $direccion)) ?>"></div>
            </div>

            <h2 class="mt-4">Firma y conformidad</h2>
            <div class="campo<?= isset($errores['nombre_firma']) ? ' campo--error' : '' ?>"><label for="nombre_firma">Nombre completo con el que firmas *</label><input id="nombre_firma" name="nombre_firma" type="text" required maxlength="160" value="<?= e(viejo('nombre_firma')) ?>"><?php if (isset($errores['nombre_firma'])): ?><span class="error"><?= e($errores['nombre_firma']) ?></span><?php endif; ?></div>
            <label class="campo--inline flex-centro" style="gap:9px;align-items:flex-start;">
                <input type="checkbox" name="acepto" value="1" required style="width:18px;height:18px;accent-color:var(--teal);margin-top:3px;">
                <span style="font-size:.84rem;color:var(--tinta-suave);">Manifiesto que los datos son verdaderos y acepto el <a href="<?= url('/privacidad') ?>" target="_blank">aviso de privacidad</a>.</span>
            </label>
            <?php if (isset($errores['acepto'])): ?><span class="error"><?= e($errores['acepto']) ?></span><?php endif; ?>

            <div class="formulario__pie">
                <button class="boton" type="submit"><?= icono('correo') ?> Enviar solicitud</button>
                <span class="texto-tenue" style="font-size:.78rem;">Recibirás un folio de confirmación.</span>
            </div>
            <div class="nota mt-3" style="font-size:.82rem;"><strong>Privacidad:</strong> los documentos se guardan fuera de la carpeta pública y solo los consulta la administración para atender tu solicitud. No sustituye asesoría legal.</div>
        </form>
    </div>
</section>
