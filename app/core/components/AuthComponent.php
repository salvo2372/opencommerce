<?php

namespace App\core\components;

use App\core\Session as Session;
use App\core\Component as Component;
use App\utility\Utility as Utility;
use App\models\LoginModel as LoginModel;

class AuthComponent extends Component{
    protected $config = [
        'authenticate' => [],
        'authorize' => []
    ];
    public function startup(){
         // authenticate
         if(!empty($this->config["authenticate"])){
             if(!$this->authenticate()){
                 return $this->unauthenticated();
             }
         }
         // authorize
         if(!empty($this->config["authorize"])){
             if(!$this->authorize()){
                 return $this->unauthorized();
             }
         }
     }
    public function unauthenticated(){

        //$this->controller->user->logOut(Session::getUserId());
        $loginModel = $this->controller->model("loginModel");
        //$loginModel->logOut(Session::getUserId());
        if($this->request->isAjax()) {
            return $this->controller->error(401);
        }else{
            //var_dump($this->controller->request->uri());
            //return $this->controller->error(401);
            //$redirectTo = $this->controller->request->isGet()? "index" : "";
             //return $this->controller->redirector->to($redirectTo);
        }
    }
     public function authenticate(){
         return $this->check($this->config["authenticate"], "authenticate");
     }
    public function authorize(){
        return $this->check($this->config["authorize"], "authorize");
    }
    public function unauthorized(){
        return $this->controller->error(403);
    }
    public function isLoggedIn(){

        if(Session::getIsLoggedIn() === true){
            return true;
        }

        return false;
    }
     private function check($config, $type){

         if (empty($config)) {
             //throw new Exception($type . ' methods arent initialized yet in config');
         }

         $auth = Utility::normalize($config);

         foreach($auth as $method => $config){

             $method = "_" . ucfirst($method) . ucfirst($type);
             //var_dump(__CLASS__);var_dump($method);

             if (!method_exists(__CLASS__, $method)) {
                 //throw new Exception('Auth Method doesnt exists: ' . $method);
             	echo "Errore";
             }
             //var_dump($this->{$method}($config));
             //var_dump($method);var_dump($config);
             if($this->{$method}($config) === false){
                 return false;
             }

         }

        return true;
    }
    private function _ControllerAuthorize($config){

        if (!method_exists($this->controller, 'isAuthorized')) {
           // throw new Exception(sprintf('%s does not implement an isAuthorized() method.', get_class($this->controller)));
        	sprintf('%s does not implement an isAuthorized() method.', get_class($this->controller));
        }
        return (bool)$this->controller->isAuthorized();
    }
    private function _UserAuthenticate($config){
    	/*
        if($this->concurentSession()){
            return false;
        }
        */

        if(!$this->isLoggedIn()){
            return false;
        }

        return true;
    }
}
