<?php

use App\core\App as App;
use App\core\Session as Session;
use App\core\Handler as Handler;
use App\core\Request as Request;
use Cart\Cart;
use Cart\Storage\SessionStore;

//require_once dirname(__FILE__) . '/core/App.php';
	// DIRECTORY_SEPARATOR adds a slash to the end of the path
	define('ROOT', dirname(__DIR__) . DIRECTORY_SEPARATOR);
	// set a constant that holds the project's "application" folder, like "/var/www/application".
	define('APP', ROOT . 'app' . DIRECTORY_SEPARATOR);

	//define('PUBLIC_ROOT', dirname(__DIR__) . DIRECTORY_SEPARATOR . 'public/');

	define('BASE_DIR', str_replace("\\", "/", dirname(__DIR__)));
	define('IMAGES',   str_replace("\\", "/", __DIR__) . "/images/");
	//define('APP',  BASE_DIR . "/app/");


require_once ROOT . DIRECTORY_SEPARATOR . 'vendor/autoload.php';

//require_once ROOT . '/config/app.php';
require_once APP. DIRECTORY_SEPARATOR . 'core/Config.php';
/*
|--------------------------------------------------------------------------
| Register Error & Exception handlers
|--------------------------------------------------------------------------
|
| Here we will register the methods that will fire whenever there is an error
| or an exception has been thrown.
|
*/

//Handler::register();
/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| Here we will create the application instance which will take care of routing
| the incoming request to the corresponding controller and action method if valid
|
*/
Session::init();
$id = "Cart-01";
$cartSessionStore = new SessionStore();

$cart = new Cart($id, $cartSessionStore);
$tommaso = $cart;

//echo Config::get('DB_NAME');
$app = new App($tommaso);

define('PUBLIC_ROOT', $app->request->root());
$app->run();
