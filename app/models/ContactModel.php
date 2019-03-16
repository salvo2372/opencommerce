<?php
namespace App\models;

use App\models\Database as Database;
use App\core\Model as Model;
use App\core\Session as Session;
use App\core\Config as Config;
use App\core\Email as Email;

class ContactModel extends Model{

    /**
      * Table name for this & extending classes.
      *
      * @var string
      */
    public $table = "";

    public static function sendEmailContact($name, $email, $subject, $message)
    {
       return  Email::sendEmailContact($name, $email, $subject, $message);
    }
    
    public function inviaEmail($name,$email,$subject,$message){

      return Email::inviaEmail($name,$email,$subject,$message);
    }
}
