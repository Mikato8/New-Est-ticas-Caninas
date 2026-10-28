<?php
declare(strict_types=1);

$iconosServicio = ['bano' => 'bano', 'corte' => 'tijeras', 'unas' => 'una', 'oidos' => 'oido', 'spa' => 'chispa', 'paquete' => 'caja'];
$arteProducto = static function (string $categoria): string {
    return match ($categoria) {
        'perfume' => '/assets/img/producto-espray.svg',
        'cepillo' => '/assets/img/producto-cepillo.svg',
        'herramienta' => '/assets/img/producto-herramienta.svg',
        default => '/assets/img/producto-botella.svg',
    };
};
$categoriasPost = \App\Repositories\PublicacionRepository::categorias();
$whatsapp = whatsapp_link();

$porNombre = [];
foreach ($servicios as $servicio) {
    $nombre = (string) $servicio['nombre'];
    if (!isset($porNombre[$nombre])) {
        $porNombre[$nombre] = $servicio;
        $porNombre[$nombre]['precio_min'] = (float) $servicio['precio'];
        $porNombre[$nombre]['tamanos'] = [(string) $servicio['tamano']];
    } else {
        $porNombre[$nombre]['precio_min'] = min((float) $porNombre[$nombre]['precio_min'], (float) $servicio['precio']);
        $porNombre[$nombre]['tamanos'][] = (string) $servicio['tamano'];
    }
}
$serviciosHome = array_slice(array_values($porNombre), 0, 6);
?>
<section class="hero">
    <div class="contenedor">
        <div class="hero__rejilla">
            <div>
                <span class="hero__eyebrow"><span></span>Mikato · Estilistas caninos de México</span>
                <h1>El oficio de consentir perros, con <em>técnica y cariño</em>.</h1>
                <p class="hero__lead">Baño, corte y cuidado para perros de todas las tallas. Un espacio para estilistas caninos con guías de oficio, calculadoras, comunidad y agenda en línea. Productos fabricados en México.</p>
                <div class="hero__acciones">
                    <a class="boton boton--oro boton--grande" href="<?= url('/agenda') ?>"><?= icono('agenda') ?> Agendar una cita</a>
                    <a class="boton boton--contorno boton--grande" href="<?= url('/guias') ?>"><?= icono('libro') ?> Aprender el oficio</a>
                </div>
                <ul class="hero__pruebas">
                    <li><?= icono('check') ?> Atención personalizada</li>
                    <li><?= icono('check') ?> Productos hechos en México</li>
                    <li><?= icono('check') ?> Confirmación por WhatsApp</li>
                </ul>
            </div>
            <div class="hero__arte">
                <img src="<?= asset('/assets/img/hero-perro.svg') ?>" alt="Perro recién bañado y peinado en Mikato" width="470" height="520">
                <div class="flotante flotante--1">
                    <?= icono('agenda') ?>
                    <div><strong>Agenda abierta</strong><small>Lun a sáb · 9:00 a 19:00</small></div>
                </div>
                <div class="flotante flotante--2">
                    <?= icono('libro') ?>
                    <div><strong><?= (int) ($stats['guias'] ?? 12) ?> guías de oficio</strong><small>Baño, corte, uñas y negocio</small></div>
                </div>
                <div class="flotante flotante--3">
                    <?= icono('directorio') ?>
                    <div><strong>Directorio nacional</strong><small>Estéticas en México</small></div>
                </div>
            </div>
        </div>
        <div class="hero__cinta">
            <span><?= icono('pata') ?> Baño · corte · uñas · oídos · spa</span>
            <span><?= icono('escudo') ?> Manejo de bajo estrés</span>
            <span><?= icono('corazon') ?> Perros de todas las edades</span>
            <span><?= icono('pin') ?> Hecho en México</span>
        </div>
    </div>
</section>

