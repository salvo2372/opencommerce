<?php class L {
const page_title = 'Cart';
const button_checkout = 'Checkout';
const product_name = 'Product Name';
const product_price = 'Price';
const product_quantity = 'Quantity';
const product_total = 'Total';
const textitemcart = 'Your cart is empty';
const pagemeta_meta_title = 'online purchase of mini cruises, excursions departing from Milazzo, Lipari and Vulcano to visit the Aeolian Islands, services for the Aeolian Islands';
const pagemeta_meta_description = 'online shopping excursions departing from Milazzo, Lipari and Vulcano to visit the Aeolian Islands, mini cruises and services for the Aeolian Islands';
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