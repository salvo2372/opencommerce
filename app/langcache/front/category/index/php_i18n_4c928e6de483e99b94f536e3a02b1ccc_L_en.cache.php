<?php class L {
const page_title = 'Category Products';
const button_description = 'Description';
const product = 'product';
const category = 'categories';
public static function __callStatic($string, $args) {
    return vsprintf(constant("self::" . $string), $args);
}
}
function L($string, $args=NULL) {
    $return = constant("L::".$string);
    return $args ? vsprintf($return,$args) : $return;
}