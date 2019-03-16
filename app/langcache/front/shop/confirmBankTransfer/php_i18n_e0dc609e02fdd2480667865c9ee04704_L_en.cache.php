<?php class L {
const subjectBankTransfer = 'soggetto';
const messageBankTransfer = '<p>Il tuo ordine stato elaborato correttamente!</p><p>Grazie per aver acquistato sul nostro negozio!</p>';
public static function __callStatic($string, $args) {
    return vsprintf(constant("self::" . $string), $args);
}
}
function L($string, $args=NULL) {
    $return = constant("L::".$string);
    return $args ? vsprintf($return,$args) : $return;
}