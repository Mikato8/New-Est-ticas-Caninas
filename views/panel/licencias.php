<?php
declare(strict_types=1);
$errores = errores();
$planes = ['comercial' => 'Comercial · un equipo', 'multi' => 'Multi-sucursal', 'demo' => 'Demostración'];
$expira = viejo('expira', date('Y-m-d', strtotime('+1 year')));
$totalActivas = count(array_filter($licencias, static fn (array $l): bool => ($l['estado'] ?? 'activa') === 'activa'));
?>
<header class="panel__cabecera">
    <div>
        <span class="mono texto-oro">Administración</span>
        <h1>Licencias del CRM</h1>
        <p>Genera una clave, entrégala y el cliente activa el CRM desde el navegador. Sin instalar nada.</p>
    </div>
    <div class="panel__acciones">
        <a class="boton boton--pequeno" href="<?= url('/crm/') ?>" target="_blank" rel="noopener"><?= icono('flecha') ?> Abrir CRM</a>
        <a class="boton boton--contorno boton--pequeno" href="<?= url('/instalar') ?>" target="_blank" rel="noopener">Guía de instalación</a>
    </div>
</header>

<?php if ($origenLlave === 'demo'): ?>
    <div class="aviso-flash aviso-flash--error" role="status" style="margin-bottom:16px;">
        <?= icono('alerta') ?>
        <div>
            <strong>Firma con la llave de demostración.</strong> Las claves funcionan, pero esa llave está en el repositorio.
            Para control exclusivo configura <code>LICENCIA_PRIVATE_KEY</code> en el hosting y vuelve a desplegar.
        </div>
    </div>
<?php elseif ($origenLlave === 'ninguna'): ?>
    <div class="aviso-flash aviso-flash--error" role="alert" style="margin-bottom:16px;">
        <?= icono('alerta') ?><div>No hay llave privada configurada: la activación fallará. Configura <code>LICENCIA_PRIVATE_KEY</code>.</div>
    </div>
<?php endif; ?>

<?php if ($nueva !== ''): ?>
    <section class="panel-caja" style="border:2px solid var(--oro);margin-bottom:16px;">
        <div class="panel__seccion-titulo"><h2>Paso 3 · Clave creada</h2><span class="badge badge--activo">Lista para entregar</span></div>
        <p style="margin:0 0 8px;">Esta es la clave de activación. Cópiala y mándala al cliente:</p>
        <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
            <code style="font-size:1.15rem;letter-spacing:.12em;background:var(--crema);border:1px solid var(--oro);padding:10px 14px;border-radius:8px;"><?= e($nueva) ?></code>
            <span class="texto-tenue" style="font-size:.8rem;">También quedó guardada en la lista de abajo.</span>
        </div>
        <div style="margin-top:14px;padding:12px 14px;background:var(--crema);border-radius:8px;font-size:.85rem;line-height:1.6;">
            <strong>Cómo la usa el cliente:</strong><br>
            1. Abre <a href="<?= url('/crm/') ?>" target="_blank" rel="noopener">mikatoestilistascaninos.com/crm/</a>.<br>
            2. Pulsa <strong>Instalar</strong> en el navegador (opcional pero recomendado).<br>
            3. Entra a <strong>Activación</strong>, escribe la clave y pulsa <strong>Activar licencia</strong>.<br>
            4. La app queda ligada a ese equipo y funciona sin internet después de activar.
        </div>
    </section>
<?php endif; ?>