<section class="seccion--compacta">
    <div class="contenedor">
        <div class="grid grid--4">
            <div class="tarjeta-dato">
                <span class="mono">Guías publicadas</span>
                <strong class="dato"><?= (int) ($stats['guias'] ?? 0) ?></strong>
                <span class="texto-tenue" style="font-size:.8rem;">De baño, corte, salud y negocio</span>
            </div>
            <div class="tarjeta-dato">
                <span class="mono">Casos en comunidad</span>
                <strong class="dato"><?= (int) ($stats['publicaciones'] ?? 0) ?></strong>
                <span class="texto-tenue" style="font-size:.8rem;">Compartidos por estilistas</span>
            </div>
            <div class="tarjeta-dato">
                <span class="mono">Estéticas en el directorio</span>
                <strong class="dato"><?= (int) ($stats['negocios'] ?? 0) ?></strong>
                <span class="texto-tenue" style="font-size:.8rem;">Fichas revisadas por Mikato</span>
            </div>
            <div class="tarjeta-dato">
                <span class="mono">Citas agendadas</span>
                <strong class="dato"><?= (int) ($stats['citas'] ?? 0) ?></strong>
                <span class="texto-tenue" style="font-size:.8rem;">Reservas en línea y mostrador</span>
            </div>
        </div>
    </div>
</section>

<section class="seccion" id="servicios">
    <div class="contenedor">
        <div class="encabezado-seccion encabezado-seccion--fila">
            <div>
                <span class="mono">Servicios de estética</span>
                <h2>Lo que tu perro necesita, sin prisas.</h2>
                <p>Baño, corte, uñas, oídos y spa. Cada servicio se adapta al tamaño, al manto y al temperamento del perro. Precios claros y tiempos reales.</p>
            </div>
            <a class="enlace-flecha" href="<?= url('/servicios') ?>">Ver todos los precios <?= icono('flecha') ?></a>
        </div>
        <div class="grid grid--3">
            <?php foreach ($serviciosHome as $servicio): ?>
                <article class="tarjeta tarjeta--enlace tarjeta-servicio">
                    <div class="tarjeta__cuerpo">
                        <div class="tarjeta-servicio__icono"><?= icono($iconosServicio[(string) $servicio['categoria']] ?? 'pata') ?></div>
                        <h3><?= e($servicio['nombre']) ?></h3>
                        <p><?= e(limitar_texto((string) $servicio['descripcion'], 110)) ?></p>
                        <div class="tarjeta-servicio__precio">
                            <strong>Desde <?= precio($servicio['precio_min'] ?? $servicio['precio']) ?></strong>
                            <small><?= count($servicio['tamanos'] ?? [1]) > 1 ? 'Varios tamaños' : e(tamano_label((string) $servicio['tamano'])) ?></small>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
        <p class="texto-tenue mt-3" style="font-size:.84rem;">Los precios son por tamaño y pueden variar si el manto tiene nudos o requiere deslanado. Te confirmamos antes de empezar.</p>
    </div>
</section>

