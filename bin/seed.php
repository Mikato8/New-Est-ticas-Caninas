<?php

declare(strict_types=1);

use App\Core\Database;
use App\Repositories\CitaRepository;
use App\Repositories\ClienteRepository;
use App\Repositories\GastoRepository;
use App\Repositories\MascotaRepository;
use App\Repositories\PaqueteRepository;
use App\Repositories\ProductoRepository;
use App\Repositories\ServicioRepository;
use App\Repositories\VentaRepository;

require dirname(__DIR__) . '/app/bootstrap.php';

/**
 * Contenido inicial idempotente. Los precios, productos, clientes y fichas de
 * ejemplo son demostrativos: edítalos desde el panel antes de publicar.
 */

$creados = 0;
$omitidos = 0;

function aviso(string $mensaje): void
{
    echo '  · ' . $mensaje . PHP_EOL;
}

// -------------------------------------------------------------------------
// Administrador
// -------------------------------------------------------------------------
$admin = Database::first('select * from usuarios where lower(correo) = lower(?) limit 1', [(string) config('admin.correo')]);
if ($admin === null) {
    Database::insert(
        'insert into usuarios (nombre, usuario, correo, telefono, password_hash, rol, estado, ciudad, estado_geo, bio)
         values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?) returning id',
        [
            (string) config('admin.nombre'),
            (string) config('admin.usuario'),
            (string) config('admin.correo'),
            null,
            password_hash((string) config('admin.password'), PASSWORD_DEFAULT),
            'admin',
            'activo',
            'Ciudad de México',
            'CDMX',
            'Administración de Mikato Estilistas Caninos.',
        ]
    );
    $admin = Database::first('select * from usuarios where lower(correo) = lower(?) limit 1', [(string) config('admin.correo')]);
    aviso('Administrador creado: ' . config('admin.usuario'));
    $creados++;
} else {
    aviso('Administrador ya existe.');
    $omitidos++;
}
$adminId = (int) ($admin['id'] ?? 0);

// -------------------------------------------------------------------------
// Servicios
// -------------------------------------------------------------------------
if ((int) Database::value('select count(*) from servicios') === 0) {
    $servicios = [
        ['Baño express', 'Baño con shampoo diluido, secado completo y acabado. Ideal para mantenimiento.', 'bano', 'chico', 220, 45, 10],
        ['Baño express', 'Baño con shampoo diluido, secado completo y acabado. Ideal para mantenimiento.', 'bano', 'mediano', 300, 60, 11],
        ['Baño express', 'Baño con shampoo diluido, secado completo y acabado. Ideal para mantenimiento.', 'bano', 'grande', 420, 75, 12],
        ['Baño express', 'Baño con shampoo diluido, secado completo y acabado. Ideal para mantenimiento.', 'bano', 'gigante', 560, 90, 13],
        ['Baño + corte', 'Baño completo, secado, corte de manto y acabado. Incluye revisión de uñas y oídos externos.', 'corte', 'chico', 380, 90, 20],
        ['Baño + corte', 'Baño completo, secado, corte de manto y acabado. Incluye revisión de uñas y oídos externos.', 'corte', 'mediano', 480, 105, 21],
        ['Baño + corte', 'Baño completo, secado, corte de manto y acabado. Incluye revisión de uñas y oídos externos.', 'corte', 'grande', 620, 120, 22],
        ['Baño + corte', 'Baño completo, secado, corte de manto y acabado. Incluye revisión de uñas y oídos externos.', 'corte', 'gigante', 780, 150, 23],
        ['Corte de uñas y limado', 'Corte seguro, limado y revisión de almohadillas. Incluye polvo hemostático si hace falta.', 'unas', 'todos', 90, 20, 30],
        ['Limpieza de oídos', 'Limpieza del pabellón y pliegues con producto veterinario. Revisión de señales de infección.', 'oidos', 'todos', 90, 20, 40],
        ['Spa de patas', 'Baño de patas, exfoliación suave, corte de pelo entre dedos y bálsamo pet-safe.', 'spa', 'todos', 150, 30, 50],
        ['Deslanado', 'Retiro de subpelo muerto con rastrillo y cepillado. No incluye baño.', 'bano', 'todos', 250, 45, 60],
        ['Corte higiénico', 'Zona sanitaria, abdomen y patas. Recomendado cada 4 a 6 semanas.', 'corte', 'todos', 180, 40, 70],
        ['Spa relajante', 'Baño con masaje, acondicionador, aroma pet-safe y acabado. Para perros tranquilos.', 'spa', 'mediano', 480, 75, 80],
    ];
    foreach ($servicios as [$nombre, $descripcion, $categoria, $tamano, $precio, $duracion, $orden]) {
        ServicioRepository::crear([
            'nombre' => $nombre,
            'descripcion' => $descripcion,
            'categoria' => $categoria,
            'tamano' => $tamano,
            'precio' => $precio,
            'duracion_min' => $duracion,
            'activo' => true,
            'orden' => $orden,
        ]);
    }
    aviso('14 servicios creados.');
    $creados += 14;
} else {
    aviso('Servicios ya existen.');
    $omitidos++;
}

