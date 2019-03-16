<?php class L {
const page_title = 'EXCURSION AEOLIAN ISLANDS';
const button_description = 'Description';
const button_cart = 'Add Cart';
const pagemeta_meta_title = 'minicruise aeolian islands,aeolian islands excursions, boat trips, tours, day cruises with departures from the port of Milazzo, Lipari and Vulcano, parking at the port of Milazzo, guarded parking, transfer Catania aeolian islands, Milazzo transfer Catania fontanarossa, trekking aeolian islands, Hotels, tickets ships aeolian';
const pagemeta_meta_description = 'minicruise aeolian islands, cruise aeolian islands, climbing Stromboli And Vulcano, boat trips, tours, cruises reservations, tours, tickets ship, vacation to the islands, Vulcano, Lipari, Salina, Alicudi, Filicudi, Stromboli and Panarea.';
public static function __callStatic($string, $args) {
    return vsprintf(constant("self::" . $string), $args);
}
}
function L($string, $args=NULL) {
    $return = constant("L::".$string);
    return $args ? vsprintf($return,$args) : $return;
}