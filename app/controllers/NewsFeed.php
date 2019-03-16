<?php
namespace App\controllers;
/**
 * The default home controller, called when no controller/method has been passed
 * to the application.
 */
use App\core\Controller as Controller;
use App\models\UserModel as UserModel;
use App\models\NewsfeedModel as NewsfeedModel;
use App\core\Config as Config;
use App\core\Session as Session;
use App\models\Permission as Permission;

class NewsFeed extends Controller
{
    public $setting = [];

    public $data = [];

    public $model;

    /**
     * The default controller method.
     *
     * @return void
     */
    public function __construct($tommaso, $request, $response){
        parent::__construct($request,$response);
        $this->tommaso = $tommaso;
    }
    public function beforeAction(){

        parent::beforeAction();

        Config::setJsConfig('csrfToken', Session::generateCsrfToken());
        Config::setJsConfig('curPage', "newsfeed");

        $action = $this->request->param('action');
        $actions = ['create', 'getUpdateForm', 'update', 'getById', 'delete'];
        $this->Security->requirePost($actions);

        switch($action){
            case "create":
                $this->Security->config("form", [ 'fields' => ['content']]);
                break;
            case "getUpdateForm":
                $this->Security->config("form", [ 'fields' => ['newsfeed_id']]);
                break;
            case "update":
                $this->Security->config("form", [ 'fields' => ['newsfeed_id', 'content']]);
                break;
            case "getById":
            case "delete":
                $this->Security->config("form", [ 'fields' => ['newsfeed_id']]);
                break;
        }
    }
    public function index()
    {

                $userModel = $this->model('UserModel');

                //echo Session::destroy();
                // check first if user is already logged in via session or cookie
                if($this->Auth->isLoggedIn()){
                    $this->data = ($userModel->getProfileInfo(Session::get("user_id"))!= null) ? $userModel->getProfileInfo(Session::get("user_id")) : [];
                }

                $newsfeedModel = $this->model('NewsfeedModel');
                $newsfeeds = $newsfeedModel->getAll();

                $this->setting = array_merge(
                	$newsfeeds,
                    array("setting" =>array("page_title" => "NewsFeed Page","url" => Config::get('URL'), "public_root" => PUBLIC_ROOT)),
                    array("generateCsrfToken" => Session::generateCsrfToken(), "sessionId" => Session::get("user_id"), "getUserRole" => Session::getUserRole(), "config" => json_encode(Config::getJsConfig())),
                    array("userLogged" => $this->data)
                );

                return $this->response->setContent($this->view->parseView('back/admin/newsfeed/index', $this->setting));

    }
    public function create(){

        $content  = $this->request->post("content");

        $newsFeedModel = $this->model('NewsfeedModel');

        $newsfeed = $newsFeedModel->create(Session::getUserId(), $content);

        if(!$newsfeed){

            Session::set('newsfeed-errors', $newsfeedModel->errors());
            return $this->redirector->root("newsFeed/index");

        }else{

            return $this->redirector->root("newsFeed/index");
        }
    }
    public function getUpdateForm(){
        $newsfeedModel = $this->model('NewsfeedModel');

        //$newsfeedId = Encryption::decryptIdWithDash($this->request->data("newsfeed_id"));
        $newsfeedId = $this->request->post("newsfeed_id");

        if(!$newsfeedModel->exists($newsfeedId)){
            return $this->error(404);
        }

        $newsfeed = $newsfeedModel->getById($newsfeedId);
      
        $html = $this->view->parseView('back/admin/newsfeed/updateform', array("newsfeed" => $newsfeed[0], "url" => Config::get('URL')));

        return $this->view->parseJson(array("data" => $html));

    }
    public function update(){

        // Remember? each news feed has an id that looks like this: feed-51b2cfa
        $newsfeedId = $this->request->post("newsfeed_id");
        $content    = $this->request->post("content");

        if(!$newsfeedModel->exists($newsfeedId)){
            return $this->error(404);
        }

        $newsfeed = $newsfeedModel->update($newsfeedId, $content);
        //if(!$newsfeed){
          //  $this->view->renderErrors($this->newsfeed->errors());
        //}else{
        $html = $this->view->parseView('back/admin/newsfeed/newsfeed', array("newsfeed" => $newsfeed, "url" => Config::get('URL')));
        var_dump($html);
 //           $html = $this->view->render(Config::get('VIEWS_PATH') . 'newsfeed/newsfeed.php', array("newsfeed" => $newsfeed));
   //         $this->view->renderJson(array("data" => $html));
        //}
    }    

    public function delete(){
        $newsfeedModel = $this->model('NewsfeedModel');
        $newsfeedId = $this->request->post("newsfeed_id");
        $newsfeedModel->deleteById($newsfeedId);

        return $this->view->parseJson(array("success" => true));
    }

    public function getById(){

        $newsfeedId = $this->request->post("newsfeed_id");
        $newsfeedModel = $this->model('NewsfeedModel');
        if(!$newsfeedModel->exists($newsfeedId)){
            return $this->error(404);
        }

        $newsfeed = $newsfeedModel->getById($newsfeedId);

        $html = $this->view->parseView('back/admin/layout/newsfeed', array("newsfeed" => $newsfeed));        

        return $this->view->parseJson(array("data" => $html));        
    }

    public function isAuthorized(){

        $action = $this->request->param('action');
        $role = Session::getUserRole();
        $resource = "newsfeed";

        // only for admins
        Permission::allow('admin', $resource, ['*']);

        // only for normal users
        Permission::allow('user', $resource, ['index', 'getById', 'create']);
        Permission::allow('user', $resource, ['update', 'delete', 'getUpdateForm'], 'owner');

        $newsfeedId = $this->request->post("newsfeed_id");
        if(!empty($newsfeedId)){
            //$newsfeedId = Encryption::decryptIdWithDash($newsfeedId);
            $newsfeedId = (int)$newsfeedId;
        }

        $config = [
            "user_id" => Session::getUserId(),
            "table" => "newsfeed",
            "id" => $newsfeedId];

        return Permission::check($role, $resource, $action, $config);
    }
}
