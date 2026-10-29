<?php
declare(strict_types=1);
$ayer = date('Y-m-d', strtotime($fecha . ' -1 day'));
$manana = date('Y-m-d', strtotime($fecha . ' +1 day'));
$acciones = [
    'pendiente' => [['confirmada', 'Confirmar', 'boton--pequeno'], ['cancelada', 'Cancelar', 'boton--contorno']],
    'confirmada' => [['en_proceso', 'Iniciar', 'boton--pequeno'], ['no_asistio', 'No asistió', 'boton--contorno']],
    'en_proceso' => [['completada', 'Completar', 'boton--pequeno'],],
];
?>
<header class="panel__cabecera">
    <div>
        <span class="mono texto-oro">Agenda</span>
        <h1><?= e(dia_semana($fecha)) ?> <?= e(fecha_larga($fecha)) ?></h1>
        <p><?= count($citas) ?> cita<?= count($citas) === 1 ? '' : 's' ?> · <?= (int) $resumen['completada'] ?> completadas hoy.</p>
    </div>
    <div class="panel__acciones">
        <a class="boton boton--pequeno" href="<?= e($whatsappAgenda) ?>" target="_blank" rel="noopener"><?= icono('whatsapp') ?> Enviar agenda</a>
        <a class="boton boton--pequeno boton--contorno" href="<?= url('/panel/agenda/exportar?fecha=' . $fecha) ?>"><?= icono('descargar') ?> Descargar CSV</a>
        <a class="boton boton--pequeno boton--contorno" href="<?= url('/panel/agenda?fecha=' . $ayer) ?>">← <?= e(fecha($ayer, 'd M')) ?></a>
        <a class="boton boton--pequeno boton--contorno" href="<?= url('/panel/agenda?fecha=' . date('Y-m-d')) ?>">Hoy</a>
        <a class="boton boton--pequeno boton--contorno" href="<?= url('/panel/agenda?fecha=' . $manana) ?>"><?= e(fecha($manana, 'd M')) ?> →</a>
    </div>
</header>
<p class="texto-tenue" style="font-size:.78rem;margin:-8px 0 14px;">El enlace de WhatsApp es privado y vence en 7 días. Incluye la agenda del día y un botón para descargar el CSV.</p>

