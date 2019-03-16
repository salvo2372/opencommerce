<?php class L {
const page_title = 'Categoria Prodotti';
const button_description = 'Descrizione';
const product = 'Prodotti';
const category = 'categorie';
public static function __callStatic($string, $args) {
    return vsprintf(constant("self::" . $string), $args);
}
}
function L($string, $args=NULL) {
    $return = constant("L::".$string);
    return $args ? vsprintf($return,$args) : $return;
}