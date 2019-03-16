<?php class L {
const page_title = 'Checkout Paypal';
const proceed_paypal = 'Procedi su Paypal';
const button_confirm_paypal = 'Paypal';
public static function __callStatic($string, $args) {
    return vsprintf(constant("self::" . $string), $args);
}
}
function L($string, $args=NULL) {
    $return = constant("L::".$string);
    return $args ? vsprintf($return,$args) : $return;
}