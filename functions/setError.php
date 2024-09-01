<?php 

function setError($key, $message) {
    $_SESSION["error"][$key] = $message;
}

?>