<?php
declare(strict_types=1);
$errores = errores();
?>
<header class="panel__cabecera">
    <div><span class="mono texto-oro">Administración</span><h1>Usuarios y personal</h1><p>Altas de personal, roles y acceso. Los clientes se crean desde su registro o desde el panel.</p></div>
</header>

<div class="panel__rejilla panel__rejilla--2" style="align-items:start;">
    <section class="panel-caja">
        <div class="panel__seccion-titulo"><h2>Usuarios</h2><span class="etiqueta"><?= count($usuarios) ?></span></div>
        <div class="tabla-scroll">
            <table class="tabla" style="table-layout:fixed;">
                <colgroup><col style="width:30%"><col style="width:28%"><col style="width:22%"><col style="width:20%"></colgroup>
                <thead><tr><th>Nombre</th><th>Correo</th><th>Rol</th><th>Estado</th></tr></thead>
                <tbody>
                    <?php foreach ($usuarios as $item): ?>
                        <tr>
                            <td><strong><?= e($item['nombre']) ?></strong><br><small class="texto-tenue">@<?= e($item['usuario']) ?> · <?= $item['ultimo_acceso'] ? e(fecha($item['ultimo_acceso'], 'd M Y')) : 'sin acceso' ?></small></td>
                            <td><?= e($item['correo']) ?></td>
                            <td>
                                <?php if ((int) $item['id'] === (int) $usuario['id']): ?>
                                    <span class="badge badge--activo"><?= e(rol_label((string) $item['rol'])) ?></span>
                                <?php else: ?>
                                    <form method="post" action="<?= url('/panel/usuarios/rol') ?>" class="flex-centro" style="gap:4px;">
                                        <?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $item['id'] ?>">
                                        <select name="rol" style="padding:5px 7px;border:1px solid var(--borde-fuerte);border-radius:6px;font-size:.78rem;">
                                            <?php foreach ($roles as $clave => $etiqueta): ?><option value="<?= e($clave) ?>" <?= $item['rol'] === $clave ? 'selected' : '' ?>><?= e($etiqueta) ?></option><?php endforeach; ?>
                                        </select>
                                        <button class="boton boton--pequeno boton--contorno" type="submit">OK</button>
                                    </form>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="flex-centro" style="gap:6px;align-items:flex-start;">
                                    <?= estado_badge($item['estado']) ?>
                                    <?php if ((int) $item['id'] !== (int) $usuario['id']): ?>
                                        <form method="post" action="<?= url('/panel/usuarios/estado') ?>">
                                            <?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $item['id'] ?>">
                                            <input type="hidden" name="estado" value="<?= $item['estado'] === 'activo' ? 'suspendido' : 'activo' ?>">
                                            <button class="boton boton--pequeno boton--contorno" type="submit" style="color:<?= $item['estado'] === 'activo' ? 'var(--coral)' : 'var(--teal)' ?>;"><?= $item['estado'] === 'activo' ? 'Suspender' : 'Activar' ?></button>
                                        </form>
                                    <?php else: ?>
                                        <span class="etiqueta">Tú</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>

    <form class="panel-caja formulario" method="post" action="<?= url('/panel/usuarios/guardar') ?>">
        <?= csrf_field() ?>
        <div class="panel__seccion-titulo"><h2>Nuevo usuario de personal</h2></div>
        <div class="campo<?= isset($errores['nombre']) ? ' campo--error' : '' ?>"><label for="nombre">Nombre *</label><input id="nombre" name="nombre" type="text" required maxlength="120" value="<?= e(viejo('nombre')) ?>"></div>
        <div class="campo<?= isset($errores['correo']) ? ' campo--error' : '' ?>"><label for="correo">Correo *</label><input id="correo" name="correo" type="email" required maxlength="160" value="<?= e(viejo('correo')) ?>"><?php if (isset($errores['correo'])): ?><span class="error"><?= e($errores['correo']) ?></span><?php endif; ?></div>
        <div class="rejilla-campos">
            <div class="campo campo--6"><label for="rol">Rol</label><select id="rol" name="rol"><option value="estilista">Estilista</option><option value="editor">Editor</option><option value="admin">Administrador</option><option value="cliente">Cliente</option></select></div>
            <div class="campo campo--6<?= isset($errores['password']) ? ' campo--error' : '' ?>"><label for="password">Contraseña temporal *</label><input id="password" name="password" type="password" required minlength="8"><?php if (isset($errores['password'])): ?><span class="error"><?= e($errores['password']) ?></span><?php endif; ?></div>
        </div>
        <p class="texto-tenue" style="font-size:.78rem;">Comparte la contraseña por un medio seguro. El usuario puede cambiarla en su perfil.</p>
        <button class="boton" type="submit">Crear usuario</button>
    </form>
</div>
