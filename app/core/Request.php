<?php

namespace App\core;

use App\core\Environment;

class Request{

    private static $trustedHostPatterns = [];
    /**
     * Array of parameters parsed from the URL.
     *
     * @var array
    */
    public $query = [];
    public $file = [];
    public $post = [];
    public $params = [
        "controller" => null, "action"  => null, "args"  => null
    ];

    public function __construct($config = []){

        $this->post    = $_POST;
        $this->file    = $_FILES;
        $this->query   = $_GET;
        $this->params += isset($config["params"])? $config["params"]: [];
        //$this->url     = $this->fullUrl();
    }
     public function post($key){
         return array_key_exists($key, $this->post)? $this->post[$key]: null;
     }

    public function file($key){
         return array_key_exists($key, $this->file)? $this->file[$key]: null;
     }

     public function countData(array $exclude = []){
         $count = count($this->post);
         foreach($exclude as $field){
             if(array_key_exists($field, $this->post)){
                 $count--;
             }
         }
         return $count;
     }
     public function query($key){
         return array_key_exists($key, $this->query)? $this->query[$key]: null;
     }


    public function isSSL(){
        return isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== "off";
    }

     public function param($key){
         return array_key_exists($key, $this->params)? $this->params[$key]: null;
     }

    public function addParams(array $params){
        $this->params = array_merge($this->params, $params);
        return $this;
    }

    public function isAjax(){
        if(!empty($_SERVER['HTTP_X_REQUESTED_WITH'])){
            return strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
        }
        return false;
    }

    public function isGet(){
        return $_SERVER["REQUEST_METHOD"] === "GET";
    }

    public function isPost(){
        return $_SERVER["REQUEST_METHOD"] === "POST";
    }

    public function contentLength(){
        return (int)$_SERVER['CONTENT_LENGTH'];
    }
     public function dataSizeOverflow(){
         $contentLength = $this->contentLength();
         return empty($this->post) && isset($contentLength);
    }

    public function uri(){
        return isset($_SERVER['REQUEST_URI'])? $_SERVER['REQUEST_URI']: null;
    }

    public function host(){

        if (!$host = Environment::get('HTTP_HOST')) {
            if (!$host = $this->name()) {
                $host = Enviroment::get('SERVER_ADDR');
            }
        }

        // trim and remove port number from host
        $host = strtolower(preg_replace('/:\d+$/', '', trim($host)));

        // check that it does not contain forbidden characters
        if ($host && preg_replace('/(?:^\[)?[a-zA-Z0-9-:\]_]+\.?/', '', $host) !== '') {
            throw new UnexpectedValueException(sprintf('Invalid Host "%s"', $host));
        }

        // TODO
        // check the hostname against a trusted list of host patterns to avoid host header injection attacks
        if (count(self::$trustedHostPatterns) > 0) {

            foreach (self::$trustedHostPatterns as $pattern) {
                if (preg_match($pattern, $host)) {
                    return $host;
                }
            }

            throw new UnexpectedValueException(sprintf('Untrusted Host "%s"', $host));
        }

        return $host;
    }
    /**
     * Get the name of the server host
     *
     * @return string|null
     */
    public function name(){
        return isset($_SERVER['SERVER_NAME'])? $_SERVER['SERVER_NAME']: null;
    }

    /**
     * Get the referer of this request.
     *
     * @return string|null
     */
     public function referer(){
         return isset($_SERVER['HTTP_REFERER'])? $_SERVER['HTTP_REFERER']: null;
     }

    /**
     * get the client IP addresses.
     *
     * 'REMOTE_ADDR' is the most trusted one,
     * otherwise you can use HTTP_CLIENT_IP, or HTTP_X_FORWARDED_FOR.
     *
     * @return string|null
     */
    public function clientIp(){
        return isset($_SERVER['REMOTE_ADDR'])? $_SERVER['REMOTE_ADDR']: null;
    }

    /**
     * get the contents of the User Agent
     *
     * @return string|null
     */
    public function userAgent(){
        return isset($_SERVER['HTTP_USER_AGENT'])? $_SERVER['HTTP_USER_AGENT']: null;
    }

    public function protocol(){
        return $this->isSSL() ? 'https' : 'http';
    }

    public function getProtocolAndHost(){
        return $this->protocol() . '://' . $this->host();
    }

    public function getBaseUrl(){
        $baseUrl = str_replace(['public', '\\'], ['', '/'], dirname(Environment::get('SCRIPT_NAME')));
        return $baseUrl;
    }

    public function root(){
        return $this->getProtocolAndHost() . $this->getBaseUrl();
    }
}
