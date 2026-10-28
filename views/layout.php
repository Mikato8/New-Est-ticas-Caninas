<?php

declare(strict_types=1);

use App\Core\Auth;
use App\Core\Session;

$usuario = Auth::usuario();
$rutaActual = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$canonica = url($rutaActual);
$imagenSocial = url('/assets/img/og.png');
$jsonLd = $jsonLd ?? [
    '@context' => 'https://schema.org',
    '@type' => 'LocalBusiness',
    'name' => (string) config('negocio.nombre'),
    'description' => (string) config('tagline'),
    'url' => (string) config('url'),
    'image' => $imagenSocial,
    'priceRange' => '$$',
    'areaServed' => 'México',
    'email' => (string) config('contacto.email'),
];
if ((string) config('contacto.telefono') !== '') {
    $jsonLd['telephone'] = (string) config('contacto.telefono');
}
if ((string) config('contacto.direccion') !== '') {
    $jsonLd['address'] = ['@type' => 'PostalAddress', 'streetAddress' => (string) config('contacto.direccion'), 'addressCountry' => 'MX'];
}
?>
<!doctype html>
<html lang="es-MX">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($titulo ?? config('name')) ?><?= str_contains((string) ($titulo ?? ''), 'Mikato') ? '' : ' · ' . e(config('short')) ?></title>
    <meta name="description" content="<?= e($descripcion ?? config('tagline')) ?>">
    <link rel="canonical" href="<?= e($canonica) ?>">
    <meta name="theme-color" content="#245a53">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="<?= e(config('name')) ?>">
    <meta property="og:title" content="<?= e($titulo ?? config('name')) ?>">
    <meta property="og:description" content="<?= e($descripcion ?? config('tagline')) ?>">
    <meta property="og:url" content="<?= e($canonica) ?>">
    <meta property="og:image" content="<?= e($imagenSocial) ?>">
    <meta property="og:locale" content="es_MX">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= e($titulo ?? config('name')) ?>">
    <meta name="twitter:description" content="<?= e($descripcion ?? config('tagline')) ?>">
    <link rel="icon" href="<?= asset('/favicon.ico') ?>" sizes="any">
    <link rel="icon" type="image/svg+xml" href="<?= asset('/assets/img/favicon.svg') ?>">
    <link rel="apple-touch-icon" href="<?= asset('/assets/img/apple-touch-icon.png') ?>">
    <link rel="icon" type="image/png" sizes="512x512" href="<?= asset('/assets/img/icono-512.png') ?>">
    <link rel="stylesheet" href="<?= asset('/assets/fonts/fonts.css') ?>">
    <link rel="stylesheet" href="<?= asset('/assets/css/app.css') ?>">
    <script defer src="<?= asset('/assets/js/app.js') ?>"></script>
    <script type="application/ld+json"><?= json_encode($jsonLd, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) ?></script>
</head>
<body>
<a class="sr-only" href="#contenido">Saltar al contenido</a>
<?php parcial('cabecera', ['usuario' => $usuario]); ?>

<?php $flashExito = Session::flash('exito'); $flashError = Session::flash('error'); ?>
<?php if ($flashExito !== null): ?>
    <div class="contenedor"><div class="aviso-flash aviso-flash--exito" role="status"><?= icono('check') ?><div><?= e($flashExito) ?></div></div></div>
<?php endif; ?>
<?php if ($flashError !== null): ?>
    <div class="contenedor"><div class="aviso-flash aviso-flash--error" role="alert"><?= icono('alerta') ?><div><?= e($flashError) ?></div></div></div>
<?php endif; ?>

<main id="contenido">
    <?= $contenido ?>
</main>

<?php parcial('pie'); ?>
</body>
</html>
