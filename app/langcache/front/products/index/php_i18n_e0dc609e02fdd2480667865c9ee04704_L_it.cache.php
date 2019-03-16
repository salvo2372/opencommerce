<?php class L {
const page_title = 'Product Page Italiano';
const button_description = 'Descrizione';
const button_cart = 'Add Cart';
const category_somethingother = 'Something other...';
const page_home = 'HOME';
const page_service = 'SERVIZI';
const page_category = 'CATEGORIE';
const page_shop = 'NEGOZIO';
const page_cart = 'CARRELLO';
const page_contact = 'CONTATTO';
public static function __callStatic($string, $args) {
    return vsprintf(constant("self::" . $string), $args);
}
}
function L($string, $args=NULL) {
    $return = constant("L::".$string);
    return $args ? vsprintf($return,$args) : $return;
}