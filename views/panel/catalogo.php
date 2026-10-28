<?php
declare(strict_types=1);
$errores = errores();
$editando = $editar !== null;
$v = static function (string $campo, string $defecto = '') use ($editar): string {
    return $editar !== null ? (string) ($editar[$campo] ?? $defecto) : viejo($campo, $defecto);
};
$mostrar = static function (string $campo, mixed $valor) use ($tipo): string {
    if ($campo === 'precio' || $campo === 'precio_mayoreo' || $campo === 'costo') {
        return $valor === null || $valor === '' ? '—' : precio($valor);
    }
    if ($campo === 'activo' || $campo === 'destacado') {
        return es_verdadero($valor) ? '<span class="badge badge--activo">Sí</span>' : '<span class="texto-tenue">No</span>';
    }
    if ($campo === 'categoria' && $tipo === 'servicios') {
        return e(\App\Repositories\ServicioRepository::categorias()[(string) $valor] ?? (string) $valor);
    }
    if ($campo === 'categoria' && $tipo === 'productos') {
        return e(\App\Repositories\ProductoRepository::categorias()[(string) $valor] ?? (string) $valor);
    }
    if ($campo === 'tamano') {
        return e(tamano_label((string) $valor));
    }
    return e((string) $valor);
};
?>
<header class="panel__cabecera">
    <div><span class="mono texto-oro">Catálogo</span><h1><?= e($config['titulo']) ?></h1><p><?= e($config['descripcion']) ?></p></div>
    <div class="panel__acciones">
        <a class="boton boton--pequeno boton--contorno" href="<?= url('/panel/' . $tipo) ?>"><?= icono('mas') ?> Nuevo</a>
        <a class="boton boton--pequeno" href="<?= url('/' . ($tipo === 'servicios' ? 'servicios' : 'productos')) ?>" target="_blank">Ver público</a>
    </div>
</header>

