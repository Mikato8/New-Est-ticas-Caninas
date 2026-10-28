<?php
declare(strict_types=1);
?>
<div class="contenedor">
    <nav class="migas" aria-label="Ruta"><a href="<?= url('/') ?>">Inicio</a><span>/</span>Servicios</nav>
    <header class="portada-interior">
        <span class="mono texto-oro">CRM + fábrica</span>
        <h1>No somos una estética. Somos la herramienta de las estéticas.</h1>
        <p>Mikato desarrolla un <strong>CRM para estéticas caninas</strong> y fabrica <strong>productos profesionales</strong> en Guadalajara, Jalisco. No atendemos perros directamente: damos a los estilistas la agenda, el expediente, el inventario y los números para que su negocio funcione mejor.</p>
        <div class="flex-centro mt-3">
            <a class="boton boton--oro" href="<?= url('/contacto') ?>"><?= icono('agenda') ?> Solicitar una demo</a>
            <a class="boton boton--contorno" href="<?= url('/productos') ?>">Ver productos Mikato</a>
        </div>
    </header>
</div>

<section class="seccion seccion--compacta">
    <div class="contenedor">
        <div class="grid grid--2">
            <div class="nota nota--teal"><strong>Para estéticas:</strong> agenda, clientes, mascotas, historial, inventario, ventas, gastos y reportes en un solo panel. Tus clientes pueden reservar en línea.</div>
            <div class="nota"><strong>Para distribuidores:</strong> catálogo de productos Mikato con precios de mayoreo, fichas técnicas y contacto directo. Compra en <a href="https://mikato.com.mx" target="_blank" rel="noopener">mikato.com.mx</a>.</div>
        </div>
    </div>
</section>

<section class="seccion seccion--compacta">
    <div class="contenedor">
        <div class="encabezado-seccion">
            <span class="mono texto-oro">Qué incluye el CRM</span>
            <h2>Las funciones que una estética usa todos los días.</h2>
            <p>No es un catálogo con un blog: es el sistema operativo del salón.</p>
        </div>
        <div class="grid grid--3">
            <article class="tarjeta tarjeta__cuerpo"><div class="tarjeta-servicio__icono"><?= icono('agenda') ?></div><h3>Agenda y citas</h3><p class="texto-suave">Horarios por día, duración por servicio, estados de cita y reserva pública sin llamadas.</p></article>
            <article class="tarjeta tarjeta__cuerpo"><div class="tarjeta-servicio__icono"><?= icono('clientes') ?></div><h3>Clientes y mascotas</h3><p class="texto-suave">Ficha de cada perro, raza, tamaño, alergias, temperamento y Kardex de servicios.</p></article>
            <article class="tarjeta tarjeta__cuerpo"><div class="tarjeta-servicio__icono"><?= icono('caja') ?></div><h3>Catálogo e inventario</h3><p class="texto-suave">Servicios, paquetes y productos con precio, stock y alertas de existencias.</p></article>
            <article class="tarjeta tarjeta__cuerpo"><div class="tarjeta-servicio__icono"><?= icono('dinero') ?></div><h3>Ventas y caja</h3><p class="texto-suave">Tickets con servicios y productos, métodos de pago, descuentos y anulación con reposición de stock.</p></article>
            <article class="tarjeta tarjeta__cuerpo"><div class="tarjeta-servicio__icono"><?= icono('grafica') ?></div><h3>Gastos y reportes</h3><p class="texto-suave">Ingresos, gastos, utilidad y rendimiento por periodo para tomar decisiones con datos.</p></article>
            <article class="tarjeta tarjeta__cuerpo"><div class="tarjeta-servicio__icono"><?= icono('escudo') ?></div><h3>Usuarios y permisos</h3><p class="texto-suave">Administrador, editor, estilista y clientes con accesos separados y sesiones protegidas.</p></article>
        </div>
    </div>
</section>

<section class="seccion seccion--crema">
    <div class="contenedor">
        <div class="split">
            <div>
                <span class="mono texto-oro">Cómo funciona la página</span>
                <h2>De la primera visita al reporte del mes.</h2>
                <div class="pasos mt-3">
                    <div class="paso"><div><h3>1. Crea tu cuenta</h3><p>Registra tu estética y entra al panel. Configura tus servicios, precios, horarios y datos del negocio.</p></div></div>
                    <div class="paso"><div><h3>2. Agenda y registra</h3><p>Da de alta clientes y mascotas, agenda citas y documenta cada servicio en el Kardex.</p></div></div>
                    <div class="paso"><div><h3>3. Cobra y controla</h3><p>Registra ventas, productos, gastos y stock. El sistema calcula totales y actualiza inventario.</p></div></div>
                    <div class="paso"><div><h3>4. Revisa y comparte</h3><p>Consulta reportes, publica casos en la comunidad, sube trabajos a las guías y usa las calculadoras.</p></div></div>
                </div>
            </div>
            <div class="tarjeta tarjeta__cuerpo">
                <span class="mono texto-oro">Además</span>
                <h3 style="font-size:1.5rem;">Comunidad, contenido y productos.</h3>
                <ul class="sin-lista" style="display:grid;gap:10px;">
                    <li class="flex-centro"><?= icono('libro', 'texto-teal') ?> 12 guías de oficio y un glosario para formar a tu equipo.</li>
                    <li class="flex-centro"><?= icono('chispa', 'texto-teal') ?> Calculadoras de dilución, precio, secado y rentabilidad.</li>
                    <li class="flex-centro"><?= icono('directorio', 'texto-teal') ?> Directorio de estéticas y comunidad moderada.</li>
                    <li class="flex-centro"><?= icono('caja', 'texto-teal') ?> Productos Mikato fabricados en Guadalajara, Jalisco.</li>
                </ul>
                <div class="nota mt-3" style="font-size:.84rem;"><strong>Importante:</strong> Mikato no presta servicios de estética canina. La agenda pública es una demostración del módulo de citas para que veas cómo reservarían tus clientes.</div>
                <a class="boton boton--bloque mt-3" href="<?= url('/contacto') ?>">Quiero una demo del CRM</a>
            </div>
        </div>
    </div>
</section>

<section class="seccion">
    <div class="contenedor">
        <div class="cta">
            <div>
                <span class="mono texto-oro">Estéticas y distribuidores</span>
                <h2>¿Tienes una estética o vendes productos?</h2>
                <p>Te mostramos el CRM con tus propios servicios y horarios, o te armamos una propuesta de mayoreo.</p>
            </div>
            <div class="cta__acciones">
                <a class="boton boton--oro boton--grande" href="<?= url('/contacto') ?>">Hablar con Mikato</a>
                <a class="boton boton--fantasma boton--grande" href="<?= url('/nosotros') ?>">Conocer la empresa</a>
            </div>
        </div>
    </div>
</section>
