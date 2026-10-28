<?php
declare(strict_types=1);
$errores = errores();
$editando = $editar !== null;
$v = static function (string $campo, string $defecto = '') use ($editar): string {
    return $editar !== null ? (string) ($editar[$campo] ?? $defecto) : viejo($campo, $defecto);
};
$clientePreseleccionado = (int) ($_GET['cliente'] ?? 0);
?>
<header class="panel__cabecera">
    <div><span class="mono texto-oro">Mascotas</span><h1>Mascotas</h1><p><?= count($mascotas) ?> fichas activas · manto, salud y alertas para un servicio seguro.</p></div>
    <div class="panel__acciones">
        <form class="buscador" method="get" action="<?= url('/panel/mascotas') ?>"><?= icono('buscar') ?><input type="search" name="q" value="<?= e($busqueda) ?>" placeholder="Buscar mascota o tutor…"><button type="submit">Buscar</button></form>
        <a class="boton boton--pequeno" href="<?= url('/panel/mascotas') ?>"><?= icono('mas') ?> Nueva</a>
    </div>
</header>

<div class="panel__rejilla panel__rejilla--2" style="align-items:start;">
    <section class="panel-caja">
        <div class="panel__seccion-titulo"><h2>Listado</h2><span class="etiqueta"><?= count($mascotas) ?></span></div>
        <div class="tabla-scroll">
            <table class="tabla" style="table-layout:fixed;">
                <colgroup><col style="width:25%"><col style="width:24%"><col style="width:22%"><col style="width:13%"><col style="width:16%"></colgroup>
                <thead><tr><th>Mascota</th><th>Tutor</th><th>Raza / tamaño</th><th>Alertas</th><th></th></tr></thead>
                <tbody>
                    <?php foreach ($mascotas as $mascota): ?>
                        <tr>
                            <td><a href="<?= url('/panel/mascotas/' . $mascota['id']) ?>" style="font-weight:600;"><?= e($mascota['nombre']) ?></a><br><small class="texto-tenue"><?= e($mascota['especie']) ?> · <?= e((string) ($mascota['sexo'] ?: 's/d')) ?></small></td>
                            <td><?= e($mascota['cliente_nombre']) ?><br><small class="texto-tenue"><?= e((string) ($mascota['cliente_telefono'] ?: '')) ?></small></td>
                            <td><?= e((string) ($mascota['raza'] ?: 'Mestizo')) ?><br><small class="texto-tenue"><?= e(tamano_label((string) $mascota['tamano'])) ?></small></td>
                            <?php $alergias = mb_strtolower(trim((string) $mascota['alergias'])); $manejo = mb_strtolower(trim((string) $mascota['temperamento'])); $tieneAlergias = $alergias !== '' && !in_array($alergias, ['ninguna', 'ninguna.', 'ninguno', 'no', 'n/a'], true); $tieneManejo = $manejo !== '' && !in_array($manejo, ['ninguna', 'ninguna.', 'ninguno', 'no', 'n/a'], true); ?><td><?= $tieneAlergias ? '<span class="badge badge--cancelada">Alergias</span>' : '<span class="texto-tenue">—</span>' ?><?= $tieneManejo ? '<br><span class="badge badge--pendiente">Manejo</span>' : '' ?></td>
                            <td class="acciones">
                                <a class="boton boton--contorno" href="<?= url('/panel/mascotas?editar=' . $mascota['id']) ?>" title="Editar" aria-label="Editar" style="width:34px;height:34px;padding:0;"><?= icono('editar') ?></a>
                                <form method="post" action="<?= url('/panel/mascotas/eliminar') ?>" data-confirmar="¿Archivar a <?= e($mascota['nombre']) ?>?"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $mascota['id'] ?>"><button class="boton boton--contorno" type="submit" title="Archivar" aria-label="Archivar" style="width:34px;height:34px;padding:0;color:var(--coral);"><?= icono('basura') ?></button></form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if ($mascotas === []): ?><tr><td colspan="5" class="tabla-vacia">Sin mascotas registradas.</td></tr><?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>

    <form class="panel-caja formulario" method="post" action="<?= url('/panel/mascotas/guardar') ?>" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <input type="hidden" name="id" value="<?= $editando ? (int) $editar['id'] : 0 ?>">
        <div class="panel__seccion-titulo"><h2><?= $editando ? 'Editar mascota' : 'Nueva mascota' ?></h2></div>
        <div class="campo<?= isset($errores['cliente_id']) ? ' campo--error' : '' ?>">
            <label for="cliente_id">Tutor *</label>
            <select id="cliente_id" name="cliente_id" required>
                <option value="">Elige cliente</option>
                <?php $sel = (int) ($editar['cliente_id'] ?? $clientePreseleccionado); foreach ($clientes as $cliente): ?>
                    <option value="<?= (int) $cliente['id'] ?>" <?= $sel === (int) $cliente['id'] ? 'selected' : '' ?>><?= e($cliente['nombre']) ?><?= $cliente['telefono'] ? ' · ' . e($cliente['telefono']) : '' ?></option>
                <?php endforeach; ?>
            </select>
            <?php if (isset($errores['cliente_id'])): ?><span class="error"><?= e($errores['cliente_id']) ?></span><?php endif; ?>
        </div>
        <div class="rejilla-campos">
            <div class="campo campo--6<?= isset($errores['nombre']) ? ' campo--error' : '' ?>"><label for="nombre">Nombre *</label><input id="nombre" name="nombre" type="text" required maxlength="80" value="<?= e($v('nombre')) ?>"></div>
            <div class="campo campo--6"><label for="especie">Especie</label><select id="especie" name="especie"><option value="perro" <?= $v('especie', 'perro') === 'perro' ? 'selected' : '' ?>>Perro</option><option value="gato" <?= $v('especie') === 'gato' ? 'selected' : '' ?>>Gato</option><option value="otro" <?= $v('especie') === 'otro' ? 'selected' : '' ?>>Otro</option></select></div>
            <div class="campo campo--6"><label for="raza">Raza</label><input id="raza" name="raza" type="text" maxlength="80" value="<?= e($v('raza')) ?>"></div>
            <div class="campo campo--6"><label for="tamano">Tamaño</label><select id="tamano" name="tamano"><?php foreach (['chico','mediano','grande','gigante'] as $t): ?><option value="<?= $t ?>" <?= $v('tamano', 'mediano') === $t ? 'selected' : '' ?>><?= e(tamano_label($t)) ?></option><?php endforeach; ?></select></div>
            <div class="campo campo--6"><label for="sexo">Sexo</label><select id="sexo" name="sexo"><option value="">Sin dato</option><option value="macho" <?= $v('sexo') === 'macho' ? 'selected' : '' ?>>Macho</option><option value="hembra" <?= $v('sexo') === 'hembra' ? 'selected' : '' ?>>Hembra</option></select></div>
            <div class="campo campo--6"><label for="nacimiento">Nacimiento</label><input id="nacimiento" name="nacimiento" type="date" value="<?= e($v('nacimiento')) ?>"></div>
            <div class="campo campo--6"><label for="peso">Peso (kg)</label><input id="peso" name="peso" type="number" min="0" max="90" step="0.1" value="<?= e($v('peso')) ?>"></div>
            <div class="campo campo--6"><label for="foto">Foto</label><input id="foto" name="foto" type="file" accept="image/jpeg,image/png,image/webp" class="archivo"></div>
        </div>
        <div class="campo"><label for="temperamento">Temperamento / manejo</label><input id="temperamento" name="temperamento" type="text" maxlength="200" value="<?= e($v('temperamento')) ?>" placeholder="Nervioso con secadora, requiere bozal…"></div>
        <div class="campo"><label for="alergias">Alergias / salud</label><input id="alergias" name="alergias" type="text" maxlength="300" value="<?= e($v('alergias')) ?>"></div>
        <div class="campo"><label for="notas">Notas</label><textarea id="notas" name="notas" maxlength="1000" style="min-height:80px;"><?= e($v('notas')) ?></textarea></div>
        <div class="flex-centro">
            <button class="boton" type="submit"><?= $editando ? 'Guardar cambios' : 'Registrar mascota' ?></button>
            <?php if ($editando): ?><a class="boton boton--contorno" href="<?= url('/panel/mascotas') ?>">Cancelar</a><?php endif; ?>
        </div>
    </form>
</div>
