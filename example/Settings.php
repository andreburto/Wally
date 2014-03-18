<?php
/*****
 * Name: Settings.php
 * Date: 2014-03-17
 *****/

// Global constants
define("WALLY_PATH", preg_replace("|\\\|", "/", dirname(__FILE__)));
define("WWW_PATH", "/www/");
define("TBL_DIR", "/tables/");
define("FNC_DIR", "/functions/");
define("WWW_DOMAIN", "alexandria");

// Include the core libraries for E-Learning
include_once(WALLY_PATH . "/Query2.php");
include_once(WALLY_PATH . "/WallyTables.php");
include_once(WALLY_PATH . "/WallyQuery2.php");

// Include the individual table objects
foreach(glob(WALLY_PATH.TBL_DIR.'/*.php') as $tbl) { include_once($tbl); }

// Include the individual function objects
// If you have function classes that inherit from other function classes it
// may better to load these files manually.
foreach(glob(WALLY_PATH.FNC_DIR.'/*.php') as $fnc) { include_once($fnc); }

// Global database variables
$domain = "aglaope";
$dbase  = "todo";
$user   = "todo";
$pass   = "todo";

?>
