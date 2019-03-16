<?php class L {
const page_title = 'Contatti';
const your_name = 'Cognome Nome';
const your_email = 'indirizzo Mail';
const your_subject = 'Soggetto';
const your_message = 'Messaggio';
const send_email = 'Invia';
const pagemeta_meta_title = 'Contatti del nostro Tour Operator che organizza escursioni, mini croiere alle isole eolie';
const pagemeta_meta_description = 'Contatti del nostro Tour Operator che organizza escursioni, mini croiere alle isole eolie, richieste di informazioni e modlità di prenotazione';
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