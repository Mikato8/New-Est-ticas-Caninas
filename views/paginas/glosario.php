<?php
declare(strict_types=1);
$iconos = ['manto' => 'cepillo', 'herramienta' => 'tijeras', 'salud' => 'escudo', 'manejo' => 'corazon', 'negocio' => 'dinero'];
$porCategoria = [];
foreach ($terminos as $termino) {
    $porCategoria[$termino['categoria']][] = $termino;
}
?>
<div class="contenedor">
    <nav class="migas" aria-label="Ruta"><a href="<?= url('/') ?>">Inicio</a><span>/</span>Glosario</nav>
    <header class="portada-interior">
        <span class="mono texto-oro">Vocabulario del oficio</span>
        <h1>Glosario del estilista canino.</h1>
        <p>Las palabras que se usan en el salón, explicadas sin rodeos. Útil si estás empezando, si atiendes clientes o si quieres entender mejor una ficha técnica.</p>
    </header>
</div>

<section class="seccion seccion--compacta">
    <div class="contenedor">
        <div class="grid grid--3 mb-4">
            <?php foreach ($categorias as $clave => $etiqueta): ?>
                <a class="tarjeta tarjeta__cuerpo tarjeta--enlace" href="#<?= e($clave) ?>" style="text-decoration:none;">
                    <div class="flex-centro">
                        <span class="tarjeta-servicio__icono" style="margin:0;width:42px;height:42px;"><?= icono($iconos[$clave] ?? 'libro') ?></span>
                        <div><strong><?= e($etiqueta) ?></strong><br><small class="texto-tenue"><?= count($porCategoria[$clave] ?? []) ?> términos</small></div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>

        <?php foreach ($categorias as $clave => $etiqueta): ?>
            <?php if (empty($porCategoria[$clave])) continue; ?>
            <section class="mb-4" id="<?= e($clave) ?>">
                <div class="encabezado-seccion" style="margin-bottom:18px;">
                    <span class="mono texto-oro"><?= e($etiqueta) ?></span>
                    <h2 style="font-size:1.9rem;"><?= e($etiqueta) ?></h2>
                </div>
                <div class="grid grid--2">
                    <?php foreach ($porCategoria[$clave] as $termino): ?>
                        <div class="tarjeta tarjeta__cuerpo">
                            <h3 style="font-size:1.05rem;margin-bottom:4px;"><?= e($termino['termino']) ?></h3>
                            <p class="texto-suave mb-0" style="font-size:.9rem;"><?= e($termino['definicion']) ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endforeach; ?>
    </div>
</section>

<section class="seccion seccion--compacta">
    <div class="contenedor">
        <div class="cta">
            <div>
                <span class="mono texto-oro">Sigue aprendiendo</span>
                <h2>El glosario se entiende mejor con las guías.</h2>
                <p>Cada término tiene su técnica, su riesgo y su contexto. Lee la guía completa y practica con supervisión.</p>
            </div>
            <div class="cta__acciones">
                <a class="boton boton--oro boton--grande" href="<?= url('/guias') ?>">Ver guías</a>
                <a class="boton boton--fantasma boton--grande" href="<?= url('/calculadoras') ?>">Calculadoras</a>
            </div>
        </div>
    </div>
</section>
