<?php
declare(strict_types=1);

$catalogo = $catalogo ?? [];
$whatsapp = whatsapp_link();
$categoriasPost = \App\Repositories\PublicacionRepository::categorias();
$categoriasGuia = \App\Domain\Contenido\Guias::categorias();
$categoriasProducto = \App\Repositories\ProductoRepository::categorias();

$porNombre = [];
foreach ($servicios as $servicio) {
    $nombre = (string) $servicio['nombre'];
    if (!isset($porNombre[$nombre])) {
        $porNombre[$nombre] = $servicio;
        $porNombre[$nombre]['precio_min'] = (float) $servicio['precio'];
    } else {
        $porNombre[$nombre]['precio_min'] = min((float) $porNombre[$nombre]['precio_min'], (float) $servicio['precio']);
    }
}
$serviciosHome = array_slice(array_values($porNombre), 0, 6);
$productosHome = [];
if ($catalogo !== []) {
    $porCategoria = [];
    foreach ($catalogo as $item) {
        if ((string) ($item['imagen'] ?? '') === '' || isset($porCategoria[(string) $item['categoria']])) {
            continue;
        }
        $porCategoria[(string) $item['categoria']] = $item;
    }
    $productosHome = array_slice(array_values($porCategoria), 0, 4);
    if (count($productosHome) < 4) {
        foreach ($catalogo as $item) {
            if (count($productosHome) >= 4 || (string) ($item['imagen'] ?? '') === '') {
                continue;
            }
            $ids = array_column($productosHome, 'id');
            if (!in_array($item['id'], $ids, true)) {
                $productosHome[] = $item;
            }
        }
    }
}
$guiasHome = array_slice($guias, 0, 3);
$negociosHome = array_slice(array_values(array_filter($negocios, static fn (array $n): bool => !es_verdadero($n['es_demo'] ?? false))), 0, 3);
?>
<section class="t-portada">
    <div class="contenedor">
        <div class="t-portada__rejilla">
            <div>
                <span class="t-eyebrow">Software para estéticas caninas</span>
                <h1>El CRM de las estéticas caninas, con orden.</h1>
                <p class="t-portada__lead">Agenda, clientes, mascotas, inventario, ventas y reportes en un solo programa. Se usa desde el navegador, sin instalar archivos, y todo el equipo trabaja sobre la misma base.</p>
                <div class="t-portada__acciones">
                    <a class="t-btn t-btn--azul t-btn--grande" href="<?= url('/panel') ?>"><?= icono('flecha') ?> Abrir el CRM</a>
                    <a class="t-btn t-btn--linea t-btn--grande" href="<?= url('/servicios') ?>">Ver cómo funciona</a>
                </div>
                <ul class="t-portada__pruebas">
                    <li><?= icono('check') ?> Software en línea: se usa desde cualquier navegador, sin instalar.</li>
                    <li><?= icono('check') ?> Cuentas conectadas: recepción, estilistas y administración ven lo mismo.</li>
                    <li><?= icono('check') ?> Roles y permisos por persona, desde el panel.</li>
                </ul>
            </div>
            <div class="t-app-marco" aria-label="Vista de la agenda del CRM">
                <div class="t-app">
                    <div class="t-app__barra">
                        <span class="t-app__punto"></span><span class="t-app__punto"></span><span class="t-app__punto"></span>
                        <span class="t-app__titulo">Mikato CRM · Panel</span>
                    </div>
                    <div class="t-app__cuerpo">
                        <aside class="t-app__lado">
                            <div class="t-app__marca"><span>M</span> Mikato</div>
                            <div class="t-app__menu">
                                <span class="activo">Hoy</span>
                                <span>Agenda</span>
                                <span>Clientes</span>
                                <span>Mascotas</span>
                                <span>Ventas</span>
                                <span>Reportes</span>
                            </div>
                        </aside>
                        <div class="t-app__principal">
                            <div class="t-app__cabeza">
                                <strong>Agenda de hoy</strong>
                                <small>4 citas</small>
                            </div>
                            <div class="t-app__fila">
                                <span class="t-app__hora">09:00</span>
                                <span class="t-app__cliente"><strong>Luna · Baño y corte</strong><small>María Fernanda · Golden retriever</small></span>
                                <span class="t-app__estado">Confirmada</span>
                            </div>
                            <div class="t-app__fila">
                                <span class="t-app__hora">10:30</span>
                                <span class="t-app__cliente"><strong>Toby · Uñas y oídos</strong><small>Jorge · French poodle</small></span>
                                <span class="t-app__estado">Confirmada</span>
                            </div>
                            <div class="t-app__fila">
                                <span class="t-app__hora">12:00</span>
                                <span class="t-app__cliente"><strong>Nina · Baño</strong><small>Paola · Schnauzer</small></span>
                                <span class="t-app__estado t-app__estado--espera">En espera</span>
                            </div>
                            <div class="t-app__fila">
                                <span class="t-app__hora">13:30</span>
                                <span class="t-app__cliente"><strong>Rocky · Corte</strong><small>Andrés · Shih tzu</small></span>
                                <span class="t-app__estado">Confirmada</span>
                            </div>
                            <div class="t-app__pie">
                                <span>Estimado del día</span>
                                <strong>$1,850</strong>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="t-app__flotante">
                    <?= icono('check') ?>
                    <div><strong>Datos compartidos</strong><small>Todo el equipo conectado</small></div>
                </div>
            </div>
        </div>
        <div class="t-confianza">
            <div><?= icono('pin') ?><div><strong>Hecho en Guadalajara</strong><span>Productos y software de Mikato.</span></div></div>
            <div><?= icono('escudo') ?><div><strong>Datos conectados</strong><span>Todo el equipo en una sola base.</span></div></div>
            <div><?= icono('descargar') ?><div><strong>Software en línea</strong><span>Se usa desde el navegador, sin instalar.</span></div></div>
            <div><?= icono('whatsapp') ?><div><strong>Soporte por WhatsApp</strong><span>Te acompañamos a empezar.</span></div></div>
        </div>
    </div>
