<?php 

function createHash($input) {
    $inputString = (string)$input;
    $hash = hash('sha256', $inputString);
    return $hash;
}

?>