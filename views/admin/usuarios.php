<?php
declare(strict_types=1);
$errores = errores();
$hoy = new \DateTimeImmutable('today');
?>
<header class="panel__cabecera">
    <div>
        <span class="mono texto-oro">Administración</span>
        <h1>Usuarios y accesos</h1>
        <p>Tú decides hasta cuándo puede entrar cada cuenta. Una fecha vacía significa acceso sin límite; una fecha vencida bloquea el acceso.</p>
    </div>
    <div class="panel__acciones">
        <a class="boton boton--pequeno boton--contorno" href="<?= url('/panel/usuarios') ?>"><?= icono('perfil') ?> Panel de usuarios</a>
    </div>
</header>

<div class="grid grid--4 mb-3">
    <div class="panel-caja"><span class="mono">Cuentas</span><strong class="dato"><?= (int) $resumen['total'] ?></strong><small class="texto-tenue">Total registradas</small></div>
    <div class="panel-caja"><span class="mono">Activas</span><strong class="dato"><?= (int) $resumen['activos'] ?></strong><small class="texto-tenue">Pueden iniciar sesión</small></div>
    <div class="panel-caja"><span class="mono">Vencidas</span><strong class="dato"><?= (int) $resumen['vencidos'] ?></strong><small class="texto-tenue">Bloqueadas por fecha</small></div>
    <div class="panel-caja"><span class="mono">Sin fecha</span><strong class="dato"><?= (int) $resumen['sin_fecha'] ?></strong><small class="texto-tenue">Acceso libre actualmente</small></div>
</div>

<?php if ((int) $resumen['sin_fecha'] > 0): ?>
    <div class="nota nota--teal mb-3 flex-entre" style="gap:16px;flex-wrap:wrap;">
        <div><strong><?= (int) $resumen['sin_fecha'] ?> cuenta(s) sin fecha de vencimiento.</strong> Puedes asignarles un periodo ahora para que no queden con acceso libre.</div>
        <form method="post" action="<?= url('/admin/usuarios/acceso-masivo') ?>" class="flex-centro" style="gap:6px;">
            <?= csrf_field() ?>
            <input type="number" name="dias" min="1" max="3650" value="<?= (int) $dias_default ?>" style="width:78px;padding:6px 8px;border:1px solid var(--borde-fuerte);border-radius:6px;" aria-label="Días de acceso">
            <button class="boton boton--pequeno" type="submit">Asignar días a cuentas sin fecha</button>
        </form>
    </div>
<?php endif; ?>

