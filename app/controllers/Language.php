<?php
namespace App\controllers;
/**
 * The default home controller, called when no controller/method has been passed
 * to the application.
 */
use App\core\Controller as Controller;
use App\models\UserModel as UserModel;
use App\models\PostModel as PostModel;
use App\core\Config as Config;
use App\core\Session as Session;

class Language extends Controller
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

    }

    public function getLang(){
      $languages = CONFIG::get("LANGUAGES");

      $http_lang = substr($_SERVER["HTTP_ACCEPT_LANGUAGE"],0,2);

        switch ($http_lang) {
          case 'en':
            $_SESSION["lang"] = 'en';
            break;
          case 'it':
            $_SESSION["lang"] = 'it';
            break;
          default:
            $_SESSION["lang"] = CONFIG::get("LANG");
        }
    }

    public function changeLang(){
      $lang = $this->request->post("lang");
      $redirect = $this->request->post("redirect");
      $languages = Config::get("LANGUAGES");
            foreach($languages as $key => $value){
               if ($key == $lang){
                $_SESSION['language'] = $value;
                $_SESSION['lang'] = $key;
               }
            }
      return $this->view->parseJson(array("redirect" => $redirect));
    }
}
