<?php
declare(strict_types=1);
?>
<div class="auth">
    <div class="auth__lado">
        <div>
            <a class="marca" href="<?= url('/') ?>" style="color:var(--crema);text-decoration:none;">
                <img src="<?= asset('/assets/img/logo-mikato.png') ?>" alt="Mikato" style="height:28px;filter:brightness(0) invert(1);">
            </a>
            <h2 style="margin-top:28px;">Una contraseña nueva, un nuevo comienzo.</h2>
            <p>Elige una contraseña que no uses en otros sitios. El enlace es de un solo uso y vence en 60 minutos.</p>
            <blockquote>“Un buen corte se nota. Un buen trato, también.”</blockquote>
        </div>
        <span class="mono" style="color:rgba(251,247,239,.5);">Mikato · Estilistas Caninos</span>
    </div>
    <div class="auth__formulario">
        <div class="auth__caja">
            <?php if (!empty($invalido)): ?>
                <span class="mono texto-oro">Enlace no válido</span>
                <h1>Este enlace ya no sirve.</h1>
                <p class="texto-suave">Venció o ya se usó. Solicita uno nuevo; por seguridad no podemos reutilizarlo.</p>
                <a class="boton boton--bloque" href="<?= url('/recuperar') ?>">Solicitar otro enlace</a>
                <p class="auth__cambiar"><a href="<?= url('/entrar') ?>">Volver a entrar</a></p>
            <?php else: ?>
                <span class="mono texto-oro">Cuenta</span>
                <h1>Nueva contraseña.</h1>
                <p class="texto-suave">Hola, <?= e($nombre) ?>. Elige una contraseña de al menos 8 caracteres.</p>
                <form class="formulario" method="post" action="<?= url('/recuperar/' . rawurlencode($token)) ?>">
                    <?= csrf_field() ?>
                    <div class="campo">
                        <label for="password">Contraseña nueva</label>
                        <div class="campo-password">
                            <input id="password" name="password" type="password" required minlength="8" maxlength="100" autocomplete="new-password" autofocus>
                            <input id="ver-password" type="checkbox" hidden data-ver-password="password" data-ver-label="ver-password-label">
                            <label id="ver-password-label" class="password-ver" for="ver-password" hidden>Ver</label>
                        </div>
                    </div>
                    <div class="campo">
                        <label for="password_confirm">Repite la contraseña</label>
                        <div class="campo-password">
                            <input id="password_confirm" name="password_confirm" type="password" required minlength="8" maxlength="100" autocomplete="new-password">
                            <input id="ver-password-confirm" type="checkbox" hidden data-ver-password="password_confirm" data-ver-label="ver-password-confirm-label">
                            <label id="ver-password-confirm-label" class="password-ver" for="ver-password-confirm" hidden>Ver</label>
                        </div>
                    </div>
                    <button class="boton boton--bloque" type="submit">Guardar contraseña nueva</button>
                    <script>(function(){document.querySelectorAll('[data-ver-password]').forEach(function(c){var p=document.getElementById(c.getAttribute('data-ver-password')),l=document.getElementById(c.getAttribute('data-ver-label'));if(!p||!l){return;}l.hidden=false;c.addEventListener('change',function(){p.type=c.checked?'text':'password';l.textContent=c.checked?'Ocultar':'Ver';p.focus();});});})();</script>
                </form>
                <p class="auth__cambiar"><a href="<?= url('/entrar') ?>">Volver a entrar</a></p>
            <?php endif; ?>
        </div>
    </div>
</div>
