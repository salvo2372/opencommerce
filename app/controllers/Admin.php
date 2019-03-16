<?php
namespace App\controllers;
/**
 * The default home controller, called when no controller/method has been passed
 * to the application.
 */
use App\core\Controller as Controller;

use App\models\AdminModel as AdminModel;
use App\core\Config as Config;
use App\core\Session as Session;

class Admin extends User
{
    public $data = [];

    private $setting = [];

    private $info = [];
    /**
     * The default controller method.
     *
     * @return void
     */
    public function beforeAction(){

        parent::beforeAction();

        $action = $this->request->param('action');
        $actions = ['getUsers', 'updateUserInfo', 'deleteUser'];

        // define the action methods that needs to be triggered only through POST & Ajax request.
        $this->Security->requireAjax($actions);
        $this->Security->requirePost($actions);

        // You need to explicitly define the form fields that you expect to be returned in POST request,
        // if form field wasn't defined, this will detected as form tampering attempt.
        switch($action){
            case "getUsers":
                $this->Security->config("form", [ 'fields' => ['name', 'email', 'role', 'page']]);
                break;
            case "updateUserInfo":
                $this->Security->config("form", [ 'fields' => ['user_id', 'name', 'password', 'role']]);
                break;
            case "deleteUser":
                $this->Security->config("form", [ 'fields' => ['user_id']]);
                break;
            case "updateBackup":
            case "restoreBackup":
                $this->Security->config("validateCsrfToken", true);
                break;
        }

    }
    public function users()
    {
                Config::setJsConfig('curPage', "users");

                $adminModel = $this->model('AdminModel');
                //echo Session::destroy();
                // check first if user is already logged in via session or cookie
                if($this->Auth->isLoggedIn()){

                    $this->data = ($adminModel->getProfileInfo(Session::get("user_id"))!= null) ? $adminModel->getProfileInfo(Session::get("user_id")) : [];
                }

                $this->info = $adminModel->dashboard();
                $stats = $this->info["stats"];
                $updates = $this->info["updates"];

                $usersData = $adminModel->getUsers();

                $this->setting = array_merge(
                    $usersData,
                    array("updates" => $updates,  "stats" => $stats),
                    array("setting" =>array("page_title" => "User Page","url" => Config::get('URL'), "public_root" => PUBLIC_ROOT)),
                    array("generateCsrfToken" => Session::generateCsrfToken(), "getUserRole" => Session::getUserRole(), "config" => json_encode(Config::getJsConfig())),
                    array("userLogged" => $this->data)
                );

                return $this->response->setContent($this->view->parseView('back/admin/users/index', $this->setting));

    }
    public function viewUser($userId = 0){
                $adminModel = $this->model('AdminModel');
                Config::setJsConfig('curPage', "users");
                Config::setJsConfig('userId', $userId);
                $userInfo = ($adminModel->getProfileInfo($userId)!= null) ? $adminModel->getProfileInfo($userId) : [];

                if($this->Auth->isLoggedIn()){
                    $this->data = ($adminModel->getProfileInfo(Session::get("user_id"))!= null) ? $adminModel->getProfileInfo(Session::get("user_id")) : [];
                }

                $this->setting = array_merge(
                    array("info" => $userInfo),
                    array("setting" =>array("page_title" => "User Page","url" => Config::get('URL'), "public_root" => PUBLIC_ROOT)),
                    array("generateCsrfToken" => Session::generateCsrfToken(), "getUserRole" => Session::getUserRole(), "config" => json_encode(Config::getJsConfig())),
                    array("userLogged" => $this->data)
                );


                return $this->response->setContent($this->view->parseView('back/admin/users/viewuser', $this->setting));

    }
    /**
     * delete a user
     *
     */
    public function deleteUser(){

        $userId = $this->request->post("user_id");

        $adminModel = $this->model('AdminModel');
        $userModel =  $this->model('UserModel');

        if(!$userModel->exists($userId)){
            return $this->error(404);
        }

        $adminModel->deleteUser(Session::getUserId(), $userId);
        $this->view->parseJson(array("success" => true));

    }

    public function getUsers(){

        $name     = $this->request->post("name");
        $email    = $this->request->post("email");
        $role     = $this->request->post("role");
        $pageNum  = $this->request->post("page");

        $adminModel = $this->model('AdminModel');
        $usersData = $adminModel->getUsers($name, $email, $role, $pageNum);

        $adminModel = $this->model('AdminModel');
        $usersData = $adminModel->getUsers($name, $email, $role, $pageNum);

        if(!$usersData){
            $this->view->parseErrors($this->admin->errors());
        } else{
            $usersHTML = $this->view->parseView('back/admin/layout/users', array("users" => $usersData["users"]));
            //$usersHTML       = $this->view->render(Config::get('ADMIN_VIEWS_PATH') . 'users/users.php', array("users" => $usersData["users"]));
            //$paginationHTML  = $this->view->render(Config::get('VIEWS_PATH') . 'pagination/default.php', array("pagination" => $usersData["pagination"]));
            $this->view->parseJson(array("data" => ["users" => $usersHTML]));
            $this->response->send();
        }

    }
    public function updateUserInfo(){
      $adminModel = $this->model('AdminModel');
      $userModel =  $this->model('UserModel');
        //$userId     = Encryption::decryptId($this->request->data("user_id"));
        $userId     = $this->request->post("user_id");
        $name       = $this->request->post("name");
        $password   = $this->request->post("password");
        $role       = $this->request->post("role");

        if(!$userModel->exists($userId)){
            return $this->error(404);
        }

        $result = $adminModel->updateUserInfo($userId, Session::getUserId(), $name, $password, $role);


        if(!$result){
            $this->view->parseErrors($adminModel->errors());
        }else{
            $this->view->parseSuccess("Profile has been updated.");
        }
        $this->response->send();
    }
    public function isAuthorized(){

        $role = Session::getUserRole();
        if(isset($role) && $role === "admin"){
            return true;
        }
        return false;
    }
}
