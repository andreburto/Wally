<?php
/*****
 * Name: tables.php
 * Date: 2014-03-17
 * 
 * This script generates the WallyTables.php file that holds the WallyTables
 * class.  Modifying this script will affect every aspect of your project and
 * could break the entire site.
 *****/

define("TABLE_FILE", dirname(__FILE__) . "/WallyTables.php");

include_once("../Settings.php");

$q = new Query2($domain, $user, $pass, $dbase);
$q->mysqli();
$resa = $q->Select("show tables");
$tables_in = "Tables_in_" . $dbase;
$generated = date("Y-m-d");

$php_file_text=<<<EOL1
<?php

/*****
 * Name: WallyTables.php
 * Date: $generated
 *
 * This is a mid-level, interface class that sits between Query2 and
 * WallyQuery2. Its sole purpose is to house the elearn tables in a hashed
 * array.
 *****/

include_once("Query2.php");

class WallyTables extends Query2 {
    public \$wally_tables = array();
    public function __construct(\$d=null, \$u=null, \$p=null, \$db=null) {
        parent::__construct(\$d, \$u, \$p, \$db);
        \$this->LoadTables();
    }
    public function __destruct() {
        parent::__destruct();
    }
    protected function LoadTables() {
        \$tables = array();

EOL1;

if (count($resa) > 0) {
    foreach($resa as $tbl) {
        $sqlb = sprintf("describe %s", $tbl[$tables_in]);
        $resb = $q->Select($sqlb);
        if (count($resb) == 0) { break; }
        $php_file_text .= sprintf("        \$tables['%s'] = array();\n", $tbl[$tables_in]);
        foreach($resb as $col) {
            $type = preg_split("/\(/", $col['Type'], 2);
            $line = sprintf("        \$tables['%s']['%s'] = \"%s\";\n",
                            $tbl[$tables_in], $col['Field'], $type[0]);
            $php_file_text .= $line;
        }
    }
}

$php_file_text.=<<<EOL2
        \$this->wally_tables = \$tables;
    }
    public function NewClass(\$type=null) {
        if (\$type == null) { return \$this->SetError("NO class type given"); }
        if (\$this->conn == null) { return \$this->SetError("NO connection"); }
        \$obj = null;
        switch(\$type) {

EOL2;

if (count($resa) > 0) {
    foreach($resa as $tbl) {
        $php_file_text .= sprintf("            case '%s': \$obj = new %s(); break;\n",
                                  $tbl[$tables_in],
                                  $tbl[$tables_in]);
    }
}

$php_file_text.=<<<EOL3
            default: return \$this->SetError("No table object selected");
        }
        \$obj->ShareConnection(\$this->conn);
        return \$obj;
    }
}

?>
EOL3;

// Save the PHP
$fh = fopen(TABLE_FILE, "w");
if ($fh == false) { die("Could not write file."); }
fwrite($fh, $php_file_text, strlen($php_file_text));
fclose($fh);

?>
