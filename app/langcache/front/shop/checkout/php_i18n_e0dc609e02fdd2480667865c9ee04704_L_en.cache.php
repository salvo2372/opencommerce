<?php class L {
const page_title = 'Checkout Page';
const button_confirm = 'Confirm';
const button_confirm_paypal = 'Paypal';
const text_paypal = 'Proceed with the purchase on paypal';
const text_bank = 'Proceed with the purchase by bank transfer';
const first_name = 'First Name';
const last_name = 'Last Name';
const telephone = 'Telephone';
const email_address = 'Email Address';
const category_somethingother = 'Something other...';
public static function __callStatic($string, $args) {
    return vsprintf(constant("self::" . $string), $args);
}
}
function L($string, $args=NULL) {
    $return = constant("L::".$string);
    return $args ? vsprintf($return,$args) : $return;
}