<div class="panel__rejilla panel__rejilla--2" style="align-items:start;margin-bottom:16px;">
    <section class="panel-caja">
        <div class="panel__seccion-titulo"><h2>Paso 1 · Datos de la licencia</h2></div>
        <form method="post" action="<?= url('/panel/licencias/crear') ?>" class="formulario">
            <?= csrf_field() ?>
            <div class="campo<?= isset($errores['titular']) ? ' campo--error' : '' ?>">
                <label for="titular">Cliente o estética *</label>
                <input id="titular" name="titular" type="text" required maxlength="120" value="<?= e(viejo('titular')) ?>" placeholder="Estética Luna">
                <?php if (isset($errores['titular'])): ?><span class="error"><?= e($errores['titular']) ?></span><?php endif; ?>
            </div>
            <div class="campo">
                <label for="negocio">Negocio</label>
                <input id="negocio" name="negocio" type="text" maxlength="120" value="<?= e(viejo('negocio', 'Mikato Estilistas Caninos')) ?>" placeholder="Mikato Estilistas Caninos">
            </div>
            <div class="rejilla-campos">
                <div class="campo campo--6">
                    <label for="plan">Plan</label>
                    <select id="plan" name="plan">
                        <?php foreach ($planes as $valor => $etiqueta): ?>
                            <option value="<?= e($valor) ?>" <?= viejo('plan', 'comercial') === $valor ? 'selected' : '' ?>><?= e($etiqueta) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="campo campo--6">
                    <label for="expira">Vence el *</label>
                    <input id="expira" name="expira" type="date" required value="<?= e($expira) ?>">
                </div>
            </div>
            <p class="texto-tenue" style="font-size:.78rem;margin:0 0 12px;">La clave se crea al instante. El cliente la activa cuando abra el CRM; hasta entonces no está ligada a ningún equipo.</p>
            <button class="boton boton--oro" type="submit">Generar clave de activación</button>
        </form>
    </section>

    <section class="panel-caja">
        <div class="panel__seccion-titulo"><h2>Paso 2 · Entrega y activación</h2></div>
        <ol style="margin:0;padding-left:20px;font-size:.9rem;line-height:1.8;">
            <li><strong>Genera la clave</strong> con el formulario de la izquierda.</li>
            <li><strong>Cópiala</strong> del recuadro dorado que aparece arriba.</li>
            <li>Envíala por WhatsApp o correo junto con la liga:<br><a href="<?= url('/crm/') ?>" target="_blank" rel="noopener">mikatoestilistascaninos.com/crm/</a></li>
            <li>El cliente abre la liga, escribe la clave en <strong>Activación</strong> y pulsa <strong>Activar licencia</strong>.</li>
            <li>La app queda activa en ese equipo. Puede usarla desde el navegador, sin instalar archivos.</li>
        </ol>
        <div style="margin-top:14px;border-top:1px solid var(--borde);padding-top:12px;font-size:.82rem;line-height:1.6;" class="texto-tenue">
            <strong style="color:var(--tinta);">Para mover la licencia a otro equipo</strong><br>
            La clave se liga al primer equipo donde se activa. Si el cliente cambia de computadora, revoca esa clave y genera una nueva, o escríbenos para moverla desde el registro.
        </div>
    </section>
</div>

<section class="panel-caja">
    <div class="panel__seccion-titulo">
        <h2>Licencias generadas</h2>
        <span class="etiqueta"><?= $totalActivas ?> activa<?= $totalActivas === 1 ? '' : 's' ?> de <?= count($licencias) ?></span>
    </div>
    <?php if ($licencias === []): ?>
        <p class="texto-suave" style="font-size:.9rem;">Aún no hay licencias. Genera la primera arriba.</p>
    <?php else: ?>
        <div class="tabla-scroll">
            <table class="tabla">
                <thead><tr><th>Clave</th><th>Cliente</th><th>Plan</th><th>Vence</th><th>Estado</th><th>Equipo</th><th></th></tr></thead>
                <tbody>
                    <?php foreach ($licencias as $fila): ?>
                        <?php $estado = (string) ($fila['estado'] ?? 'activa'); ?>
                        <tr>
                            <td><code style="font-size:.78rem;letter-spacing:.06em;"><?= e((string) $fila['clave']) ?></code></td>
                            <td><strong><?= e((string) ($fila['titular'] ?? '')) ?></strong><br><small class="texto-tenue"><?= e((string) ($fila['negocio'] ?? '')) ?></small></td>
                            <td><?= e((string) ($fila['plan'] ?? '')) ?></td>
                            <td><?= e((string) ($fila['expira_en'] ?? '')) ?></td>
                            <td>
                                <?php if ($estado === 'activa'): ?><span class="badge badge--activo">Activa</span>
                                <?php elseif ($estado === 'revocada'): ?><span class="badge" style="background:#fdecea;color:#b42318;">Revocada</span>
                                <?php else: ?><span class="badge"><?= e($estado) ?></span><?php endif; ?>
                            </td>
                            <td><?= ($fila['equipo'] ?? null) ? '<code style="font-size:.72rem;">' . e((string) $fila['equipo']) . '</code>' : '<span class="texto-tenue">Sin activar</span>' ?></td>
                            <td>
                                <?php if ($estado === 'activa'): ?>
                                    <form method="post" action="<?= url('/panel/licencias/revocar') ?>">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="clave" value="<?= e((string) $fila['clave']) ?>">
                                        <button class="boton boton--pequeno boton--contorno" type="submit" style="color:#b42318;">Revocar</button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>
