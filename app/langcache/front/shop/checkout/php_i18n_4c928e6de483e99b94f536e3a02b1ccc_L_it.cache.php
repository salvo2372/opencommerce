<?php class L {
const page_title = 'Acquisto Prodotti';
const button_confirm = 'Conferma';
const first_name = 'Nome';
const last_name = 'Cognome';
const telephone = 'Telefono';
const email_address = 'Indirizzo Email';
const category_somethingother = 'Something other...';
public static function __callStatic($string, $args) {
    return vsprintf(constant("self::" . $string), $args);
}
}
function L($string, $args=NULL) {
    $return = constant("L::".$string);
    return $args ? vsprintf($return,$args) : $return;
}