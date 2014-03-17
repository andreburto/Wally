<?php

include_once("settings.php");
include_once("Error.php");
include_once("Wally.php");
include_once("constants.php");
include_once("../Settings.php");

global $path, $handler, $page, $args;

$path = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : PATH_DEFAULT;
$handler = isset($_REQUEST['h']) ? $_REQUEST['h'] : HANDLER_DEFAULT;
$page = isset($_REQUEST['p']) ? $_REQUEST['p'] : PAGE_DEFAULT;

if (!isset($handler) || !is_file(PATH_TO_HANDLERS . $handler . '.php')) {
    Error("No handler found.");
}

// Create database connection
$conn = new mysqli($domain, $user, $pass, $dbase);

// If the connection failed.
if ($conn->connect_errno) { Error($conn->connect_error); }

// Set the characters to utf8
if ($conn->set_charset("utf8") == false) { Error("Could not set charset to utf8"); }

// Begin the session
session_start();

// Include, instatiate, and execute the handler
include(PATH_TO_HANDLERS . $handler . '.php');
$h = new $handler();
$h->dbconn = $conn;
$p = $page . '_' . strtolower($_SERVER['REQUEST_METHOD']);
$h->$p();

// Close the connection
$conn->close();

// Exit the whole shebang
exit;

?>