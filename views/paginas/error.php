<?php
declare(strict_types=1);
$codigo = (int) ($codigo ?? 404);
$mensaje = (string) ($mensaje ?? 'La página que buscas no existe.');
?>
<section class="seccion">
    <div class="contenedor">
        <div class="error-pagina">
            <div class="codigo-error"><?= $codigo ?></div>
            <h1><?= e($titulo ?? 'Algo salió mal') ?></h1>
            <p class="texto-suave" style="max-width:52ch;margin-inline:auto;"><?= e($mensaje) ?></p>
            <div class="flex-centro" style="justify-content:center;margin-top:24px;">
                <a class="boton" href="<?= url('/') ?>">Volver al inicio</a>
                <a class="boton boton--contorno" href="<?= url('/agenda') ?>">Agendar una cita</a>
            </div>
        </div>
    </div>
</section>
