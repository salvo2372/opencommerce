<?php

require_once __DIR__ . '/../vendor/autoload.php';

define('BASE_DIR', str_replace("\\", "/", dirname(__DIR__)));
define('IMAGES',   str_replace("\\", "/", __DIR__) . "/img/");
define('APP',  BASE_DIR . "/app/");

$app = new App();
$app->run();