// -------------------------------------------------------------------------
// Productos
// -------------------------------------------------------------------------
if ((int) Database::value('select count(*) from productos') === 0) {
    $productos = [
        ['Shampoo Mikato Hipoalergénico', 'Fórmula suave para piel sensible. Dilución según ficha técnica. Aroma neutro.', 'shampoo', '500 ml', 280, 210, 120, 24, 'MK-SH-500', true],
        ['Shampoo Mikato Blanco', 'Para mantos claros y blancos. Ayuda a neutralizar tonos amarillos.', 'shampoo', '1 L', 450, 340, 210, 15, 'MK-SH-BL-1L', false],
        ['Shampoo Mikato Cachorro', 'Fórmula extra suave para cachorros y piel delicada.', 'shampoo', '500 ml', 260, 195, 110, 18, 'MK-SH-PU-500', false],
        ['Acondicionador Mikato Sedas', 'Desenreda y facilita el cepillado en mantos largos. Uso profesional.', 'acondicionador', '500 ml', 320, 240, 150, 20, 'MK-AC-500', true],
        ['Perfume Mikato Brisa', 'Aroma pet-safe para el acabado. Aplicar a distancia y evitar ojos y nariz.', 'perfume', '120 ml', 260, 190, 100, 30, 'MK-PF-120', true],
        ['Cepillo slicker Mikato', 'Cerdas finas para desenredar y alisar mantos medios y largos.', 'cepillo', 'Mediano', 390, 290, 180, 12, 'MK-CE-SL', false],
        ['Tijera recta Mikato 6.5"', 'Acero inoxidable, punta fina. Para acabados y patas.', 'herramienta', '6.5 pulgadas', 850, 680, 480, 8, 'MK-TJ-65', true],
        ['Secadora turbo Mikato 2200 W', 'Potencia regulable, temperatura controlada y boquilla direccional.', 'herramienta', '2200 W', 3200, 2650, 2100, 5, 'MK-SE-2200', false],
        ['Kit de inicio Mikato', 'Shampoo 500 ml, acondicionador 500 ml, cepillo y guía de dilución.', 'otro', 'Kit', 1250, 980, 720, 10, 'MK-KIT-01', true],
        ['Snack dental Mikato', 'Premio dental para después del servicio. Uso moderado.', 'snack', '200 g', 120, 90, 55, 40, 'MK-SN-200', false],
    ];
    foreach ($productos as [$nombre, $descripcion, $categoria, $presentacion, $precio, $mayoreo, $costo, $stock, $sku, $destacado]) {
        ProductoRepository::crear([
            'nombre' => $nombre,
            'descripcion' => $descripcion,
            'categoria' => $categoria,
            'presentacion' => $presentacion,
            'precio' => $precio,
            'precio_mayoreo' => $mayoreo,
            'costo' => $costo,
            'stock' => $stock,
            'sku' => $sku,
            'activo' => true,
            'destacado' => $destacado,
        ]);
    }
    aviso('10 productos creados.');
    $creados += 10;
} else {
    aviso('Productos ya existen.');
    $omitidos++;
}

