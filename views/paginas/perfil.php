<?php
declare(strict_types=1);
use App\Core\Auth;
$errores = errores();
$esPersonal = Auth::esPersonal();
?>
<div class="contenedor">
    <nav class="migas" aria-label="Ruta"><a href="<?= url('/') ?>">Inicio</a><span>/</span>Mi perfil</nav>
    <header class="portada-interior">
        <span class="mono texto-oro">Cuenta</span>
        <h1>Mi perfil.</h1>
        <p>Actualiza tus datos, tu avatar y tu contraseña. Tu correo se usa para identificarte y para avisos de tu cuenta.</p>
    </header>
</div>

<section class="seccion seccion--compacta">
    <div class="contenedor">
        <div class="grid grid--2" style="align-items:start;">
            <form class="tarjeta tarjeta__cuerpo formulario" method="post" action="<?= url('/perfil') ?>" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="flex-centro mb-2">
                    <?php if ($usuario['avatar']): ?>
                        <img src="<?= e($usuario['avatar']) ?>" alt="" style="width:64px;height:64px;border-radius:50%;object-fit:cover;">
                    <?php else: ?>
                        <span class="avatar avatar--grande avatar--oro"><?= e(iniciales((string) $usuario['nombre'])) ?></span>
                    <?php endif; ?>
                    <div><strong><?= e($usuario['nombre']) ?></strong><br><small class="texto-tenue"><?= e(rol_label((string) $usuario['rol'])) ?> · <?= e($usuario['usuario']) ?></small></div>
                </div>
                <div class="rejilla-campos">
                    <div class="campo campo--6<?= isset($errores['nombre']) ? ' campo--error' : '' ?>"><label for="nombre">Nombre</label><input id="nombre" name="nombre" type="text" required maxlength="120" value="<?= e($usuario['nombre']) ?>"><?php if (isset($errores['nombre'])): ?><span class="error"><?= e($errores['nombre']) ?></span><?php endif; ?></div>
                    <div class="campo campo--6<?= isset($errores['correo']) ? ' campo--error' : '' ?>"><label for="correo">Correo</label><input id="correo" name="correo" type="email" required maxlength="160" value="<?= e($usuario['correo']) ?>"><?php if (isset($errores['correo'])): ?><span class="error"><?= e($errores['correo']) ?></span><?php endif; ?></div>
                    <div class="campo campo--6"><label for="telefono">Teléfono</label><input id="telefono" name="telefono" type="tel" maxlength="30" value="<?= e((string) $usuario['telefono']) ?>"></div>
                    <div class="campo campo--6"><label for="whatsapp">WhatsApp</label><input id="whatsapp" name="whatsapp" type="tel" maxlength="30" value="<?= e((string) $usuario['whatsapp']) ?>"></div>
                    <div class="campo campo--6"><label for="ciudad">Ciudad</label><input id="ciudad" name="ciudad" type="text" maxlength="80" value="<?= e((string) $usuario['ciudad']) ?>"></div>
                    <div class="campo campo--6"><label for="estado_geo">Estado</label><input id="estado_geo" name="estado_geo" type="text" maxlength="80" value="<?= e((string) $usuario['estado_geo']) ?>"></div>
                    <div class="campo"><label for="bio">Sobre mí</label><textarea id="bio" name="bio" maxlength="500" placeholder="Cuéntanos de tu trabajo, tu estética o tu perro."><?= e((string) $usuario['bio']) ?></textarea></div>
                    <div class="campo"><label for="avatar">Avatar</label><input id="avatar" name="avatar" type="file" accept="image/jpeg,image/png,image/webp" class="archivo"><span class="ayuda">JPG, PNG o WebP. Máximo 3 MB.</span></div>
                </div>
                <div class="formulario__pie">
                    <button class="boton" type="submit">Guardar cambios</button>
                    <a class="boton boton--contorno" href="<?= url($esPersonal ? '/panel' : '/mi-cuenta') ?>">Volver</a>
                </div>
            </form>

            <div style="display:grid;gap:18px;">
                <form class="tarjeta tarjeta__cuerpo formulario" method="post" action="<?= url('/perfil/password') ?>">
                    <?= csrf_field() ?>
                    <h2 style="font-size:1.35rem;margin-bottom:0;">Cambiar contraseña</h2>
                    <div class="campo"><label for="password_actual">Contraseña actual</label><input id="password_actual" name="password_actual" type="password" required autocomplete="current-password"></div>
                    <div class="campo"><label for="password_nueva">Contraseña nueva</label><input id="password_nueva" name="password_nueva" type="password" required minlength="8" autocomplete="new-password"></div>
                    <div class="campo"><label for="password_confirm">Repite la nueva</label><input id="password_confirm" name="password_confirm" type="password" required autocomplete="new-password"></div>
                    <button class="boton boton--contorno" type="submit">Actualizar contraseña</button>
                </form>

                <div class="tarjeta tarjeta__cuerpo">
                    <h2 style="font-size:1.2rem;">Datos de cuenta</h2>
                    <div class="datos-lista">
                        <div><dt>Usuario</dt><dd><?= e($usuario['usuario']) ?></dd></div>
                        <div><dt>Rol</dt><dd><?= e(rol_label((string) $usuario['rol'])) ?></dd></div>
                        <div><dt>Miembro desde</dt><dd><?= e(fecha($usuario['creado_en'])) ?></dd></div>
                        <div><dt>Último acceso</dt><dd><?= e(fecha($usuario['ultimo_acceso'], 'd M Y H:i')) ?></dd></div>
                    </div>
                    <form method="post" action="<?= url('/salir') ?>" class="mt-2">
                        <?= csrf_field() ?>
                        <button class="boton boton--pequeno boton--contorno" type="submit"><?= icono('salir') ?> Cerrar sesión</button>
                    </form>
                </div>

                <?php if (Auth::esCliente() && $cliente !== null): ?>
                    <div class="tarjeta tarjeta__cuerpo">
                        <h2 style="font-size:1.2rem;">Ficha de cliente</h2>
                        <p class="texto-suave" style="font-size:.9rem;">Estos son los datos que usamos para tus citas. Puedes actualizarlos desde tu cuenta.</p>
                        <a class="boton boton--pequeno" href="<?= url('/mi-cuenta/mascotas') ?>">Mis mascotas</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
