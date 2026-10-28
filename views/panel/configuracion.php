<?php
declare(strict_types=1);
$campos = [
    'negocio_nombre' => ['Negocio', 'text', 'Nombre comercial que aparece en el sitio.'],
    'negocio_telefono' => ['Teléfono', 'tel', 'Se muestra en el pie y en el ticket.'],
    'negocio_whatsapp' => ['WhatsApp', 'tel', 'Con lada de país; se usa para el enlace wa.me.'],
    'negocio_direccion' => ['Dirección', 'text', 'Dirección del salón (opcional).'],
    'negocio_horario' => ['Horario', 'text', 'Ej. Lun a sáb · 9:00 a 19:00.'],
    'capacidad_diaria' => ['Capacidad diaria', 'number', 'Número de servicios que puedes atender al día.'],
    'anticipo_pct' => ['Anticipo (%)', 'number', 'Porcentaje de anticipo para servicios largos.'],
];
?>
<header class="panel__cabecera">
    <div><span class="mono texto-oro">Configuración</span><h1>Configuración</h1><p>Datos del negocio que se muestran en el sitio, el ticket y los correos.</p></div>
</header>

<div class="panel__rejilla panel__rejilla--2" style="align-items:start;">
    <form class="panel-caja formulario" method="post" action="<?= url('/panel/configuracion') ?>">
        <?= csrf_field() ?>
        <div class="panel__seccion-titulo"><h2>Datos del negocio</h2></div>
        <?php foreach ($campos as $clave => [$etiqueta, $tipo, $ayuda]): ?>
            <div class="campo">
                <label for="<?= e($clave) ?>"><?= e($etiqueta) ?></label>
                <input id="<?= e($clave) ?>" name="<?= e($clave) ?>" type="<?= e($tipo) ?>" value="<?= e((string) ($config[$clave] ?? '')) ?>" <?= $tipo === 'number' ? 'step="0.01"' : '' ?>>
                <span class="ayuda"><?= e($ayuda) ?></span>
            </div>
        <?php endforeach; ?>
        <button class="boton" type="submit">Guardar configuración</button>
    </form>

    <div style="display:grid;gap:18px;">
        <div class="panel-caja">
            <h3>Qué se guarda aquí</h3>
            <p class="texto-suave" style="font-size:.9rem;">Estos valores viven en la base de datos y se pueden cambiar sin tocar archivos. Los datos sensibles (contraseñas, base de datos, correo SMTP) viven en el archivo <code>.env</code> fuera de <code>public_html</code>.</p>
        </div>
        <div class="panel-caja">
            <h3>Checklist de puesta en marcha</h3>
            <ul class="sin-lista" style="display:grid;gap:8px;font-size:.88rem;color:var(--tinta-suave);">
                <li class="flex-centro"><?= icono('check', 'texto-teal') ?> Cambiar la contraseña del administrador.</li>
                <li class="flex-centro"><?= icono('check', 'texto-teal') ?> Configurar teléfono y WhatsApp reales.</li>
                <li class="flex-centro"><?= icono('check', 'texto-teal') ?> Revisar precios y duraciones del catálogo.</li>
                <li class="flex-centro"><?= icono('check', 'texto-teal') ?> Crear el buzón de contacto y activar MAIL_ENABLED.</li>
                <li class="flex-centro"><?= icono('check', 'texto-teal') ?> Revisar el aviso de privacidad con tu abogado.</li>
                <li class="flex-centro"><?= icono('check', 'texto-teal') ?> Verificar licencia y uso de suelo municipal.</li>
            </ul>
        </div>
        <div class="nota">
            <strong>Consejo:</strong> cambia la contraseña inicial en <a href="<?= url('/perfil') ?>">tu perfil</a> y no compartas el acceso de administrador con todo el equipo. Crea usuarios con rol de estilista para el día a día.
        </div>
    </div>
</div>
