<?php
/**
 * THIS SCRIPT DOES NOT WORK AS OF 2014-03-17.
 * IT IS AN OLD, BROKEN VERSION OF A SCRIPT TO GENERATE TABLE CLASS STUBS.
 * tbl_template_php.tmp IS A VALID STUB.
 */

define("ELEARN_PATH", "");
define("TABLE_FILE", "tables.php");
define("TEMPL_FILE", "tbl_template_php.tmp");

include_once(TABLE_FILE);

$fh = fopen(TEMPL_FILE, "r");
if ($fh == false) { exit; }
$data = "";
while(!feof($fh)) {
    $data .= fread($fh, 128);
}
fclose($fh);

foreach(array_keys($tables) as $tbl) {
    $template = preg_replace("/people/", $tbl, $data);
    $fh = fopen(ELEARN_PATH.'/tables/'.$tbl.".php", "w");
    if ($fh == false) { die($tbl); }
    fwrite($fh, $template, strlen($template));
    fclose($fh);
}

exit;

?>