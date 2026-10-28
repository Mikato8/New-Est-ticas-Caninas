<?php
declare(strict_types=1);
$correo = (string) config('contacto.email');
?>
<div class="contenedor">
    <nav class="migas" aria-label="Ruta"><a href="<?= url('/') ?>">Inicio</a><span>/</span>Aviso de privacidad</nav>
    <header class="portada-interior">
        <span class="mono texto-oro">Legal</span>
        <h1>Aviso de privacidad.</h1>
        <p>Última actualización: 27 de octubre de 2026. Este aviso explica qué datos personales tratamos, para qué y cómo ejercer tus derechos.</p>
    </header>
</div>

<section class="seccion seccion--compacta">
    <div class="contenedor">
        <article class="prosa" style="max-width:820px;">
            <h2>1. Responsable</h2>
            <p><strong>Mikato</strong>, con domicilio en Guadalajara, Jalisco, México y correo de contacto <a href="mailto:<?= e($correo) ?>"><?= e($correo) ?></a>, es responsable del tratamiento de los datos personales que recaba a través de este portal.</p>

            <h2>2. Datos que tratamos</h2>
            <ul>
                <li><strong>Identificación y contacto:</strong> nombre, correo, teléfono, WhatsApp, ciudad y estado.</li>
                <li><strong>Datos de tu mascota:</strong> nombre, especie, raza, tamaño, sexo, fecha de nacimiento, peso, temperamento, alergias, notas e historial de servicios.</li>
                <li><strong>Citas y compras:</strong> servicios solicitados, fechas, horarios, precios, método de pago y folios.</li>
                <li><strong>Contenido que publicas:</strong> publicaciones, comentarios, imágenes y datos de tu estética si registras una ficha en el directorio.</li>
                <li><strong>Datos técnicos:</strong> dirección IP, tipo de navegador y registros de acceso necesarios para seguridad y funcionamiento.</li>
            </ul>
            <p>No solicitamos datos personales sensibles. Si nos compartes información de salud de tu mascota, lo haces de forma voluntaria y solo la usamos para prestar el servicio con seguridad.</p>

            <h2>3. Finalidades</h2>
            <ul>
                <li>Agendar, confirmar y dar seguimiento a citas.</li>
                <li>Prestar servicios de estética y llevar el historial de cada mascota.</li>
                <li>Emitir comprobantes, cobrar y administrar la relación comercial.</li>
                <li>Responder mensajes, solicitudes de mayoreo y reportes.</li>
                <li>Moderar la comunidad y publicar fichas del directorio.</li>
                <li>Enviar avisos operativos y, si lo autorizas, novedades por correo.</li>
                <li>Cumplir obligaciones legales y proteger la seguridad del portal.</li>
            </ul>
            <p>No usamos tus datos para entrenar modelos de inteligencia artificial. El asistente del portal es una búsqueda en PHP sobre contenido público del sitio; no envía tus preguntas a modelos externos.</p>

            <h2>4. Consentimiento y aviso integral</h2>
            <p>Al registrarte, agendar o enviar un formulario, aceptas este aviso. Cuando se trate de datos patrimoniales o de salud de la mascota, recabamos tu consentimiento expreso. Puedes solicitar el aviso de privacidad integral escribiendo al correo de contacto.</p>

            <h2>5. Transferencias y encargados</h2>
            <p>No vendemos ni rentamos datos personales. Para operar el portal usamos proveedores de hospedaje y base de datos que actúan como encargados y están obligados a confidencialidad. Solo compartimos información cuando una autoridad competente lo requiere conforme a la ley.</p>

            <h2>6. Conservación y seguridad</h2>
            <p>Conservamos los datos mientras exista la relación de servicio y durante los plazos legales aplicables. Aplicamos medidas administrativas, técnicas y físicas razonables: contraseñas con hash, control de acceso por roles, conexiones cifradas, respaldos y registro de incidentes. Ningún sistema es 100% invulnerable; si ocurre un incidente relevante, lo comunicaremos conforme a la ley.</p>

            <h2>7. Cookies y sesión</h2>
            <p>Usamos cookies de sesión necesarias para mantener tu cuenta y proteger los formularios (CSRF). No usamos cookies publicitarias de terceros. Puedes bloquear cookies en tu navegador, pero algunas funciones dejarán de funcionar.</p>

            <h2>8. Derechos ARCO y oposición</h2>
            <p>Puedes solicitar el acceso, rectificación, cancelación u oposición al tratamiento de tus datos, así como revocar tu consentimiento, escribiendo a <a href="mailto:<?= e($correo) ?>"><?= e($correo) ?></a>. Incluye tu nombre, medio de contacto y una descripción clara de la solicitud. Responderemos en los plazos que marca la ley.</p>

            <h2>9. Autoridad</h2>
            <p>La Ley Federal de Protección de Datos Personales en Posesión de los Particulares fue publicada el 20 de marzo de 2025. La autoridad de control ya no es el INAI: es la <strong>Secretaría Anticorrupción y Buen Gobierno</strong>. Si consideras que no atendimos tu solicitud, puedes acudir a esa dependencia.</p>

            <h2>10. Menores de edad</h2>
            <p>El portal no está dirigido a menores de edad sin supervisión. Si un tutor registra datos de un menor, debe hacerlo con su consentimiento y bajo su responsabilidad.</p>

            <h2>11. Cambios</h2>
            <p>Podemos actualizar este aviso. Publicaremos la fecha de la última modificación en esta página. Si el cambio es sustancial, lo avisaremos por el correo registrado o en la portada del sitio.</p>

            <div class="nota nota--teal mt-3"><strong>Contacto de privacidad:</strong> <a href="mailto:<?= e($correo) ?>"><?= e($correo) ?></a>. Para temas de servicio, usa el <a href="<?= url('/contacto') ?>">formulario de contacto</a>.</div>
        </article>
    </div>
</section>
