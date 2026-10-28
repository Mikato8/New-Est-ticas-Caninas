<?php
declare(strict_types=1);
$servicios = $negocio['servicios'] ? array_filter(array_map('trim', explode(',', (string) $negocio['servicios']))) : [];
?>
<div class="contenedor">
    <nav class="migas" aria-label="Ruta"><a href="<?= url('/') ?>">Inicio</a><span>/</span><a href="<?= url('/directorio') ?>">Directorio</a><span>/</span><?= e($negocio['nombre']) ?></nav>
    <header class="portada-interior">
        <div class="flex-centro" style="justify-content:space-between;align-items:flex-start;">
            <div class="flex-centro" style="align-items:flex-start;gap:18px;">
                <?php if ($negocio['logo']): ?>
                    <img src="<?= e($negocio['logo']) ?>" alt="" style="width:76px;height:76px;border-radius:18px;object-fit:cover;">
                <?php else: ?>
                    <span class="avatar" style="width:76px;height:76px;border-radius:18px;font-size:1.5rem;background:var(--oro);"><?= e(iniciales((string) $negocio['nombre'])) ?></span>
                <?php endif; ?>
                <div>
                    <div class="flex-centro">
                        <h1 style="font-size:clamp(2rem,4vw,3.2rem);margin:0;"><?= e($negocio['nombre']) ?></h1>
                        <?php if (es_verdadero($negocio['verificado'])): ?><span class="etiqueta etiqueta--teal"><?= icono('escudo') ?> Verificada</span><?php endif; ?>
                        <?php if (es_verdadero($negocio['es_demo'])): ?><span class="etiqueta etiqueta--coral">Ficha de ejemplo</span><?php endif; ?>
                    </div>
                    <p class="texto-suave mt-1"><?= e($negocio['ciudad']) ?>, <?= e($negocio['estado_geo']) ?> · Estética canina</p>
                </div>
            </div>
            <span class="etiqueta"><?= icono('pin') ?> <?= e($negocio['ciudad']) ?></span>
        </div>
    </header>
</div>

<section class="seccion seccion--compacta" style="padding-top:20px;">
    <div class="contenedor">
        <div class="split" style="align-items:start;">
            <div>
                <?php if (es_verdadero($negocio['es_demo'])): ?>
                    <div class="nota nota--coral mb-3"><strong>Ficha de ejemplo.</strong> Esta ficha es demostrativa y no corresponde a un negocio real. El directorio se llena con estéticas que se registran y se revisan.</div>
                <?php endif; ?>
                <div class="prosa">
                    <h2 style="margin-top:0;">Sobre la estética</h2>
                    <p><?= nl2br(e((string) $negocio['descripcion'])) ?></p>
                    <?php if ($servicios !== []): ?>
                        <h2>Servicios</h2>
                        <div class="filtros">
                            <?php foreach ($servicios as $servicio): ?><span class="etiqueta etiqueta--teal"><?= e($servicio) ?></span><?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    <?php if ($negocio['horario']): ?>
                        <h2>Horario</h2>
                        <p><?= nl2br(e((string) $negocio['horario'])) ?></p>
                    <?php endif; ?>
                    <?php if ($negocio['direccion']): ?>
                        <h2>Dirección</h2>
                        <p><?= e($negocio['direccion']) ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <aside class="aside-sticky">
                <div class="aside-caja">
                    <h3>Contacto</h3>
                    <div class="datos-lista">
                        <?php if ($negocio['telefono']): ?><div><dt>Teléfono</dt><dd><a href="tel:<?= e(preg_replace('/\s+/', '', (string) $negocio['telefono'])) ?>"><?= e($negocio['telefono']) ?></a></dd></div><?php endif; ?>
                        <?php if ($negocio['correo']): ?><div><dt>Correo</dt><dd><a href="mailto:<?= e($negocio['correo']) ?>"><?= e($negocio['correo']) ?></a></dd></div><?php endif; ?>
                        <?php if ($negocio['web']): ?><div><dt>Sitio</dt><dd><a href="<?= e($negocio['web']) ?>" target="_blank" rel="noopener">Visitar</a></dd></div><?php endif; ?>
                        <div><dt>Ciudad</dt><dd><?= e($negocio['ciudad']) ?>, <?= e($negocio['estado_geo']) ?></dd></div>
                    </div>
                    <?php if ($whatsapp !== ''): ?>
                        <a class="boton boton--bloque mt-3" href="<?= e($whatsapp) ?>" target="_blank" rel="noopener"><?= icono('whatsapp') ?> WhatsApp</a>
                    <?php endif; ?>
                    <?php if ($negocio['telefono']): ?>
                        <a class="boton boton--contorno boton--bloque mt-1" href="tel:<?= e(preg_replace('/\s+/', '', (string) $negocio['telefono'])) ?>"><?= icono('telefono') ?> Llamar</a>
                    <?php endif; ?>
                </div>
                <div class="aside-caja" style="background:var(--teal-claro);border-color:#b9d4c9;">
                    <h3>¿Tienes una estética?</h3>
                    <p style="font-size:.87rem;color:var(--teal);">Publica tu ficha gratis. Revisamos los datos antes de publicarla.</p>
                    <a class="boton boton--pequeno boton--bloque" href="<?= url('/directorio/registrar') ?>">Registrar mi estética</a>
                </div>
                <?php if ($relacionados !== []): ?>
                <div class="aside-caja">
                    <h3>Otras estéticas en <?= e($negocio['estado_geo']) ?></h3>
                    <ul>
                        <?php foreach ($relacionados as $relacionado): ?>
                            <li><a href="<?= url('/directorio/' . $relacionado['slug']) ?>"><?= e($relacionado['nombre']) ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>
            </aside>
        </div>
    </div>
</section>