</section>

<section class="t-seccion">
    <div class="contenedor">
        <div class="t-seccion__cabeza">
            <div>
                <span class="t-kicker">Módulos</span>
                <h2>Todo el salón, en un solo programa.</h2>
                <p>Lo que hoy está en cuadernos, hojas de cálculo y mensajes sueltos, ordenado en una pantalla.</p>
            </div>
            <a href="<?= url('/servicios') ?>">Ver el detalle del CRM</a>
        </div>
        <div class="t-modulos">
            <article class="t-modulo">
                <div class="t-modulo__icono"><?= icono('agenda') ?></div>
                <h3>Agenda del salón</h3>
                <p>Citas por día, estados, reprogramación y avisos por WhatsApp con enlace privado.</p>
                <div class="t-modulo__pie"><span>Vista diaria</span><span>1 min</span></div>
            </article>
            <article class="t-modulo">
                <div class="t-modulo__icono"><?= icono('pata') ?></div>
                <h3>Clientes y mascotas</h3>
                <p>Expediente por mascota, historial de estética, alertas de manejo y datos de contacto.</p>
                <div class="t-modulo__pie"><span>Ficha completa</span><span>3 min</span></div>
            </article>
            <article class="t-modulo">
                <div class="t-modulo__icono"><?= icono('caja') ?></div>
                <h3>Inventario y catálogo</h3>
                <p>Servicios, productos y paquetes con precio, duración, tamaño y existencias.</p>
                <div class="t-modulo__pie"><span>Stock y precios</span><span>2 min</span></div>
            </article>
            <article class="t-modulo">
                <div class="t-modulo__icono"><?= icono('dinero') ?></div>
                <h3>Ventas y gastos</h3>
                <p>Ticket imprimible, descuentos, métodos de pago y gastos del día.</p>
                <div class="t-modulo__pie"><span>Corte de caja</span><span>1 min</span></div>
            </article>
            <article class="t-modulo">
                <div class="t-modulo__icono"><?= icono('grafica') ?></div>
                <h3>Reportes</h3>
                <p>Ventas, utilidad, punto de equilibrio y stock bajo para decidir con números.</p>
                <div class="t-modulo__pie"><span>Por mes</span><span>1 min</span></div>
            </article>
            <article class="t-modulo">
                <div class="t-modulo__icono"><?= icono('descargar') ?></div>
                <h3>Software en línea</h3>
                <p>100% en línea: se abre desde el navegador y funciona en cualquier dispositivo.</p>
                <div class="t-modulo__pie"><span>Sin instaladores</span><span>En línea</span></div>
            </article>
        </div>
    </div>
