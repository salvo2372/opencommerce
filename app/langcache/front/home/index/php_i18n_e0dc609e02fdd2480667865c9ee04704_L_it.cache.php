<?php class L {
const page_title = 'MINICROCIERE ISOLE EOLIE';
const button_description = 'Descrizione';
const button_cart = 'carrello';
const detail = 'dettagli';
const minicruise = 'minicrociera';
const heading_title = 'SCEGLI IL PORTO DI PARTENZA PER LA TUA MINICROCIERA';
const heading_text = 'Le minicrociere alle Isole Eolie, è un servizio turistico della nostra Agenzia Viaggi operativo tutti i giorni dal 14 Marzo al 31 Ottobre. Le minicrociere offrono la possibilità al turista di visitare in 1 giorno più di un’isola a bordo di Motonavi dal Porto di Milazzo, o imbarcazioni tipiche Eoliane dai porti di Vulcano e Lipari. Le Minicrociere sono una esperienza bellissima, suggestiva, indimenticabile che consigliamo a tutti coloro che visitano la Sicilia. Si consiglia di prenotare/acquistare i biglietti per effettuare le minicrociere  con largo anticipo sulla data desiderata. Con partenza dai Porti di Vulcano e di Lipari, la nostra Agenzia Viaggi dispone di imbarcazioni tipiche eoliane di varia portate (50, 80, 120 e 160 posti). Le imbarcazioni sono dotate di tutti i confort con personale di bordo altamente qualificato. Questo tipo di imbarcazione permette di entrare in piccole insenature, navigare più vicini alla zone costiere delle isole e dà modo di ammirare le varie sculture impresse nelle rocce da eventi atmosferici attraverso i secoli.';
const pagemeta_meta_title = 'Minicrociere Isole Eolie, Minicrociera Isole Eolie, Escursioni Isole Eolie, gite in barca, tour, crociere giornaliere con partenze dal porto di Milazzo, Lipari e Vulcano, imbarchi dal porto Milazzo, Vulcano e Lipari gite e turismo Isole Eolie';
const pagemeta_meta_description = 'Minicrociere isole eolie, minicrociera isole eolie, escursioni giornaliere con partenza dai porti di milazzo, lipari e vulcano, gite in barca, tour, prenotazioni minicrociere.';
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