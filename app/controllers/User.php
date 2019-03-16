<?php
namespace App\controllers;
/**
 * The default home controller, called when no controller/method has been passed
 * to the application.
 */
use App\core\Controller as Controller;
use App\models\UserModel as UserModel;
use App\core\Config as Config;
use App\core\Session as Session;


class User extends Controller
{
    private $dashbord;

    public $data = [];

    private $setting = [];

    public $model;

    /**
     * The default controller method.
     *
     * @return void
     */
    public function __construct($tommaso, $request, $response){
        parent::__construct($this->request,$this->response);
        $this->tommaso = $tommaso;
    }
    public function beforeAction(){

        parent::beforeAction();

        Config::setJsConfig('csrfToken', Session::generateCsrfToken());
        Config::setJsConfig('curPage', "users");

    }
    public function index()
    {
                Config::setJsConfig('curPage', "dashboard");
                $userModel = $this->model('UserModel');

                //echo Session::destroy();
                // check first if user is already logged in via session or cookie
                if($this->Auth->isLoggedIn()){

                    $this->data = (UserModel::getProfileInfo(Session::get("user_id"))!= null) ? UserModel::getProfileInfo(Session::get("user_id")) : [];
                }

                $dashboard = UserModel::dashboard();
                $stats = $dashboard["stats"];
                $updates = $dashboard["updates"];

                $this->setting = array_merge(
                  array("setting" =>array("page_title" => "Dashboard Page","url" => Config::get('URL'), "public_root" => PUBLIC_ROOT)),
                  array("generateCsrfToken" => Session::generateCsrfToken(), "getUserRole" => Session::getUserRole(), "stats" => $stats, "updates" => $updates, "config" => json_encode(Config::getJsConfig())),
                  array("userLogged" => $this->data)
                );
                //var_dump($_SESSION["user_id"]);

                return $this->response->setContent($this->view->parseView('back/admin/dashboard/index', $this->setting));


    }
    public function profile(){
        Config::setJsConfig('curPage', "dashboard");
        $userModel = $this->model('UserModel');
        if($this->Auth->isLoggedIn()){
            $this->data = ($userModel->getProfileInfo(Session::get("user_id"))!= null) ? $userModel->getProfileInfo(Session::get("user_id")) : [];
        }

        $this->setting = array_merge(
            array("setting" =>array("page_title" => "Post page","url" => Config::get('URL'), "public_root" => PUBLIC_ROOT)),
            array("generateCsrfToken" => Session::generateCsrfToken(), "getUserRole" => Session::getUserRole(), "config" => json_encode(Config::getJsConfig())),
            array("userLogged" => $this->data)
        );

        return $this->response->setContent($this->view->parseView('back/admin/users/profile', $this->setting));

    }
    public function updateProfilePicture(){

        $fileData   = $this->request->file("file");     
        $userModel  = $this->model('UserModel');
        $image      = $userModel->updateProfilePicture(Session::getUserId(), $fileData);

        if(!$image){
            Session::set('profile-picture-errors', $userModel->errors());
        }

        return $this->redirector->root("user/profile");
    }
    
    public function isAuthorized(){
        return true;
    }
}
