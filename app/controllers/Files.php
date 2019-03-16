<?php
namespace App\controllers;
use App\models\UserModel as UserModel;
use App\models\Permission as Permission;
use App\core\Config as Config;
use App\core\Session as Session;
/**
 * The default home controller, called when no controller/method has been passed
 * to the application.
 */
use App\core\Controller as Controller;

use App\core\components\AuthComponent as Auth;
class Files extends Controller
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
      Config::setJsConfig('curPage', "files");
      $action = $this->request->param('action');
      $actions = ['create', 'delete'];
      $this->Security->requireAjax($actions);
      $this->Security->requirePost($actions);
      switch($action){
          case "create":
              $this->Security->config("form", [ 'fields' => ['file']]);
              break;
          case "delete":
              $this->Security->config("form", [ 'fields' => ['file_id']]);
              break;
      }
  }
  public function index(){
    $userModel = $this->model('UserModel');

    $this->i18n->setCachePath(APP . 'langcache/front/shop/shop/');
    $this->i18n->setFilePath(APP . 'templates/lang/back/files/index/lang_{LANGUAGE}.ini'); // language file path
    $this->i18n->setFallbackLang(Session::getLang());
    $this->i18n->init();
      // clear all notifications whenever you hit 'files' in the navigation bar
      //$this->user->clearNotifications(Session::getUserId(), $this->file->table);
      $pageNum  = $this->request->query("page");
      //$this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/default/", Config::get('VIEWS_PATH') . 'files/index.php', ['pageNum' => $pageNum]);
      $action  = $this->request->query('action');

              $this->setting = array_merge(
                array("lang" =>array("page_title" => L("page_title"),"button_upload_files" => L("button_upload_files"),"button_upload" => L("button_upload"),"new_file" =>L("new_file"))),
                array("setting" =>array( "locale" => Session::getLang(), "language" => Session::getLanguage(), "languages" => Config::get('LANGUAGES'),"url" => Config::get('URL'), "public_root" => PUBLIC_ROOT)),
                array("generateCsrfToken" => Session::generateCsrfToken(), "getUserRole" => Session::getUserRole(), "config" => json_encode(Config::getJsConfig())),
                array("userLogged" => $this->data, "itemsMenu" => $itemsMenu)
              );

      return $this->response->setContent($this->view->parseView('back/admin/files/index', $this->setting));

  }
  public function isAuthorized(){

      $role = Session::getUserRole();
      if(isset($role) && $role === "admin"){
          return true;
      }
      return false;
  }
}
