<?php

declare(strict_types=1);

use App\Domain\Contenido\Glosario;
use App\Domain\Contenido\Guias;
use App\Domain\Estetica\CalculadoraPrecio;
use App\Domain\Estetica\CalculadoraRentabilidad;
use App\Domain\Estetica\CalculadoraSecado;
use App\Domain\Estetica\CalculadoraShampoo;
use App\Domain\Estetica\DatosInvalidos;
use App\Support\Markdown;
use App\Support\Slug;

require dirname(__DIR__) . '/app/bootstrap.php';

$pruebas = 0;
$fallos = 0;

function ok(bool $condicion, string $descripcion, string $detalle = ''): void
{
    global $pruebas, $fallos;
    $pruebas++;
    if ($condicion) {
        echo "  ok  {$descripcion}\n";
        return;
    }
    $fallos++;
    echo "  FALLA {$descripcion}" . ($detalle !== '' ? " → {$detalle}" : '') . "\n";
}

function igual(mixed $esperado, mixed $obtenido, string $descripcion): void
{
    ok($esperado === $obtenido, $descripcion, 'esperado ' . var_export($esperado, true) . ', obtenido ' . var_export($obtenido, true));
}

echo "Dilución de shampoo\n";
$shampoo = (new CalculadoraShampoo())->calcular([
    'concentrado_ml' => 500, 'ratio' => 10, 'tamano' => 'mediano', 'pelo' => 'medio',
    'suciedad' => 'normal', 'precio_litro' => 450,
]);
igual(5500.0, (float) $shampoo['total_ml'], '500 ml a 1:10 rinden 5,500 ml');
igual(5000.0, (float) $shampoo['agua_ml'], 'Se agregan 5,000 ml de agua');
igual(200.0, (float) $shampoo['dosis_por_bano'], 'Dosis por baño mediano = 200 ml');
igual(27, $shampoo['banos'], 'La mezcla rinde 27 baños');
igual(18, (int) $shampoo['concentrado_por_bano'], 'Concentrado por baño ≈ 18 ml');
igual(225.0, (float) $shampoo['costo_mezcla'], 'Costo de la mezcla = $225');
igual(8.18, (float) $shampoo['costo_bano'], 'Costo por baño = $8.18');

$doble = (new CalculadoraShampoo())->calcular([
    'concentrado_ml' => 1000, 'ratio' => 20, 'tamano' => 'grande', 'pelo' => 'doble',
    'suciedad' => 'mucho', 'precio_litro' => 600,
]);
igual(478.0, (float) $doble['dosis_por_bano'], 'Doble capa grande con suciedad pesada = 478 ml');
ok($doble['alertas'] !== [], 'La doble capa muestra alerta de saturación');

try {
    (new CalculadoraShampoo())->calcular(['concentrado_ml' => 0, 'ratio' => 0, 'tamano' => 'x', 'pelo' => 'x']);
    ok(false, 'Debe rechazar concentrado y dilución inválidos');
} catch (DatosInvalidos $e) {
    ok(isset($e->errores()['concentrado_ml']), 'Rechaza concentrado en cero');
    ok(isset($e->errores()['ratio']), 'Rechaza dilución fuera de rango');
}

echo "Precio de servicio\n";
$precio = (new CalculadoraPrecio())->calcular([
    'minutos' => 90, 'insumos' => 45, 'costo_fijo_mensual' => 15000, 'servicios_mes' => 120,
    'tarifa_hora' => 120, 'comision_pct' => 0, 'margen_pct' => 35, 'iva_pct' => 0,
]);
igual(125.0, (float) $precio['costo_fijo'], 'Costo fijo por servicio = $125');
igual(180.0, (float) $precio['mano_obra'], 'Mano de obra 1.5 h = $180');
igual(350.0, (float) $precio['costo_directo'], 'Costo directo = $350');
igual(538.46, (float) $precio['precio_sin_iva'], 'Precio sin IVA = $538.46');
igual(540.0, (float) $precio['precio_sugerido'], 'Precio redondeado = $540');
igual(188.46, (float) $precio['utilidad'], 'Utilidad = $188.46');
igual(35.0, (float) $precio['margen_real'], 'Margen real = 35 %');