<section class="seccion seccion--crema">
    <div class="contenedor">
        <div class="split">
            <div>
                <span class="mono texto-oro">Para estilistas caninos</span>
                <h2>Tu oficio también se estudia.</h2>
                <p class="texto-suave">Doce guías escritas para el día a día del salón: cómo bañar sin resecar, hasta dónde cortar una uña, cómo secar sin riesgo, cómo cotizar sin regalar el trabajo y qué verificar para abrir tu estética en México.</p>
                <ul class="sin-lista" style="display:grid;gap:12px;margin:24px 0;">
                    <li class="flex-centro"><?= icono('check', 'texto-teal') ?> <span>Guías con tablas, checklists y fuentes.</span></li>
                    <li class="flex-centro"><?= icono('check', 'texto-teal') ?> <span>Calculadoras de dilución, precio, secado y rentabilidad.</span></li>
                    <li class="flex-centro"><?= icono('check', 'texto-teal') ?> <span>Comunidad moderada para casos y dudas reales.</span></li>
                </ul>
                <div class="flex-centro">
                    <a class="boton" href="<?= url('/guias') ?>">Explorar las guías</a>
                    <a class="boton boton--contorno" href="<?= url('/calculadoras') ?>">Abrir calculadoras</a>
                </div>
            </div>
            <div class="grid grid--2">
                <?php foreach (array_slice($guias, 0, 4) as $guia): ?>
                    <article class="tarjeta tarjeta--enlace tarjeta-guia">
                        <div class="tarjeta-guia__portada tarjeta-guia__portada--<?= e($guia['categoria']) ?>">
                            <?= icono($guia['icono']) ?>
                            <span class="etiqueta etiqueta--oscura"><?= e(\App\Domain\Contenido\Guias::categorias()[$guia['categoria']] ?? $guia['categoria']) ?></span>
                        </div>
                        <div class="tarjeta-guia__cuerpo">
                            <h3><a href="<?= url('/guias/' . $guia['slug']) ?>"><?= e($guia['titulo']) ?></a></h3>
                            <p><?= e(limitar_texto($guia['resumen'], 90)) ?></p>
                            <div class="tarjeta-guia__meta">
                                <span><?= e($guia['nivel']) ?></span>
                                <span><?= (int) $guia['minutos'] ?> min</span>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<section class="seccion">
    <div class="contenedor">
        <div class="encabezado-seccion encabezado-seccion--fila">
            <div>
                <span class="mono">Productos Mikato</span>
                <h2>Fabricados en México para el salón.</h2>
                <p>Shampoos, acondicionadores, aromas y herramientas. Lo que usamos en la estética y lo que recomendamos a otros estilistas.</p>
            </div>
            <a class="enlace-flecha" href="<?= url('/productos') ?>">Ver catálogo <?= icono('flecha') ?></a>
        </div>
        <div class="grid grid--4">
            <?php foreach (array_slice($productos, 0, 4) as $producto): ?>
                <article class="tarjeta tarjeta--enlace tarjeta-producto">
                    <div class="tarjeta-producto__imagen">
                        <img src="<?= asset($arteProducto((string) $producto['categoria'])) ?>" alt="<?= e($producto['nombre']) ?>" width="170" height="170" loading="lazy">
                    </div>
                    <div class="tarjeta-producto__cuerpo">
                        <span class="etiqueta"><?= e(\App\Repositories\ProductoRepository::categorias()[(string) $producto['categoria']] ?? $producto['categoria']) ?></span>
                        <h3 class="mt-1"><?= e($producto['nombre']) ?></h3>
                        <p><?= e(limitar_texto((string) $producto['descripcion'], 80)) ?></p>
                        <div class="tarjeta-producto__precio">
                            <strong><?= precio($producto['precio']) ?></strong>
                            <small><?= e((string) ($producto['presentacion'] ?? '')) ?></small>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="seccion seccion--crema">
    <div class="contenedor">
        <div class="encabezado-seccion">
            <span class="mono">Comunidad</span>
            <h2>Lo que se habla en el salón.</h2>
            <p>Casos, técnicas y dudas de estilistas caninos de todo México. Un foro moderado, sin ruido y con respuestas útiles.</p>
        </div>
        <div class="grid grid--3">
            <?php foreach ($publicaciones as $publicacion): ?>
                <article class="tarjeta tarjeta--enlace tarjeta-post">
                    <div class="tarjeta-post__meta">
                        <span class="etiqueta etiqueta--teal"><?= e($categoriasPost[(string) $publicacion['categoria']] ?? $publicacion['categoria']) ?></span>
                        <span><?= e(fecha($publicacion['creado_en'], 'd M')) ?></span>
                    </div>
                    <h3><a href="<?= url('/comunidad/' . $publicacion['slug']) ?>"><?= e($publicacion['titulo']) ?></a></h3>
                    <p><?= e(limitar_texto((string) ($publicacion['resumen'] ?: $publicacion['contenido']), 120)) ?></p>
                    <div class="tarjeta-post__pie">
                        <span class="flex-centro"><span class="avatar" style="width:26px;height:26px;font-size:.65rem;"><?= e(iniciales((string) $publicacion['autor_nombre'])) ?></span> <?= e($publicacion['autor_nombre']) ?></span>
                        <span><?= (int) $publicacion['comentarios'] ?> respuestas</span>
                    </div>
                </article>
            <?php endforeach; ?>
            <?php if ($publicaciones === []): ?>
                <div class="tarjeta tarjeta__cuerpo" style="grid-column:1/-1;">
                    <h3>Sé la primera persona en compartir</h3>
                    <p class="texto-suave">Publica un caso, una técnica o una duda. La comunidad crece con lo que compartes.</p>
                    <a class="boton" href="<?= url('/comunidad/publicar') ?>">Publicar en la comunidad</a>
                </div>
            <?php endif; ?>
        </div>
        <div class="flex-centro mt-3">
            <a class="boton boton--contorno" href="<?= url('/comunidad') ?>">Entrar a la comunidad</a>
            <a class="enlace-flecha" href="<?= url('/comunidad/publicar') ?>">Compartir mi caso <?= icono('flecha') ?></a>
        </div>
    </div>
