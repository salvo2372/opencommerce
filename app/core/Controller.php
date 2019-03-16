<?php
namespace App\core;
/**
 * The controller class.
 *
 * The base controller for all other controllers. Extend this for each
 * created controller and get access to it's wonderful functionality.
 */

use App\core\View as View;
use App\core\Request as Request;
use App\core\Response as Response;
use App\core\Redirector as Redirector;

use Cart\Cart as Cart;
//use Cart\Storage\SessionStore as SessionStore;
use App\core\I18n as I18n;
use App\utility\Utility as Utility;
use App\core\Shopping as Shopping;

class Controller
{
    /**
     * view
     *
     * @var View
     */
    protected $view;

    /**
     * request
     *
     * @var Request
     */
    public $request;
    /**
     * response
     *
     * @var Response
     */
    public $response;

     /**
      * data
      *
      * @var data
      */
    public $I18n;
    public $components = [];

    /**
     * redirector
     *
     * @var Redirector
     */
    public $redirector;

    public function __construct(Request $request = null, Response $response = null){

        $this->request      =  $request  !== null ? $request  : new Request();
        $this->response     =  $response !== null ? $response : new Response();
        //$this->sessionStore = new SessionStore($this->cartId, []);
        //$this->cart = new Cart($this->cartId,  $this->sessionStore);
        $this->i18n = new I18n();
        $this->redirector = new Redirector();
        $this->view = new View($this);
    }

    public function startUpProcess(){
        $this->initialize();

        $this->beforeAction();
        $result = $this->triggerComponents();
        if($result instanceof Response){
            return $result;
        }
    }

    public function initialize(){
         $this->loadComponents([
             'Auth' => [
                     'authenticate' => ['User'],
                     'authorize'    => ['Controller']
                 ],
             'Security'
         ]);
    }
    public function loadComponents(array $components){

        if(!empty($components)){

            $components = Utility::normalize($components);

            foreach($components as $component => $config){
                if(!in_array($component, $this->components, true)){
                    $this->components[] = $component;
                }
                $class = $component . "Component";

                require_once APP . 'core/components/' . $class . '.php';
                $test1 =  $class;
                $test2 = 'App\core\components\\' . $test1;

                $this->{$component} = empty($config)? new $test2($this): new $test2($this, $config);
            }
        }
    }
    private function triggerComponents(){

        $components = ['Auth', 'Security'];
        foreach($components as $key => $component){
            if(!in_array($component, $this->components)){
                unset($components[$key]);
            }
        }

        $result = null;
        foreach($components as $component){

            if($component === "Auth"){

                $authenticate = $this->Auth->config("authenticate");

                if(!empty($authenticate)){
                    if(!$this->Auth->authenticate()){
                        $result = $this->Auth->unauthenticated();
                    }
                }

                // delay checking authorize till after the loop
                $authorize = $this->Auth->config("authorize");

            }else{
                $result = $this->{$component}->startup();
            }

            if($result instanceof Response){ return $result; }
        }

        // authorize
        if(!empty($authorize)){
            if(!$this->Auth->authorize()){
                $result = $this->Auth->unauthorized();
            }
        }

        return $result;
    }
    public function beforeAction(){}

    public function model($model)
    {
        if (file_exists(APP . 'models/' . ucfirst($model) . '.php')) {
            require_once APP . 'models/' . ucfirst($model) . '.php';
            $test1 =  $model;  //$test1 =  $this->controller;
            $test2 = 'App\models\\' . $test1;
            $model = new $test2();
            return $model;
        }
        return null;
    }

    public function error($code){

        $errors = [
            404 => "NotFound",
            401 => "unauthenticated",
            403 => "unauthorized",
            400 => "badrequest",
            500 => "system"
        ];

        //$this->response->clearBuffer();
        $test1 =  "Errors";
        $test2 = 'App\controllers\\' . $test1;
        if(!isset($errors[$code]) || !method_exists($test2, $errors[$code])){
            $code = 500;
        }

        $action = isset($errors[$code])? $errors[$code]: "System";
        $this->response->setStatusCode($code);


        (new $test2($this->request, $this->response))->{$action}();

        return $this->response;

    }

}
