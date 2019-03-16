<?php class L {
const page_title = 'Acquisto Prodotti';
const button_confirm = 'Conferma';
const text_paypal = 'Proceda con Paypal o con Carta Di Credito';
const text_bank = 'Proceda effettuando il Bonifico Bancario';
const first_name = 'Nome';
const last_name = 'Cognome';
const telephone = 'Telefono';
const email_address = 'Indirizzo Email';
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