// -------------------------------------------------------------------------
// Paquetes
// -------------------------------------------------------------------------
if ((int) Database::value('select count(*) from paquetes') === 0) {
    $paquetes = [
        ['Bienestar chico', 'Baño express, corte de uñas y limpieza de oídos para perros hasta 8 kg.', 480, 'chico'],
        ['Mantenimiento mediano', 'Baño, corte higiénico y uñas para perros de 8 a 15 kg.', 650, 'mediano'],
        ['Spa completo grande', 'Baño, deslanado, uñas y spa de patas para perros de 15 a 30 kg.', 890, 'grande'],
    ];
    foreach ($paquetes as [$nombre, $descripcion, $precio, $tamano]) {
        PaqueteRepository::crear(['nombre' => $nombre, 'descripcion' => $descripcion, 'precio' => $precio, 'tamano' => $tamano, 'activo' => true]);
    }
    aviso('3 paquetes creados.');
    $creados += 3;
} else {
    aviso('Paquetes ya existen.');
    $omitidos++;
}

// -------------------------------------------------------------------------
// Clientes y mascotas
// -------------------------------------------------------------------------
if ((int) Database::value('select count(*) from clientes') === 0) {
    $clientes = [
        ['María Fernanda López', '55 1234 8801', 'maria.lopez@example.com', 'Coyoacán, CDMX', 'Prefiere citas por la mañana.'],
        ['Jorge Ramírez', '33 2211 4502', 'jorge.ramirez@example.com', 'Guadalajara, Jalisco', 'Paga con transferencia.'],
        ['Ana Sofía Torres', '81 3344 5503', 'ana.torres@example.com', 'Monterrey, Nuevo León', 'Perro nervioso con la secadora.'],
        ['Luis Hernández', '55 4455 6604', 'luis.hernandez@example.com', 'Iztapalapa, CDMX', 'Cliente frecuente.'],
        ['Paola Méndez', '22 5566 7705', 'paola.mendez@example.com', 'Puebla, Puebla', 'Pidió seguimiento por WhatsApp.'],
        ['Diego Castro', '55 6677 8806', 'diego.castro@example.com', 'Benito Juárez, CDMX', 'Primera visita en la estética.'],
    ];
    $clientesIds = [];
    foreach ($clientes as [$nombre, $telefono, $correo, $direccion, $notas]) {
        $clientesIds[] = ClienteRepository::crear([
            'nombre' => $nombre,
            'telefono' => $telefono,
            'correo' => $correo,
            'direccion' => $direccion,
            'notas' => $notas,
        ]);
    }

    $mascotas = [
        [$clientesIds[0], 'Maya', 'perro', 'Poodle', 'mediano', 'hembra', '2021-03-12', 9.5, 'Sociable, tolera bien la secadora.', 'Piel sensible a fragancias fuertes.', 'Manto rizado: cepillar antes de mojar.'],
        [$clientesIds[0], 'Nube', 'perro', 'Shih Tzu', 'chico', 'hembra', '2023-07-01', 5.2, 'Tranquila, se estresa con el secador.', 'Lágrimas frecuentes.', 'Cuidado con ojos y nudos detrás de orejas.'],
        [$clientesIds[1], 'Rocky', 'perro', 'Schnauzer', 'mediano', 'macho', '2019-11-20', 12.0, 'Tolerante, algo impaciente con uñas.', null, 'Corte de barba y cejas.'],
        [$clientesIds[2], 'Luna', 'perro', 'Husky', 'grande', 'hembra', '2020-01-15', 24.0, 'Nerviosa, requiere manejo lento.', null, 'Doble capa: nunca rasurar. Deslanado.'],
        [$clientesIds[2], 'Canelo', 'perro', 'Criollo', 'mediano', 'macho', '2018-06-10', 14.5, 'Muy sociable.', 'Alergia a pulgas.', 'Revisar piel y orejas.'],
        [$clientesIds[3], 'Chocolate', 'perro', 'Chihuahua', 'chico', 'macho', '2022-02-14', 3.8, 'Nervioso, muerde si lo sujetan del cuello.', null, 'Usar bozal de cesta y manejo de bajo estrés.'],
        [$clientesIds[4], 'Frida', 'perro', 'Golden retriever', 'grande', 'hembra', '2017-09-09', 28.0, 'Obediente, geriátrica.', 'Artritis leve.', 'Base antideslizante y pausas.'],
        [$clientesIds[5], 'Tinto', 'perro', 'Xoloitzcuintle', 'mediano', 'macho', '2016-12-01', 10.0, 'Tranquilo.', 'Piel seca.', 'Protector solar pet-safe e hidratación.'],
    ];
    $mascotasIds = [];
    foreach ($mascotas as [$clienteId, $nombre, $especie, $raza, $tamano, $sexo, $nacimiento, $peso, $temperamento, $alergias, $notas]) {
        $mascotasIds[] = MascotaRepository::crear([
            'cliente_id' => $clienteId,
            'nombre' => $nombre,
            'especie' => $especie,
            'raza' => $raza,
            'tamano' => $tamano,
            'sexo' => $sexo,
            'nacimiento' => $nacimiento,
            'peso' => $peso,
            'temperamento' => $temperamento,
            'alergias' => $alergias,
            'notas' => $notas,
        ]);
    }
    aviso('6 clientes y 8 mascotas creados.');
    $creados += 14;
} else {
    aviso('Clientes ya existen.');
    $omitidos++;
}

