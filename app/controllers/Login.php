<?php
namespace App\controllers;

use App\core\Session as Session;
use App\core\Controller as Controller;
use App\models\LoginModel as LoginModel;
use App\core\View as View;
use App\core\Config as Config;

use Gregwar\Captcha\CaptchaBuilder;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
class Login extends Controller
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

        Config::setJsConfig('curPage', "login");

        $action = $this->request->param('action');
        $actions = ['login', 'register', 'forgotPassword', 'updatePassword'];
        $this->Security->requirePost($actions);
        $this->Security->requireGet(['index', 'verifyUser', 'resetPassword', 'logOut']);

        switch($action){
            case "register":
                $this->Security->config("form", [ 'fields' => ['name', 'email', 'password', 'confirm_password', 'captcha']]);
                break;
            case "login":
                $this->Security->config("form", [ 'fields' => ['email', 'password'], 'exclude' => ['remember_me', 'redirect']]);
                break;
            case "forgotPassword":
                $this->Security->config("form", [ 'fields' => ['email']]);
                break;
            case "updatePassword":
                $this->Security->config("form", [ 'fields' => ['password', 'confirm_password', 'id', 'token']]);
                break;
        }
    }

    public function index()
    {
        $this->i18n->setCachePath(APP . 'langcache/front/login/index/');
        $this->i18n->setFilePath(APP . 'templates/lang/front/login/index/lang_{LANGUAGE}.ini'); // language file path
        $this->i18n->setFallbackLang(Session::getLang());
        $this->i18n->init();
        // check first if user is already logged in via session or cookie
        if($this->Auth->isLoggedIn()){

            return $this->redirector->root();

        } else {
                $userModel = $this->model('UserModel');
                //echo Session::destroy();
                // check first if user is already logged in via session or cookie
                if(Session::getIsLoggedIn()){
                    $this->data = ($userModel->getProfileInfo(Session::get("user_id"))!= null) ? $userModel->getProfileInfo(Session::get("user_id")) : [];
                }
                $itemsMenu = $userModel->getMenu();
                $redirect = $this->request->query('redirect');
                $page = array("home" => L("page_home"), "service" => L("page_service"), "category" => L("page_category"), "shop" => L("page_shop"), "cart" => L("page_cart"), "contact" => L("page_contact"));
                $this->setting = array_merge(
                            array("register_success" => false, "display_form" => true, "redirect" => $redirect),
                            array("lang" =>array("page_title" => L("page_title"))),
                            array("setting" =>array( "locale" => Session::getLang(), "userLang" => Config::get('LANGUAGE'),"language" => Session::getLanguage(), "languages" => Config::get('LANGUAGES'), "url" => Config::get('URL'), "public_root" => PUBLIC_ROOT)),
                            array("generateCsrfToken" => Session::generateCsrfToken(), "getUserRole" => Session::getUserRole(), "config" => json_encode(Config::getJsConfig())),
                            array("userLogged" => $this->data, "itemsMenu" => $itemsMenu,"page" => $page)
                );

                return $this->response->setContent($this->view->parseView('front/login/index', $this->setting));
        }

    }

    public function login()
    {
        $loginModel = $this->model('LoginModel');

        $email       = $this->request->post("email");
        $password    = $this->request->post("password");
        $rememberMe  = $this->request->post("remember_me");
        $redirect    = $this->request->post("redirect");

        if($this->Auth->isLoggedIn()){
            return $this->redirector->dashboard();
        } else {

                $login_successful = $loginModel->login($email, $password, $rememberMe, $this->request->clientIp(), $this->request->userAgent());

                if(!$login_successful){
                    return $this->redirector->login();
                }else{

                    // check if redirect url exists, then construct full url
                    if(!empty($redirect)){
                        $redirect = $this->request->getProtocolAndHost() . $redirect;
                        return $this->redirector->to($redirect);
                    }
                    return $this->redirector->dashboard();
                }
        }

    }
    public function getCaptcha(){

        // create a captcha with the Captcha library
        $captcha = new CaptchaBuilder();
        $captcha->build();
        // save the captcha characters in session
        Session::set('captcha', $captcha->getPhrase());

        return $captcha;
    }
    public function register(){

        $loginModel = $this->model('LoginModel');

        $name            = $this->request->post("name");
        $email           = $this->request->post("email");
        $password        = $this->request->post("password");
        $confirmPassword = $this->request->post("confirm_password");
        $userCaptcha     = "344555";
        $sessionCaptcha  = "344555";

        $result = $loginModel->writeNewUserToDatabase($name, $email, $password, $confirmPassword, ['user' => $userCaptcha, 'session' => $sessionCaptcha]);
        Session::set('display-form', 'register');

        if(!$result){
            //Session::set('register-errors', $this->login->errors());
            Session::set('register-errors', "Errore");
        }else{
            Session::set('register-success', "Congratulations!, Your account has been created. Please check your email to validate your account within 24 hour");
        }

        return $this->redirector->login();

    }
    public function verifyUser(){

        Config::setJsConfig('curPage', "verifyUser");

        $userId  = $this->request->query("id");
        $userId  = empty($userId)? null: $this->request->query("id");
        $token   = $this->request->query("token");

        $loginModel = $this->model('LoginModel');
        $result = $loginModel->isEmailVerificationTokenValid($userId, $token);

        if(!$result){
            return $this->error(404);
        }else{
                $this->setting = array("config" => json_encode(Config::getJsConfig()), "setting" => array_merge($this->data,array("page_title" => "Veriy User", "public_root" => PUBLIC_ROOT, "url" => Config::get('URL'))));

                return $this->response->setContent($this->view->parseView('front/login/userverified', $this->setting));
        }
    }
    public function logout(){

        LoginModel::logOut(Session::getUserId());

        return $this->redirector->login();

    }

}
