<?php

declare(strict_types=1);

use App\Core\Config;
use App\Core\Database;
use App\Core\Env;
use App\Core\Router;
use App\Core\View;

if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}

require BASE_PATH . '/app/autoload.php';
require BASE_PATH . '/app/helpers.php';

Env::load([
    (string) getenv('APP_ENV_FILE'),
    BASE_PATH . '/.env',
    dirname(BASE_PATH) . '/.env',
]);

Config::set(require BASE_PATH . '/config/app.php');

date_default_timezone_set((string) config('timezone', 'America/Mexico_City'));

error_reporting(E_ALL);
ini_set('display_errors', config('debug', false) ? '1' : '0');
ini_set('log_errors', '0');

set_error_handler(static function (int $severidad, string $mensaje, string $archivo, int $linea): bool {
    if (!(error_reporting() & $severidad)) {
        return false;
    }
    throw new \ErrorException($mensaje, 0, $severidad, $archivo, $linea);
});

View::setBase(BASE_PATH . '/views');

Database::configure((array) config('db', []));

$router = new Router();
(require BASE_PATH . '/routes/web.php')($router);

return new \App\Core\Application($router);
