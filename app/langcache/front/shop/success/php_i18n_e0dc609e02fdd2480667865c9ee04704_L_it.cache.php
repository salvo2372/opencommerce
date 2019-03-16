<?php class L {
const page_title = 'Ordine Confermato';
const subjectBankTransferseller = 'HAI APPENA RICEVUTO UN ORDINE';
const subjectBankTransferclient = 'HAI APPENA EFFETTUATO UN ORDINE';
const textBank_total_cost = 'Costo Totale';
const textBank_order_register = 'Ordine Effettuato';
const textBank_order = 'Ordine';
const textBank_payment_method = 'Metodo Pagamento';
const textBank_payment_state = 'Stato Pagamento';
const textBank_order_information = 'Informazioni sull\'ordine';
const textBank_order_info = 'Order Info';
const textBank_detail_order = 'Dettaglio Ordine';
const textBank_element = 'Elemento';
const textBank_quantity = 'Quantita';
const textBank_total_import = 'Importo Totale';
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