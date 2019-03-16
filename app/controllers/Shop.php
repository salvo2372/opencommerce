<?php
namespace App\controllers;


use App\core\Controller as Controller;
use App\models\UserModel as UserModel;
use App\models\ProductsModel as ProductsModel;
use App\core\Config as Config;
use App\core\Session as Session;
use App\core\Encryption as Encryption;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

use Cart\CartItem as CartItem;
class Shop extends Controller
{
    public $setting = [];
    public $tommaso;
    public $data = [];

    public function __construct($tommaso, $request,$response){
        parent::__construct($request,$response);
        $this->tommaso = $tommaso;
    }

    public function initialize(){

        $this->loadComponents([
            'Auth',
            'Security'
        ]);
    }

    public function beforeAction(){

        parent::beforeAction();

        Config::setJsConfig('csrfToken', Session::generateCsrfToken());
        Config::setJsConfig('curPage', "shop");

    }

    public function index()
    {
        $userModel = $this->model('UserModel');

        $this->i18n->setCachePath(APP . 'langcache/front/shop/index/');
        $this->i18n->setFilePath(APP . 'templates/lang/front/shop/index/lang_{LANGUAGE}.ini'); // language file path
        $this->i18n->setFallbackLang(Session::getLang());
        $this->i18n->init();

        if($this->Auth->isLoggedIn()){
            $this->data = ($userModel->getProfileInfo(Session::get("user_id"))!= null) ? $userModel->getProfileInfo(Session::get("user_id")) : [];
        }
        $productsModel = $this->model('ProductsModel');
        $itemsMenu = $productsModel->getMenu();
        $pageNum  = $this->request->query("page");
        $productData = $productsModel->getProducts(empty($pageNum)? 1: $pageNum);
        $products = $productData["products"];
        $pagination = $productData["pagination"];
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
          array("emptyPagination" => $emptyPagination, "totalPagesPagination" => $totalPagesPagination, "hasPreviousPage" => $hasPreviousPage, "currentPagePagination" => $currentPagePagination, "hasNextPage" => $hasNextPage, "linkPagination" => "shop"),
          array("products" => $products,"pagemeta" => $pagemeta,"page" => $page),
          array("lang" =>array("page_title" => L("page_title"), "button_description" => L("button_description"))),
          array("setting" =>array( "locale" => Session::getLang(), "language" => Session::getLanguage(), "languages" => Config::get('LANGUAGES'),"url" => Config::get('URL'), "public_root" => PUBLIC_ROOT)),
          array("generateCsrfToken" => Session::generateCsrfToken(), "getUserRole" => Session::getUserRole(), "config" => json_encode(Config::getJsConfig())),
          array("userLogged" => $this->data, "itemsMenu" => $itemsMenu)
        );

