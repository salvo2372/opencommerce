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
use Gregwar\Captcha\CaptchaBuilder;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

use App\core\components\AuthComponent as Auth;
class Contact extends Controller
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
    public function initialize(){

        $this->loadComponents([
            'Auth',
            'Security'
        ]);
    }
    public function beforeAction(){

        parent::beforeAction();

        Config::setJsConfig('csrfToken', Session::generateCsrfToken());
        Config::setJsConfig('curPage', "contact");

    }
    public function index()
    {

                $captcha = new CaptchaBuilder();

                $userModel = $this->model('UserModel');
                $this->i18n->setCachePath(APP . 'langcache/front/contact/index/');
                $this->i18n->setFilePath(APP . 'templates/lang/front/contact/index/lang_{LANGUAGE}.ini'); // language file path
                $this->i18n->setFallbackLang(Session::getLang());
                $this->i18n->init();

                $contactModel = $this->model('ContactModel');
                $itemsMenu = $contactModel->getMenu();
                Config::setJsConfig('curMenu', "contact");
                if ($this->Auth->isLoggedIn()) {
                    $this->data = ($userModel->getProfileInfo(Session::get("user_id"))!= null) ? $userModel->getProfileInfo(Session::get("user_id")) : [];
                }

                $pagemeta = array("meta_title" => L("pagemeta_meta_title"), "meta_description" => L("pagemeta_meta_description"));

                $page = array("home" => L("page_home"), "service" => L("page_service"), "category" => L("page_category"), "shop" => L("page_shop"), "cart" => L("page_cart"), "contact" => L("page_contact"));

                $send_email_error = (Session::get('sendEmail-error')!= null)? SESSION::getAndDestroy('sendEmail-error'):null;

                $send_email_success = (Session::get('sendEmail-success')!= null)? SESSION::getAndDestroy('sendEmail-success'):null;

                $this->setting = array_merge(
                  array("lang" => array("page_title" => L("page_title"), "your_name" => L("your_name"), "your_email" => L("your_email"), "your_subject" => L("your_subject"), "your_message" => L("your_message"), "send_email" => L("send_email"))),
                  array("setting" =>array( "locale" => Session::getLang(), "language" => Session::getLanguage(), "languages" => Config::get('LANGUAGES'),"url" => Config::get('URL'), "public_root" => PUBLIC_ROOT)),
                  array("generateCsrfToken" => Session::generateCsrfToken(), "getUserRole" => Session::getUserRole(), "config" => json_encode(Config::getJsConfig())),
                  array("userLogged" => $this->data, "itemsMenu" => $itemsMenu,"pagemeta" => $pagemeta, "page" => $page, "send_email_error" => $send_email_error, "send_email_success" => $send_email_success)
                );

                return $this->response->setContent($this->view->parseView('front/contact/index', $this->setting));
    }
    public function send(){
        $contactModel = $this->model('ContactModel');

        $contactName      = $this->request->post("contactName");
        $contactEmail     = $this->request->post("contactEmail");
        $contactSubject   = $this->request->post("contactSubject");
        $contactMessage   = strip_tags(html_entity_decode($this->request->post("contactMessage"), ENT_QUOTES, 'UTF-8'));

        $result = $contactModel->sendEmailContact($contactName, $contactEmail, $contactSubject, $contactMessage);

        if(!$result){
            //Session::set('register-errors', $this->login->errors());
            Session::set('sendEmail-error', "Errore");
        }else{
            Session::set('sendEmail-success', "Congratulations!, Your email has been sended.");

        }

        return $this->redirector->root("contact/index");

    }
    public function invia(){
      $contactModel = $this->model('ContactModel');
      $contactModel->inviaEmail();
    }
}