<div class="panel__rejilla panel__rejilla--2" style="align-items:start;">
    <section>
        <div class="panel__seccion-titulo"><h2>Citas del día</h2><span class="etiqueta"><?= e(fecha($fecha, 'd/m/Y')) ?></span></div>
        <?php if ($citas === []): ?>
            <div class="panel-caja texto-centro"><p class="texto-suave">No hay citas para este día.</p><a class="boton boton--pequeno" href="<?= url('/agenda') ?>" target="_blank">Abrir agenda pública</a></div>
        <?php endif; ?>
        <div class="agenda-panel">
            <?php foreach ($citas as $cita): ?>
                <div class="agenda-item agenda-item--<?= e($cita['estado']) ?>">
                    <div class="agenda-item__hora"><?= e(substr((string) $cita['hora'], 0, 5)) ?><small><?= (int) $cita['duracion_min'] ?> min</small></div>
                    <div class="agenda-item__datos">
                        <strong><?= e($cita['mascota_nombre']) ?> · <?= e($cita['servicio_nombre']) ?></strong>
                        <span><?= e($cita['cliente_nombre']) ?> · <?= e((string) ($cita['cliente_telefono'] ?: 'sin teléfono')) ?> · <?= e($cita['codigo']) ?></span>
                        <?php if ($cita['notas']): ?><span style="display:block;margin-top:3px;color:var(--tinta-tenue);">“<?= e(limitar_texto((string) $cita['notas'], 120)) ?>”</span><?php endif; ?>
                        <div class="flex-centro" style="margin-top:5px;"><?= estado_badge($cita['estado']) ?><span class="texto-tenue" style="font-size:.75rem;"><?= precio($cita['precio']) ?></span></div>
                    </div>
                    <div class="agenda-item__acciones">
                        <?php if (isset($acciones[$cita['estado']])): ?>
                            <?php foreach ($acciones[$cita['estado']] as [$estado, $texto, $clase]): ?>
                                <form method="post" action="<?= url('/panel/cita/estado') ?>">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="id" value="<?= (int) $cita['id'] ?>">
                                    <input type="hidden" name="estado" value="<?= e($estado) ?>">
                                    <button class="boton <?= e($clase) ?> <?= $estado === 'cancelada' || $estado === 'no_asistio' ? 'boton--contorno' : '' ?>" type="submit"><?= e($texto) ?></button>
                                </form>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        <a class="boton boton--pequeno boton--contorno" href="<?= url('/panel/mascotas/' . $cita['mascota_id']) ?>">Ficha</a>
                    </div>
                </div>
                <details class="panel-caja" style="padding:10px 14px;">
                    <summary style="cursor:pointer;font-size:.8rem;font-weight:600;color:var(--teal);">Reprogramar</summary>
                    <form class="flex-centro" method="post" action="<?= url('/panel/cita/reagendar') ?>" style="margin-top:10px;gap:8px;">
                        <?= csrf_field() ?>
                        <input type="hidden" name="id" value="<?= (int) $cita['id'] ?>">
                        <input type="date" name="fecha" value="<?= e($cita['fecha']) ?>" required style="padding:7px 9px;border:1px solid var(--borde-fuerte);border-radius:7px;">
                        <select name="hora" required style="padding:7px 9px;border:1px solid var(--borde-fuerte);border-radius:7px;">
                            <?php foreach ($horas as $hora): ?><option value="<?= e($hora) ?>" <?= substr((string) $cita['hora'], 0, 5) === $hora ? 'selected' : '' ?>><?= e($hora) ?></option><?php endforeach; ?>
                        </select>
                        <button class="boton boton--pequeno" type="submit">Guardar</button>
                    </form>
                </details>
            <?php endforeach; ?>
        </div>
    </section>

    <aside class="panel-caja">
        <div class="panel__seccion-titulo"><h2>Nueva cita</h2></div>
        <form class="formulario" method="post" action="<?= url('/panel/agenda') ?>">
            <?= csrf_field() ?>
            <div class="campo">
                <label for="cliente_id">Cliente</label>
                <select id="cliente_id" name="cliente_id" required>
                    <option value="">Elige cliente</option>
                    <?php foreach ($clientes as $cliente): ?><option value="<?= (int) $cliente['id'] ?>"><?= e($cliente['nombre']) ?><?= $cliente['telefono'] ? ' · ' . e($cliente['telefono']) : '' ?></option><?php endforeach; ?>
                </select>
                <span class="ayuda"><a href="<?= url('/panel/clientes') ?>">Crear cliente nuevo</a></span>
            </div>
            <div class="campo">
                <label for="mascota_id">Mascota</label>
                <select id="mascota_id" name="mascota_id" required>
                    <option value="">Elige mascota</option>
                    <?php foreach ($mascotas as $mascota): ?>
                        <option value="<?= (int) $mascota['id'] ?>"><?= e($mascota['nombre']) ?> · <?= e($mascota['cliente_nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="campo">
                <label for="servicio_id">Servicio</label>
                <select id="servicio_id" name="servicio_id" required>
                    <option value="">Elige servicio</option>
                    <?php foreach ($servicios as $servicio): ?><option value="<?= (int) $servicio['id'] ?>"><?= e($servicio['nombre']) ?> · <?= precio($servicio['precio']) ?></option><?php endforeach; ?>
                </select>
            </div>
            <div class="rejilla-campos">
                <div class="campo campo--6"><label for="fecha">Fecha</label><input id="fecha" name="fecha" type="date" value="<?= e($fecha) ?>" required></div>
                <div class="campo campo--6"><label for="hora">Hora</label><select id="hora" name="hora" required><?php foreach ($horas as $hora): ?><option value="<?= e($hora) ?>"><?= e($hora) ?></option><?php endforeach; ?></select></div>
            </div>
            <div class="campo"><label for="notas">Notas</label><textarea id="notas" name="notas" maxlength="1000" style="min-height:80px;"></textarea></div>
            <button class="boton" type="submit">Registrar cita</button>
        </form>
    </aside>
</div>
