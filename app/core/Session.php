<?php

namespace App\core;

use App\models\Database as Database;

class Session{
    /**
     * Inizia la sessione
     */
    
    public static function init()
    {
        // if no session exist, start the session
        if (session_id() == '') {
            session_start();
        }
    }

    public static function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    public static function get($key)
    {
        if (isset($_SESSION[$key])) {
            $value = $_SESSION[$key];
            // filter the value for XSS vulnerabilities
            return $value;
        }
    }

    public static function getIsLoggedIn(){
        return empty($_SESSION["is_logged_in"]) || !is_bool($_SESSION["is_logged_in"]) ? false : $_SESSION["is_logged_in"];
    }

    public static function getUserId(){
        return empty($_SESSION["user_id"]) ? null : (int)$_SESSION["user_id"];
    }

    public static function getUserRole(){
        return empty($_SESSION["role"]) ? null : $_SESSION["role"];
    }

    public static function reset($data){

        // remove old and regenerate session ID.
        session_regenerate_id(true);
        $_SESSION = array();

        $_SESSION["is_logged_in"] = true;
        $_SESSION["user_id"]      = (int)$data["user_id"];
        $_SESSION["role"]         = $data["role"];

        // save these values in the session,
        // they are needed to avoid session hijacking and fixation
        $_SESSION['ip']             = $data["ip"];
        $_SESSION['user_agent']     = $data["user_agent"];
        $_SESSION['generated_time'] = time();

        // update session id in database
        self::updateSessionId($data["user_id"], session_id());
    }

    private static function updateSessionId($userId, $sessionId = null){

        $database = Database::openConnection();
        $database->prepare("UPDATE users SET session_id = :session_id WHERE id = :id");

        $database->bindValue(":session_id", $sessionId);
        $database->bindValue(":id", $userId);
        $database->execute();
    }

    /**
     * distrugge e termina la sessione
     */
    public static function destroy()
    {
        session_destroy();
    }

    public static function remove(){

        // update session in database
        $userId = self::getUserId();
        if(!empty($userId)){
            self::updateSessionId(self::getUserId());
        }

        // clear session data
        /*
        $_SESSION = array();

        // remove session cookie
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        */


        // destroy session file on server(if not already)
        if(session_status() === PHP_SESSION_ACTIVE){
            session_destroy();
        }
    }

    public static function getCsrfToken(){
        return empty($_SESSION["csrf_token"]) ? null : $_SESSION["csrf_token"];
    }

    public static function getCsrfTokenTime(){
        return empty($_SESSION["csrf_token_time"]) ? null : $_SESSION["csrf_token_time"];
    }

    public static function getAndDestroy($key){

        if(array_key_exists($key, $_SESSION)){

            $value = $_SESSION[$key];
            $_SESSION[$key] = null;
            unset($_SESSION[$key]);

            return $value;
        }
        
        return null;
    }
    public static function generateCsrfToken(){

        $max_time = 60 * 60 * 24; // 1 day
        $stored_time = self::getCsrfTokenTime();
        $csrf_token  = self::getCsrfToken();

        if($max_time + $stored_time <= time() || empty($csrf_token)){
            $token = md5(uniqid(rand(), true));
            $_SESSION["csrf_token"] = $token;
            $_SESSION["csrf_token_time"] = time();
        }

        return self::getCsrfToken();
    }
    public static function getLanguage(){
       $lang = empty($_SESSION["lang"]) ? CONFIG::get("LANG"):$_SESSION["lang"];
       $languages = Config::get("LANGUAGES");
            foreach($languages as $key => $value){
               if ($key == $lang){                
                 $_SESSION['language'] = $value;
                 $language = $value;
                 $_SESSION['lang'] = $key;
               }
            }
        return $language;    
    }
    public static function getLang(){
       return $_SESSION["lang"] = empty($_SESSION["lang"]) ? CONFIG::get("LANG"):$_SESSION["lang"];
   
    }    
    public static function changeLang(){
      return $_SESSION["lang"] = "en";
    }
    public static function setLang($lang){
        return $_SESSION["lang"] = $lang;
    } 
    public static function unsetLang(){
         unset($_SESSION["lang"]);
    }       
}