$conComision = (new CalculadoraPrecio())->calcular([
    'minutos' => 90, 'insumos' => 45, 'costo_fijo_mensual' => 15000, 'servicios_mes' => 120,
    'tarifa_hora' => 120, 'comision_pct' => 10, 'margen_pct' => 30, 'iva_pct' => 16,
]);
igual(583.33, (float) $conComision['precio_sin_iva'], 'Comisión 10 % y margen 30 %: $583.33');
igual(676.67, (float) $conComision['precio_con_iva'], 'Con IVA 16 %: $676.67');

echo "Secado\n";
$secado = (new CalculadoraSecado())->calcular(['peso_kg' => 10, 'pelo' => 'medio', 'equipo' => 'turbo', 'tarifa_kwh' => 3.5]);
igual(25, $secado['minutos'], '10 kg manto medio con turbo ≈ 25 min');
igual('20 a 30 min', $secado['rango'], 'Rango 20 a 30 min');
igual(3.21, (float) $secado['costo_energia'], 'Costo de energía = $3.21');
igual(0, $secado['min_pausas'], 'Sin pausas por debajo de 40 min');

$secadoRiesgo = (new CalculadoraSecado())->calcular(['peso_kg' => 30, 'pelo' => 'doble', 'equipo' => 'jaula', 'tarifa_kwh' => 3.5]);
ok($secadoRiesgo['alertas'] !== [], 'Doble capa y jaula generan alertas');
ok($secadoRiesgo['minutos'] >= 45, 'Un perro grande de doble capa tarda 45 min o más', (string) $secadoRiesgo['minutos']);

echo "Rentabilidad\n";
$rent = (new CalculadoraRentabilidad())->calcular([
    'precio' => 550, 'insumos' => 45, 'minutos' => 90, 'comision_pct' => 0,
    'costo_fijo_mensual' => 15000, 'meta_hora' => 180,
]);
igual(505.0, (float) $rent['neto'], 'Neto por servicio = $505');
igual(336.67, (float) $rent['utilidad_hora'], 'Utilidad por hora = $336.67');
igual('verde', $rent['semaforo'], 'Supera la meta por hora');
igual(29.7, (float) $rent['punto_equilibrio'], 'Punto de equilibrio ≈ 30 servicios');
igual(315.0, (float) $rent['precio_meta'], 'Precio para meta de $180/h = $315');
ok($rent['brecha'] < 0, 'Con $550 ya supera el precio meta');

$rentBaja = (new CalculadoraRentabilidad())->calcular(['precio' => 200, 'insumos' => 45, 'minutos' => 120, 'comision_pct' => 10, 'costo_fijo_mensual' => 15000, 'meta_hora' => 180]);
igual('rojo', $rentBaja['semaforo'], 'Un servicio de $200 por 2 h queda en rojo');
ok($rentBaja['recomendaciones'] !== [], 'Sugiere recomendaciones cuando no alcanza la meta');

echo "Contenido\n";
igual(12, Guias::total(), 'Hay 12 guías');
ok(Guias::porSlug('secado-seguro') !== null, 'La guía de secado existe');
igual(5, count(Guias::categorias()), 'Hay 5 categorías de guías');
igual(4, count(Guias::porCategoria('salud')), 'Hay 4 guías de salud');
igual(32, count(Glosario::todos()), 'El glosario tiene 32 términos');
ok(Glosario::porCategoria('manto') !== [], 'El glosario tiene términos de manto');

echo "Utilidades\n";
igual('bano-profesional-paso-a-paso', Slug::make('Baño profesional paso a paso'), 'El slug quita acentos');
igual('Chico', tamano_label('chico'), 'tamano_label traduce chico');
igual('$250', precio(250), 'precio sin decimales en enteros');
igual('$1,250.50', precio(1250.5), 'precio con decimales');
ok(str_contains(Markdown::toHtml("| A | B |\n|---|---|\n| 1 | 2 |"), '<table>'), 'Markdown renderiza tablas');
ok(str_contains(Markdown::toHtml('<script>alert(1)</script>'), '&lt;script&gt;'), 'Markdown escapa HTML crudo');
ok(str_contains(Markdown::toHtml('**negritas**'), '<strong>negritas</strong>'), 'Markdown renderiza negritas');

echo "\n{$pruebas} pruebas, {$fallos} fallos\n";
exit($fallos === 0 ? 0 : 1);