</section>

<section class="t-seccion t-seccion--fondo">
    <div class="contenedor">
        <div class="t-descarga">
            <div>
                <span class="t-kicker" style="color:var(--t-oro-claro);">CRM en línea</span>
                <h2>Tu CRM, listo en tu navegador.</h2>
                <p>Entra con tu cuenta desde cualquier dispositivo. Tu equipo comparte clientes, mascotas, citas y ventas en una sola base.</p>
                <ul class="t-descarga__puntos">
                    <li>Cuentas para recepción, estilistas y administración.</li>
                    <li>Los cambios se ven al instante en todos los dispositivos.</li>
                    <li>Sin instalar nada: se entra desde el navegador.</li>
                </ul>
                <p class="t-descarga__nota">¿Primera vez? <a href="<?= url('/contacto') ?>">Pide tu cuenta de acceso</a> y te acompañamos.</p>
            </div>
            <div class="t-sistemas">
                <a class="t-sistema" href="<?= url('/panel') ?>">
                    <span class="t-sistema__icono"><?= icono('flecha') ?></span>
                    <span class="t-sistema__texto"><strong>CRM en línea</strong><small>100% en línea · cualquier navegador</small></span>
                    <span class="t-sistema__flecha">Abrir</span>
                </a>
                <p style="font-size:12px;color:#9fb3cc;margin:4px 0 0;">El CRM es <a href="<?= url('/panel') ?>" style="color:var(--t-oro-claro);">software en línea</a>: se abre desde el navegador y no se descarga ningún archivo.</p>
            </div>
        </div>
    </div>
</section>

