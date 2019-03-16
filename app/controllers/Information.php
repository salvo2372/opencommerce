<?php
namespace App\controllers;
/**
 * The default home controller, called when no controller/method has been passed
 * to the application.
 */
use App\core\Controller as Controller;
use App\models\UserModel as UserModel;
use App\models\InformationModel as InformationModel;
use App\core\Config as Config;
use App\core\Session as Session;
use App\core\Encryption as Encryption;
use App\models\Permission as Permission;

use Cart\CartItem as CartItem;
class Information extends Controller
{
    /**
     * The default controller method.
     *
     * @return void
     */
    public $setting = [];
    public $tommaso;
    public $data = [];

    public function __construct($tommaso, $request,$response){
        parent::__construct($request,$response);
        $this->tommaso = $tommaso;
    }

    public function initialize(){
        $this->loadComponents([
            'Auth' => [
                    'authorize'    => ['Controller']
                ],
            'Security'
        ]);
    }

    public function beforeAction(){

        parent::beforeAction();

        Config::setJsConfig('csrfToken', Session::generateCsrfToken());
        Config::setJsConfig('curPage', "information");
        $action  = $this->request->param('action');
        $actions = ['create', 'update'];
        $this->Security->requirePost($actions);

        switch($action){
            case "create":
                $this->Security->config("form", [ 'fields' => ['title', 'summary']]);
                break;
            case "update":
                $this->Security->config("form", [ 'fields' => ['information_id', 'title', 'informationSummary','informationContent','informationMetaTitle','informationDescription']]);
                break;
            case "delete":
                $this->Security->config("validateCsrfToken", true);
                $this->Security->config("form", [ 'fields' => ['information_id']]);
                break;
        }

    }

    public function index($informationId = 0){
        $this->i18n->setCachePath(APP . 'langcache/front/information/index/');
        $this->i18n->setFilePath(APP . 'templates/lang/front/information/index/lang_{LANGUAGE}.ini'); // language file path
        $this->i18n->setFallbackLang(Session::getLang());
        $this->i18n->init();

        $userModel = $this->model('UserModel');
        Config::setJsConfig('curMenu', "services");
        if($this->Auth->isLoggedIn()){
            $this->data = ($userModel->getProfileInfo(Session::get("user_id"))!= null) ? $userModel->getProfileInfo(Session::get("user_id")) : [];
        }

        $informationModel = $this->model('informationModel');
        $itemsMenu = $informationModel->getMenu();

        $informationId = Encryption::decryNameId($informationId, "information");

        if(!$informationModel->exists($informationId)){
            return $this->error(404);
        }

        Config::setJsConfig('curPage', "information");
        //Config::setJsConfig('informationId', Encryption::encryptId($informationId));
        Config::setJsConfig('informationId', Encryption::encryNameId($informationId,"information"));

        //$pagemeta = $informationModel->pageMeta($informationId);
        $information = $informationModel->getById($informationId);
        $pagemeta = $informationModel->pageMeta($informationId);
        $action  = $this->request->query('action');
        $page = array("home" => L("page_home"), "service" => L("page_service"), "category" => L("page_category"), "shop" => L("page_shop"), "cart" => L("page_cart"), "contact" => L("page_contact"));

        $this->setting = array_merge(
                  array("information" => $information, "action" => $action, "pagemeta" => $pagemeta, "informationId" => Encryption::encryNameId($informationId,"information")),
                  array("setting" =>array( "locale" => Session::getLang(), "language" => Session::getLanguage(), "languages" => Config::get('LANGUAGES'),"url" => Config::get('URL'), "public_root" => PUBLIC_ROOT)),
                  array("generateCsrfToken" => Session::generateCsrfToken(), "getUserRole" => Session::getUserRole(), "config" => json_encode(Config::getJsConfig())),
                  array("userLogged" => $this->data, "itemsMenu" => $itemsMenu, "page" => $page)
        );

    return $this->response->setContent($this->view->parseView('front/information/index', $this->setting));

    }