        return $this->response->setContent($this->view->parseView('front/shop/index', $this->setting));

    }

    public function AddToCart(){

        $productId = $this->request->post("product_id");

        $productsModel = $this->model('productsModel');

        $product = $productsModel->getById($productId);

        $this->tommaso->restore();
        $item = new CartItem;
        $item->productId = $product["id"];
        $item->name = $product["name"];
        $item->src = $product["src"];
        $item->sku = 'MBP8GB';
        $item->price = 21.00;
        $item->data_servizio = '9/08/2018';
        $item->tax = 2.50;
        $item['options'] = [
            1 => 'adulti',
            2 => 'bambini',
            3 => 'bambini 0-3 anni'
        ];
        //$this->tommaso->save();

        $this->tommaso->add($item);
        $this->tommaso->save();


        $this->view->parseSuccess("Cart has been updated.");
        $this->response->send();
/*
        if(!$userModel->exists($userId)){
            return $this->error(404);
        }

        $adminModel->deleteUser(Session::getUserId(), $userId);
*/

    }

   public function RemoveItem(){

        $itemId = $this->request->post("item_id");

        $this->tommaso->restore();
        $this->tommaso->remove($itemId);
        $this->tommaso->save();

        return $this->view->parseSuccess("The item has been deleted.");
   }

   public function updateQuantity(){

        $itemId = $this->request->post("item_id");
        $itemPrice = $this->request->post("item_price");
        $itemQuantity = $this->request->post("item_quantity");
        $this->tommaso->restore();

        $itemQuantity = (int) $itemQuantity;
        $itemPrice = number_format($itemPrice, 2, '.', '');
        $itemTotal = $itemPrice * $itemQuantity;
        $this->tommaso->update($itemId,'quantity', $itemQuantity);
        $this->tommaso->update($itemId,'total',$itemTotal);
        $this->tommaso->save();

        return $this->view->parseSuccess("The item quantity has been updated.");
   }

   public function Shop(){
        $userModel = $this->model('UserModel');

        $this->i18n->setCachePath(APP . 'langcache/front/shop/shop/');
        $this->i18n->setFilePath(APP . 'templates/lang/front/shop/shop/lang_{LANGUAGE}.ini'); // language file path
        $this->i18n->setFallbackLang(Session::getLang());
        $this->i18n->init();

              //$cart = $this->sessionStore->get($this->cartId);

        if($this->Auth->isLoggedIn()){
            $this->data = ($userModel->getProfileInfo(Session::get("user_id"))!= null) ? $userModel->getProfileInfo(Session::get("user_id")) : [];
        }

        $productsModel = $this->model('ProductsModel');
        $itemsMenu = $productsModel->getMenu();

        $pagemeta = array("meta_title" => L("pagemeta_meta_title"), "meta_description" => L("pagemeta_meta_description"));

        $page = array("home" => L("page_home"), "service" => L("page_service"), "category" => L("page_category"), "shop" => L("page_shop"), "cart" => L("page_cart"), "contact" => L("page_contact"));

        $this->tommaso->restore();
        $cartElements = $this->tommaso->toArray()["items"];

                //var_dump($cartElements);

                $cartItems = array();
                if (count($cartElements) > 0) {
                    foreach ($cartElements as $keyElements ) {
                        foreach ($keyElements as $key => $value) {
                          if (is_array($value))
                                foreach ($value as $cartkey => $cartvalue){
                                  if ($cartkey == "price" || $cartkey == "total"){
                                        $cartItem[$cartkey] = number_format($cartvalue, 2, '.', '');
                                  } else {
                                      $cartItem[$cartkey] = $cartvalue;
                                  }
                                }
                          else
                                $cartItem[$key] = $value;
                        }
                        array_push($cartItems,$cartItem);
                    }
                }

                $subTotal = $this->tommaso->totalExcludingTax();
                $subTotal = number_format($subTotal, 2, '.', '');
                $total = $this->tommaso->total();
                $total = number_format($total, 2, '.', '');

        $this->setting = array_merge(
            array("cartItems" => $cartItems, "total" => $total, "subTotal" => $subTotal),
            array("lang" =>array("page_title" => L("page_title"),"button_checkout" => L("button_checkout"), "product_name" => L("product_name"), "product_quantity" => L("product_quantity"), "product_price" => L("product_price"), "product_total" => L("product_total"),"textitemcart" => L("textitemcart"))),
            array("setting" =>array( "locale" => Session::getLang(), "language" => Session::getLanguage(), "languages" => Config::get('LANGUAGES'),"url" => Config::get('URL'), "public_root" => PUBLIC_ROOT)),
            array("generateCsrfToken" => Session::generateCsrfToken(), "getUserRole" => Session::getUserRole(), "config" => json_encode(Config::getJsConfig())),
            array("userLogged" => $this->data, "itemsMenu" => $itemsMenu, "pagemeta" => $pagemeta,"page" => $page)
        );

        return $this->response->setContent($this->view->parseView('/front/shop/shop', $this->setting));

    }

   public function Checkout(){
        $userModel = $this->model('UserModel');

        $this->i18n->setCachePath(APP . 'langcache/front/shop/checkout/');
        $this->i18n->setFilePath(APP . 'templates/lang/front/shop/checkout/lang_{LANGUAGE}.ini'); // language file path
        $this->i18n->setFallbackLang(Session::getLang());
        $this->i18n->init();

              //$cart = $this->sessionStore->get($this->cartId);

              if($this->Auth->isLoggedIn()){
                  $this->data = ($userModel->getProfileInfo(Session::get("user_id"))!= null) ? $userModel->getProfileInfo(Session::get("user_id")) : [];
               }

               $productsModel = $this->model('ProductsModel');
               $itemsMenu = $productsModel->getMenu();

               $page = array("home" => L("page_home"), "service" => L("page_service"), "category" => L("page_category"), "shop" => L("page_shop"), "cart" => L("page_cart"), "contact" => L("page_contact"));

               $this->tommaso->restore();
               $cartElements = $this->tommaso->toArray()["items"];

                $cartItems = array();
                if (count($cartElements) > 0) {
                    foreach ($cartElements as $keyElements ) {
                        foreach ($keyElements as $key => $value) {
                          if (is_array($value))
                                foreach ($value as $cartkey => $cartvalue){
                                  $cartItem[$cartkey] = $cartvalue;
                                }
                          else
                                $cartItem[$key] = $value;
                        }
                        array_push($cartItems,$cartItem);
                    }
                }
                $subTotal = $this->tommaso->totalExcludingTax();
                $subTotal = number_format($subTotal, 2, ',', '');
                $total = $this->tommaso->total();
                $total = number_format($total, 2, ',', '');
                $totalItem = $this->tommaso->totalUniqueItems();
                $this->setting = array_merge(
                  array("cartItems" => $cartItems, "total" => $total, "subTotal" => $subTotal, "totalItem" => $totalItem),
                  array("lang" =>array("page_title" => L("page_title"),"button_confirm" => L("button_confirm"), "text_bank" => L("text_bank"), "text_paypal" => L("text_paypal"),"first_name" => L("first_name"),"last_name" => L("last_name"), "telephone" => L("telephone"), "email_address" => L("email_address"))),
                  array("setting" =>array( "locale" => Session::getLang(), "language" => Session::getLanguage(), "languages" => Config::get('LANGUAGES'),"url" => Config::get('URL'), "public_root" => PUBLIC_ROOT)),
                  array("generateCsrfToken" => Session::generateCsrfToken(), "getUserRole" => Session::getUserRole(), "config" => json_encode(Config::getJsConfig())),
                  array("userLogged" => $this->data, "itemsMenu" => $itemsMenu,"page" => $page)
                );

              return $this->response->setContent($this->view->parseView('/front/shop/checkout', $this->setting));

    }

   public function Confirm(){
        $userModel = $this->model('UserModel');

        $this->i18n->setCachePath(APP . 'langcache/front/shop/confirm/');
        $this->i18n->setFilePath(APP . 'templates/lang/front/shop/confirm/lang_{LANGUAGE}.ini'); // language file path
        $this->i18n->setFallbackLang(Session::getLang());
        $this->i18n->init();

        $order_info['firstName'] = $this->request->post("firstName");
        $order_info['lastName'] = $this->request->post("lastName");
        $order_info['phoneNumber'] = $this->request->post("phoneNumber");
        $order_info['email']   = $this->request->post("email");
        $url     = $this->request->post("url");
        $totalItem = $this->tommaso->totalUniqueItems();
        $order_info["paymentMethod"] = "Paypal";
        $order_info["paymentState"] = "Completato";
        $order_info["url"]  = Config::get('URL');
              if($this->Auth->isLoggedIn()){
                  $this->data = ($userModel->getProfileInfo(Session::get("user_id"))!= null) ? $userModel->getProfileInfo(Session::get("user_id")) : [];
               }

        $page = array("home" => L("page_home"), "service" => L("page_service"), "category" => L("page_category"), "shop" => L("page_shop"), "cart" => L("page_cart"), "contact" => L("page_contact"));

               $this->tommaso->restore();
               $cartElements = $this->tommaso->toArray()["items"];

                //var_dump($cartElements);

                $cartItems = array();
                if (count($cartElements) > 0) {
                    foreach ($cartElements as $keyElements ) {
                        foreach ($keyElements as $key => $value) {
                          if (is_array($value))
                                foreach ($value as $cartkey => $cartvalue){
                                  $cartItem[$cartkey] = $cartvalue;
                                }
                          else
                                $cartItem[$key] = $value;
                        }
                        array_push($cartItems,$cartItem);
                    }
                }
                $order_info['cartItems'] = $cartItems;
                $subTotal = $this->tommaso->totalExcludingTax();
                $subTotal = number_format($subTotal, 2, ',', '');
                $order_info['subTotal'] = $subTotal;
                $total = $this->tommaso->total();
                $total = number_format($total, 2, ',', '');
                $order_info['total'] = $total;
                Session::set('order_info',$order_info);

                $html = $this->view->parseView('/front/shop/confirm', array("cartItems" => $cartItems, "firstName" => $order_info['firstName'], "lastName" => $order_info['lastName'], "phoneNumber" => $order_info['phoneNumber'], "email" => $order_info['email'], "url" => $url));

                return $this->view->parseJson(array("data" => $html));


    }

   public function ConfirmBankTransfer(){
        $userModel = $this->model('UserModel');

        $this->i18n->setCachePath(APP . 'langcache/front/shop/confirmBankTransfer/');
        $this->i18n->setFilePath(APP . 'templates/lang/front/shop/confirmBankTransfer/lang_{LANGUAGE}.ini'); // language file path
        $this->i18n->setFallbackLang(Session::getLang());
        $this->i18n->init();

        $order_info['firstName'] = $this->request->post("firstName");
        $order_info['lastName'] = $this->request->post("lastName");
        $order_info['phoneNumber'] = $this->request->post("phoneNumber");
        $order_info['email']   = $this->request->post("email");
        $totalItem = $this->tommaso->totalUniqueItems();
        $order_info["paymentMethod"] = "Bank Transfer";
        $order_info["paymentState"] = "Completato";
        $order_info["url"]  = Config::get('URL');
              if($this->Auth->isLoggedIn()){
                  $this->data = ($userModel->getProfileInfo(Session::get("user_id"))!= null) ? $userModel->getProfileInfo(Session::get("user_id")) : [];
               }

               $this->tommaso->restore();
               $cartElements = $this->tommaso->toArray()["items"];

                //var_dump($cartElements);

                $cartItems = array();
                if (count($cartElements) > 0) {
                    foreach ($cartElements as $keyElements ) {
                        foreach ($keyElements as $key => $value) {
                          if (is_array($value))
                                foreach ($value as $cartkey => $cartvalue){
                                  $cartItem[$cartkey] = $cartvalue;
                                }
                          else
                                $cartItem[$key] = $value;
                        }
                        array_push($cartItems,$cartItem);
                    }
                }

                $order_info['cartItems'] = $cartItems;
                $subTotal = $this->tommaso->totalExcludingTax();
                $subTotal = number_format($subTotal, 2, ',', '');
                $order_info['subTotal'] = $subTotal;
                $total = $this->tommaso->total();
                $total = number_format($total, 2, ',', '');
                $order_info['total'] = $total;
                Session::set('order_info',$order_info);

                $html = $this->view->parseView('/front/shop/confirmBankTransfer', array("cartItems" => $cartItems, "firstName" => $order_info['firstName'], "lastName" => $order_info['lastName'], "phoneNumber" => $order_info['phoneNumber'], "email" => $order_info['email']));

                return $this->view->parseJson(array("data" => $html));

    }

    public function Callback(){

        // STEP 1: read POST data
        // Reading POSTed data directly from $_POST causes serialization issues with array data in the POST.
        // Instead, read raw POST data from the input stream.
        $raw_post_data = file_get_contents('php://input');
        $raw_post_array = explode('&', $raw_post_data);
        $myPost = array();
        foreach ($raw_post_array as $keyval) {
          $keyval = explode ('=', $keyval);
          if (count($keyval) == 2)
            $myPost[$keyval[0]] = urldecode($keyval[1]);
        }
        // read the IPN message sent from PayPal and prepend 'cmd=_notify-validate'
        $req = 'cmd=_notify-validate';
        if (function_exists('get_magic_quotes_gpc')) {
          $get_magic_quotes_exists = true;
        }
        foreach ($myPost as $key => $value) {
          if ($get_magic_quotes_exists == true && get_magic_quotes_gpc() == 1) {
            $value = urlencode(stripslashes($value));
          } else {
            $value = urlencode($value);
          }
          $req .= "&$key=$value";
        }

        // Step 2: POST IPN data back to PayPal to validate
        $ch = curl_init('https://ipnpb.paypal.com/cgi-bin/webscr');
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close'));
        // In wamp-like environments that do not come bundled with root authority certificates,
        // please download 'cacert.pem' from "https://curl.haxx.se/docs/caextract.html" and set
        // the directory path of the certificate as shown below:
        // curl_setopt($ch, CURLOPT_CAINFO, dirname(__FILE__) . '/cacert.pem');
        if ( !($res = curl_exec($ch)) ) {
          // error_log("Got " . curl_error($ch) . " when processing IPN data");
          curl_close($ch);
          exit;
        }
        if (strcmp ($res, "VERIFIED") == 0) {
          // The IPN is verified, process it:
          // check whether the payment_status is Completed
          // check that txn_id has not been previously processed
          // check that receiver_email is your Primary PayPal email
          // check that payment_amount/payment_currency are correct
          // process the notification
          // assign posted variables to local variables
          $item_name = $_POST['item_name'];
          $item_number = $_POST['item_number'];
          $payment_status = $_POST['payment_status'];
          $payment_amount = $_POST['mc_gross'];
          $payment_currency = $_POST['mc_currency'];
          $txn_id = $_POST['txn_id'];
          $receiver_email = $_POST['receiver_email'];
          $payer_email = $_POST['payer_email'];
          Session::set("order_info['paymentState']", $payment_status);
          // IPN message values depend upon the type of notification sent.
          // To loop through the &_POST array and print the NV pairs to the screen:
          foreach($_POST as $key => $value) {
            $pay_order[$key] = $value;
          }
        } else if (strcmp ($res, "INVALID") == 0) {
          // IPN invalid, log for manual investigation
          echo "The response from IPN was: <b>" .$res ."</b>";
        }
        Session::set("pay_order",$pay_order);
        curl_close($ch);

    }
    public function Success(){

        $this->i18n->setCachePath(APP . 'langcache/front/shop/success/');
        $this->i18n->setFilePath(APP . 'templates/lang/front/shop/success/lang_{LANGUAGE}.ini'); // language file path
        $this->i18n->setFallbackLang(Session::getLang());
        $this->i18n->init();

        $productsModel = $this->model('ProductsModel');
        $itemsMenu = $productsModel->getMenu();

        $page = array("home" => L("page_home"), "service" => L("page_service"), "category" => L("page_category"), "shop" => L("page_shop"), "cart" => L("page_cart"), "contact" => L("page_contact"));

        $order_info = Session::getAndDestroy('order_info');
        //var_dump($order_info);

        $subjectBankTransfer_client  = L("subjectBankTransferclient");
        $subjectBankTransfer_seller  = L("subjectBankTransferseller");
        $textBank = array("total_cost" => L("textBank_total_cost"), "order_register" => L("textBank_order_register"), "order_phoneNumber" => L("textBank_order_phoneNumber"), "order_email" => L("textBank_order_email"), "order" => L("textBank_order"), "payment_method" => L("textBank_payment_method"), "payment_state" => L("textBank_payment_state"),"order_info" => L("textBank_order_info"),"total_import" => L("textBank_total_import"),"element" => L("textBank_element"),"detail_order" => L("textBank_detail_order"), "quantity" => L("textBank_quantity"), "total_import" => L("textBank_total_import"));
        $html = $this->view->templateOrder('/front/layout/successOrder', ["orderinfo" => $order_info,"textBank" => $textBank]);
                //echo html_entity_decode($html, ENT_QUOTES, 'UTF-8');

        $messageBankTransfer  = html_entity_decode($html, ENT_QUOTES, 'UTF-8');

                $contactModel = $this->model('ContactModel');
                $result = $contactModel->inviaEmail($order_info['lastName']. ' '.$order_info['firstName'], $order_info['email'], $subjectBankTransfer_client, $messageBankTransfer);

                $result = $contactModel->inviaEmail(Config::get('EMAIL_FROM_NAME'), Config::get('EMAIL_SMTP_USERNAME'), $subjectBankTransfer_seller, $messageBankTransfer);
                if(!$result){
                    //Session::set('register-errors', $this->login->errors());
                    Session::set('sendEmail-error', "Errore");

                }else{
                    Session::set('sendEmail-success', "Congratulations!, Your email has been sended.");

                }
               $this->setting = array_merge(
                  array("lang" =>array("page_title" => L("page_title"))),
                  array("setting" =>array( "locale" => Session::getLang(), "language" => Session::getLanguage(), "languages" => Config::get('LANGUAGES'),"url" => Config::get('URL'), "public_root" => PUBLIC_ROOT)),
                  array("generateCsrfToken" => Session::generateCsrfToken(), "getUserRole" => Session::getUserRole(), "config" => json_encode(Config::getJsConfig())),
                  array("userLogged" => $this->data, "itemsMenu" => $itemsMenu,"page" => $page)
                );

              return $this->response->setContent($this->view->parseView('/front/shop/success', $this->setting));

    }

    public function clearCart(){
           $this->tommaso->clear();
           $this->tommaso->save();
    }

    public function isAuthorized(){
        return true;
    }
}
