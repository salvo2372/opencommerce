<?php
namespace App\controllers;
use App\core\Controller as Controller;
use App\models\CategoryModel as CategoryModel;
use App\models\UserModel as UserModel;
use App\core\Config as Config;
use App\core\Session as Session;
use App\core\Encryption as Encryption;

use App\models\Permission as Permission;
class Category extends Controller
{
    public $setting = [];

    public $data = [];

    public $tommaso;
    /**
     * The default controller method.
     *
     * @return void
     */
    public function __construct($tommaso, $request, $response){
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
        Config::setJsConfig('curPage', "category");

    }
    public function index($categoryId = 0){
        $this->i18n->setCachePath(APP . 'langcache/front/category/index/');
        $this->i18n->setFilePath(APP . 'templates/lang/front/category/index/lang_{LANGUAGE}.ini'); // language file path
        $this->i18n->setFallbackLang(Session::getLang());
        $this->i18n->init();

        $userModel = $this->model('UserModel');

        if($this->Auth->isLoggedIn()){
            $this->data = ($userModel->getProfileInfo(Session::get("user_id"))!= null) ? $userModel->getProfileInfo(Session::get("user_id")) : [];
        }

        $categoryModel = $this->model('categoryModel');
        $categoryId = Encryption::decryNameId($categoryId,"category");

        if(!$categoryModel->exists($categoryId)){
            return $this->error(404);
        }


        $itemsMenu     = $categoryModel->getMenu();
        $category      = $categoryModel->getById($categoryId);
        $subCategories = $categoryModel->subCategory($categoryId);
        $subProducts   = $categoryModel->subProducts($categoryId);

        $pagemeta = $categoryModel->pageMeta($categoryId);
        $action  = $this->request->query('action');

        Config::setJsConfig('curPage', "category");
        Config::setJsConfig('categoryId', Encryption::encryNameId($categoryId,"category"));

        $page = array("home" => L("page_home"), "service" => L("page_service"), "category" => L("page_category"), "shop" => L("page_shop"), "cart" => L("page_cart"), "contact" => L("page_contact"));
        
                $this->setting = array_merge(
                  array("category" => $category, "action" => $action, "subCategories" => $subCategories, "subProducts" => $subProducts),
                  array("lang" =>array("page_title" => L("page_title"),"button_description" => L("button_description"), "product" => L("product"), "category" => L("category"))),
                  array("setting" =>array( "locale" => Session::getLang(), "language" => Session::getLanguage(), "languages" => Config::get('LANGUAGES'),"url" => Config::get('URL'), "public_root" => PUBLIC_ROOT)),
                  array("generateCsrfToken" => Session::generateCsrfToken(), "getUserRole" => Session::getUserRole(), "config" => json_encode(Config::getJsConfig())),
                  array("userLogged" => $this->data, "pagemeta" => $pagemeta,"itemsMenu" => $itemsMenu, "page" => $page)
                );

        return $this->response->setContent($this->view->parseView('front/category/index', $this->setting));
    }
    public function category()
    {
                $this->i18n->setCachePath(APP . 'langcache/front/category/category/');
                $this->i18n->setFilePath(APP . 'templates/lang/front/category/category/lang_{LANGUAGE}.ini'); // language file path
                $this->i18n->setFallbackLang(Session::getLang());
                $this->i18n->init();
                $userModel = $this->model('UserModel');

                if ($this->Auth->isLoggedIn()) {
                    $this->data = ($userModel->getProfileInfo(Session::get("user_id"))!= null) ? $userModel->getProfileInfo(Session::get("user_id")) : [];
                }

                $categoryModel = $this->model('CategoryModel');
                $itemsMenu = $categoryModel->getMenu();
                $categories = $categoryModel->getAll();
                $action  = $this->request->query('action');
                $this->setting = array_merge(
                  array("categories" => $categories, "action" => $action),
                  array("lang" =>array("page_title" => L("page_title"),"button_description" => L("button_description"), "product" => L("product"), "category" => L("category"))),
                  array("setting" =>array( "locale" => Session::getLang(), "language" => Session::getLanguage(), "languages" => Config::get('LANGUAGES'),"url" => Config::get('URL'), "public_root" => PUBLIC_ROOT)),
                  array("generateCsrfToken" => Session::generateCsrfToken(), "getUserRole" => Session::getUserRole(), "config" => json_encode(Config::getJsConfig())),
                  array("userLogged" => $this->data, "itemsMenu" => $itemsMenu)
                );

                return $this->response->setContent($this->view->parseView('front/category/category', $this->setting));
    }
    public function viewall(){

                Config::setJsConfig('curPage', 'category');

                $this->i18n->setCachePath(APP . 'langcache/back/category/viewall/');
                $this->i18n->setFilePath(APP . 'templates/lang/back/category/viewall/lang_{LANGUAGE}.ini'); // language file path
                $this->i18n->setFallbackLang(Session::getLang());
                $this->i18n->init();

                $userModel = $this->model('UserModel');
                if($this->Auth->isLoggedIn()){
                    $this->data = ($userModel->getProfileInfo(Session::get("user_id"))!= null) ? $userModel->getProfileInfo(Session::get("user_id")) : [];
                }

                $categoryModel = $this->model('CategoryModel');
                $categories = $categoryModel->getCategories();

                $this->setting = array_merge(
                  array("categories" => $categories),
                  array("page_title" => L("page_title")),
                  array("setting" =>array( "locale" => Session::getLang(), "language" => Session::getLanguage(), "languages" => Config::get('LANGUAGES'),"url" => Config::get('URL'), "public_root" => PUBLIC_ROOT)),
                  array("generateCsrfToken" => Session::generateCsrfToken(), "getUserRole" => Session::getUserRole(), "config" => json_encode(Config::getJsConfig())),
                  array("userLogged" => $this->data)
                );
                return $this->response->setContent($this->view->parseView('back/admin/category/viewall', $this->setting));
    }
    public function view($categoryId = 0){

        $categoryId = Encryption::decryNameId($categoryId, "category");
        $userModel = $this->model('UserModel');

        if($this->Auth->isLoggedIn()){
            $this->data = ($userModel->getProfileInfo(Session::get("user_id"))!= null) ? $userModel->getProfileInfo(Session::get("user_id")) : [];
        }

        $categoryModel = $this->model('CategoryModel');
        if(!$categoryModel->exists($categoryId)){
            return $this->error(404);
        }
        Config::setJsConfig('curPage', 'category');
        Config::setJsConfig('categoryId', Encryption::encryNameId($categoryId,"category"));

        $category = $categoryModel->getById($categoryId);

        $action  = $this->request->query('action');

        $this->setting = array_merge(
            array("category" => $category, "action" => $action, "categoryId" => $categoryId),
            array("setting" =>array("page_title" => "Product Page","url" => Config::get('URL'), "public_root" => PUBLIC_ROOT)),
            array("generateCsrfToken" => Session::generateCsrfToken(), "getUserRole" => Session::getUserRole()),
            array("userLogged" => $this->data)
        );
        return $this->response->setContent($this->view->parseView('back/admin/category/viewcategory', $this->setting));

    }
    public function update(){
        $categoryId = $this->request->post("category_id");
        $name = $this->request->post("categoryName");
        $slug = $this->request->post("categorySlug");
        $summary = $this->request->post("categorySummary");
        $content = $this->request->post("categoryContent");
        $metaTitle = $this->request->post("categoryMetaTitle");
        $metaDescription = $this->request->post("categoryMetaDescription");
        $categoryId = Encryption::decryNameId($categoryId,"category");

        $categoryModel = $this->model('CategoryModel');
        $result = $categoryModel->update($categoryId,$name,$slug,$summary,$content,$metaTitle,$metaDescription);
        $categoryId = Encryption::encryNameId($categoryId,"category");
        if(!$result){
            Session::set('category-errors', $categoryModel->errors());
            return $this->redirector->root("category/view/" . $categoryId . "?action=update");

        }else{
            return $this->redirector->root("category/view/" . $categoryId);
        }
    }
    public function isAuthorized(){
      $action = $this->request->param('action');
      $role = Session::getUserRole();
      $resource = "category";
      if(!isset($role) && in_array($action,["index","category"],true)){
          return true;
      }
      // only for admins
      Permission::allow('admin', $resource, ['*']);
      // only for normal users
      Permission::allow('user', $resource, ['index', 'view', 'newPost', 'create']);
      Permission::allow('user', $resource, ['update', 'delete'], 'owner');

      $categoryId  = ($action === "delete")? $this->request->param("args")[0]: $this->request->post("category_id");
        if(!empty($categoryId)){
            $categoryId = Encryption::decryNameId($categoryId,"category");
        }

        $config = [
            "user_id" => Session::getUserId(),
            "table" => "category",
            "id" => $categoryId
        ];

        return Permission::check($role, $resource, $action, $config);
    }
}
