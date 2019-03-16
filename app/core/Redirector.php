<?php
namespace App\core;
/**
 * The redirector class.
 *
 * Provides multiple options for redirection
 *
 * @license    http://opensource.org/licenses/MIT The MIT License (MIT)
 * @author     Omar El Gabry <omar.elgabry.93@gmail.com>
 */
use App\core\Response as Response;
class Redirector{

    /**
     * Constructor
     *
     */
    public function __construct(){
    }

    /**
     * Redirect to the given location
     *
     * @param string $location
     */
    public function to($location, $query = ""){
        if(!empty($query)){
            $query = '?' . http_build_query((array)$query, null, '&');
        }

        $response = new Response('', 302, ["Location" => $location . $query]);
        //echo "Entra";
        //print_r($response);
        return $response;
    }

    /**
     * Redirect to the given location from the root
     *
     * @param string $location
     */
    public function root($location = "", $query = ""){
        return $this->to( Config::get('URL') . $location, $query);
    }

    /**
     * Redirect to the dashboard
     */
    public function dashboard(){
        return $this->to(Config::get('URL') . "user");
    }

    /**
     * Redirect to the login page
     * $redirect_url is to send the user back to where he/she came from after login
     *
     * @param string|null $redirect_url
     */
    public function login($redirect_url = null){
        if(!empty($redirect_url)){
            return $this->to(Config::get('URL') . "?redirect=" . urlencode($redirect_url));
        }else{
            return $this->to(Config::get('URL'));
        }
    }
    public static function home()
    {
        header("location: " . Config::get('URL'));
    }
} 