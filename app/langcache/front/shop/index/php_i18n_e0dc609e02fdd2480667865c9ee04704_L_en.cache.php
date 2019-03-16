<?php class L {
const page_title = 'Shop';
const button_description = 'Description';
const button_cart = 'Add Cart';
const pagemeta_meta_title = 'mini cruises to the Aeolian Islands, purchase excursions departing from Milazzo, Lipari and Vulcano to visit the Aeolian Islands';
const pagemeta_meta_description = 'Our cruises to the Aeolian islands, purchase excursions departing from Milazzo, Lipari and Vulcano to visit the Aeolian Islands';
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