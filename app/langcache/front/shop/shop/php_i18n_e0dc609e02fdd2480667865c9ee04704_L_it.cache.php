<?php class L {
const page_title = 'Carrello';
const button_checkout = 'Checkout';
const product_name = 'Nome Prodotto';
const product_price = 'Prezzo Prodotto';
const product_quantity = 'Quantità';
const product_total = 'Totale';
const textitemcart = 'Non ci sono ancora prodotti nel carrello';
const pagemeta_meta_title = 'acquisto online di mini crociere, escursioni in partenza da Milazzo, Lipari e Vulcano per visitare le Isole Eolie, servizi per le Isole Eolie';
const pagemeta_meta_description = 'acquisto online escursioni con partenza da Milazzo, Lipari e Vulcano per visitare le Isole Eolie, mini crociere e servizi per le Isole Eolie';
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