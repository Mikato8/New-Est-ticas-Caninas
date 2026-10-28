<?php
declare(strict_types=1);
?>
<div class="contenedor">
    <nav class="migas" aria-label="Ruta"><a href="<?= url('/') ?>">Inicio</a><span>/</span>Cuidados</nav>
    <header class="portada-interior">
        <span class="mono texto-oro">Guía para tutores</span>
        <h1>Antes y después de la cita.</h1>
        <p>Una cita tranquila empieza en casa. Esto es lo que puedes hacer para que tu perro llegue mejor, disfrute el servicio y salga con un resultado que dure.</p>
    </header>
</div>

<section class="seccion seccion--compacta">
    <div class="contenedor">
        <div class="grid grid--3">
            <article class="tarjeta tarjeta__cuerpo">
                <span class="tarjeta-servicio__icono"><?= icono('pata') ?></span>
                <h3>Antes de venir</h3>
                <ul class="sin-lista" style="display:grid;gap:8px;font-size:.9rem;color:var(--tinta-suave);">
                    <li class="flex-centro"><?= icono('check', 'texto-teal') ?> Pasea a tu perro para que llegue relajado.</li>
                    <li class="flex-centro"><?= icono('check', 'texto-teal') ?> Cepíllalo en casa si tolera; los nudos se aprietan con el agua.</li>
                    <li class="flex-centro"><?= icono('check', 'texto-teal') ?> Trae correa y, si es necesario, bozal de cesta.</li>
                    <li class="flex-centro"><?= icono('check', 'texto-teal') ?> Primera visita: cartilla de vacunación y datos de salud.</li>
                    <li class="flex-centro"><?= icono('check', 'texto-teal') ?> Avisa si tiene pulgas, heridas, alergias o medicamentos.</li>
                </ul>
            </article>
            <article class="tarjeta tarjeta__cuerpo">
                <span class="tarjeta-servicio__icono"><?= icono('corazon') ?></span>
                <h3>Durante el servicio</h3>
                <ul class="sin-lista" style="display:grid;gap:8px;font-size:.9rem;color:var(--tinta-suave);">
                    <li class="flex-centro"><?= icono('check', 'texto-teal') ?> Cuéntanos si tu perro se asusta con la secadora o las uñas.</li>
                    <li class="flex-centro"><?= icono('check', 'texto-teal') ?> No lo dejamos solo si está ansioso; ajustamos el manejo.</li>
                    <li class="flex-centro"><?= icono('check', 'texto-teal') ?> Si hay riesgo, detenemos el servicio y te llamamos.</li>
                    <li class="flex-centro"><?= icono('check', 'texto-teal') ?> No sedamos ni anestesiamos: eso es decisión veterinaria.</li>
                </ul>
            </article>
            <article class="tarjeta tarjeta__cuerpo">
                <span class="tarjeta-servicio__icono"><?= icono('escudo') ?></span>
                <h3>Después de la cita</h3>
                <ul class="sin-lista" style="display:grid;gap:8px;font-size:.9rem;color:var(--tinta-suave);">
                    <li class="flex-centro"><?= icono('check', 'texto-teal') ?> Mantén abrigado a perros pequeños o sin pelo al salir.</li>
                    <li class="flex-centro"><?= icono('check', 'texto-teal') ?> Evita parques y tierra el primer día si hubo corte.</li>
                    <li class="flex-centro"><?= icono('check', 'texto-teal') ?> Revisa orejas, axilas y entre dedos; seca si quedó humedad.</li>
                    <li class="flex-centro"><?= icono('check', 'texto-teal') ?> Cepilla 2 o 3 veces por semana según el manto.</li>
                    <li class="flex-centro"><?= icono('check', 'texto-teal') ?> Agenda la siguiente visita en 3 a 5 semanas.</li>
                </ul>
            </article>
        </div>
    </div>
</section>

<section class="seccion">
    <div class="contenedor">
        <div class="split">
            <div>
                <span class="mono texto-oro">Cuándo reprogramar</span>
                <h2>Hay días en los que es mejor esperar.</h2>
                <p class="texto-suave">Bañar a un perro enfermo o con dolor no es un favor. Si presenta alguno de estos signos, llama a tu veterinario y reprograma:</p>
                <ul class="sin-lista" style="display:grid;gap:9px;margin-top:18px;">
                    <li class="flex-centro"><?= icono('alerta', 'texto-coral') ?> Fiebre, decaimiento, vómito o diarrea.</li>
                    <li class="flex-centro"><?= icono('alerta', 'texto-coral') ?> Tos, dificultad para respirar o jadeo excesivo.</li>
                    <li class="flex-centro"><?= icono('alerta', 'texto-coral') ?> Heridas abiertas, hongos o infección de piel.</li>
                    <li class="flex-centro"><?= icono('alerta', 'texto-coral') ?> Dolor al caminar o al tocarlo.</li>
                    <li class="flex-centro"><?= icono('alerta', 'texto-coral') ?> Pulgas o garrapatas activas: primero tratamiento.</li>
                    <li class="flex-centro"><?= icono('alerta', 'texto-coral') ?> Golpe de calor o convulsiones: urgencia veterinaria.</li>
                </ul>
            </div>
            <div class="tarjeta tarjeta__cuerpo">
                <h3>Perros que necesitan un plan especial</h3>
                <div class="datos-lista">
                    <div><dt>Braquicéfalos</dt><dd>Pug, bulldog, shih tzu</dd></div>
                    <div><dt>Cuidado</dt><dd>Sesiones cortas, fresco, sin calor</dd></div>
                    <div><dt>Geriátricos</dt><dd>Base antideslizante y pausas</dd></div>
                    <div><dt>Cachorros</dt><dd>Sesiones breves y refuerzo positivo</dd></div>
                    <div><dt>Ansiosos</dt><dd>Horario tranquilo y manejo suave</dd></div>
                    <div><dt>Sin pelo</dt><dd>Protector solar pet-safe</dd></div>
                </div>
                <a class="boton boton--bloque mt-3" href="<?= url('/agenda') ?>">Agendar con estos datos</a>
            </div>
        </div>
    </div>
</section>

<section class="seccion seccion--compacta">
    <div class="contenedor">
        <div class="cta">
            <div>
                <span class="mono texto-oro">Primera visita</span>
                <h2>¿Es la primera vez de tu perro en una estética?</h2>
                <p>Cuéntanos su edad, raza, manto, salud y carácter al agendar. Así preparamos el servicio y el manejo antes de que llegue.</p>
            </div>
            <div class="cta__acciones">
                <a class="boton boton--oro boton--grande" href="<?= url('/agenda') ?>">Agendar primera cita</a>
                <a class="boton boton--fantasma boton--grande" href="<?= url('/guias') ?>">Leer guías</a>
            </div>
        </div>
    </div>
</section>
