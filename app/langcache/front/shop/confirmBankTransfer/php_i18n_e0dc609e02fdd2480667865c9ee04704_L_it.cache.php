<?php class L {
const page_title = 'Checkout Bank  Transfer';
const subjectBankTransfer = 'soggetto';
const messageBankTransfer = '<p>Il tuo ordine stato elaborato correttamente!</p><p>Grazie per aver acquistato sul nostro negozio!</p>';
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