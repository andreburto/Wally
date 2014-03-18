<?php

// $path_default has to start with '/'
if (substr($path_default, 0, 1) != '/') {
    $path_default = '/' . $path_default;
}

// Global Constants
define("PATH_TO_MAIN", $main);
define("PATH_DEFAULT", $path_default);

define("HANDLER_DIR", "handlers");
define("HANDLER_DEFAULT", $handler_default);

define("PAGES_DIR", "pages");
define("PAGE_DEFAULT", $page_default);

// Make sure these work
if (!is_dir(PATH_TO_MAIN . HANDLER_DIR)) {
    Error("Handler directory not found.");
}

if (!is_dir(PATH_TO_MAIN . PAGES_DIR)) {
    Error("Pages directory not found.");
}

define("PATH_TO_HANDLERS", PATH_TO_MAIN . HANDLER_DIR . '/');
define("PATH_TO_PAGES", PATH_TO_MAIN . PAGES_DIR . '/');

?>