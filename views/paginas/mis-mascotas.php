<?php
declare(strict_types=1);
$errores = errores();
$editando = $editar !== null;
$valor = static function (string $campo, string $defecto = '') use ($editar): string {
    if ($editar !== null) {
        return (string) ($editar[$campo] ?? $defecto);
    }
    return viejo($campo, $defecto);
};
?>
<div class="contenedor">
    <nav class="migas" aria-label="Ruta"><a href="<?= url('/') ?>">Inicio</a><span>/</span><a href="<?= url('/mi-cuenta') ?>">Mi cuenta</a><span>/</span>Mis mascotas</nav>
    <header class="portada-interior">
        <span class="mono texto-oro">Mis mascotas</span>
        <h1>Los datos de tu perro.</h1>
        <p>Guardar raza, tamaño, alergias y temperamento nos ayuda a preparar el servicio y a cuidar mejor a tu perro.</p>
    </header>
</div>

<section class="seccion seccion--compacta">
    <div class="contenedor">
        <div class="cuenta">
            <?php parcial('cuenta-nav'); ?>
            <div>
                <div class="grid grid--2" style="align-items:start;">
                    <div>
                        <h2 style="font-size:1.35rem;">Mis perros</h2>
                        <div style="display:grid;gap:12px;">
                            <?php foreach ($mascotas as $mascota): ?>
                                <div class="tarjeta tarjeta__cuerpo">
                                    <div class="flex-entre" style="align-items:flex-start;">
                                        <div class="flex-centro">
                                            <?php if ($mascota['foto']): ?><img src="<?= e($mascota['foto']) ?>" alt="" style="width:56px;height:56px;border-radius:12px;object-fit:cover;"><?php else: ?><span class="avatar avatar--grande avatar--oro"><?= e(iniciales((string) $mascota['nombre'])) ?></span><?php endif; ?>
                                            <div>
                                                <strong style="font-size:1.05rem;"><?= e($mascota['nombre']) ?></strong><br>
                                                <small class="texto-tenue"><?= e((string) ($mascota['raza'] ?: 'Mestizo')) ?> · <?= e(tamano_label((string) $mascota['tamano'])) ?> · <?= e((string) ($mascota['sexo'] ?: 'Sin dato')) ?></small>
                                            </div>
                                        </div>
                                        <div class="flex-centro">
                                            <a class="boton boton--pequeno boton--contorno" href="<?= url('/mi-cuenta/mascotas?editar=' . $mascota['id']) ?>">Editar</a>
                                            <form method="post" action="<?= url('/mi-cuenta/mascotas/eliminar') ?>">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="id" value="<?= (int) $mascota['id'] ?>">
                                                <button class="boton boton--pequeno boton--contorno" type="submit" style="color:var(--coral);">Quitar</button>
                                            </form>
                                        </div>
                                    </div>
                                    <?php if ($mascota['alergias'] || $mascota['temperamento'] || $mascota['notas']): ?>
                                        <div class="nota mt-2" style="font-size:.82rem;padding:10px 12px;">
                                            <?php if ($mascota['alergias']): ?><div><strong>Alergias:</strong> <?= e($mascota['alergias']) ?></div><?php endif; ?>
                                            <?php if ($mascota['temperamento']): ?><div><strong>Temperamento:</strong> <?= e($mascota['temperamento']) ?></div><?php endif; ?>
                                            <?php if ($mascota['notas']): ?><div><strong>Notas:</strong> <?= e($mascota['notas']) ?></div><?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                    <div class="flex-centro mt-2">
                                        <a class="enlace-flecha" href="<?= url('/mi-cuenta/historial/' . $mascota['id']) ?>">Ver historial <?= icono('flecha') ?></a>
                                        <a class="enlace-flecha" href="<?= url('/agenda') ?>">Agendar cita <?= icono('flecha') ?></a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            <?php if ($mascotas === []): ?>
                                <div class="tarjeta tarjeta__cuerpo texto-centro">
                                    <h3>Aún no hay mascotas</h3>
                                    <p class="texto-suave">Agrega a tu perro con el formulario de al lado.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <form class="tarjeta tarjeta__cuerpo formulario" method="post" action="<?= url('/mi-cuenta/mascotas') ?>" enctype="multipart/form-data">
                        <?= csrf_field() ?>
                        <input type="hidden" name="id" value="<?= $editando ? (int) $editar['id'] : 0 ?>">
                        <h2 style="font-size:1.35rem;margin-bottom:0;"><?= $editando ? 'Editar mascota' : 'Agregar mascota' ?></h2>
                        <div class="rejilla-campos">
                            <div class="campo campo--6<?= isset($errores['nombre']) ? ' campo--error' : '' ?>"><label for="nombre">Nombre *</label><input id="nombre" name="nombre" type="text" required maxlength="80" value="<?= e($valor('nombre')) ?>"></div>
                            <div class="campo campo--6"><label for="especie">Especie</label><select id="especie" name="especie"><option value="perro" <?= $valor('especie', 'perro') === 'perro' ? 'selected' : '' ?>>Perro</option><option value="gato" <?= $valor('especie') === 'gato' ? 'selected' : '' ?>>Gato</option><option value="otro" <?= $valor('especie') === 'otro' ? 'selected' : '' ?>>Otro</option></select></div>
                            <div class="campo campo--6"><label for="raza">Raza</label><input id="raza" name="raza" type="text" maxlength="80" value="<?= e($valor('raza')) ?>"></div>
                            <div class="campo campo--6"><label for="tamano">Tamaño</label><select id="tamano" name="tamano"><?php foreach (['chico','mediano','grande','gigante'] as $t): ?><option value="<?= $t ?>" <?= $valor('tamano', 'mediano') === $t ? 'selected' : '' ?>><?= e(tamano_label($t)) ?></option><?php endforeach; ?></select></div>
                            <div class="campo campo--6"><label for="sexo">Sexo</label><select id="sexo" name="sexo"><option value="">Sin dato</option><option value="macho" <?= $valor('sexo') === 'macho' ? 'selected' : '' ?>>Macho</option><option value="hembra" <?= $valor('sexo') === 'hembra' ? 'selected' : '' ?>>Hembra</option></select></div>
                            <div class="campo campo--6"><label for="nacimiento">Nacimiento</label><input id="nacimiento" name="nacimiento" type="date" value="<?= e($valor('nacimiento')) ?>"></div>
                            <div class="campo campo--6"><label for="peso">Peso (kg)</label><input id="peso" name="peso" type="number" min="0" max="90" step="0.1" value="<?= e($valor('peso')) ?>"></div>
                            <div class="campo campo--6"><label for="foto">Foto</label><input id="foto" name="foto" type="file" accept="image/jpeg,image/png,image/webp" class="archivo"></div>
                            <div class="campo"><label for="temperamento">Temperamento</label><input id="temperamento" name="temperamento" type="text" maxlength="200" placeholder="Nervioso con la secadora, sociable, muerde al cortar uñas…" value="<?= e($valor('temperamento')) ?>"></div>
                            <div class="campo"><label for="alergias">Alergias o condiciones</label><input id="alergias" name="alergias" type="text" maxlength="300" placeholder="Piel sensible, alergia a fragancias…" value="<?= e($valor('alergias')) ?>"></div>
                            <div class="campo"><label for="notas">Notas</label><textarea id="notas" name="notas" maxlength="1000"><?= e($valor('notas')) ?></textarea></div>
                        </div>
                        <div class="formulario__pie">
                            <button class="boton" type="submit"><?= $editando ? 'Guardar cambios' : 'Agregar mascota' ?></button>
                            <?php if ($editando): ?><a class="boton boton--contorno" href="<?= url('/mi-cuenta/mascotas') ?>">Cancelar</a><?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