    public function services()
    {
        $userModel = $this->model('UserModel');
        $this->i18n->setCachePath(APP . 'langcache/front/information/services/');
        $this->i18n->setFilePath(APP . 'templates/lang/front/information/services/lang_{LANGUAGE}.ini'); // language file path
        $this->i18n->setFallbackLang(Session::getLang());
        $this->i18n->init();

        if($this->Auth->isLoggedIn()){
            $this->data = ($userModel->getProfileInfo(Session::get("user_id"))!= null) ? $userModel->getProfileInfo(Session::get("user_id")) : [];
        }
        Config::setJsConfig('curMenu', "services");        
        $informationModel = $this->model('InformationModel');
        $itemsMenu = $informationModel->getMenu();

        $informations = $informationModel->getInformation();
        $pagemeta = array("meta_title" => L("pagemeta_meta_title"), "meta_description" => L("pagemeta_meta_description"));
        $page = array("home" => L("page_home"), "service" => L("page_service"), "category" => L("page_category"), "shop" => L("page_shop"), "cart" => L("page_cart"), "contact" => L("page_contact"));
        $action  = $this->request->query('action');

        $this->setting = array_merge(
            array("informations" =>$informations, "action" => $action, "pagemeta" => $pagemeta),
            array("lang" =>array("page_title" => L("page_title"))),
                  array("setting" =>array( "locale" => Session::getLang(), "language" => Session::getLanguage(), "languages" => Config::get('LANGUAGES'),"url" => Config::get('URL'), "public_root" => PUBLIC_ROOT)),
            array("generateCsrfToken" => Session::generateCsrfToken(), "getUserRole" => Session::getUserRole(), "config" => json_encode(Config::getJsConfig())),
            array("userLogged" => $this->data, "itemsMenu" => $itemsMenu, "page" => $page)
        );

        return $this->response->setContent($this->view->parseView('front/information/services', $this->setting));
    }

    public function newinformation(){
        $userModel = $this->model('UserModel');

        if ($this->Auth->isLoggedIn()) {
            $this->data = ($userModel->getProfileInfo(Session::get("user_id"))!= null) ? $userModel->getProfileInfo(Session::get("user_id")) : [];
        }
        if (!empty(Session::get('information-success')))
        {
            $informationSuccess = Session::getAndDestroy('information-success');

        } else {$informationSuccess = null;}
        $this->setting = array_merge(
                  array("informationSuccess" => $informationSuccess),
                  array("setting" =>array( "locale" => Session::getLang(), "languages" => Config::get('LANGUAGES'),"url" => Config::get('URL'), "public_root" => PUBLIC_ROOT)),
                  array("generateCsrfToken" => Session::generateCsrfToken(), "getUserRole" => Session::getUserRole(), "config" => json_encode(Config::getJsConfig())),
                  array("userLogged" => $this->data)
        );

        return $this->response->setContent($this->view->parseView('back/admin/information/newinformation', $this->setting));

    }

    public function viewall(){

                Config::setJsConfig('curPage', 'information');

                $userModel = $this->model('UserModel');
                $this->i18n->setCachePath(APP . 'langcache/back/information/viewall/');
                $this->i18n->setFilePath(APP . 'templates/lang/back/information/viewall/lang_{LANGUAGE}.ini'); // language file path
                $this->i18n->setFallbackLang(Session::getLang());
                $this->i18n->init();
                //echo Session::destroy();
                // check first if user is already logged in via session or cookie
                if($this->Auth->isLoggedIn()){

                    $this->data = ($userModel->getProfileInfo(Session::get("user_id"))!= null) ? $userModel->getProfileInfo(Session::get("user_id")) : [];
                }
                $informationModel = $this->model('InformationModel');
                $informations = $informationModel->getInformation();
                //$informations = array("informations" => $informations);

                $this->setting = array_merge(
                  array("informations" => $informations),
                  array("page_title" => L("page_title")),
                array("setting" =>array( "locale" => Session::getLang(), "languages" => Config::get('LANGUAGES'),"url" => Config::get('URL'), "public_root" => PUBLIC_ROOT)),
                  array("generateCsrfToken" => Session::generateCsrfToken(), "getUserRole" => Session::getUserRole(), "config" => json_encode(Config::getJsConfig())),
                  array("userLogged" => $this->data)
                );

                return $this->response->setContent($this->view->parseView('back/admin/information/viewall', $this->setting));

    }