<div class="panel__rejilla panel__rejilla--catalogo">
    <section class="panel-caja">
        <div class="panel__seccion-titulo"><h2><?= e($config['titulo']) ?></h2><span class="etiqueta"><?= count($items) ?></span></div>
        <div class="tabla-scroll">
            <table class="tabla">
                <thead><tr><?php foreach ($config['columnas'] as $col => $etiqueta): ?><th<?= in_array($col, ['precio','stock','duracion_min'], true) ? ' class="num"' : '' ?>><?= e($etiqueta) ?></th><?php endforeach; ?><th></th></tr></thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <?php foreach ($config['columnas'] as $col => $etiqueta): ?>
                                <td<?= in_array($col, ['precio','stock','duracion_min'], true) ? ' class="num"' : '' ?>><?= $mostrar($col, $item[$col] ?? '') ?></td>
                            <?php endforeach; ?>
                            <td class="acciones">
                                <a class="boton boton--contorno" href="<?= url('/panel/' . $tipo . '?editar=' . $item['id']) ?>" title="Editar" aria-label="Editar" style="width:34px;height:34px;padding:0;"><?= icono('editar') ?></a>
                                <form method="post" action="<?= url('/panel/' . $tipo . '/eliminar') ?>"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $item['id'] ?>"><button class="boton boton--contorno" type="submit" title="Archivar" aria-label="Archivar" style="width:34px;height:34px;padding:0;color:var(--coral);"><?= icono('basura') ?></button></form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if ($items === []): ?><tr><td colspan="<?= count($config['columnas']) + 1 ?>" class="tabla-vacia">Sin registros. Crea el primero.</td></tr><?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>

    <form class="panel-caja formulario" method="post" action="<?= url('/panel/' . $tipo . '/guardar') ?>" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <input type="hidden" name="id" value="<?= $editando ? (int) $editar['id'] : 0 ?>">
        <div class="panel__seccion-titulo"><h2><?= $editando ? 'Editar ' . e($config['singular']) : 'Nuevo ' . e($config['singular']) ?></h2></div>
        <div class="rejilla-campos">
            <?php foreach ($config['campos'] as $campo): ?>
                <?php
                $nombre = $campo['nombre'];
                $ancho = (string) ($campo['ancho'] ?? '12');
                $clase = 'campo campo--' . $ancho . (isset($errores[$nombre]) ? ' campo--error' : '');
                ?>
                <div class="<?= e($clase) ?>">
                    <?php if ($campo['tipo'] === 'checkbox'): ?>
                        <label class="campo--inline flex-centro" style="gap:8px;margin-top:22px;">
                            <input type="checkbox" name="<?= e($nombre) ?>" value="1" <?= ($editando ? es_verdadero($editar[$nombre] ?? false) : viejo($nombre) === '1') ? 'checked' : '' ?> style="width:18px;height:18px;accent-color:var(--teal);">
                            <span><?= e($campo['etiqueta']) ?></span>
                        </label>
                    <?php elseif ($campo['tipo'] === 'textarea'): ?>
                        <label for="<?= e($nombre) ?>"><?= e($campo['etiqueta']) ?><?= !empty($campo['requerido']) ? ' *' : '' ?></label>
                        <textarea id="<?= e($nombre) ?>" name="<?= e($nombre) ?>" maxlength="2000" <?= !empty($campo['requerido']) ? 'required' : '' ?> style="min-height:90px;"><?= e($v($nombre)) ?></textarea>
                    <?php elseif ($campo['tipo'] === 'select'): ?>
                        <label for="<?= e($nombre) ?>"><?= e($campo['etiqueta']) ?></label>
                        <select id="<?= e($nombre) ?>" name="<?= e($nombre) ?>">
                            <?php foreach (($campo['opciones'] ?? []) as $valor => $etiqueta): ?>
                                <option value="<?= e((string) $valor) ?>" <?= $v($nombre) === (string) $valor ? 'selected' : '' ?>><?= e($etiqueta) ?></option>
                            <?php endforeach; ?>
                        </select>
                    <?php else: ?>
                        <label for="<?= e($nombre) ?>"><?= e($campo['etiqueta']) ?><?= !empty($campo['requerido']) ? ' *' : '' ?></label>
                        <input id="<?= e($nombre) ?>" name="<?= e($nombre) ?>" type="<?= e($campo['tipo']) ?>" value="<?= e($v($nombre)) ?>" <?= isset($campo['paso']) ? 'step="' . e($campo['paso']) . '"' : '' ?> <?= !empty($campo['requerido']) ? 'required' : '' ?>>
                    <?php endif; ?>
                    <?php if (isset($errores[$nombre])): ?><span class="error"><?= e($errores[$nombre]) ?></span><?php endif; ?>
                </div>
            <?php endforeach; ?>
            <?php if ($tipo === 'productos'): ?>
                <div class="campo">
                    <label for="imagen_archivo">Imagen del producto</label>
                    <input id="imagen_archivo" name="imagen_archivo" type="file" accept="image/jpeg,image/png,image/webp">
                    <?php if ($editando && !empty($editar['imagen'])): ?><img src="<?= e((string) $editar['imagen']) ?>" alt="Imagen actual" style="width:90px;height:90px;object-fit:cover;border-radius:8px;margin-top:8px;"><?php endif; ?>
                    <span class="texto-tenue" style="font-size:.78rem;">JPG, PNG o WebP de hasta 3 MB. Si no subes una, se conserva la actual o se usa la ilustración por categoría.</span>
                </div>
            <?php endif; ?>
        </div>
        <div class="flex-centro">
            <button class="boton" type="submit"><?= $editando ? 'Guardar cambios' : 'Crear' ?></button>
            <?php if ($editando): ?><a class="boton boton--contorno" href="<?= url('/panel/' . $tipo) ?>">Cancelar</a><?php endif; ?>
        </div>
    </form>
</div>