// -------------------------------------------------------------------------
// Citas e historial
// -------------------------------------------------------------------------
if ((int) Database::value('select count(*) from citas') === 0) {
    $clientes = Database::select('select id, nombre from clientes order by id');
    $mascotas = Database::select('select id, cliente_id, nombre from mascotas order by id');
    $servicios = Database::select('select id, nombre, precio from servicios order by id');
    if ($clientes !== [] && $mascotas !== [] && $servicios !== []) {
        $agenda = [
            [0, 0, 1, 0, '10:00', 'confirmada', 'Baño de mantenimiento.'],
            [1, 2, 5, 0, '12:00', 'pendiente', 'Primera visita. Trae cartilla.'],
            [2, 3, 7, 0, '16:00', 'confirmada', 'Manto doble: deslanado y baño.'],
            [0, 1, 4, 1, '11:00', 'confirmada', 'Corte de cara y patas.'],
            [3, 5, 8, 1, '13:00', 'pendiente', 'Usar bozal de cesta, por favor.'],
            [4, 6, 6, 2, '10:00', 'confirmada', 'Perra geriátrica: pausas y base antideslizante.'],
        ];
        foreach ($agenda as [$clienteIdx, $mascotaIdx, $servicioIdx, $dia, $hora, $estado, $notas]) {
            $fecha = date('Y-m-d', strtotime('+' . $dia . ' days'));
            if (CitaRepository::slotOcupado($fecha, $hora)) {
                continue;
            }
            $cliente = $clientes[$clienteIdx] ?? null;
            $mascota = $mascotas[$mascotaIdx] ?? null;
            $servicio = $servicios[$servicioIdx] ?? null;
            if ($cliente === null || $mascota === null || $servicio === null) {
                continue;
            }
            CitaRepository::crear([
                'cliente_id' => (int) $cliente['id'],
                'mascota_id' => (int) $mascota['id'],
                'servicio_id' => (int) $servicio['id'],
                'fecha' => $fecha,
                'hora' => $hora,
                'estado' => $estado,
                'precio' => (float) $servicio['precio'],
                'notas' => $notas,
                'origen' => $dia === 0 ? 'telefono' : 'web',
            ]);
        }

        // Historial de ejemplo para las mascotas con cita.
        $historial = [
            [0, 'Baño express y corte de uñas', 'Piel sin hallazgos. Próxima visita en 4 semanas.', '-28 days'],
            [1, 'Baño y corte de cara', 'Ojos limpios. Se recomendó cepillado diario.', '-35 days'],
            [3, 'Deslanado y baño', 'Mucho subpelo. Se programó seguimiento en 3 semanas.', '-21 days'],
            [5, 'Corte higiénico y uñas', 'Usamos bozal de cesta. Sin incidentes.', '-14 days'],
        ];
        foreach ($historial as [$mascotaIdx, $servicio, $observaciones, $offset]) {
            $mascota = $mascotas[$mascotaIdx] ?? null;
            if ($mascota === null) {
                continue;
            }
            MascotaRepository::agregarHistorial((int) $mascota['id'], [
                'fecha' => date('Y-m-d', strtotime($offset)),
                'servicio' => $servicio,
                'observaciones' => $observaciones,
                'proxima_visita' => date('Y-m-d', strtotime($offset . ' +28 days')),
            ]);
        }
        aviso('6 citas y 4 registros de historial creados.');
        $creados += 10;
    }
} else {
    aviso('Citas ya existen.');
    $omitidos++;
}

