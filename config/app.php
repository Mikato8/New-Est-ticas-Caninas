<?php

declare(strict_types=1);

use App\Core\Env;

return [
    'name' => env('APP_NAME', 'Mikato Estilistas Caninos'),
    'short' => env('APP_SHORT', 'Mikato Estética'),
    'domain' => env('APP_DOMAIN', 'mikatoestilistascaninos.com'),
    'tagline' => 'CRM, productos y comunidad para estéticas caninas · Guadalajara, Jalisco',
    'env' => env('APP_ENV', 'production'),
    'debug' => env_bool('APP_DEBUG', false),
    'url' => rtrim((string) env('APP_URL', 'http://127.0.0.1:8080'), '/'),
    'timezone' => env('APP_TIMEZONE', 'America/Mexico_City'),
    'locale' => env('APP_LOCALE', 'es_MX'),
    'version' => '1.0.0',

    'db' => [
        'url' => env('DATABASE_URL', ''),
        'driver' => env('DB_DRIVER', 'auto'), // auto | pdo | http
        'timeout' => env_int('DB_TIMEOUT', 25),
    ],

    'session' => [
        'name' => env('SESSION_NAME', 'mikato_estetica_sid'),
        'lifetime' => env_int('SESSION_LIFETIME', 1209600),
    ],

    'uploads' => [
        'path' => BASE_PATH . '/public/uploads',
        'url' => '/uploads',
        'max_bytes' => env_int('UPLOAD_MAX_BYTES', 3 * 1024 * 1024),
        'video_max_bytes' => env_int('VIDEO_MAX_BYTES', 40 * 1024 * 1024),
        'max_dimension' => 1800,
    ],

    'limites' => [
        'publicaciones_por_dia' => env_int('POSTS_POR_DIA', 3),
        'comentarios_por_hora' => env_int('COMENTARIOS_POR_HORA', 10),
        'login_intentos' => 8,
        'login_ventana' => 900,
        'contacto_por_hora' => 5,
        'citas_por_dia' => 3,
        'guia_media_por_dia' => 5,
    ],

    'contacto' => [
        'email' => env('CONTACT_EMAIL', 'clientes@mikatoestilistascaninos.com'),
        'whatsapp' => env('WHATSAPP', '523324333262'),
        'telefono' => env('TELEFONO', '+52 33 2433 3262'),
        'direccion' => env('DIRECCION', ''),
        'ciudad' => env('CIUDAD', 'Guadalajara'),
        'horario' => env('HORARIO', 'Lun a sáb · 9:00 a 19:00'),
    ],

    'mail' => [
        'enabled' => env_bool('MAIL_ENABLED', false),
        'host' => env('MAIL_HOST', 'smtp.hostinger.com'),
        'port' => (int) env('MAIL_PORT', '465'),
        'username' => env('MAIL_USERNAME', env('MAIL_FROM', 'cuentas@mikatoestilistascaninos.com')),
        'password' => env('MAIL_PASSWORD', ''),
        'encryption' => strtolower(env('MAIL_ENCRYPTION', 'ssl')),
        'from' => env('MAIL_FROM', 'cuentas@mikatoestilistascaninos.com'),
        'from_name' => env('MAIL_FROM_NAME', 'Mikato Estilistas Caninos'),
        'to' => env('MAIL_TO', 'clientes@mikatoestilistascaninos.com'),
    ],

    'admin' => [
        'usuario' => env('ADMIN_USER', 'admin'),
        'password' => env('ADMIN_PASSWORD', ''),
        'nombre' => env('ADMIN_NAME', 'Administrador'),
        'correo' => env('ADMIN_EMAIL', 'hola@mikatoestilistascaninos.com'),
    ],

    'negocio' => [
        'nombre' => env('NEGOCIO_NOMBRE', 'Mikato Estética Canina'),
        'razon_social' => env('NEGOCIO_RAZON', 'Mikato'),
        'rfc' => env('NEGOCIO_RFC', ''),
        'ciudad' => env('NEGOCIO_CIUDAD', 'Guadalajara'),
        'estado' => env('NEGOCIO_ESTADO', 'Jalisco'),
        'direccion' => env('NEGOCIO_DIRECCION', ''),
        'telefono' => env('NEGOCIO_TELEFONO', '+52 33 2433 3262'),
        'whatsapp' => env('NEGOCIO_WHATSAPP', '523324333262'),
        'correo' => env('NEGOCIO_CORREO', 'clientes@mikatoestilistascaninos.com'),
        'iva' => (float) env('NEGOCIO_IVA', '0'),
        'capacidad_diaria' => env_int('NEGOCIO_CAPACIDAD', 12),
    ],
];
