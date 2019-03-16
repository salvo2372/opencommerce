<?php class L {
const page_title = 'Servizi Isole Eolie';
const pagemeta_meta_title = 'Servizi per le Isole Eolie, biglietti, transfer, parcheggi, escursioni, gite in barca, tour, crociere giornaliere con partenze dal porto di Milazzo, Lipari e Vulcano, imbarchi dal porto Milazzo, Vulcano e Lipari';
const pagemeta_meta_description = 'Tutti i servizi disponibili per i nostro tour operator sule isole eolie , biglietti, transfer, parcheggi, escursioni, gite in barca, tour, crociere giornaliere con partenze dal porto di Milazzo, Lipari e Vulcano';
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