// -------------------------------------------------------------------------
// Ventas
// -------------------------------------------------------------------------
if ((int) Database::value('select count(*) from ventas') === 0) {
    $clientes = Database::select('select id, nombre from clientes order by id');
    $servicios = Database::select('select id, nombre, precio from servicios where activo = true order by id');
    $productos = Database::select('select id, nombre, precio from productos where activo = true order by id');
    $ventas = [
        [0, '-1 days', 'tarjeta', [[0, 1], [8, 1]], [[5, 1]]],
        [1, '-2 days', 'efectivo', [[4, 1]], []],
        [2, '-3 days', 'transferencia', [[6, 1], [9, 1]], [[0, 1]]],
        [0, '-4 days', 'efectivo', [[1, 1]], [[6, 1]]],
        [3, '-5 days', 'tarjeta', [[2, 1], [10, 1]], []],
        [4, '-6 days', 'efectivo', [[3, 1]], [[4, 1]]],
        [5, '-8 days', 'transferencia', [[11, 1]], [[0, 2]]],
        [1, '-9 days', 'efectivo', [[7, 1], [8, 1]], [[8, 1]]],
    ];
    foreach ($ventas as [$clienteIdx, $offset, $metodo, $lineasServicio, $lineasProducto]) {
        $cliente = $clientes[$clienteIdx] ?? null;
        $items = [];
        foreach ($lineasServicio as [$servicioIdx, $cantidad]) {
            $servicio = $servicios[$servicioIdx] ?? null;
            if ($servicio === null) continue;
            $items[] = [
                'tipo' => 'servicio',
                'referencia_id' => (int) $servicio['id'],
                'descripcion' => (string) $servicio['nombre'],
                'cantidad' => $cantidad,
                'precio_unitario' => (float) $servicio['precio'],
                'importe' => (float) $servicio['precio'] * $cantidad,
            ];
        }
        foreach ($lineasProducto as [$productoIdx, $cantidad]) {
            $producto = $productos[$productoIdx] ?? null;
            if ($producto === null) continue;
            $items[] = [
                'tipo' => 'producto',
                'referencia_id' => (int) $producto['id'],
                'descripcion' => (string) $producto['nombre'],
                'cantidad' => $cantidad,
                'precio_unitario' => (float) $producto['precio'],
                'importe' => (float) $producto['precio'] * $cantidad,
            ];
        }
        if ($items === []) {
            continue;
        }
        VentaRepository::crearConItems([
            'cliente_id' => $cliente !== null ? (int) $cliente['id'] : null,
            'fecha' => date('Y-m-d', strtotime($offset)),
            'metodo_pago' => $metodo,
            'estado' => 'pagada',
            'descuento' => 0,
            'notas' => null,
            'registrado_por' => $adminId,
        ], $items);
    }
    aviso('8 ventas de ejemplo creadas.');
    $creados += 8;
} else {
    aviso('Ventas ya existen.');
    $omitidos++;
}

// -------------------------------------------------------------------------
// Gastos
// -------------------------------------------------------------------------
if ((int) Database::value('select count(*) from gastos') === 0) {
    $gastos = [
        ['Renta del local', 'renta', 8000, date('Y-m-01')],
        ['Insumos y shampoo', 'insumos', 1850, date('Y-m-03')],
        ['Luz CFE', 'servicios', 920, date('Y-m-05')],
        ['Agua', 'servicios', 310, date('Y-m-05')],
        ['Publicidad en redes', 'marketing', 600, date('Y-m-08')],
        ['Mantenimiento de secadora', 'equipo', 750, date('Y-m-12')],
    ];
    foreach ($gastos as [$concepto, $categoria, $monto, $fecha]) {
        GastoRepository::crear(['concepto' => $concepto, 'categoria' => $categoria, 'monto' => $monto, 'fecha' => $fecha, 'metodo_pago' => 'transferencia']);
    }
    aviso('6 gastos creados.');
    $creados += 6;
} else {
    aviso('Gastos ya existen.');
    $omitidos++;
}