</section>

<section class="seccion">
    <div class="contenedor">
        <div class="encabezado-seccion encabezado-seccion--fila">
            <div>
                <span class="mono">Directorio</span>
                <h2>Estéticas que cuidan como tú.</h2>
                <p>Encuentra estilistas y estéticas caninas por estado. Cada ficha pasa por revisión antes de publicarse.</p>
            </div>
            <a class="enlace-flecha" href="<?= url('/directorio') ?>">Buscar por estado <?= icono('flecha') ?></a>
        </div>
        <div class="grid grid--3">
            <?php foreach ($negocios as $negocio): ?>
                <article class="tarjeta tarjeta--enlace tarjeta-negocio">
                    <div class="tarjeta-negocio__cabeza">
                        <span class="avatar avatar--grande avatar--oro"><?= e(iniciales((string) $negocio['nombre'])) ?></span>
                        <div>
                            <h3><a href="<?= url('/directorio/' . $negocio['slug']) ?>" style="text-decoration:none;"><?= e($negocio['nombre']) ?></a></h3>
                            <span class="tarjeta-negocio__ciudad"><?= e($negocio['ciudad']) ?>, <?= e($negocio['estado_geo']) ?></span>
                        </div>
                    </div>
                    <p><?= e(limitar_texto((string) $negocio['descripcion'], 110)) ?></p>
                    <div class="tarjeta-negocio__pie">
                        <?php if (es_verdadero($negocio['es_demo'])): ?>
                            <span class="etiqueta etiqueta--coral">Ejemplo</span>
                        <?php elseif (es_verdadero($negocio['verificado'])): ?>
                            <span class="verificado"><?= icono('escudo') ?> Verificada</span>
                        <?php else: ?>
                            <span class="etiqueta">Ficha revisada</span>
                        <?php endif; ?>
                        <a class="enlace-flecha" href="<?= url('/directorio/' . $negocio['slug']) ?>">Ver ficha <?= icono('flecha') ?></a>
                    </div>
                </article>
            <?php endforeach; ?>
            <?php if ($negocios === []): ?>
                <div class="tarjeta tarjeta__cuerpo" style="grid-column:1/-1;">
                    <div class="flex-centro" style="align-items:flex-start;">
                        <span class="tarjeta-servicio__icono" style="margin:0;"><?= icono('directorio') ?></span>
                        <div>
                            <h3>El directorio está abriendo</h3>
                            <p class="texto-suave mb-0">Estamos revisando las primeras fichas de estéticas caninas. Si tienes una, regístrala y aparece cuando la aprobemos.</p>
                            <a class="boton boton--pequeno mt-2" href="<?= url('/directorio/registrar') ?>">Registrar mi estética</a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="seccion seccion--compacta">
    <div class="contenedor">
        <div class="cta">
            <div>
                <span class="mono texto-oro">Reserva en línea</span>
                <h2>Tu perro merece una cita tranquila.</h2>
                <p>Elige servicio, día y hora. Te confirmamos por WhatsApp y te decimos qué llevar. Sin filas y sin sorpresas.</p>
            </div>
            <div class="cta__acciones">
                <a class="boton boton--oro boton--grande" href="<?= url('/agenda') ?>">Agendar cita</a>
                <?php if ($whatsapp !== ''): ?>
                    <a class="boton boton--fantasma boton--grande" href="<?= e($whatsapp) ?>" target="_blank" rel="noopener"><?= icono('whatsapp') ?> WhatsApp</a>
                <?php else: ?>
                    <a class="boton boton--fantasma boton--grande" href="<?= url('/contacto') ?>">Escribirnos</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