<div class="panel__rejilla panel__rejilla--2" style="align-items:start;">
    <section class="panel-caja">
        <div class="panel__seccion-titulo"><h2>Cuentas</h2><span class="etiqueta"><?= count($usuarios) ?></span></div>
        <div class="tabla-scroll">
            <table class="tabla" style="table-layout:fixed;">
                <colgroup><col style="width:22%"><col style="width:20%"><col style="width:14%"><col style="width:12%"><col style="width:32%"></colgroup>
                <thead><tr><th>Nombre</th><th>Correo</th><th>Rol</th><th>Estado</th><th>Acceso hasta</th></tr></thead>
                <tbody>
                    <?php foreach ($usuarios as $item): ?>
                        <tr>
                            <td><strong><?= e($item['nombre']) ?></strong><br><small class="texto-tenue">@<?= e($item['usuario']) ?> · <?= $item['ultimo_acceso'] ? e(fecha($item['ultimo_acceso'], 'd M Y')) : 'sin acceso' ?></small></td>
                            <td style="overflow-wrap:anywhere;"><?= e($item['correo']) ?></td>
                            <td>
                                <?php if ((int) $item['id'] === (int) $usuario['id']): ?>
                                    <span class="badge badge--activo"><?= e(rol_label((string) $item['rol'])) ?></span>
                                <?php else: ?>
                                    <form method="post" action="<?= url('/admin/usuarios/rol') ?>">
                                        <?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $item['id'] ?>">
                                        <select name="rol" style="width:100%;padding:4px 5px;border:1px solid var(--borde-fuerte);border-radius:6px;font-size:.72rem;">
                                            <?php foreach ($roles as $clave => $etiqueta): ?><option value="<?= e($clave) ?>" <?= $item['rol'] === $clave ? 'selected' : '' ?>><?= e($etiqueta) ?></option><?php endforeach; ?>
                                        </select>
                                        <button class="boton boton--pequeno boton--contorno mt-1" type="submit" style="font-size:.66rem;">Cambiar rol</button>
                                    </form>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ((int) $item['id'] !== (int) $usuario['id']): ?>
                                    <form method="post" action="<?= url('/admin/usuarios/estado') ?>">
                                        <?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $item['id'] ?>">
                                        <input type="hidden" name="estado" value="<?= $item['estado'] === 'activo' ? 'suspendido' : 'activo' ?>">
                                        <button class="boton boton--pequeno boton--contorno" type="submit" style="color:<?= $item['estado'] === 'activo' ? 'var(--coral)' : 'var(--teal)' ?>;font-size:.66rem;"><?= $item['estado'] === 'activo' ? 'Suspender' : 'Activar' ?></button>
                                    </form>
                                <?php else: ?><span class="etiqueta">Tú</span><?php endif; ?>
                            </td>
                            <td>
                                <?php if (($item['rol'] ?? '') === 'admin'): ?>
                                    <span class="texto-tenue" style="font-size:.78rem;">Sin límite (admin)</span>
                                <?php else: ?>
                                    <?php
                                    $acceso = (string) ($item['acceso_hasta'] ?? '');
                                    $dias = null;
                                    if ($acceso !== '') {
                                        $fin = \DateTimeImmutable::createFromFormat('!Y-m-d', $acceso);
                                        if ($fin !== false) {
                                            $dias = (int) $hoy->diff($fin)->format('%r%a');
                                        }
                                    }
                                    ?>
                                    <form method="post" action="<?= url('/admin/usuarios/acceso') ?>" class="flex-centro" style="gap:3px;flex-wrap:nowrap;">
                                        <?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $item['id'] ?>">
                                        <input type="date" name="acceso_hasta" value="<?= e($acceso) ?>" style="width:120px;padding:4px 5px;border:1px solid var(--borde-fuerte);border-radius:6px;font-size:.72rem;" aria-label="Acceso hasta para <?= e($item['nombre']) ?>">
                                        <button class="boton boton--pequeno boton--contorno" type="submit" title="Guardar fecha">OK</button>
                                    </form>
                                    <small class="texto-tenue" style="font-size:.68rem;">
                                        <?php if ($acceso === ''): ?>Sin límite
                                        <?php elseif ($dias !== null && $dias < 0): ?>Vencido hace <?= abs($dias) ?> día<?= abs($dias) === 1 ? '' : 's' ?>
                                        <?php elseif ($dias === 0): ?>Vence hoy
                                        <?php else: ?>Quedan <?= (int) $dias ?> día<?= $dias === 1 ? '' : 's' ?>
                                        <?php endif; ?>
                                    </small>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>

    <form class="panel-caja formulario" method="post" action="<?= url('/admin/usuarios/guardar') ?>">
        <?= csrf_field() ?>
        <div class="panel__seccion-titulo"><h2>Nueva cuenta</h2></div>
        <div class="campo<?= isset($errores['nombre']) ? ' campo--error' : '' ?>"><label for="nombre">Nombre *</label><input id="nombre" name="nombre" type="text" required maxlength="120" value="<?= e(viejo('nombre')) ?>"><?php if (isset($errores['nombre'])): ?><span class="error"><?= e($errores['nombre']) ?></span><?php endif; ?></div>
        <div class="campo<?= isset($errores['correo']) ? ' campo--error' : '' ?>"><label for="correo">Correo *</label><input id="correo" name="correo" type="email" required maxlength="160" value="<?= e(viejo('correo')) ?>"><?php if (isset($errores['correo'])): ?><span class="error"><?= e($errores['correo']) ?></span><?php endif; ?></div>
        <div class="rejilla-campos">
            <div class="campo campo--6"><label for="rol">Rol</label><select id="rol" name="rol"><option value="cliente">Cliente</option><option value="estilista">Estilista</option><option value="editor">Editor</option><option value="admin">Administrador</option></select></div>
            <div class="campo campo--6<?= isset($errores['password']) ? ' campo--error' : '' ?>"><label for="password">Contraseña temporal *</label><input id="password" name="password" type="password" required minlength="8"><?php if (isset($errores['password'])): ?><span class="error"><?= e($errores['password']) ?></span><?php endif; ?></div>
        </div>
        <div class="campo<?= isset($errores['acceso_hasta']) ? ' campo--error' : '' ?>"><label for="acceso_hasta">Acceso hasta</label><input id="acceso_hasta" name="acceso_hasta" type="date" value="<?= e(viejo('acceso_hasta', date('Y-m-d', strtotime('+' . (int) $dias_default . ' days')))) ?>"><span class="ayuda">Si lo dejas vacío, se asignan <?= (int) $dias_default ?> días. Los administradores no tienen límite.</span><?php if (isset($errores['acceso_hasta'])): ?><span class="error"><?= e($errores['acceso_hasta']) ?></span><?php endif; ?></div>
        <p class="texto-tenue" style="font-size:.78rem;">Comparte la contraseña por un medio seguro. El usuario puede cambiarla en su perfil.</p>
        <button class="boton" type="submit">Crear cuenta con acceso</button>
    </form>
</div>
