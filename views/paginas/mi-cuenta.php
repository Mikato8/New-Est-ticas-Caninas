<?php
declare(strict_types=1);
$totalCompras = 0.0;
foreach ($compras as $compra) { $totalCompras += (float) $compra['total']; }
$proxima = $proximas[0] ?? null;
$historialTotal = 0;
foreach ($historial as $items) { $historialTotal += count($items); }
?>
<div class="contenedor">
    <nav class="migas" aria-label="Ruta"><a href="<?= url('/') ?>">Inicio</a><span>/</span>Mi cuenta</nav>
    <header class="portada-interior">
        <span class="mono texto-oro">Mi cuenta</span>
        <h1>Hola, <?= e(explode(' ', (string) $usuario['nombre'])[0]) ?>.</h1>
        <p>Aquí están tus mascotas, próximas citas e historial de estética. Si necesitas algo, escríbenos.</p>
    </header>
</div>

<section class="seccion seccion--compacta">
    <div class="contenedor">
        <div class="cuenta">
            <?php parcial('cuenta-nav'); ?>
            <div>
                <div class="grid grid--4 mb-3">
                    <div class="tarjeta-dato"><span class="mono">Mascotas</span><strong class="dato"><?= count($mascotas) ?></strong></div>
                    <div class="tarjeta-dato"><span class="mono">Próximas citas</span><strong class="dato"><?= count($proximas) ?></strong></div>
                    <div class="tarjeta-dato"><span class="mono">Servicios registrados</span><strong class="dato"><?= $historialTotal ?></strong></div>
                    <div class="tarjeta-dato"><span class="mono">Compras</span><strong class="dato" style="font-size:1.4rem;"><?= dinero($totalCompras) ?></strong></div>
                </div>

                <?php if ($proxima !== null): ?>
                    <div class="tarjeta tarjeta__cuerpo mb-3" style="border-left:5px solid var(--oro);">
                        <div class="flex-entre">
                            <div>
                                <span class="mono texto-oro">Próxima cita</span>
                                <h2 style="font-size:1.6rem;margin:6px 0;"><?= e($proxima['mascota_nombre']) ?> · <?= e($proxima['servicio_nombre']) ?></h2>
                                <p class="texto-suave mb-0"><?= e(dia_semana($proxima['fecha'])) ?> <?= e(fecha_larga($proxima['fecha'])) ?> a las <?= e(hora_ampm($proxima['hora'])) ?> · <?= e($proxima['codigo']) ?></p>
                            </div>
                            <div class="flex-centro">
                                <?= estado_badge($proxima['estado']) ?>
                                <a class="boton boton--pequeno boton--contorno" href="<?= url('/mi-cuenta/citas') ?>">Ver detalle</a>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="tarjeta tarjeta__cuerpo mb-3">
                        <div class="flex-entre">
                            <div><h2 style="font-size:1.4rem;margin:0;">No tienes citas próximas</h2><p class="texto-suave mb-0">Reserva un baño, corte o spa para tu perro.</p></div>
                            <a class="boton boton--oro" href="<?= url('/agenda') ?>">Agendar cita</a>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="flex-entre mb-2">
                    <h2 style="font-size:1.35rem;margin:0;">Mis mascotas</h2>
                    <a class="enlace-flecha" href="<?= url('/mi-cuenta/mascotas') ?>">Administrar <?= icono('flecha') ?></a>
                </div>
                <div class="grid grid--2 mb-4">
                    <?php foreach ($mascotas as $mascota): ?>
                        <div class="mascota-tarjeta">
                            <?php if ($mascota['foto']): ?><img src="<?= e($mascota['foto']) ?>" alt=""><?php else: ?><span class="avatar avatar--oro"><?= e(iniciales((string) $mascota['nombre'])) ?></span><?php endif; ?>
                            <div style="flex:1;">
                                <strong><?= e($mascota['nombre']) ?></strong>
                                <small><?= e(tamano_label((string) $mascota['tamano'])) ?> · <?= e((string) ($mascota['raza'] ?: 'Mestizo')) ?></small>
                                <div class="flex-centro" style="gap:12px;margin-top:4px;">
                                    <a class="enlace-flecha" style="font-size:.78rem;" href="<?= url('/mi-cuenta/historial/' . $mascota['id']) ?>">Historial</a>
                                    <a class="enlace-flecha" style="font-size:.78rem;" href="<?= url('/mi-cuenta/mascotas?editar=' . $mascota['id']) ?>">Editar</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <?php if ($mascotas === []): ?>
                        <div class="tarjeta tarjeta__cuerpo" style="grid-column:1/-1;">
                            <p class="texto-suave">Aún no has registrado a tu perro. Agrégalo para guardar su historial y agilizar tus citas.</p>
                            <a class="boton boton--pequeno" href="<?= url('/mi-cuenta/mascotas') ?>">Agregar mascota</a>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if ($citas !== []): ?>
                    <div class="flex-entre mb-2">
                        <h2 style="font-size:1.35rem;margin:0;">Historial de citas</h2>
                        <a class="enlace-flecha" href="<?= url('/mi-cuenta/citas') ?>">Ver todas <?= icono('flecha') ?></a>
                    </div>
                    <div class="tarjeta">
                        <div class="tabla-scroll">
                            <table class="tabla">
                                <thead><tr><th>Fecha</th><th>Mascota</th><th>Servicio</th><th>Estado</th><th class="num">Precio</th></tr></thead>
                                <tbody>
                                    <?php foreach (array_slice($citas, 0, 6) as $cita): ?>
                                        <tr>
                                            <td><?= e(fecha($cita['fecha'], 'd M Y')) ?> · <?= e(substr((string) $cita['hora'], 0, 5)) ?></td>
                                            <td><?= e($cita['mascota_nombre']) ?></td>
                                            <td><?= e($cita['servicio_nombre']) ?></td>
                                            <td><?= estado_badge($cita['estado']) ?></td>
                                            <td class="num"><?= precio($cita['precio']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($compras !== []): ?>
                    <h2 class="mt-4" style="font-size:1.35rem;">Mis compras</h2>
                    <div class="tarjeta">
                        <div class="tabla-scroll">
                            <table class="tabla">
                                <thead><tr><th>Folio</th><th>Fecha</th><th>Método</th><th class="num">Total</th></tr></thead>
                                <tbody>
                                    <?php foreach ($compras as $compra): ?>
                                        <tr><td class="mono"><?= e($compra['folio']) ?></td><td><?= e(fecha($compra['fecha'])) ?></td><td><?= e(ucfirst((string) $compra['metodo_pago'])) ?></td><td class="num"><?= dinero($compra['total']) ?></td></tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
