<?php class L {
const page_title = 'Category Products';
const button_description = 'Description';
const product = 'product';
const category = 'categories';
const page_home = 'HOME';
const page_service = 'SERVICES';
const page_category = 'CATEGORY';
const page_shop = 'SHOP';
const page_cart = 'CART';
const page_contact = 'CONTACT';
public static function __callStatic($string, $args) {
    return vsprintf(constant("self::" . $string), $args);
}
}
function L($string, $args=NULL) {
    $return = constant("L::".$string);
    return $args ? vsprintf($return,$args) : $return;
}