// -------------------------------------------------------------------------
// Comunidad
// -------------------------------------------------------------------------
if ((int) Database::value('select count(*) from publicaciones') === 0) {
    $publicaciones = [
        [
            'Antes y después: manto doble sin rasurar',
            'casos',
            'Un Husky con muda intensa y nudos en la zona sanitaria. Así lo resolvimos sin máquina en el lomo.',
            <<<'MD'
Llegó Luna, una Husky de 24 kg, con el subpelo suelto y varios nudos detrás de las orejas. El tutor pedía "rasurarla para que no suelte pelo". Le expliqué por qué no es la mejor idea y le propuse un plan de deslanado.

**Proceso:**

1. Revisión en seco: piel sin lesiones, nudos localizados en orejas y zona sanitaria.
2. Deslanado con rastrillo en pasadas cortas, sin presionar.
3. Baño con shampoo diluido 1:10 y dos enjuagues.
4. Secado con soplo a temperatura media, primero la capa interna.
5. Corte higiénico y uñas.
6. Cepillado final y recomendaciones para casa.

**Resultado:** el manto quedó parejo, sin rasurar. El tutor entendió que la muda no se detiene con la máquina y se llevó un plan de cepillado 3 veces por semana.

**Aprendizaje:** explicar el porqué toma cinco minutos y evita un cliente molesto en un mes. Documenté el antes y después con autorización.
MD,
            'publicado',
            true,
        ],
        [
            'Cómo cobrar el deslanado sin perder clientes',
            'negocio',
            'El deslanado no es "un baño más". Es tiempo, herramienta y riesgo. Así lo cobro.',
            <<<'MD'
Durante mucho tiempo incluí el deslanado en el baño. El resultado: servicios de dos horas cobrados como uno. Cambié tres cosas:

- **Cobro por tiempo y complejidad.** No por raza. Un manto doble chico puede tardar más que un grande de pelo corto.
- **Aviso antes de empezar.** Le muestro al tutor el nudo, explico el proceso y doy un precio cerrado. Si dice que no, se agenda otro día.
- **Plan de mantenimiento.** Ofrezco cepillado cada 2 semanas o un paquete mensual. El cliente gasta menos que en un deslanado de emergencia y yo tengo ingreso recurrente.

Lo que no hago: prometer que "no va a doler" si el nudo está pegado a la piel, ni rasurar para "resolver rápido". Si el nudo es severo, se rasura con cuidado o se deriva.

La calculadora de precio me ayudó a poner números. Cobrar bien no es perder clientes; es filtrar a los que valoran el trabajo.
MD,
            'publicado',
            false,
        ],
        [
            'Mi perro muerde cuando le corto las uñas: qué hice',
            'dudas',
            'Chocolate es un Chihuahua de 3.8 kg. En la mesa se congela y muerde. Esto funcionó y esto no.',
            <<<'MD'
Chocolate llegó con historial de mordidas en dos estéticas. El tutor lo sujetaba del cuello, lo que empeoraba todo.

**Lo que no funcionó:** sujetarlo fuerte, usar un lazo, hacerlo "de una vez" entre dos personas. El perro escaló de congelarse a gruñir y luego a morder.

**Lo que sí funcionó:**

- Cambiar a un arnés y una toalla; nada de cuello.
- Bozal de cesta desde el inicio, presentado con calma.
- Sesiones de 10 minutos: un día una pata, otro día la lima.
- Premios de alto valor después de cada uña.
- Luz frontal y lima en lugar de cortaúñas en las uñas negras.

Después de cuatro sesiones, el perro tolera el manejo. No es "obediente", es que ya no siente que lo van a lastimar. Si tu perro llega a este nivel, para y busca ayuda profesional. La mordida es información, no rebelión.
MD,
            'publicado',
            false,
        ],
        [
            'Qué pedimos en la primera visita',
            'casos',
            'Cartilla, datos de salud, alergias y una foto del manto. La ficha previa evita sorpresas.',
            <<<'MD'
En la primera cita hacemos una entrevista de cinco minutos:

- Edad, raza o mestizo, peso aproximado.
- Vacunas y desparasitación (no sustituye la valoración veterinaria).
- Alergias, medicamentos, condiciones de piel u oídos.
- Temperamento: ¿se deja tocar?, ¿muerde?, ¿se estresa con la secadora?
- Último baño y corte, productos que usa en casa.
- Autorización para foto antes y después.

Con eso armamos la ficha y decidimos si el servicio se puede hacer completo o por partes. Si el perro llega con dolor, herida o enfermedad contagiosa, se reprograma.

La ficha también sirve para el tutor: sabe qué esperar y qué no. La transparencia evita reclamos.
MD,
            'publicado',
            false,
        ],
        [
            'Secadora de jaula: por qué no la uso',
            'salud',
            'No es solo comodidad. El riesgo de sobrecalentamiento es real, sobre todo en doble capa y braquicéfalos.',
            <<<'MD'
La secadora de jaula parece una solución: dejas al perro, avanzas con otro y "se seca solo". El problema es que el aire caliente circula en un espacio cerrado y el perro no puede alejarse.

**Riesgos:**

- Golpe de calor, especialmente en doble capa, braquicéfalos, geriátricos y obesos.
- Quemaduras de piel y estrés térmico.
- Miedo al secador y reactividad en futuras visitas.
- Muerte por dejar al perro sin supervisión.

Si se usa, debe ser con supervisión directa, temperatura controlada, perro que tolere el encierro y tiempos cortos. En mi salón no la uso para perros mojados de doble capa.

Prefiero tardar más y secar con soplo en movimiento. El perro sale tranquilo y el riesgo baja.
MD,
            'publicado',
            false,
        ],
        [
            'Busco estilista en Guadalajara para integrarme a mi equipo',
            'empleo',
            'Estética en crecimiento busca estilista con experiencia en razas pequeñas y manejo de bajo estrés.',
            <<<'MD'
Somos una estética en Guadalajara con agenda creciente. Buscamos estilista con experiencia en:

- Baño y secado de razas pequeñas y medianas.
- Corte de uñas y limpieza de oídos.
- Manejo de bajo estrés y comunicación con tutores.
- Disponibilidad de martes a sábado.

Ofrecemos sueldo base + comisión, material de trabajo y capacitación continua. No pedimos "certificado NOM" ni cursos milagro; nos interesa tu criterio y tu trato con los perros.

Escribe a la estética con tu experiencia y una foto de un trabajo reciente. Este es un caso de ejemplo de la comunidad.
MD,
            'publicado',
            false,
        ],
    ];

    $ids = [];
    foreach ($publicaciones as [$titulo, $categoria, $resumen, $contenido, $estado, $fijado]) {
        $fila = Database::insert(
            'insert into publicaciones (usuario_id, titulo, slug, categoria, resumen, contenido, estado, fijado, vistas, creado_en)
             values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?) returning id',
            [
                $adminId,
                $titulo,
                \App\Support\Slug::make($titulo),
                $categoria,
                $resumen,
                $contenido,
                $estado,
                $fijado,
                random_int(40, 180),
                date('Y-m-d H:i:s', strtotime('-' . random_int(1, 20) . ' days')),
            ]
        );
        $ids[] = (int) ($fila['id'] ?? 0);
    }

    $comentarios = [
        [$ids[0], 'Gracias por explicarlo. Me pasó con un Golden y el tutor no entendía por qué no lo rasuraba.', 'aprobado'],
        [$ids[0], 'El secado de doble capa es lo que más me cuesta. ¿Qué boquilla usas?', 'aprobado'],
        [$ids[1], 'Cobrar el deslanado aparte me cambió el negocio. Antes regalaba dos horas.', 'aprobado'],
        [$ids[2], 'El bozal de cesta y las sesiones cortas también me funcionaron con un Chihuahua.', 'aprobado'],
    ];
    foreach ($comentarios as [$pubId, $contenido, $estado]) {
        Database::execute(
            'insert into comentarios (publicacion_id, usuario_id, contenido, estado, creado_en) values (?, ?, ?, ?, ?)',
            [$pubId, $adminId, $contenido, $estado, date('Y-m-d H:i:s', strtotime('-' . random_int(1, 10) . ' days'))]
        );
    }
    aviso('6 publicaciones y 4 comentarios creados.');
    $creados += 10;
} else {
    aviso('Publicaciones ya existen.');
    $omitidos++;
}

