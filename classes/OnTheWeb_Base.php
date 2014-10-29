<?php
class OnTheWeb_Base{

// gets rid of those pesky emoji that break the database import
  function remove_non_utf8($text){
    $text = htmlspecialchars_decode(htmlspecialchars($text, ENT_IGNORE, 'UTF-8'));
    // remove non-breaking spaces and other non-standart spaces
    $text = preg_replace('~\s+~u', ' ', $text);
    // replace controls symbols with "?"
    $text = preg_replace('~\p{C}+~u', '-', $text);

    return $text;
  }
  
}
