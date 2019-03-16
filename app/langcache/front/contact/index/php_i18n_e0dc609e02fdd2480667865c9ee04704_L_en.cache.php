<?php class L {
const page_title = 'Contact Us';
const your_name = 'Your name';
const your_email = 'your Email';
const your_subject = 'Your Subject';
const your_message = 'Your Message';
const send_email = 'Send';
const pagemeta_meta_title = 'Contact details of our Tour Operator who organizes excursions, mini croiers to the Aeolian islands';
const pagemeta_meta_description = 'Contact details of our Tour Operator who organizes excursions, mini-cruises to the Aeolian islands, requests for information and booking modalities';
public static function __callStatic($string, $args) {
    return vsprintf(constant("self::" . $string), $args);
}
}
function L($string, $args=NULL) {
    $return = constant("L::".$string);
    return $args ? vsprintf($return,$args) : $return;
}