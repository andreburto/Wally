<?php
/*****
 * Name: make_templates.php
 * Date: 2014-03-17.
 * 
 * This script generates stub templates for the tables in your database using
 * the tbl_template_php.tmp file. These are only stubs. You will have to fill
 * in the CRUD functions within the classes yourself.
 *****/

define("TEMPL_FILE", "tbl_template_php.tmp");

include_once("../Settings.php");

// Connect to the database
$q = new Query2($domain, $user, $pass, $dbase);
$q->mysqli();
$resa = $q->Select("show tables");
$tables_in = "Tables_in_" . $dbase;

// If there are no tables, exit out
if (count($resa) == 0) { die("No tables.\n"); }

// SLURP up the template template.
$fh = fopen(TEMPL_FILE, "r");
if ($fh == false) { exit; }
$data = "";
while(!feof($fh)) { $data .= fread($fh, 1024); }
fclose($fh);

// Write out the templates
foreach($resa as $tbl) {
    $template = preg_replace("/table_name_here/", $tbl[$tables_in], $data);
    $fh = fopen($tbl[$tables_in].".php", "w");
    if ($fh == false) { die("Could not generate: ".$tbl[$tables_in]."\n"); }
    fwrite($fh, $template, strlen($template));
    fclose($fh);
}

// Exit cleanly
exit;

?>