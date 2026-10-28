<?php
declare(strict_types=1);
?>
<div class="contenedor">
    <nav class="migas" aria-label="Ruta"><a href="<?= url('/') ?>">Inicio</a><span>/</span><a href="<?= url('/agenda') ?>">Agenda</a><span>/</span>Consultar</nav>
    <header class="portada-interior">
        <span class="mono texto-oro">Tu cita</span>
        <h1>Consulta el estado.</h1>
        <p>Escribe el código que te dimos al reservar. Si no lo tienes, escríbenos con el nombre del perro y la fecha.</p>
    </header>
</div>

<section class="seccion seccion--compacta">
    <div class="contenedor">
        <div class="grid grid--2" style="align-items:start;">
            <form class="tarjeta tarjeta__cuerpo formulario" method="get" action="<?= url('/agenda/consultar') ?>">
                <div class="campo">
                    <label for="codigo">Código de cita</label>
                    <input id="codigo" name="codigo" type="text" value="<?= e($codigo) ?>" placeholder="MK-A1B2C3" autocomplete="off" required>
                </div>
                <button class="boton" type="submit"><?= icono('buscar') ?> Buscar cita</button>
            </form>

            <?php if ($codigo !== '' && $cita === null): ?>
                <div class="tarjeta tarjeta__cuerpo">
                    <h3>No encontramos esa cita</h3>
                    <p class="texto-suave">Revisa el código o escríbenos. También puedes agendar de nuevo.</p>
                    <a class="boton boton--contorno" href="<?= url('/agenda') ?>">Agendar otra cita</a>
                </div>
            <?php elseif ($cita !== null): ?>
                <div class="tarjeta tarjeta__cuerpo">
                    <div class="flex-entre">
                        <div><span class="mono texto-oro"><?= e($cita['codigo']) ?></span><h3 style="margin:4px 0;"><?= e($cita['mascota_nombre']) ?></h3></div>
                        <?= estado_badge($cita['estado']) ?>
                    </div>
                    <div class="datos-lista mt-2">
                        <div><dt>Fecha</dt><dd><?= e(fecha_larga($cita['fecha'])) ?></dd></div>
                        <div><dt>Hora</dt><dd><?= e(hora_ampm($cita['hora'])) ?></dd></div>
                        <div><dt>Servicio</dt><dd><?= e($cita['servicio_nombre']) ?></dd></div>
                        <div><dt>Precio</dt><dd><?= precio($cita['precio']) ?></dd></div>
                        <div><dt>Tutor</dt><dd><?= e($cita['cliente_nombre']) ?></dd></div>
                    </div>
                    <div class="nota nota--teal mt-3" style="font-size:.84rem;">Si necesitas cambiar la fecha, contáctanos con tu código. No compartas tu código en redes sociales.</div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
