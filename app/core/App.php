<?php
namespace App\core;

use ReflectionMethod;
use App\controllers;
use App\controllers\Errors as Errors;
use App\core\Request as Request;
use App\core\Response as Response;
use App\core\Shopping as Shopping;
class App
{
    /**
     * Stores the controller from the split URL
     *
     * @var string
     */
    protected $controller = null;
    /**
     * Stores the method from the split URL
     * @var string
     */
    protected $method = null;
    /**
    * request
    * @var Request
    */
    public $request = null;
    /**
    * request
    * @var Request
    */
    public $tommaso;
    /**
     * cart
     * @var Crt
     */
    public $cart;
    /**
     * response
     * @var Response
     */
    public $response = null;
    protected $params = [];
    public function __construct($tommaso){
        // initialize request and respond objects
        $this->request  = new Request();
        $this->response  = new Response();
        $this->tommaso = $tommaso;
    }

    public function run()
    {
        $this->parseUrl();

        if(!self::isControllerValid($this->controller)){
            return $this->notFound();
        }

        $controllerName = $this->controller;

        if(!empty($this->controller)){
            if(!self::isMethodValid($controllerName, $this->method)){
                return $this->notFound();
            }
            if(!empty($this->method)){
                if(!self::areArgsValid($this->controller, $this->method, $this->params)){
                    return $this->notFound();

                }

                return $this->invoke($this->controller, $this->method, $this->params);

            } else{
                $this->method = "index";

                if(!method_exists($this->controller, $this->method)){
                    return $this->notFound();
                }
                return $this->invoke($this->controllerName, $this->method, $this->params);
            }



        } else {
            $this->method = "index";
            $controllerName = "Home";
            return $this->invoke($controllerName, $this->method, $this->params);
        }
    }
    /**
     * Parse the URL for the current request. Effectivly splits it, stores the controller
     * and the method for that controller.
     *
     * @return void
     */
    public function parseUrl()
    {
        $url = $this->request->query('url');
        if ($url) {
            // Explode a trimmed and sanitized URL by /

            $url = explode('/', filter_var(rtrim($url, '/'), FILTER_SANITIZE_URL));
            $this->controller = !empty($url[0]) ? ucwords($url[0]) : null;
            $this->method = !empty($url[1]) ? $url[1] : null;

            unset($url[0], $url[1]);

            $this->params = !empty($url)? array_values($url): [];
        }
    }

    private function invoke($controller, $method = "index", $args = []){

        $this->request->addParams(['controller' => $controller, 'action' => $method, 'args' => $args]);

        require_once APP . 'controllers/' . $controller . '.php';
        $test1 =  $controller;
        $test2 = 'App\controllers\\' . $test1;

        $this->controller = new $test2($this->tommaso, $this->request, $this->response);
        $result = $this->controller->startUpProcess();
        if ($result instanceof Response) {
            return $result->send();
        }

        $response = call_user_func_array([$this->controller, $method], $args);

        if ($response instanceof Response) {
            return $response->send();
        }

        return $this->response->send();
    }

    private static function areArgsValid($controllerArgs, $method, $args){
       require_once APP . 'controllers/' . $controllerArgs . '.php';
       $test1 =  $controllerArgs;
       $test2 = 'App\controllers\\' . $test1;
       $controllerArgs = new $test2($tommaso = null, $request = null, $response= null);

         $reflection = new ReflectionMethod ($controllerArgs, $method);
         $_args = $reflection->getNumberOfParameters();

         if($_args !== count($args)) { return false; }
         foreach($args as $arg){
             if(!preg_match('/\A[a-z0-9_-]+\z/i', $arg)){ return false; }
         }
         return true;
    }

    private static function isMethodValid($controllerMethod, $method){
        require_once APP . 'controllers/' . $controllerMethod . '.php';
        $test1 =  $controllerMethod;
        $test2 = 'App\controllers\\' . $test1;

        $controllerMethod = new $test2($tommaso = null, $request = null, $response= null);
        if(!empty($method)){
            if (!method_exists($controllerMethod, $method)  ){
                return false;
            }else { return true; }

         }else { return true; }

        return true;
    }


    private static function isControllerValid($controller){
        if(!empty($controller)){
            if (!file_exists(APP . 'controllers/' . ucfirst($controller) . '.php')){
                return false;
            }else {
             require_once APP . 'controllers/' . $controller . '.php';
                return true; }

        }else { return true; }

    }

    private function notFound(){
        $test1 =  "Errors";
        $test2 = 'App\controllers\\' . $test1;
        $test2 = new $test2;
        $response = (new $test2())->error(404);
        //var_dump($response);
/*
        if ($response instanceof Response) {
            return $response;
         }
*/
        return (new $test2())->error(404)->send();

    }
}