    public function view($informationId = 0){

        $informationId = Encryption::decryNameId($informationId, "information");
        $userModel = $this->model('UserModel');

        if ($this->Auth->isLoggedIn()) {
            $this->data = ($userModel->getProfileInfo(Session::get("user_id"))!= null) ? $userModel->getProfileInfo(Session::get("user_id")) : [];
        }

        $informationModel = $this->model('InformationModel');
        if(!$informationModel->exists($informationId)){
            return $this->error(404);
        }

        Config::setJsConfig('curPage', 'information');
        Config::setJsConfig('informationId', Encryption::encryNameId($informationId,"information"));
        $information = $informationModel->getById($informationId);
        $action  = $this->request->query('action');

            $this->setting = array_merge(
                array("information" => $information, "action" => $action, "informationId" => $informationId),
                array("setting" =>array( "locale" => Session::getLang(), "languages" => Config::get('LANGUAGES'),"url" => Config::get('URL'), "public_root" => PUBLIC_ROOT)),
                array("generateCsrfToken" => Session::generateCsrfToken(), "getUserRole" => Session::getUserRole(), "config" => json_encode(Config::getJsConfig())),
                array("userLogged" => $this->data)
        );

        return $this->response->setContent($this->view->parseView('back/admin/information/viewinformation', $this->setting));

    }

    public function create(){

        $informationModel = $this->model('InformationModel');

        $title     = $this->request->post("title");
        $summary   = $this->request->post("summary");

        $result = $informationModel->create(Session::getUserId(), $title, $summary);

        if(!$result){
            Session::set('information-errors', "Errore Information Creation");
        }else{
            Session::set('information-success', "Information has been created");
        }

        return $this->redirector->root("information/newinformation");
    }

    public function update(){
        $informationId  = $this->request->post("information_id");
        $title   = $this->request->post("title");
        $summary = $this->request->post("informationSummary");
        $content = $this->request->post("informationContent");
        $metaTitle = $this->request->post("informationMetaTitle");
        $metaDescription = $this->request->post("informationMetaDescription");
        $informationId = Encryption::decryNameId($informationId,"information");
        $informationModel = $this->model('InformationModel');
        $result = $informationModel->update($informationId,$title,$summary,$content,$metaTitle,$metaDescription);
        $informationId = Encryption::encryNameId($informationId,"information");
        if(!$result){
            Session::set('information-errors', $informationModel->errors());
            return $this->redirector->root("information/view/" . $informationId . "?action=update");
        }else{
            return $this->redirector->root("information/view/" . $informationId);
        }
    }

    public function isAuthorized(){
      $action = $this->request->param('action');
      $role = Session::getUserRole();
      $resource = "information";
      //var_dump($action);
      if(!isset($role) && in_array($action,["index","services"],true)){
          return true;
      }
      // only for admins
      Permission::allow('admin', $resource, ['*']);
      // only for normal users
      Permission::allow('user', $resource, ['index', 'view', 'newinformation', 'create']);
      Permission::allow('user', $resource, ['update', 'delete'], 'owner');

      $productId  = ($action === "delete")? $this->request->param("args")[0]: $this->request->post("information_id");
        if(!empty($categoryId)){
            $productId = Encryption::decryNameId($informationId,"information");
        }

        $config = [
            "user_id" => Session::getUserId(),
            "table" => "information",
            "id" => $productId
        ];

        return Permission::check($role, $resource, $action, $config);
    }

  }