// -------------------------------------------------------------------------
// Directorio de ejemplo
// -------------------------------------------------------------------------
if ((int) Database::value('select count(*) from negocios') === 0) {
    $negocios = [
        ['Estética de ejemplo · Patitas', 'Ciudad de México', 'CDMX', 'Ficha de ejemplo para mostrar el directorio. Estética de barrio con baño, corte y uñas para razas pequeñas. Reemplaza esta ficha con tu negocio real.', 'Baño, corte, uñas, oídos', 'Lun a sáb 9:00–19:00', '55 0000 0001', 1],
        ['Estética de ejemplo · Pelo & Huella', 'Guadalajara', 'Jalisco', 'Ficha de ejemplo para mostrar el directorio. Especialidad en mantos dobles y deslanado. Reemplaza esta ficha con tu negocio real.', 'Baño, deslanado, spa', 'Lun a vie 10:00–20:00', '33 0000 0002', 2],
        ['Estética de ejemplo · Caninos del Norte', 'Monterrey', 'Nuevo León', 'Ficha de ejemplo para mostrar el directorio. Manejo de bajo estrés y perros geriátricos. Reemplaza esta ficha con tu negocio real.', 'Baño, corte, manejo especial', 'Mar a sáb 9:00–18:00', '81 0000 0003', 3],
        ['Estética de ejemplo · Spa Canino', 'Puebla', 'Puebla', 'Ficha de ejemplo para mostrar el directorio. Spa, aromaterapia pet-safe y baño medicado con indicación veterinaria. Reemplaza esta ficha con tu negocio real.', 'Spa, baño, uñas', 'Lun a sáb 10:00–19:00', '22 0000 0004', 4],
    ];
    foreach ($negocios as [$nombre, $ciudad, $estadoGeo, $descripcion, $servicios, $horario, $telefono, $num]) {
        Database::execute(
            'insert into negocios (nombre, slug, descripcion, ciudad, estado_geo, telefono, whatsapp, servicios, horario, estado, verificado, es_demo, destacado)
             values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
            [
                $nombre,
                \App\Support\Slug::make($nombre) . '-' . $num,
                $descripcion,
                $ciudad,
                $estadoGeo,
                $telefono,
                $telefono,
                $servicios,
                $horario,
                'aprobado',
                false,
                true,
                $num === 1,
            ]
        );
    }
    aviso('4 fichas de ejemplo en el directorio.');
    $creados += 4;
} else {
    aviso('Fichas del directorio ya existen.');
    $omitidos++;
}

