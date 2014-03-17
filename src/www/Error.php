<?php

/**
 * A small, generic error message that will halt operations. Only used before
 * the Wally object is created.
 * @param $msg string The error message to be displayed.
 */
function Error($msg = null) {
    if ($msg == null) {
        Error("You didn't pass an error message.");
    }
    echo("ERROR: " . $msg);
    exit;
}

?>