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
            <h2 style="margin-top:28px;">Tu perro, tu historial y tus citas en un solo lugar.</h2>
            <p>Entra para ver próximas citas, el historial de estética de tu perro y tus compras. Si eres estilista, accede al panel de gestión del salón.</p>
            <blockquote>“Un buen corte se nota. Un buen trato, también.”</blockquote>
        </div>
        <span class="mono" style="color:rgba(251,247,239,.5);">Mikato · Estilistas Caninos</span>
    </div>
    <div class="auth__formulario">
        <div class="auth__caja">
            <span class="mono texto-oro">Cuenta</span>
            <h1>Entrar.</h1>
            <p class="texto-suave">Usa tu correo o usuario. Si aún no tienes cuenta, créala gratis.</p>
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
                    <input id="password" name="password" type="password" required autocomplete="current-password">
                </div>
                <button class="boton boton--bloque" type="submit">Entrar</button>
                <p class="auth__cambiar">¿Olvidaste tu contraseña? <a href="<?= url('/contacto') ?>">Escríbenos</a> y la restablecemos.</p>
                <div class="separador">o</div>
                <a class="boton boton--contorno boton--bloque" href="<?= url('/registro') ?>">Crear una cuenta nueva</a>
            </form>
        </div>
    </div>
</div>
