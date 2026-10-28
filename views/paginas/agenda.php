<?php
declare(strict_types=1);
$v = static fn (string $k, string $d = ''): string => e(viejo($k, $d));
$errores = errores();
$tieneServicio = $servicio !== null;
?>
<div class="contenedor">
    <nav class="migas" aria-label="Ruta"><a href="<?= url('/') ?>">Inicio</a><span>/</span>Agenda</nav>
    <header class="portada-interior">
        <span class="mono texto-oro">Reserva en línea</span>
        <h1>Agenda una cita tranquila.</h1>
        <p>Elige servicio, día y hora. Te confirmamos por WhatsApp. Si es la primera vez, cuéntanos cómo es tu perro para preparar el manejo.</p>
    </header>
</div>

<section class="seccion seccion--compacta">
    <div class="contenedor">
        <div class="agenda-rejilla">
            <div>
                <form class="tarjeta tarjeta__cuerpo formulario" method="get" action="<?= url('/agenda') ?>">
                    <h2 style="font-size:1.45rem;margin-bottom:0;">1. Elige servicio y fecha</h2>
                    <div class="rejilla-campos">
                        <div class="campo campo--6">
                            <label for="servicio">Servicio *</label>
                            <select id="servicio" name="servicio" required>
                                <option value="">Elige un servicio</option>
                                <?php foreach ($servicios as $item): ?>
                                    <option value="<?= (int) $item['id'] ?>" <?= (int) $servicioId === (int) $item['id'] ? 'selected' : '' ?>>
                                        <?= e($item['nombre']) ?> · <?= precio($item['precio']) ?> · <?= e(tamano_label((string) $item['tamano'])) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="campo campo--6">
                            <label for="fecha">Fecha *</label>
                            <input id="fecha" name="fecha" type="date" value="<?= e($fecha) ?>" min="<?= e($minFecha) ?>" max="<?= e($maxFecha) ?>" required>
                        </div>
                    </div>
                    <div class="formulario__pie">
                        <button class="boton" type="submit"><?= icono('reloj') ?> Ver horarios disponibles</button>
                        <span class="texto-tenue" style="font-size:.8rem;">Abrimos de lunes a sábado.</span>
                    </div>
                </form>

                <?php if (!$tieneServicio): ?>
                    <div class="nota nota--teal mt-3">
                        <strong>Primer paso:</strong> elige un servicio y una fecha. Después podrás seleccionar la hora y escribir los datos de tu perro.
                    </div>
                <?php else: ?>
                    <form class="tarjeta tarjeta__cuerpo formulario mt-3" method="post" action="<?= url('/agenda') ?>">
                        <?= csrf_field() ?>
                        <input type="hidden" name="servicio_id" value="<?= (int) $servicio['id'] ?>">
                        <input type="hidden" name="fecha" value="<?= e($fecha) ?>">
                        <div style="position:absolute;left:-9999px;" aria-hidden="true"><label for="sitio_web">No llenar</label><input id="sitio_web" type="text" name="sitio_web" tabindex="-1" autocomplete="off"></div>

                        <div class="flex-entre" style="align-items:flex-start;gap:14px;">
                            <div>
                                <span class="mono texto-oro">2. Tus datos y la hora</span>
                                <h2 style="font-size:1.35rem;margin:4px 0;"><?= e($servicio['nombre']) ?> · <?= precio($servicio['precio']) ?></h2>
                                <p class="texto-suave" style="font-size:.9rem;margin:0;"><?= e(dia_semana($fecha)) ?> <?= e(fecha_larga($fecha)) ?> · <?= (int) $servicio['duracion_min'] ?> min</p>
                            </div>
                            <a class="boton boton--pequeno boton--contorno" href="<?= url('/agenda?servicio=' . (int) $servicio['id'] . '&fecha=' . urlencode($fecha)) ?>">Cambiar</a>
                        </div>

                        <div class="rejilla-campos">
                            <div class="campo campo--6<?= isset($errores['nombre']) ? ' campo--error' : '' ?>">
                                <label for="nombre">Nombre completo *</label>
                                <input id="nombre" name="nombre" type="text" required maxlength="120" value="<?= $v('nombre', (string) ($usuario['nombre'] ?? '')) ?>" autocomplete="name">
                                <?php if (isset($errores['nombre'])): ?><span class="error"><?= e($errores['nombre']) ?></span><?php endif; ?>
                            </div>
                            <div class="campo campo--6<?= isset($errores['telefono']) ? ' campo--error' : '' ?>">
                                <label for="telefono">Teléfono / WhatsApp *</label>
                                <input id="telefono" name="telefono" type="tel" required maxlength="30" value="<?= $v('telefono', (string) ($usuario['telefono'] ?? '')) ?>" autocomplete="tel">
                                <?php if (isset($errores['telefono'])): ?><span class="error"><?= e($errores['telefono']) ?></span><?php endif; ?>
                            </div>
                            <div class="campo campo--6">
                                <label for="correo">Correo (opcional)</label>
                                <input id="correo" name="correo" type="email" maxlength="160" value="<?= $v('correo', (string) ($usuario['correo'] ?? '')) ?>" autocomplete="email">
                            </div>
                            <div class="campo campo--6<?= isset($errores['mascota']) ? ' campo--error' : '' ?>">
                                <label for="mascota">Nombre del perro *</label>
                                <input id="mascota" name="mascota" type="text" required maxlength="80" value="<?= $v('mascota') ?>" list="mascotas-guardadas" autocomplete="off">
                                <?php if ($mascotas !== []): ?>
                                    <datalist id="mascotas-guardadas"><?php foreach ($mascotas as $m): ?><option value="<?= e($m['nombre']) ?>"><?= e((string) $m['raza']) ?></option><?php endforeach; ?></datalist>
                                <?php endif; ?>
                                <?php if (isset($errores['mascota'])): ?><span class="error"><?= e($errores['mascota']) ?></span><?php endif; ?>
                            </div>
                            <div class="campo campo--6"><label for="raza">Raza o mestizo</label><input id="raza" name="raza" type="text" maxlength="80" value="<?= $v('raza') ?>" placeholder="Ej. Poodle, criollo, schnauzer"></div>
                            <div class="campo campo--6">
                                <label for="tamano">Tamaño</label>
                                <select id="tamano" name="tamano">
                                    <option value="chico" <?= viejo('tamano') === 'chico' ? 'selected' : '' ?>>Chico (hasta 8 kg)</option>
                                    <option value="mediano" <?= viejo('tamano', 'mediano') === 'mediano' ? 'selected' : '' ?>>Mediano (8 a 15 kg)</option>
                                    <option value="grande" <?= viejo('tamano') === 'grande' ? 'selected' : '' ?>>Grande (15 a 30 kg)</option>
                                    <option value="gigante" <?= viejo('tamano') === 'gigante' ? 'selected' : '' ?>>Gigante (más de 30 kg)</option>
                                </select>
                            </div>
                            <div class="campo<?= isset($errores['hora']) ? ' campo--error' : '' ?>">
                                <label>Hora *</label>
                                <div class="horas">
                                    <?php if ($horas === []): ?>
                                        <p class="texto-tenue" style="grid-column:1/-1;">No hay horarios para ese día. Elige otra fecha o escríbenos.</p>
                                    <?php else: ?>
                                        <?php foreach ($horas as $i => $hora): ?>
                                            <input type="radio" name="hora" id="hora-<?= $i ?>" value="<?= e($hora) ?>" <?= $i === 0 ? 'required' : '' ?> <?= viejo('hora') === $hora ? 'checked' : '' ?>>
                                            <label for="hora-<?= $i ?>"><?= e($hora) ?></label>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                                <?php if (isset($errores['hora'])): ?><span class="error"><?= e($errores['hora']) ?></span><?php endif; ?>
                            </div>
                            <div class="campo">
                                <label for="notas">¿Algo que debamos saber?</label>
                                <textarea id="notas" name="notas" maxlength="1000" placeholder="Alergias, miedos, medicamentos, si tiene nudos, si es su primera visita…"><?= $v('notas') ?></textarea>
                            </div>
                        </div>

                        <label class="campo--inline flex-centro" style="gap:9px;-webkit-user-select:none;user-select:none;">
                            <input type="checkbox" name="acepto" value="1" required style="width:18px;height:18px;accent-color:var(--teal);">
                            <span style="font-size:.85rem;color:var(--tinta-suave);">Acepto el <a href="<?= url('/privacidad') ?>" target="_blank">aviso de privacidad</a> y las condiciones del servicio.</span>
                        </label>
                        <?php if (isset($errores['acepto'])): ?><span class="error"><?= e($errores['acepto']) ?></span><?php endif; ?>

                        <div class="formulario__pie">
                            <button class="boton boton--oro boton--grande" type="submit"><?= icono('agenda') ?> Solicitar cita</button>
                            <span class="texto-tenue" style="font-size:.78rem;">Recibirás un código de confirmación.</span>
                        </div>
                    </form>
                <?php endif; ?>
            </div>

            <aside class="agenda-resumen">
                <span class="mono" style="color:var(--oro-claro);">Resumen</span>
                <h3 style="font-size:1.6rem;">Tu cita</h3>
                <ul class="agenda-resumen__lista">
                    <li><?= icono('tijeras') ?><span><strong>Servicio:</strong> <?= $tieneServicio ? e($servicio['nombre']) : 'Elígelo en el paso 1' ?></span></li>
                    <li><?= icono('dinero') ?><span><strong>Precio:</strong> <?= $tieneServicio ? precio($servicio['precio']) : '—' ?></span></li>
                    <li><?= icono('reloj') ?><span><strong>Duración:</strong> <?= $tieneServicio ? (int) $servicio['duracion_min'] . ' min' : 'Se confirma al elegir' ?></span></li>
                    <li><?= icono('pin') ?><span><strong>Dónde:</strong> <?= e(config('contacto.ciudad')) ?>. Te enviamos la dirección al confirmar.</span></li>
                    <li><?= icono('whatsapp') ?><span><strong>Confirmación:</strong> por WhatsApp o teléfono.</span></li>
                </ul>
                <div class="nota nota--teal mt-3" style="background:rgba(251,247,239,.1);border-color:rgba(251,247,239,.2);color:var(--crema);">
                    <strong>Importante:</strong> la cita queda <em>pendiente</em> hasta que la confirmemos. Si tu perro está enfermo o tiene una urgencia, llama a tu veterinario.
                </div>
                <?php if ($usuario === null): ?>
                    <p style="font-size:.85rem;color:rgba(251,247,239,.72);">¿Quieres ver tus citas y el historial de tu perro? <a href="<?= url('/registro') ?>" style="color:var(--oro-claro);">Crea tu cuenta gratis</a>.</p>
                <?php else: ?>
                    <p style="font-size:.85rem;color:rgba(251,247,239,.72);">Estás dentro de tu cuenta. Tus datos se guardaron automáticamente.</p>
                <?php endif; ?>
            </aside>
        </div>
    </div>
</section>

<section class="seccion seccion--compacta">
    <div class="contenedor">
        <div class="grid grid--3">
            <div class="tarjeta tarjeta__cuerpo"><h3>¿Ya tienes cita?</h3><p class="texto-suave" style="font-size:.9rem;">Consulta el estado con el código que te dimos.</p><a class="enlace-flecha" href="<?= url('/agenda/consultar') ?>">Consultar mi cita <?= icono('flecha') ?></a></div>
            <div class="tarjeta tarjeta__cuerpo"><h3>Primera visita</h3><p class="texto-suave" style="font-size:.9rem;">Trae cartilla de vacunación y cuéntanos si tu perro tiene miedos o alergias.</p><a class="enlace-flecha" href="<?= url('/cuidados') ?>">Qué llevar <?= icono('flecha') ?></a></div>
            <div class="tarjeta tarjeta__cuerpo"><h3>Cancelaciones</h3><p class="texto-suave" style="font-size:.9rem;">Avísanos con 24 horas de anticipación para liberar el lugar.</p><a class="enlace-flecha" href="<?= url('/contacto') ?>">Contacto <?= icono('flecha') ?></a></div>
        </div>
    </div>
</section>