<section class="t-seccion">
    <div class="contenedor">
        <div class="t-seccion__cabeza">
            <div>
                <span class="t-kicker">Productos Mikato</span>
                <h2>El producto que usa el salón.</h2>
                <p>Shampoos, acondicionadores, aromas y herramientas que probamos con estilistas. El catálogo vive en mikato.com.mx.</p>
            </div>
            <a href="<?= url('/productos') ?>">Ver catálogo completo</a>
        </div>
        <div class="t-productos">
            <?php if ($productosHome !== []): ?>
                <?php foreach ($productosHome as $producto): ?>
                    <article class="t-producto">
                        <a class="t-producto__media" href="<?= e((string) $producto['url']) ?>">
                            <?php if ((string) ($producto['imagen'] ?? '') !== ''): ?>
                                <img src="<?= e((string) $producto['imagen']) ?>" alt="<?= e((string) $producto['nombre']) ?>" width="480" height="480" loading="lazy">
                            <?php else: ?>
                                <img src="<?= asset('/assets/img/producto-botella.svg') ?>" alt="<?= e((string) $producto['nombre']) ?>" width="480" height="480" loading="lazy">
                            <?php endif; ?>
                        </a>
                        <div class="t-producto__cuerpo">
                            <p class="t-producto__marca"><?= e((string) $producto['categoria']) ?></p>
                            <h3 class="t-producto__nombre"><a href="<?= e((string) $producto['url']) ?>"><?= e((string) $producto['nombre']) ?></a></h3>
                            <div class="t-producto__precio"><?= precio((float) $producto['precio']) ?><span>Precio en pesos mexicanos</span></div>
                        </div>
                        <div class="t-producto__accion">
                            <a class="t-btn t-btn--linea t-btn--chico" href="<?= e((string) $producto['url']) ?>">Comprar en la tienda</a>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php else: ?>
                <?php foreach (array_slice($productos, 0, 4) as $producto): ?>
                    <article class="t-producto">
                        <a class="t-producto__media" href="<?= e((string) ($producto['enlace'] ?? url('/productos'))) ?>">
                            <img src="<?= asset('/assets/img/producto-botella.svg') ?>" alt="<?= e((string) $producto['nombre']) ?>" width="480" height="480" loading="lazy">
                        </a>
                        <div class="t-producto__cuerpo">
                            <p class="t-producto__marca"><?= e($categoriasProducto[(string) $producto['categoria']] ?? (string) $producto['categoria']) ?></p>
                            <h3 class="t-producto__nombre"><a href="<?= url('/productos') ?>"><?= e((string) $producto['nombre']) ?></a></h3>
                            <div class="t-producto__precio"><?= precio((float) $producto['precio']) ?><span>Precio en pesos mexicanos</span></div>
                        </div>
                        <div class="t-producto__accion">
                            <a class="t-btn t-btn--linea t-btn--chico" href="<?= url('/productos') ?>">Ver en el catálogo</a>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="t-seccion t-seccion--fondo">
    <div class="contenedor">
        <div class="t-seccion__cabeza">
            <div>
                <span class="t-kicker">Guías de oficio</span>
                <h2>El oficio también se estudia.</h2>
                <p>Doce guías escritas para el día a día del salón: baño, uñas, oídos, secado, manejo y precios.</p>
            </div>
            <a href="<?= url('/guias') ?>">Ver las 12 guías</a>
        </div>
        <div class="t-guias">
            <?php foreach ($guiasHome as $guia): ?>
                <article class="t-guia">
                    <div class="t-guia__franja">
                        <?= icono((string) $guia['icono']) ?>
                        <span><?= e($categoriasGuia[(string) $guia['categoria']] ?? (string) $guia['categoria']) ?></span>
                    </div>
                    <div class="t-guia__cuerpo">
                        <h3><a href="<?= url('/guias/' . $guia['slug']) ?>"><?= e((string) $guia['titulo']) ?></a></h3>
                        <p><?= e(limitar_texto((string) $guia['resumen'], 100)) ?></p>
                        <div class="t-guia__meta"><span><?= e((string) $guia['nivel']) ?></span><span><?= (int) $guia['minutos'] ?> min de lectura</span></div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="t-seccion">
    <div class="contenedor">
        <div class="t-seccion__cabeza">
            <div>
                <span class="t-kicker">Directorio</span>
                <h2>Estéticas que ya trabajan con orden.</h2>
                <p>Fichas revisadas por Mikato en todo el país. Busca por estado o registra tu estética.</p>
            </div>
            <a href="<?= url('/directorio') ?>">Abrir el directorio</a>
        </div>
        <div class="t-directorio">
            <?php if ($negociosHome === []): ?>
                <article class="t-ficha-negocio" style="grid-column:1 / -1;flex-direction:row;align-items:center;justify-content:space-between;gap:18px;flex-wrap:wrap;">
                    <div>
                        <h3>Tu estética puede ser la primera ficha verificada</h3>
                        <p>Revisamos cada ficha antes de publicarla. Sin datos de ejemplo ni reseñas inventadas.</p>
                    </div>
                    <a class="t-btn t-btn--azul t-btn--chico" href="<?= url('/directorio/registrar') ?>">Registrar mi estética</a>
                </article>
            <?php endif; ?>
            <?php foreach ($negociosHome as $negocio): ?>
                <article class="t-ficha-negocio">
                    <h3><a href="<?= url('/directorio/' . $negocio['slug']) ?>" style="color:inherit;text-decoration:none;"><?= e((string) $negocio['nombre']) ?></a></h3>
                    <p><?= e((string) $negocio['ciudad']) ?>, <?= e((string) $negocio['estado_geo']) ?></p>
                    <p><?= e(limitar_texto((string) $negocio['descripcion'], 110)) ?></p>
                    <div class="t-ficha-negocio__meta">
                        <span><?= es_verdadero($negocio['verificado']) ? 'Ficha verificada' : 'Ficha revisada' ?></span>
                        <a href="<?= url('/directorio/' . $negocio['slug']) ?>">Ver ficha</a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="t-seccion t-seccion--ajustada">
    <div class="contenedor">
        <div class="t-banda-final">
            <div>
                <h2>¿Lo vemos con tu salón?</h2>
                <p>Te mostramos el CRM con tus servicios y tus precios. Si decides usarlo, te creamos la cuenta y te acompañamos con la carga inicial.</p>
            </div>
            <div class="t-banda-final__acciones">
                <a class="t-btn t-btn--azul" href="<?= url('/contacto') ?>">Solicitar una demo</a>
                <?php if ($whatsapp !== ''): ?>
                    <a class="t-btn t-btn--linea" href="<?= e($whatsapp) ?>"><?= icono('whatsapp') ?> WhatsApp</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
