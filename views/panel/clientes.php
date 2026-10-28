<?php
declare(strict_types=1);
$errores = errores();
$editando = $editar !== null;
$v = static function (string $campo) use ($editar): string {
    return $editar !== null ? (string) ($editar[$campo] ?? '') : viejo($campo);
};
?>
<header class="panel__cabecera">
    <div><span class="mono texto-oro">Clientes</span><h1>Clientes</h1><p><?= count($clientes) ?> fichas · contactos y mascotas del salón.</p></div>
    <div class="panel__acciones">
        <form class="buscador" method="get" action="<?= url('/panel/clientes') ?>"><?= icono('buscar') ?><input type="search" name="q" value="<?= e($busqueda) ?>" placeholder="Buscar nombre o teléfono…"><button type="submit">Buscar</button></form>
        <a class="boton boton--pequeno" href="<?= url('/panel/clientes') ?>"><?= icono('mas') ?> Nuevo</a>
    </div>
</header>

<div class="panel__rejilla panel__rejilla--2" style="align-items:start;">
    <section class="panel-caja">
        <div class="panel__seccion-titulo"><h2>Listado</h2><span class="etiqueta"><?= count($clientes) ?></span></div>
        <div class="tabla-scroll">
            <table class="tabla" style="table-layout:fixed;">
                <colgroup><col style="width:28%"><col style="width:27%"><col style="width:10%"><col style="width:16%"><col style="width:19%"></colgroup>
                <thead><tr><th>Cliente</th><th>Contacto</th><th>Masc.</th><th>Últ. cita</th><th></th></tr></thead>
                <tbody>
                    <?php foreach ($clientes as $cliente): ?>
                        <tr>
                            <td><a href="<?= url('/panel/clientes/' . $cliente['id']) ?>" style="font-weight:600;"><?= e($cliente['nombre']) ?></a><br><small class="texto-tenue"><?= e((string) ($cliente['correo'] ?: $cliente['correo_usuario'] ?? '')) ?></small></td>
                            <td><?= e((string) ($cliente['telefono'] ?: '—')) ?><br><small class="texto-tenue"><?= e((string) ($cliente['direccion'] ?: '')) ?></small></td>
                            <td><?= (int) $cliente['mascotas'] ?></td>
                            <td><?= $cliente['ultima_cita'] ? e(fecha($cliente['ultima_cita'], 'd M Y')) : '<span class="texto-tenue">—</span>' ?></td>
                            <td class="acciones">
                                <a class="boton boton--contorno" href="<?= url('/panel/clientes?editar=' . $cliente['id']) ?>" title="Editar" aria-label="Editar" style="width:34px;height:34px;padding:0;"><?= icono('editar') ?></a>
                                <form method="post" action="<?= url('/panel/clientes/eliminar') ?>"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $cliente['id'] ?>"><button class="boton boton--contorno" type="submit" title="Eliminar" aria-label="Eliminar" style="width:34px;height:34px;padding:0;color:var(--coral);"><?= icono('basura') ?></button></form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if ($clientes === []): ?><tr><td colspan="5" class="tabla-vacia">Sin clientes. Registra el primero o agenda una cita.</td></tr><?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>

    <form class="panel-caja formulario" method="post" action="<?= url('/panel/clientes/guardar') ?>">
        <?= csrf_field() ?>
        <input type="hidden" name="id" value="<?= $editando ? (int) $editar['id'] : 0 ?>">
        <div class="panel__seccion-titulo"><h2><?= $editando ? 'Editar cliente' : 'Nuevo cliente' ?></h2></div>
        <div class="campo<?= isset($errores['nombre']) ? ' campo--error' : '' ?>"><label for="nombre">Nombre *</label><input id="nombre" name="nombre" type="text" required maxlength="120" value="<?= e($v('nombre')) ?>"><?php if (isset($errores['nombre'])): ?><span class="error"><?= e($errores['nombre']) ?></span><?php endif; ?></div>
        <div class="rejilla-campos">
            <div class="campo campo--6"><label for="telefono">Teléfono</label><input id="telefono" name="telefono" type="tel" maxlength="30" value="<?= e($v('telefono')) ?>"></div>
            <div class="campo campo--6"><label for="correo">Correo</label><input id="correo" name="correo" type="email" maxlength="160" value="<?= e($v('correo')) ?>"></div>
        </div>
        <div class="campo"><label for="direccion">Dirección</label><input id="direccion" name="direccion" type="text" maxlength="200" value="<?= e($v('direccion')) ?>"></div>
        <div class="campo"><label for="notas">Notas</label><textarea id="notas" name="notas" maxlength="1000" style="min-height:90px;"><?= e($v('notas')) ?></textarea></div>
        <div class="flex-centro">
            <button class="boton" type="submit"><?= $editando ? 'Guardar cambios' : 'Registrar cliente' ?></button>
            <?php if ($editando): ?><a class="boton boton--contorno" href="<?= url('/panel/clientes') ?>">Cancelar</a><?php endif; ?>
        </div>
    </form>
</div>
