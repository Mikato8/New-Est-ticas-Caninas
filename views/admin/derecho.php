<?php
declare(strict_types=1);
$archivos = [
    'identificacion' => ['Identificación oficial', $solicitud['archivo_identificacion'] ?? ''],
    'representacion' => ['Documento de representación', $solicitud['archivo_representacion'] ?? ''],
    'acreditativo' => ['Documento acreditativo', $solicitud['archivo_acreditativo'] ?? ''],
];
?>
<header class="panel__cabecera">
    <div><span class="mono texto-oro">Derechos ARCO</span><h1><?= e($solicitud['folio']) ?></h1><p>Solicitud de rectificación de datos personales.</p></div>
    <div class="panel__acciones"><a class="boton boton--pequeno boton--contorno" href="<?= url('/admin/derechos') ?>">← Volver</a></div>
</header>

<div class="panel__rejilla panel__rejilla--2" style="align-items:start;">
    <section class="panel-caja">
        <div class="panel__seccion-titulo"><h2>Datos de la solicitud</h2><span class="etiqueta"><?= e($solicitud['fecha_solicitud']) ?></span></div>
        <div class="datos-lista">
            <div><dt>Titular</dt><dd><?= e($solicitud['nombre_titular']) ?></dd></div>
            <div><dt>Correo</dt><dd><a href="mailto:<?= e($solicitud['correo']) ?>"><?= e($solicitud['correo']) ?></a></dd></div>
            <div><dt>Teléfono</dt><dd><?= e((string) ($solicitud['telefono'] ?: 'No indicado')) ?></dd></div>
            <div><dt>Ciudad</dt><dd><?= e($solicitud['ciudad']) ?></dd></div>
            <div><dt>Representante</dt><dd><?= e((string) ($solicitud['nombre_representante'] ?: 'No aplica')) ?></dd></div>
            <div><dt>Tipo de representación</dt><dd><?= e((string) ($solicitud['tipo_representacion'] ?: 'No aplica')) ?></dd></div>
            <div><dt>Notificación</dt><dd><?= e($solicitud['notificacion_medio']) ?></dd></div>
            <div><dt>Domicilio</dt><dd><?= e((string) ($solicitud['domicilio'] ?: 'No indicado')) ?></dd></div>
            <div><dt>Oficina</dt><dd><?= e((string) ($solicitud['direccion_oficina'] ?: 'No indicada')) ?></dd></div>
            <div><dt>Firma</dt><dd><?= e($solicitud['nombre_firma']) ?></dd></div>
        </div>

        <h3 class="mt-3">Dato actual</h3>
        <p class="texto-suave" style="white-space:pre-wrap;"><?= e($solicitud['dato_incorrecto']) ?></p>
        <h3>Dato solicitado</h3>
        <p class="texto-suave" style="white-space:pre-wrap;"><?= e($solicitud['dato_correcto']) ?></p>
        <h3>Documento acreditativo descrito</h3>
        <p class="texto-suave" style="white-space:pre-wrap;"><?= e($solicitud['documento_acreditativo']) ?></p>

        <h3 class="mt-3">Archivos</h3>
        <div style="display:grid;gap:7px;">
            <?php foreach ($archivos as $tipo => [$etiqueta, $ruta]): ?>
                <?php if ($ruta !== ''): ?>
                    <a class="boton boton--pequeno boton--contorno" href="<?= url('/admin/derechos/' . $solicitud['id'] . '/archivo/' . $tipo) ?>"><?= icono('descargar') ?> <?= e($etiqueta) ?></a>
                <?php else: ?>
                    <span class="texto-tenue" style="font-size:.82rem;"><?= e($etiqueta) ?>: no adjunto.</span>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </section>

    <aside class="panel-caja formulario">
        <div class="panel__seccion-titulo"><h2>Seguimiento</h2></div>
        <p class="texto-tenue" style="font-size:.78rem;">Recibida: <?= e(fecha($solicitud['creado_en'], 'd M Y H:i')) ?>. Actualizada: <?= e(fecha($solicitud['actualizado_en'], 'd M Y H:i')) ?>.</p>
        <form method="post" action="<?= url('/admin/derechos/estado') ?>">
            <?= csrf_field() ?>
            <input type="hidden" name="id" value="<?= (int) $solicitud['id'] ?>">
            <div class="campo"><label for="estado">Estado</label><select id="estado" name="estado">
                <?php foreach (['recibida' => 'Recibida', 'en_revision' => 'En revisión', 'resuelta' => 'Resuelta', 'rechazada' => 'Rechazada'] as $clave => $texto): ?>
                    <option value="<?= e($clave) ?>" <?= $solicitud['estado'] === $clave ? 'selected' : '' ?>><?= e($texto) ?></option>
                <?php endforeach; ?>
            </select></div>
            <div class="campo"><label for="respuesta">Respuesta / notas internas</label><textarea id="respuesta" name="respuesta" maxlength="5000" style="min-height:140px;"><?= e((string) ($solicitud['respuesta'] ?? '')) ?></textarea></div>
            <button class="boton" type="submit">Guardar seguimiento</button>
        </form>
        <div class="nota mt-3" style="font-size:.8rem;"><strong>Privacidad:</strong> los archivos están fuera de la carpeta pública y solo se pueden descargar desde esta sesión de administración.</div>
    </aside>
</div>
