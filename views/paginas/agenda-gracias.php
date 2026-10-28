<?php
declare(strict_types=1);
$inicio = date('Ymd\THis', strtotime($cita['fecha'] . ' ' . $cita['hora']));
$fin = date('Ymd\THis', strtotime($cita['fecha'] . ' ' . $cita['hora'] . ' +' . (int) $cita['duracion_min'] . ' minutes'));
$tituloCal = 'Cita Mikato · ' . $cita['mascota_nombre'];
$detalleCal = 'Servicio: ' . $cita['servicio_nombre'] . ' · Código ' . $cita['codigo'];
$google = 'https://calendar.google.com/calendar/render?action=TEMPLATE&text=' . rawurlencode($tituloCal)
    . '&dates=' . $inicio . '/' . $fin
    . '&details=' . rawurlencode($detalleCal)
    . '&location=' . rawurlencode((string) config('contacto.ciudad'));
?>
<div class="contenedor">
    <nav class="migas" aria-label="Ruta"><a href="<?= url('/') ?>">Inicio</a><span>/</span><a href="<?= url('/agenda') ?>">Agenda</a><span>/</span>Confirmación</nav>
</div>

<section class="seccion">
    <div class="contenedor">
        <div class="cita-confirmada">
            <div style="width:64px;height:64px;border-radius:50%;background:var(--teal-claro);color:var(--teal);display:grid;place-items:center;margin:0 auto 16px;">
                <?= icono('check') ?>
            </div>
            <span class="mono texto-oro">Solicitud recibida</span>
            <h1 style="font-size:clamp(1.9rem,4vw,2.8rem);margin:10px 0;">Cita de <?= e($cita['mascota_nombre']) ?></h1>
            <p class="texto-suave">Guarda tu código. Te contactamos para confirmar el horario.</p>
            <div class="cita-confirmada__codigo" data-codigo><?= e($cita['codigo']) ?></div>
            <div class="flex-centro" style="justify-content:center;">
                <button class="boton boton--contorno boton--pequeno" type="button" data-copiar="<?= e($cita['codigo']) ?>">Copiar código</button>
                <span class="badge"><?= e(estado_cita_label($cita['estado'])) ?></span>
            </div>

            <div class="cita-confirmada__detalle">
                <div><small>Fecha</small><strong><?= e(dia_semana($cita['fecha'])) ?> <?= e(fecha_larga($cita['fecha'])) ?></strong></div>
                <div><small>Hora</small><strong><?= e(hora_ampm($cita['hora'])) ?></strong></div>
                <div><small>Servicio</small><strong><?= e($cita['servicio_nombre']) ?></strong></div>
                <div><small>Precio estimado</small><strong><?= precio($cita['precio']) ?></strong></div>
                <div><small>Mascota</small><strong><?= e($cita['mascota_nombre']) ?><?= $cita['raza'] ? ' · ' . e($cita['raza']) : '' ?></strong></div>
                <div><small>Estado</small><strong><?= e(estado_cita_label($cita['estado'])) ?></strong></div>
            </div>

            <p class="texto-tenue" style="font-size:.84rem;">Si no puedes asistir, avísanos con 24 horas de anticipación. La cita queda pendiente hasta confirmación.</p>
            <div class="flex-centro" style="justify-content:center;">
                <?php if ($whatsapp !== ''): ?>
                    <a class="boton" href="<?= e($whatsapp) ?>" target="_blank" rel="noopener"><?= icono('whatsapp') ?> Confirmar por WhatsApp</a>
                <?php endif; ?>
                <a class="boton boton--contorno" href="<?= e($google) ?>" target="_blank" rel="noopener"><?= icono('agenda') ?> Agregar al calendario</a>
                <a class="boton boton--contorno" href="<?= url('/agenda/consultar?codigo=' . urlencode($cita['codigo'])) ?>">Consultar mi cita</a>
            </div>
        </div>
    </div>
</section>
