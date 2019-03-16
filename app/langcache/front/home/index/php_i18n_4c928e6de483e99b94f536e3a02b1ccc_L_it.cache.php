<?php class L {
const page_title = 'MINICROCIERE ISOLE EOLIE';
const button_description = 'Descrizione';
const button_cart = 'carrello';
const pagemeta_meta_title = 'Minicrociere Isole Eolie, Minicrociera Isole Eolie, Escursioni Isole Eolie, gite in barca, tour, crociere giornaliere con partenze dal porto di Milazzo, Lipari e Vulcano, imbarchi dal porto Milazzo, Vulcano e Lipari gite e turismo Isole Eolie';
const pagemeta_meta_description = 'Minicrociere isole eolie, minicrociera isole eolie, escursioni giornaliere con partenza dai porti di milazzo, lipari e vulcano, gite in barca, tour, prenotazioni minicrociere.';
public static function __callStatic($string, $args) {
    return vsprintf(constant("self::" . $string), $args);
}
}
function L($string, $args=NULL) {
    $return = constant("L::".$string);
    return $args ? vsprintf($return,$args) : $return;
}