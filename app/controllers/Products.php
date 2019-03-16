<?php
namespace App\controllers;
/**
 * The default home controller, called when no controller/method has been passed
 * to the application.
 */
use App\core\Controller as Controller;
use App\models\UserModel as UserModel;
use App\models\ProductsModel as ProductsModel;
use App\core\Config as Config;
use App\core\Session as Session;
use App\core\Encryption as Encryption;

use Cart\CartItem as CartItem;
use \DateTime;

use App\models\Permission as Permission;
class Products extends Controller
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
        Config::setJsConfig('curPage', "products");

        $action  = $this->request->param('action');
        $actions = ['index','viewall','updateCart'];
        $this->Security->requireAjax($actions);
        $this->Security->requirePost($actions);

                switch($action){
                    case "create":
                        $this->Security->config("form", [ 'fields' => ['title', 'content']]);
                        break;
                    case "update":
                        $this->Security->config("form", [ 'fields' => ['product_id', 'title', 'content']]);
                        break;
                    case "delete":
                        $this->Security->config("validateCsrfToken", true);
                        $this->Security->config("form", [ 'fields' => ['product_id']]);
                        break;
                }

    }

    public function index($productId = 0){
        $this->i18n->setCachePath(APP . 'langcache/front/products/index/');
        $this->i18n->setFilePath(APP . 'templates/lang/front/products/index/lang_{LANGUAGE}.ini'); // language file path
        $this->i18n->setFallbackLang(Session::getLang());
        $this->i18n->init();

        $userModel = $this->model('UserModel');

        if($this->Auth->isLoggedIn()){
            $this->data = ($userModel->getProfileInfo(Session::get("user_id"))!= null) ? $userModel->getProfileInfo(Session::get("user_id")) : [];
        }

        $productsModel = $this->model('ProductsModel');
        $itemsMenu = $productsModel->getMenu();

        $productId = Encryption::decryNameId($productId,"products");

        if(!$productsModel->exists($productId)){
            return $this->error(404);
        }

        Config::setJsConfig('curPage', "products");
        Config::setJsConfig('productId', Encryption::encryNameId($productId,"products"));

        $products = $productsModel->getById($productId);

        $pagemeta = $productsModel->pageMeta($productId);
        $productsoption = $productsModel->productOption($productId);

        $productsoptionDate = $productsModel->productOptionDate($productId);
        Config::setJsConfig('optionDate', $productsoptionDate);
        //var_dump($productsoptionDate);
        $page = array("home" => L("page_home"), "service" => L("page_service"), "category" => L("page_category"), "shop" => L("page_shop"), "cart" => L("page_cart"), "contact" => L("page_contact"));
        $action  = $this->request->query('action');

                $this->setting = array_merge(
                  array("products" => $products, "productsoption" => $productsoption, "action" => $action,"pagemeta" => $pagemeta,"productId" =>$productId),
                  array("lang" =>array("page_title" => L("page_title"),"button_cart" => L("button_cart"),"button_description" => L("button_description"))),
                  array("setting" =>array( "locale" => Session::getLang(), "language" => Session::getLanguage(), "languages" => Config::get('LANGUAGES'),"url" => Config::get('URL'), "public_root" => PUBLIC_ROOT)),
                  array("generateCsrfToken" => Session::generateCsrfToken(), "getUserRole" => Session::getUserRole(), "config" => json_encode(Config::getJsConfig())),
                  array("userLogged" => $this->data, "itemsMenu" => $itemsMenu, "page" => $page)
                );

        return $this->response->setContent($this->view->parseView('front/products/index', $this->setting));

    }

    public function viewfront()
    {
        $userModel = $this->model('UserModel');
        $this->i18n->setCachePath(APP . 'langcache/front/products/viewfront/');
        $this->i18n->setFilePath(APP . 'templates/lang/front/products/viewfront/lang_{LANGUAGE}.ini'); // language file path
        $this->i18n->setFallbackLang(Session::getLang());
        $this->i18n->init();

        if($this->Auth->isLoggedIn()){
            $this->data = ($userModel->getProfileInfo(Session::get("user_id"))!= null) ? $userModel->getProfileInfo(Session::get("user_id")) : [];
        }

        $productsModel = $this->model('ProductsModel');
        $products = $productsModel->getProducts();

        $this->setting = array_merge(
            array("products" => $products),
            array("lang" =>array("page_title" => L("page_title"),"button_cart" => L("button_cart"), "button_description" => L("button_description"))),
            array("setting" =>array( "locale" => Session::getLang(), "language" => Session::getLanguage(), "languages" => Config::get('LANGUAGES'),"url" => Config::get('URL'), "public_root" => PUBLIC_ROOT)),
            array("generateCsrfToken" => Session::generateCsrfToken(), "getUserRole" => Session::getUserRole(), "config" => json_encode(Config::getJsConfig())),
            array("userLogged" => $this->data)
        );
        return $this->response->setContent($this->view->parseView('front/products/viewfront', $this->setting));
    }

    public function update(){
        $productId = $this->request->post("product_id");
        $name = $this->request->post("productName");
        $slug = $this->request->post("productSlug");
        $summary = $this->request->post("productSummary");
        $content = $this->request->post("productContent");
        $price = $this->request->post("productPrice");
        $quantity = $this->request->post("productQuantity");
        $minimum = $this->request->post("productMinimum");
        $metaTitle = $this->request->post("productMetaTitle");
        $metaDescription = $this->request->post("productMetaDescription");
        $productId = Encryption::decryNameId($productId,"products");
        $productsModel = $this->model('ProductsModel');
        $productOption = $this->request->post("productOption");


        $result = $productsModel->update($productId,$name,$slug,$summary,$content,$metaTitle,$metaDescription,$price,$minimum,$quantity,$productOption);

        $productId = Encryption::encryNameId($productId,"products");
        if(!$result){
            Session::set('products-errors', $productsModel->errors());
            return $this->redirector->root("products/view/" . $productId . "?action=update");

        }else{
            return $this->redirector->root("products/view/" . $productId);
        }

    }

    public function searchProducts(){
      $this->i18n->setCachePath(APP . 'langcache/front/products/search');
      $this->i18n->setFilePath(APP . 'templates/lang/front/products/search/lang_{LANGUAGE}.ini'); // language file path
      $this->i18n->setFallbackLang(Session::getLang());
      $this->i18n->init();

      $userModel = $this->model('UserModel');

      if($this->Auth->isLoggedIn()){
          $this->data = ($userModel->getProfileInfo(Session::get("user_id"))!= null) ? $userModel->getProfileInfo(Session::get("user_id")) : [];
      }
      if($this->request->post("searchText") != ''){
        $searchText = $this->request->post("searchText");
      }
      else {
        $searchText = $this->request->query("searchText");
      }

      $productsModel = $this->model('ProductsModel');
      $itemsMenu = $productsModel->getMenu();
      $pageNum  = $this->request->query("page");
      $productsData = $productsModel->getSearchProducts(empty($pageNum)? 1: $pageNum,$searchText);
      $products = $productsData["products"];
      $pagination = $productsData["pagination"];
      $emptyPagination = empty($pagination);
      $totalPagesPagination = $pagination->totalPages();
      $currentPagePagination = $pagination->currentPage;
      $hasPreviousPage = $pagination->hasPreviousPage();
      $hasNextPage =  $pagination->hasNextPage();

      //$this->tommaso->save();
      $this->tommaso->restore();

      $pagemeta = array("meta_title" => L("pagemeta_meta_title"), "meta_description" => L("pagemeta_meta_description"));

      $page = array("home" => L("page_home"), "service" => L("page_service"), "category" => L("page_category"), "shop" => L("page_shop"), "cart" => L("page_cart"), "contact" => L("page_contact"));


      $this->setting = array_merge(
        array("emptyPagination" => $emptyPagination, "totalPagesPagination" => $totalPagesPagination, "hasPreviousPage" => $hasPreviousPage, "currentPagePagination" => $currentPagePagination, "hasNextPage" => $hasNextPage, "linkPagination" => "products/searchProducts"),
        array("searchText" => $searchText, "products" => $products,"pagemeta" => $pagemeta,"page" => $page),
        array("lang" =>array("page_title" => L("page_title"), "button_description" => L("button_description"))),
        array("setting" =>array( "locale" => Session::getLang(), "language" => Session::getLanguage(), "languages" => Config::get('LANGUAGES'),"url" => Config::get('URL'), "public_root" => PUBLIC_ROOT)),
        array("generateCsrfToken" => Session::generateCsrfToken(), "getUserRole" => Session::getUserRole(), "config" => json_encode(Config::getJsConfig())),
        array("userLogged" => $this->data, "itemsMenu" => $itemsMenu)
      );

      return $this->response->setContent($this->view->parseView('front/products/search', $this->setting));

    }
    public function newproduct(){
        $this->i18n->setCachePath(APP . 'langcache/back/products/newproduct/');
        $this->i18n->setFilePath(APP . 'templates/lang/back/products/newproduct/lang_{LANGUAGE}.ini'); // language file path
        $this->i18n->setFallbackLang(Session::getLang());
        $this->i18n->init();

        $userModel = $this->model('UserModel');

        if($this->Auth->isLoggedIn()){
            $this->data = ($userModel->getProfileInfo(Session::get("user_id"))!= null) ? $userModel->getProfileInfo(Session::get("user_id")) : [];
        }

        $productsModel = $this->model('ProductsModel');
        $productCategories = $productsModel->productCategories();
        /*$productOption = $productsModel->productOption();*/
        $this->setting = array_merge(
            array("productCategories" => $productCategories),
            array("lang" =>array("page_title" => L("page_title"))),
            array("setting" =>array( "locale" => Session::getLang(), "language" => Session::getLanguage(), "languages" => Config::get('LANGUAGES'),"url" => Config::get('URL'), "public_root" => PUBLIC_ROOT)),
            array("generateCsrfToken" => Session::generateCsrfToken(), "getUserRole" => Session::getUserRole(), "config" => json_encode(Config::getJsConfig())),
            array("userLogged" => $this->data)
        );

        return $this->response->setContent($this->view->parseView('back/admin/products/newproduct', $this->setting));
    }

    public function addOption(){
         $userModel = $this->model('UserModel');

         $this->i18n->setCachePath(APP . 'langcache/back/products/addoption/');
         $this->i18n->setFilePath(APP . 'templates/lang/back/products/addoption/lang_{LANGUAGE}.ini'); // language file path
         $this->i18n->setFallbackLang(Session::getLang());
         $this->i18n->init();

         $url = $this->request->post("url");

         if($this->Auth->isLoggedIn()){
             $this->data = ($userModel->getProfileInfo(Session::get("user_id"))!= null) ? $userModel->getProfileInfo(Session::get("user_id")) : [];
         }

         $html = $this->view->parseView('/back/admin/products/addoption', array("url" => $url));

         return $this->view->parseJson(array("data" => $html));
    }
    public function create(){

        $productsModel = $this->model('ProductsModel');
        Config::setJsConfig('curPage', 'products');

        $productName            = $this->request->post("productName");
        $productSlug            = $this->request->post("productSlug");
        $productLang            = Session::getLang();
        $productSrc             = '';
        $productActive          = 1;
        $productTaxType         = 1;
        $productMenuName        = '';
        $productQuantity        = $this->request->post("productQuantity");
        $productMinimum         = $this->request->post("productMinimum");
        $productCategoryId      = $this->request->post("productCategory");
        $productSummary         = $this->request->post("productSummary");
        $productPrice           = $this->request->post("productPrice");
        $productContent         = $this->request->post("productContent");
        $productMetaTitle       = $this->request->post("productMetaTitle");
        $productMetaDescription = $this->request->post("productMetaDescription");
        $productMetaKeywords    = $this->request->post("productMetaKeywords");

        $result = $productsModel->create(Session::getUserId(), $productName, $productSlug, $productLang, $productSrc, $productActive, $productCategoryId, $productSummary, $productPrice, $productContent, $productMetaTitle, $productMetaDescription, $productMetaKeywords, $productTaxType, $productMenuName, $productQuantity, $productMinimum );

        if(!$result){
            Session::set('product-errors', "Errore Product Creation");
        }else{
            Session::set('product-success', "New Product has been created");
        }

        return $this->redirector->root();

    }

    public function viewall(){

                Config::setJsConfig('curPage', 'products');

                $userModel = $this->model('UserModel');
                $this->i18n->setCachePath(APP . 'langcache/back/products/viewall/');
                $this->i18n->setFilePath(APP . 'templates/lang/back/products/viewall/lang_{LANGUAGE}.ini'); // language file path
                $this->i18n->setFallbackLang(Session::getLang());
                $this->i18n->init();
                //echo Session::destroy();
                // check first if user is already logged in via session or cookie
                if($this->Auth->isLoggedIn()){
                    $this->data = ($userModel->getProfileInfo(Session::get("user_id"))!= null) ? $userModel->getProfileInfo(Session::get("user_id")) : [];
                }
                $productsModel = $this->model('ProductsModel');
                $pageNum  = $this->request->query("page");
                $productData = $productsModel->getAllProducts(empty($pageNum)? 1: $pageNum);
                $products = $productData["products"];
                $pagination = $productData["pagination"];
                $emptyPagination = empty($pagination);
                $totalPagesPagination = $pagination->totalPages();
                $currentPagePagination = $pagination->currentPage;
                $hasPreviousPage = $pagination->hasPreviousPage();
                $hasNextPage =  $pagination->hasNextPage();
                $this->setting = array_merge(
                  array("emptyPagination" => $emptyPagination, "totalPagesPagination" => $totalPagesPagination, "hasPreviousPage" => $hasPreviousPage, "currentPagePagination" => $currentPagePagination, "hasNextPage" => $hasNextPage, "linkPagination" => "products"),
                  array("products" => $products, "page_title" => L("page_title")),
                  array("setting" =>array("url" => Config::get('URL'), "public_root" => PUBLIC_ROOT)),
                  array("generateCsrfToken" => Session::generateCsrfToken(), "getUserRole" => Session::getUserRole(), "config" => json_encode(Config::getJsConfig())),
                  array("userLogged" => $this->data)
                );
                return $this->response->setContent($this->view->parseView('back/admin/products/viewall', $this->setting));
    }

    public function view($productId = 0){

        $productId = Encryption::decryNameId($productId, "products");
        $userModel = $this->model('UserModel');

        if($this->Auth->isLoggedIn()){
            $this->data = ($userModel->getProfileInfo(Session::get("user_id"))!= null) ? $userModel->getProfileInfo(Session::get("user_id")) : [];
        }

        $productsModel = $this->model('ProductsModel');
        if(!$productsModel->exists($productId)){
            return $this->error(404);
        }
        Config::setJsConfig('curPage', 'products');
        Config::setJsConfig('productId', Encryption::encryNameId($productId,"products"));

        $products = $productsModel->getById($productId);
        $productoption = $productsModel->productOption($productId);

        $productoptionDate = $productsModel->productOptionDate($productId);

        $action  = $this->request->query('action');

        $this->setting = array_merge(
            array("products" => $products, "productoption" => $productoption, "action" => $action, "productId" => $productId),
            array("setting" =>array("page_title" => "Product Page","url" => Config::get('URL'), "public_root" => PUBLIC_ROOT)),
            array("generateCsrfToken" => Session::generateCsrfToken(), "getUserRole" => Session::getUserRole()),
            array("userLogged" => $this->data)
        );
        return $this->response->setContent($this->view->parseView('back/admin/products/viewproduct', $this->setting));

    }

    public function getProductDates(){
        $productId = $this->request->post("product_id");
        $productsModel = $this->model('productsModel');
        $optionValue = $productsModel->productOptionDate($productId);
        return $this->view->parseJson(array("data" => $optionValue));
    }

    public function updateCart(){
        $success = true;

        $productId = $this->request->post("product_id");

        $optionValueId = $this->request->post("option_value_id");

        $optionId = $this->request->post("option_id");

        $productsModel = $this->model('productsModel');

        $quantityObject = $this->request->post("quantity");

        $dataService = $this->request->post("date_service");
        //$dataService[0]["optionDateName"]);

        if (is_array($dataService) && count($dataService) == 2 ){
          $success = false;
          $start = DateTime::createFromFormat('d/m/Y', $dataService[0]["optionValue"]);
          $end = DateTime::createFromFormat('d/m/Y', $dataService[1]["optionValue"]);

          if ( $end instanceof DateTime ){
            $interval = date_diff($start, $end);
              if ($interval->format('%R%a') > 0){
                 $intervalDay = $interval->format('%R%a');
                 $success = true;
              } else{
                 $success = false;
              }
          }
        }
        $product = $productsModel->getById($productId);

        $optionValue = $productsModel->productOptionPrice($productId,$optionId, $optionValueId);

        $this->tommaso->restore();
        $item = new CartItem;
        $item->productId = $product["id"];
        $item->name = $product["name"];
        $item->src = $product["src"];
        $item->quantity = (int) $quantityObject;
        if (isset($intervalDay)){
          $totalSingol = ((float)$product["price"] + (float)$optionValue["price"] );
          $item->price = $totalSingol + ($totalSingol * ((int)$intervalDay-1));
          $item->price = number_format($item->price, 2, '.', '');
          $item->total = ($totalSingol + ($totalSingol * ((int)$intervalDay-1))) * (int)$quantityObject ;
        } else {
          $item->price = (float)$product["price"] + (float)$optionValue["price"] ;
          $item->price = number_format($item->price, 2, '.', '');
          $item->total = ((float)$product["price"] + (float)$optionValue["price"] ) * (int)$quantityObject ;
        }
        $item->total = number_format($item->total, 2, '.', '');
        $item->tax = 0.00;
        $item["options"] = array();
        $item["options"] = [
          $optionValue["name"] => $optionValue["option_name"]
        ];

         $arr = array();
          foreach ($dataService as $servicesDay){
            foreach ($servicesDay as $key){
              array_push($arr,$key);
            }
          }
          $i=0;
          for($i = 0; $i < count($arr); $i += 2){
            if ($i < count($arr) && $i+1 <= count($arr)){
              $item["options"] +=[
                $arr[$i] => $arr[$i+1],
              ];
            }
        }

        $this->tommaso->add($item);
        $this->tommaso->save();
        if ($success){
          return $this->view->parseSuccess("Cart has been updated.");
          //$this->response->send();
        } else {
          return $this->view->parseSuccess("Error when updated the cart.");
          //$this->response->send();
        }
    }

    public function isAuthorized(){
      $action = $this->request->param('action');
      $role = Session::getUserRole();
      $resource = "products";
      //var_dump($action);
      if(!isset($role) && in_array($action,["index","viewall","searchProducts","getProductDates","UpdateCart"],true)){
          return true;
      }
      // only for admins
      Permission::allow('admin', $resource, ['*']);
      // only for normal users
      Permission::allow('user', $resource, ['index', 'view', 'newPost', 'create']);
      Permission::allow('user', $resource, ['update', 'delete'], 'owner');

      $productId  = ($action === "delete")? $this->request->param("args")[0]: $this->request->post("product_id");
        if(!empty($categoryId)){
            $productId = Encryption::decryName($productId,"products");
        }

        $config = [
            "user_id" => Session::getUserId(),
            "table" => "products",
            "id" => $productId
        ];

        return Permission::check($role, $resource, $action, $config);
    }
}
