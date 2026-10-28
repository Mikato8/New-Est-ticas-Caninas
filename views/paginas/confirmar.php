<?php
declare(strict_types=1);
?>
<div class="contenedor">
    <nav class="migas" aria-label="Ruta"><a href="<?= url('/') ?>">Inicio</a><span>/</span>Confirmar</nav>
    <section class="seccion seccion--compacta">
        <div class="tarjeta tarjeta__cuerpo" style="max-width:620px;margin:0 auto;">
            <span class="mono texto-oro">Confirmación</span>
            <h1 style="font-size:clamp(1.8rem,4vw,2.5rem);margin:8px 0 10px;">Antes de continuar</h1>
            <p class="texto-suave" style="font-size:1rem;"><?= e($mensaje) ?></p>

            <div class="nota nota--teal mt-3" style="font-size:.88rem;">
                Esta acción se ejecutará de inmediato al confirmar. Si no estás seguro, cancela y revisa el registro.
            </div>

            <form method="post" action="<?= url($destino) ?>" class="mt-3">
                <?= csrf_field() ?>
                <input type="hidden" name="confirmar" value="1">
                <?php foreach ($campos as $nombre => $valor): ?>
                    <input type="hidden" name="<?= e((string) $nombre) ?>" value="<?= e((string) $valor) ?>">
                <?php endforeach; ?>
                <div class="flex-centro" style="gap:10px;flex-wrap:wrap;">
                    <button class="boton" type="submit" style="background:var(--coral);border-color:var(--coral);color:#fff;">Sí, continuar</button>
                    <a class="boton boton--contorno" href="<?= url($volver) ?>">Cancelar y volver</a>
                </div>
            </form>
        </div>
    </section>
</div>
