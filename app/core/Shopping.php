<?php
namespace App\core;

use Cart\Cart as Cart;
use Cart\Storage\SessionStore as SessionStore;
class Shopping {

    private static $instance = false; 

    public $cartId = "cart-01"; 

    private $sessionStore; 

    private function __construct() {
           
    }

    public static function getInstance() {

            if(self::$instance == false) {
                    $sessionStore = new SessionStore("cart-01",[]);
                    self::$instance = new Cart("cart-01", $sessionStore);

            }

            return self::$instance;                 

    }
    public static function setInstance($cart) {

        self::$instance = $cart;                 

    }    
}
    	