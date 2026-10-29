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
            <span class="mono" style="color:var(--oro-claro);">CRM para estéticas</span>
            <h2 style="margin-top:10px;">Tu operación, tus datos y tu equipo bajo control.</h2>
            <p>Acceso exclusivo para cuentas autorizadas de Mikato CRM. Cada usuario entra con su propio rol y solo a la información que le corresponde.</p>
            <ul class="auth__trust">
                <li><?= icono('escudo') ?><span><strong>Datos privados:</strong> clientes, mascotas, citas y ventas no se publican.</span></li>
                <li><?= icono('perfil') ?><span><strong>Por rol:</strong> cada cliente ve solo lo suyo; el personal entra según su permiso.</span></li>
                <li><?= icono('agenda') ?><span><strong>Acceso con vigencia:</strong> administración decide hasta cuándo puede entrar cada cuenta.</span></li>
            </ul>
            <blockquote>“La confianza también se diseña.”</blockquote>
        </div>
        <span class="mono" style="color:rgba(251,247,239,.5);">Mikato · Estilistas Caninos</span>
    </div>
    <div class="auth__formulario">
        <div class="auth__caja">
            <span class="mono texto-oro">Mikato CRM · Cuenta</span>
            <h1>Entra a tu CRM.</h1>
            <p class="texto-suave">Usa el correo o usuario que te asignó administración. Este acceso es solo para cuentas del servicio; no es un directorio público.</p>
            <form class="formulario" method="post" action="<?= url('/entrar') ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="destino" value="<?= e($destino) ?>">
                <div style="position:absolute;left:-9999px;" aria-hidden="true"><label for="sitio_web">No llenar</label><input id="sitio_web" type="text" name="sitio_web" tabindex="-1" autocomplete="off"></div>
                <div class="campo">
                    <label for="login">Correo o usuario</label>
                    <input id="login" name="login" type="text" required autocomplete="username" value="<?= e(viejo('login')) ?>" autofocus>
                </div>
                <div class="campo">
                    <label for="password">Contraseña</label>
                    <div class="campo-password">
                        <input id="password" name="password" type="password" required autocomplete="current-password">
                        <input id="ver-password" type="checkbox" hidden data-ver-password="password" data-ver-label="ver-password-label">
                        <label id="ver-password-label" class="password-ver" for="ver-password" hidden>Ver</label>
                    </div>
                </div>
                <button class="boton boton--bloque" type="submit">Entrar</button>
                <script>(function(){document.querySelectorAll('[data-ver-password]').forEach(function(c){var p=document.getElementById(c.getAttribute('data-ver-password')),l=document.getElementById(c.getAttribute('data-ver-label'));if(!p||!l){return;}l.hidden=false;c.addEventListener('change',function(){p.type=c.checked?'text':'password';l.textContent=c.checked?'Ocultar':'Ver';p.focus();});});})();</script>
                <p class="auth__nota"><?= icono('escudo') ?> <span>Tus datos no se comparten con otras estéticas. Solo tu cuenta, el personal autorizado de tu estética y la administración de Mikato pueden consultarlos.</span></p>
                <p class="auth__cambiar">¿Olvidaste tu contraseña? <?php if ((bool) config('mail.enabled', false)): ?><a href="<?= url('/recuperar') ?>">Recupérala aquí</a>.<?php else: ?><a href="<?= url('/contacto') ?>">Escríbenos</a> y la restablecemos.<?php endif; ?></p>
                <div class="separador">o</div>
                <a class="boton boton--contorno boton--bloque" href="<?= url('/contacto') ?>">Solicitar acceso</a>
            </form>
        </div>
    </div>
</div>
