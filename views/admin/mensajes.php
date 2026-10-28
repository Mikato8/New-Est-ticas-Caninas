<?php
declare(strict_types=1);
$asuntos = ['servicio' => 'Servicio', 'mayoreo' => 'Mayoreo', 'directorio' => 'Directorio', 'comunidad' => 'Comunidad', 'otro' => 'Otro'];
?>
<header class="panel__cabecera">
    <div><span class="mono texto-oro">Contacto</span><h1>Mensajes</h1><p>Solicitudes de servicio, distribución, directorio y dudas. Responde desde tu correo o WhatsApp.</p></div>
</header>

<?php if ($contactos === []): ?>
    <div class="panel-caja texto-centro"><p class="texto-suave">No hay mensajes todavía.</p></div>
<?php endif; ?>

<div style="display:grid;gap:14px;">
    <?php foreach ($contactos as $contacto): ?>
        <article class="panel-caja">
            <div class="flex-entre" style="align-items:flex-start;">
                <div>
                    <div class="flex-centro"><strong><?= e($contacto['nombre']) ?></strong><span class="etiqueta"><?= e($asuntos[$contacto['asunto']] ?? $contacto['asunto']) ?></span><span class="badge <?= $contacto['estado'] === 'nuevo' ? 'badge--pendiente' : 'badge--activo' ?>"><?= e($contacto['estado']) ?></span></div>
                    <small class="texto-tenue"><?= e(fecha($contacto['creado_en'], 'd M Y · H:i')) ?></small>
                </div>
                <div class="flex-centro">
                    <?php if ($contacto['correo']): ?><a class="boton boton--pequeno boton--contorno" href="mailto:<?= e($contacto['correo']) ?>?subject=Respuesta%20Mikato">Responder</a><?php endif; ?>
                    <?php if ($contacto['telefono']): ?><a class="boton boton--pequeno boton--contorno" href="https://wa.me/<?= e(preg_replace('/\D+/', '', (string) $contacto['telefono'])) ?>" target="_blank" rel="noopener">WhatsApp</a><?php endif; ?>
                    <?php if ($contacto['estado'] === 'nuevo'): ?>
                        <form method="post" action="<?= url('/admin/mensajes/atender') ?>"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $contacto['id'] ?>"><button class="boton boton--pequeno" type="submit">Marcar atendido</button></form>
                    <?php endif; ?>
                    <form method="post" action="<?= url('/admin/mensajes/eliminar') ?>"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $contacto['id'] ?>"><button class="boton boton--pequeno boton--contorno" type="submit" style="color:var(--coral);">Eliminar</button></form>
                </div>
            </div>
            <p class="texto-suave mt-2" style="font-size:.9rem;"><?= nl2br(e($contacto['mensaje'])) ?></p>
            <p class="texto-tenue" style="font-size:.76rem;margin:0;"><?= e((string) ($contacto['correo'] ?: 'sin correo')) ?> · <?= e((string) ($contacto['telefono'] ?: 'sin teléfono')) ?></p>
        </article>
    <?php endforeach; ?>
</div>