// -------------------------------------------------------------------------
// Mensajes de ejemplo
// -------------------------------------------------------------------------
if ((int) Database::value('select count(*) from contactos') === 0) {
    \App\Repositories\ContactoRepository::crear([
        'nombre' => 'Carolina Ruiz',
        'correo' => 'carolina@example.com',
        'telefono' => '55 1111 2222',
        'asunto' => 'servicio',
        'mensaje' => 'Hola, tengo una perra schnauzer de 12 kg con nudos en las patas. ¿Cuánto costaría el deslanado y el baño?',
    ]);
    \App\Repositories\ContactoRepository::crear([
        'nombre' => 'Distribuidora del Bajío',
        'correo' => 'ventas@example.com',
        'telefono' => '477 333 4444',
        'asunto' => 'mayoreo',
        'mensaje' => 'Buscamos distribución de shampoo y acondicionador para 15 estéticas en León. ¿Nos pueden enviar precios de mayoreo y tiempos de entrega?',
    ]);
    aviso('2 mensajes de ejemplo creados.');
    $creados += 2;
} else {
    aviso('Mensajes ya existen.');
    $omitidos++;
}

// -------------------------------------------------------------------------
// Configuración del negocio
// -------------------------------------------------------------------------
\App\Repositories\ConfiguracionRepository::setMany([
    'negocio_nombre' => (string) config('negocio.nombre'),
    'negocio_telefono' => (string) config('contacto.telefono'),
    'negocio_whatsapp' => (string) config('contacto.whatsapp'),
    'negocio_direccion' => (string) config('contacto.direccion'),
    'negocio_horario' => (string) config('contacto.horario'),
    'capacidad_diaria' => (string) config('negocio.capacidad_diaria'),
    'anticipo_pct' => '0',
]);

echo PHP_EOL;
echo 'Seed terminado: ' . $creados . ' creados, ' . $omitidos . ' bloques omitidos.' . PHP_EOL;
echo 'Administrador: ' . config('admin.usuario') . ' / ' . (string) config('admin.password') . PHP_EOL;
echo 'Recuerda cambiar la contraseña y revisar los precios de ejemplo.' . PHP_EOL;
