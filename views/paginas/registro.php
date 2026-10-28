<?php
declare(strict_types=1);
$errores = errores();
?>
<div class="auth">
    <div class="auth__lado">
        <div>
            <a class="marca" href="<?= url('/') ?>" style="color:var(--crema);text-decoration:none;">
                <img src="<?= asset('/assets/img/logo-mikato.png') ?>" alt="Mikato" style="height:28px;filter:brightness(0) invert(1);">
            </a>
            <h2 style="margin-top:28px;">Crea tu cuenta y guarda lo importante.</h2>
            <p>Historial de baños y cortes, próximas citas, datos de tu perro y participación en la comunidad. Sin costo.</p>
            <ul class="sin-lista" style="display:grid;gap:10px;color:rgba(251,247,239,.85);">
                <li class="flex-centro"><?= icono('check') ?> Agenda y consulta tus citas.</li>
                <li class="flex-centro"><?= icono('check') ?> Guarda alergias, manto y temperamento.</li>
                <li class="flex-centro"><?= icono('check') ?> Comparte casos en la comunidad.</li>
            </ul>
        </div>
        <span class="mono" style="color:rgba(251,247,239,.5);">Mikato · Estilistas Caninos</span>
    </div>
    <div class="auth__formulario">
        <div class="auth__caja">
            <span class="mono texto-oro">Cuenta nueva</span>
            <h1>Crear cuenta.</h1>
            <p class="texto-suave">Tarda menos de un minuto. Los campos con * son obligatorios.</p>
            <form class="formulario" method="post" action="<?= url('/registro') ?>">
                <?= csrf_field() ?>
                <div style="position:absolute;left:-9999px;" aria-hidden="true"><label for="sitio_web">No llenar</label><input id="sitio_web" type="text" name="sitio_web" tabindex="-1" autocomplete="off"></div>
                <div class="campo<?= isset($errores['nombre']) ? ' campo--error' : '' ?>">
                    <label for="nombre">Nombre completo *</label>
                    <input id="nombre" name="nombre" type="text" required minlength="3" maxlength="120" value="<?= e(viejo('nombre')) ?>" autocomplete="name">
                    <?php if (isset($errores['nombre'])): ?><span class="error"><?= e($errores['nombre']) ?></span><?php endif; ?>
                </div>
                <div class="campo<?= isset($errores['correo']) ? ' campo--error' : '' ?>">
                    <label for="correo">Correo *</label>
                    <input id="correo" name="correo" type="email" required maxlength="160" value="<?= e(viejo('correo')) ?>" autocomplete="email">
                    <?php if (isset($errores['correo'])): ?><span class="error"><?= e($errores['correo']) ?></span><?php endif; ?>
                </div>
                <div class="campo">
                    <label for="telefono">Teléfono / WhatsApp</label>
                    <input id="telefono" name="telefono" type="tel" maxlength="30" value="<?= e(viejo('telefono')) ?>" autocomplete="tel">
                </div>
                <div class="rejilla-campos">
                    <div class="campo campo--6<?= isset($errores['password']) ? ' campo--error' : '' ?>">
                        <label for="password">Contraseña *</label>
                        <input id="password" name="password" type="password" required minlength="8" autocomplete="new-password">
                        <span class="ayuda">Mínimo 8 caracteres.</span>
                        <?php if (isset($errores['password'])): ?><span class="error"><?= e($errores['password']) ?></span><?php endif; ?>
                    </div>
                    <div class="campo campo--6<?= isset($errores['password_confirm']) ? ' campo--error' : '' ?>">
                        <label for="password_confirm">Repite la contraseña *</label>
                        <input id="password_confirm" name="password_confirm" type="password" required autocomplete="new-password">
                        <?php if (isset($errores['password_confirm'])): ?><span class="error"><?= e($errores['password_confirm']) ?></span><?php endif; ?>
                    </div>
                </div>
                <label class="campo--inline flex-centro" style="gap:9px;align-items:flex-start;">
                    <input type="checkbox" name="acepto" value="1" required style="width:18px;height:18px;margin-top:3px;accent-color:var(--teal);">
                    <span style="font-size:.84rem;color:var(--tinta-suave);">Acepto el <a href="<?= url('/privacidad') ?>" target="_blank">aviso de privacidad</a> y los <a href="<?= url('/terminos') ?>" target="_blank">términos de uso</a>.</span>
                </label>
                <?php if (isset($errores['acepto'])): ?><span class="error"><?= e($errores['acepto']) ?></span><?php endif; ?>
                <button class="boton boton--bloque" type="submit">Crear mi cuenta</button>
                <p class="auth__cambiar">¿Ya tienes cuenta? <a href="<?= url('/entrar') ?>">Entrar</a></p>
            </form>
        </div>
    </div>
</div>
