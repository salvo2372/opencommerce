<?php class L {
const page_title = 'EXCURSION AEOLIAN ISLANDS';
const button_description = 'Description';
const button_cart = 'Add Cart';
const detail = 'detail';
const minicruise = 'mini cruise';
const heading_title = 'CHOOSE THE PORT OF DEPARTURE FOR YOUR CRUISE';
const heading_text = 'The minicruises to the Aeolian Islands, is a tourist service of our travel agency operating every day from March 14 to October 31. The minicruises offer the opportunity for tourists to visit in one day more than an island aboard motorboats from the port of Milazzo, Aeolian or typical boats from the ports of Lipari and Vulcano.
The Minicruises are a wonderful experience, impressive, unforgettable that we recommend to all those who visit Sicily. We recommend to book/buy the tickets to do the minicruises well in advance of the desired date.
Departing from Ports of Vulcano and Lipari, our Travel Agency have typical aeolian boats of various capacities (50, 80, 120 and 160 seats).
The boats are equipped with all comforts with highly qualified crew. This type of boat allows you to get into small coves, navigate closer to the coastal areas of the islands and gives the opportunity to admire the sculptures impressed in the rocks by atmospheric events through the centuries.';
const pagemeta_meta_title = 'minicruise aeolian islands,aeolian islands excursions, boat trips, tours, day cruises with departures from the port of Milazzo, Lipari and Vulcano, parking at the port of Milazzo, guarded parking, transfer Catania aeolian islands, Milazzo transfer Catania fontanarossa, trekking aeolian islands, Hotels, tickets ships aeolian';
const pagemeta_meta_description = 'minicruise aeolian islands, cruise aeolian islands, climbing Stromboli And Vulcano, boat trips, tours, cruises reservations, tours, tickets ship, vacation to the islands, Vulcano, Lipari, Salina, Alicudi, Filicudi, Stromboli and Panarea.';
const page_home = 'HOME';
const page_service = 'SERVICES';
const page_category = 'CATEGORY';
const page_shop = 'SHOP';
const page_cart = 'CART';
const page_contact = 'CONTACT';
public static function __callStatic($string, $args) {
    return vsprintf(constant("self::" . $string), $args);
}
}
function L($string, $args=NULL) {
    $return = constant("L::".$string);
    return $args ? vsprintf($return,$args) : $return;
}