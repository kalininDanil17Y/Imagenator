<?php
ini_set("display_errors", 1);
error_reporting(E_ALL);

require_once __DIR__ . '/vendor/autoload.php';

use Imagenator\Main\Router;

$router = new Router();

require_once __DIR__ . '/src/routers.php';

$router->Handle();