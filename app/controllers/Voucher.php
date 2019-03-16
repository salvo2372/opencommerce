<?php
namespace App\controllers;

use App\core\Controller as Controller;
use App\core\Config as Config;
use App\core\Session as Session;
use App\models\VoucherModel as VocherModel;
//use TECNICKCOM\tcpdf\tcpdf as TCPDF;

class Voucher extends Controller{

    public function __construct($tommaso, $request, $response){
        parent::__construct($request,$response);
        $this->tommaso = $tommaso;
    }

	public function beforeAction(){

	}
  public function index(){
      Config::setJsConfig('curPage', "voucher");

      $adminModel = $this->model('AdminModel');
      //echo Session::destroy();
      // check first if user is already logged in via session or cookie
      if($this->Auth->isLoggedIn()){

          $this->data = ($adminModel->getProfileInfo(Session::get("user_id"))!= null) ? $adminModel->getProfileInfo(Session::get("user_id")) : [];
      }
              $this->setting = array_merge(
                array("setting" =>array( "locale" => Session::getLang(), "language" => Session::getLanguage(), "languages" => Config::get('LANGUAGES'),"url" => Config::get('URL'), "public_root" => PUBLIC_ROOT)),
                array("generateCsrfToken" => Session::generateCsrfToken(), "getUserRole" => Session::getUserRole(), "config" => json_encode(Config::getJsConfig())),
                array("userLogged" => $this->data)
              );

              return $this->response->setContent($this->view->parseView('back/admin/voucher/index', $this->setting));

  }
    public function newVoucher(){
        Config::setJsConfig('curPage', "voucher");

        $adminModel = $this->model('AdminModel');
        //echo Session::destroy();
        // check first if user is already logged in via session or cookie
        if($this->Auth->isLoggedIn()){

            $this->data = ($adminModel->getProfileInfo(Session::get("user_id"))!= null) ? $adminModel->getProfileInfo(Session::get("user_id")) : [];
        }
                $this->setting = array_merge(
                  array("setting" =>array( "locale" => Session::getLang(), "language" => Session::getLanguage(), "languages" => Config::get('LANGUAGES'),"url" => Config::get('URL'), "public_root" => PUBLIC_ROOT)),
                  array("generateCsrfToken" => Session::generateCsrfToken(), "getUserRole" => Session::getUserRole(), "config" => json_encode(Config::getJsConfig())),
                  array("userLogged" => $this->data)
                );

                return $this->response->setContent($this->view->parseView('back/admin/voucher/newVoucher', $this->setting));

    }
	public function createVoucher(){
        Config::setJsConfig('curPage', "users");

        $clientName    = $this->request->post("clientName");
        $quantity      = $this->request->post("quantity");
        $days          = $this->request->post("days");
        $arriveDate    = $this->request->post("arriveDate");
        $departureDate = $this->request->post("departureDate");
        $garage        = $this->request->post("garage");

        $adminModel = $this->model('AdminModel');

        // check first if user is already logged in via session or cookie
        if($this->Auth->isLoggedIn()){
            $this->data = ($adminModel->getProfileInfo(Session::get("user_id"))!= null) ? $adminModel->getProfileInfo(Session::get("user_id")) : [];
        }
        $voucherModel = $this->model('voucherModel');
        if ($garage == '1'){
          $result = $voucherModel->sendVoucherFerrari($clientName, $quantity, $days, $arriveDate, $departureDate);
        } else {
          $result = $voucherModel->sendVoucher($clientName, $quantity, $days, $arriveDate, $departureDate);
        }
        return $result;

	}
    public function isAuthorized(){

        $role = Session::getUserRole();
        if(isset($role) && $role === "admin"){
            return true;
        }
        return false;
    }
}
