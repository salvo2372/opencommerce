<?php
namespace App\controllers;
/**
 * The default home controller, called when no controller/method has been passed
 * to the application.
 */
use App\core\Controller as Controller;
use App\core\Config as Config;
use App\models\UserModel as UserModel;
use App\core\Session as Session;
use App\core\components\AuthComponent as Auth;

class Errors extends Controller{

    private $data = [];
    /**
     * Initialization method.
     *
     */
    public function initialize(){

        $this->loadComponents([
            'Auth',
            'Security'
        ]);
    }

    public function NotFound(){
        Config::setJsConfig('curPage', "error");
        $userModel = $this->model('UserModel');
        if(Auth::isLoggedIn()){
              
            $this->data = (UserModel::getProfileInfo(Session::get("user_id"))!= null) ? UserModel::getProfileInfo(Session::get("user_id")) : [];
        }
        $this->setting = array_merge(
            array("setting" =>array("page_title" => "Page Not Found","url" => Config::get('URL'), "public_root" => PUBLIC_ROOT)), 
            array("generateCsrfToken" => Session::generateCsrfToken(), "getUserRole" => Session::getUserRole()), 
            array("userLogged" => $this->data)
        ); 

        return $this->response->setContent($this->view->parseView('front/error/index', $this->setting)); 
       
    }

    public function Unauthenticated(){
        Config::setJsConfig('curPage', "error");
        $userModel = $this->model('UserModel');
        if(Auth::isLoggedIn()){
              
            $this->data = (UserModel::getProfileInfo(Session::get("user_id"))!= null) ? UserModel::getProfileInfo(Session::get("user_id")) : [];
        }
        $this->setting = array_merge(
            array("setting" =>array("page_title" => "User not authenticated","url" => Config::get('URL'), "public_root" => PUBLIC_ROOT)), 
            array("generateCsrfToken" => Session::generateCsrfToken(), "getUserRole" => Session::getUserRole()), 
            array("userLogged" => $this->data)
        ); 

        return $this->response->setContent($this->view->parseView('front/error/index', $this->setting)); 
    }

    public function Unauthorized(){
        Config::setJsConfig('curPage', "error");
        $userModel = $this->model('UserModel');
        if(Auth::isLoggedIn()){
              
            $this->data = (UserModel::getProfileInfo(Session::get("user_id"))!= null) ? UserModel::getProfileInfo(Session::get("user_id")) : [];
        }
        $this->setting = array_merge(
            array("setting" =>array("page_title" => "User not authorized","url" => Config::get('URL'), "public_root" => PUBLIC_ROOT)), 
            array("generateCsrfToken" => Session::generateCsrfToken(), "getUserRole" => Session::getUserRole()), 
            array("userLogged" => $this->data)
        ); 

        return $this->response->setContent($this->view->parseView('front/error/index', $this->setting)); 
    }

    public function BadRequest(){
        Config::setJsConfig('curPage', "error");
        $userModel = $this->model('UserModel');
        if(Auth::isLoggedIn()){
              
            $this->data = (UserModel::getProfileInfo(Session::get("user_id"))!= null) ? UserModel::getProfileInfo(Session::get("user_id")) : [];
        }
        $this->setting = array_merge(
            array("setting" =>array("page_title" => "Bad Request 400","url" => Config::get('URL'), "public_root" => PUBLIC_ROOT)), 
            array("generateCsrfToken" => Session::generateCsrfToken(), "getUserRole" => Session::getUserRole()), 
            array("userLogged" => $this->data)
        ); 

        return $this->response->setContent($this->view->parseView('front/error/index', $this->setting)); 
    }

    public function System(){
        Config::setJsConfig('curPage', "error");
        $userModel = $this->model('UserModel');
        if(Auth::isLoggedIn()){
              
            $this->data = (UserModel::getProfileInfo(Session::get("user_id"))!= null) ? UserModel::getProfileInfo(Session::get("user_id")) : [];
        }
        $this->setting = array_merge(
            array("setting" =>array("page_title" => "System Error 500","url" => Config::get('URL'), "public_root" => PUBLIC_ROOT)), 
            array("generateCsrfToken" => Session::generateCsrfToken(), "getUserRole" => Session::getUserRole()), 
            array("userLogged" => $this->data)
        ); 

        return $this->response->setContent($this->view->parseView('front/error/index', $this->setting)); 
    }
    public function isAuthorized(){
        return true;
    }     
}
