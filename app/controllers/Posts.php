<?php
namespace App\controllers;
/**
 * The default home controller, called when no controller/method has been passed
 * to the application.
 */
use App\core\Controller as Controller;
use App\models\UserModel as UserModel;
use App\models\PostModel as PostModel;
use App\models\Permission as Permission;
use App\core\Config as Config;
use App\core\Session as Session;


class Posts extends Controller
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
        Config::setJsConfig('curPage', "posts");
        $action  = $this->request->param('action');
        $actions = ['create', 'update'];
        $this->Security->requirePost($actions);

        switch($action){
            case "create":
                $this->Security->config("form", [ 'fields' => ['title', 'content']]);
                break;
            case "update":
                $this->Security->config("form", [ 'fields' => ['post_id', 'title', 'content']]);
                break;
            case "delete":
                $this->Security->config("validateCsrfToken", true);
                $this->Security->config("form", [ 'fields' => ['post_id']]);
                break;
        }

    }
    public function index()
    {

        $userModel = $this->model('UserModel');

        //echo Session::destroy();
        // check first if user is already logged in via session or cookie
        if ($this->Auth->isLoggedIn()) {
            $this->data = ($userModel->getProfileInfo(Session::get("user_id"))!= null) ? $userModel->getProfileInfo(Session::get("user_id")) : [];
        }

        $post_success = (Session::get('posts-success')!= null)? SESSION::get('posts-success'):null;

        $pageNum  = $this->request->query("page");

        $postModel = $this->model('PostModel');
        $postData = $postModel->getAll(empty($pageNum)? 1: $pageNum);

      
        $pagination = $postData["pagination"];
        $emptyPagination = empty($pagination);
        $totalPagesPagination = $pagination->totalPages();
        $currentPagePagination = $pagination->currentPage;
        $hasPreviousPage = $pagination->hasPreviousPage();
        $hasNextPage =  $pagination->hasNextPage();
      
        $this->setting = array_merge(
        array("emptyPagination" => $emptyPagination, "totalPagesPagination" => $totalPagesPagination, "hasPreviousPage" => $hasPreviousPage, "currentPagePagination" => $currentPagePagination, "hasNextPage" => $hasNextPage, "linkPagination" => "posts"),
            array("posts" => $postData["posts"], "post_success" => $post_success, "pagination" => $postData["pagination"]),
            array("setting" =>array("page_title" => "Post Page","url" => Config::get('URL'), "public_root" => PUBLIC_ROOT)),
            array("generateCsrfToken" => Session::generateCsrfToken(), "getUserRole" => Session::getUserRole(), "config" => json_encode(Config::getJsConfig())),
            array("userLogged" => $this->data)
        );

        return $this->response->setContent($this->view->parseView('back/admin/posts/index', $this->setting));

    }

    public function view($postId = 0){

        $postId = $postId;
        $userModel = $this->model('UserModel');

        if ($this->Auth->isLoggedIn()) {
            $this->data = ($userModel->getProfileInfo(Session::get("user_id"))!= null) ? $userModel->getProfileInfo(Session::get("user_id")) : [];
        }

        $postModel = $this->model('PostModel');
        if(!$postModel->exists($postId)){
            return $this->error(404);
        }

        //Config::setJsConfig('postId', Encryption::encryptId($postId));

        Config::setJsConfig('curPage', ["posts", "comments"]);

        $posts = $postModel->getById($postId);
        $action  = $this->request->query('action');

            $this->setting = array_merge(
                array("posts" => $posts, "action" => $action, "postId" => $postId),
                array("setting" =>array( "locale" => Session::getLang(), "userLang" => Config::get('LANGUAGE'),"url" => Config::get('URL'), "public_root" => PUBLIC_ROOT)),
                array("generateCsrfToken" => Session::generateCsrfToken(), "getUserRole" => Session::getUserRole(), "config" => json_encode(Config::getJsConfig())),
                array("userLogged" => $this->data)
        );

        return $this->response->setContent($this->view->parseView('back/admin/posts/viewpost', $this->setting));

    }

    public function create(){

        $postModel = $this->model('PostModel');

        $title     = $this->request->post("title");
        $content   = $this->request->post("content");

        $result = $postModel->create(Session::getUserId(), $title, $content);

        if(!$result){
            Session::set('posts-errors', "Errore Post Creation");
        }else{
            Session::set('posts-success', "Post has been created");
        }

        return $this->redirector->root("posts/newPost");
    }
    public function update(){
        $postId  = $this->request->post("post_id");
        $title   = $this->request->post("title");
        $content = $this->request->post("content");

        //$postId = Encryption::decryptId($postId);

        $postModel = $this->model('PostModel');

        $result = $postModel->update($postId,$title,$content);
        if(!$result){

            Session::set('posts-errors', $postModel->errors());
            return $this->redirector->root("Posts/View/" . $postId . "?action=update");

        }else{
            return $this->redirector->root("Posts/View/" . $postId);
        }
    }
    public function newpost(){
        $userModel = $this->model('UserModel');

        if ($this->Auth->isLoggedIn()) {
            $this->data = ($userModel->getProfileInfo(Session::get("user_id"))!= null) ? $userModel->getProfileInfo(Session::get("user_id")) : [];
        }
        if (!empty(Session::get('posts-success')))
        {
            $postsSuccess = Session::getAndDestroy('posts-success');

        } else {$postsSuccess = null;}
        $this->setting = array_merge(
                  array("postsSuccess" => $postsSuccess),
                  array("setting" =>array( "locale" => Session::getLang(), "userLang" => Config::get('LANGUAGE'),"url" => Config::get('URL'), "public_root" => PUBLIC_ROOT)),
                  array("generateCsrfToken" => Session::generateCsrfToken(), "getUserRole" => Session::getUserRole(), "config" => json_encode(Config::getJsConfig())),
                  array("userLogged" => $this->data)
        );

        return $this->response->setContent($this->view->parseView('back/admin/posts/newpost', $this->setting));

    }
    public function isAuthorized(){
        $action = $this->request->param('action');
        $role = Session::getUserRole();
        $resource = "posts";

        // only for admins
        Permission::allow('admin', $resource, ['*']);

        // only for normal users
        Permission::allow('user', $resource, ['index', 'view', 'newPost', 'create']);
        Permission::allow('user', $resource, ['update', 'delete'], 'owner');

        $postId  = ($action === "delete")? $this->request->param("args")[0]: $this->request->post("post_id");
        if(!empty($postId)){
            //$postId = Encryption::decryptId($postId);
            $postId = $postId;
        }

        $config = [
            "user_id" => Session::getUserId(),
            "table" => "posts",
            "id" => $postId
        ];

        return Permission::check($role, $resource, $action, $config);
    }
}
