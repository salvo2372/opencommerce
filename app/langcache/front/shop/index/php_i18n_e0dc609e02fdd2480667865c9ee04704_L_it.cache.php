<?php class L {
const page_title = 'negozio';
const button_description = 'Descrizione';
const button_cart = 'carrello';
const pagemeta_meta_title = 'mini crociere alle Isole Eolie, acquisto di escursioni in partenza da Milazzo, Lipari e Vulcano per visitare le Isole Eolie';
const pagemeta_meta_description = 'Le nostre crociere alle isole eolie, acquisto escursioni con partenza da Milazzo, Lipari e Vulcano per visitare le Isole Eolie';
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