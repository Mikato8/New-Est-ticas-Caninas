<?php
declare(strict_types=1);
?>
<div class="auth">
    <div class="auth__lado">
        <div>
            <a class="marca" href="<?= url('/') ?>" style="color:var(--crema);text-decoration:none;">
                <img src="<?= asset('/assets/img/logo-mikato.png') ?>" alt="Mikato" style="height:28px;filter:brightness(0) invert(1);">
            </a>
            <h2 style="margin-top:28px;">Recupera el acceso a tu cuenta.</h2>
            <p>Te enviaremos un enlace temporal para elegir una contraseña nueva. Por seguridad, no confirmamos si un correo está registrado.</p>
            <blockquote>“Un buen corte se nota. Un buen trato, también.”</blockquote>
        </div>
        <span class="mono" style="color:rgba(251,247,239,.5);">Mikato · Estilistas Caninos</span>
    </div>
    <div class="auth__formulario">
        <div class="auth__caja">
            <span class="mono texto-oro">Cuenta</span>
            <h1>Recuperar acceso.</h1>
            <p class="texto-suave">Escribe el correo con el que te registraste. Si existe una cuenta activa, recibirás un enlace válido por 60 minutos.</p>
            <?php if (!(bool) config('mail.enabled', false)): ?>
                <div class="nota nota--teal" style="font-size:.86rem;">La recuperación por correo está en preparación. Mientras tanto, <a href="<?= url('/contacto') ?>">escríbenos</a> y restablecemos tu acceso.</div>
            <?php endif; ?>
            <form class="formulario" method="post" action="<?= url('/recuperar') ?>">
                <?= csrf_field() ?>
                <div style="position:absolute;left:-9999px;" aria-hidden="true"><label for="sitio_web">No llenar</label><input id="sitio_web" type="text" name="sitio_web" tabindex="-1" autocomplete="off"></div>
                <div class="campo">
                    <label for="correo">Correo de la cuenta</label>
                    <input id="correo" name="correo" type="email" required autocomplete="email" maxlength="160" value="<?= e(viejo('correo')) ?>" autofocus>
                </div>
                <button class="boton boton--bloque" type="submit">Enviar enlace de recuperación</button>
            </form>
            <p class="auth__cambiar"><a href="<?= url('/entrar') ?>">Volver a entrar</a> · ¿No tienes cuenta? <a href="<?= url('/registro') ?>">Crea una</a>.</p>
        </div>
    </div>
</div>
