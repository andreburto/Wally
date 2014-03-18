<?php

include_once("Error.php");

class Wally {
    
    public $session = array();
    public $cookie = array();
    public $post = array();
    public $get = array();
    public $dbconn = null;
    public $error_msg = "";
    
    public function __construct() {
        if (isset($_SESSION)) {
        foreach($_SESSION as $k => $v) {
            $this->session[$k] = $this->sanitizeData($v);
        } }
        
        if (isset($_COOKIE)) {
        foreach($_COOKIE as $k => $v) {
            $this->cookie[$k] = $this->sanitizeData($v);
        } }
        
        if (isset($_POST)) {
        foreach($_POST as $k => $v) {
            $this->post[$k] = $this->sanitizeData($v);
        } }
        
        if (isset($_GET)) {
        foreach($_GET as $k => $v) {
            $this->get[$k] = $this->sanitizeData($v);
        } }
    }
    
    /**
     * This function is used to simplify retieving data from SESSION, COOKIE,
     * POST, or GET globals that have been read and sanitized into local arrays.
     * @param $key string The key you're looking for.
     * @return string Returns data from globals if found, boolean false if not.
     */
    public function v($key=null) {
        if (isset($this->get[$key])) { return $this->get[$key]; }
        if (isset($this->post[$key])) { return $this->post[$key]; }
        if (isset($this->session[$key])) { return $this->session[$key]; }
        if (isset($this->cookie[$key])) { return $this->cookie[$key]; }
        return false;
    }
    
    /******
     * This is the function that generates pages from handler classes and
     * templates. It currently only generates UTF-8 headers. Use $this->raw
     * to output other data.
     * @param $file string The file name of your template.
     * @param $vars array The key/value array of variables for your template.
     */
    public function display($file = null, $vars = null) {
        if ($file == null) { $file = PAGE_DEFAULT; }
        
        if (strpos($file, '.php') == false) { $file .= '.php'; }
        
        $page = PATH_TO_PAGES . $file;
        
        if ($vars != null && is_array($vars)) {
            $temp = array();
            foreach(array_keys($vars) as $k) {
                if (is_string($vars[$k])) {
                    $temp[$k] = $this->unsanitizeData($vars[$k]);
                } else {
                    $temp[$k] = $vars[$k];
                }
            }
            extract($temp, EXTR_PREFIX_ALL, "var");
        }
        
        if (!is_file($page)) { Error("Could not find page."); }
        
        // Flesh this out for conditional charset types later on.
        header('Content-Type: text/html; charset=UTF-8');
        echo("<?xml version=\"1.0\" encoding=\"UTF-8\"?>\r\n");
        include_once($page);
    }
    
    /**
     * This is the function to use if the result of a handler is redirecting to
     * another page.
     * @param $url string The base URL where you want to go.
     * @param $args array The key-value array of variable for URL arguments.
     */
    public function redirect($url=null, $args=null) {
        if (is_array($args)) {
            $temp = array();
            foreach ($args as $k => $v) { $temp[] = sprintf("%s=%s", $k, urlencode($v)); }
            $url .= sprintf("?%s", implode('&', $temp));
        }
        header("Location: " . $url);
    }
    
    /**
     * This function is used to output raw data, such as XML, JSON, and text.
     * @param $content string The data you want to output.
     * @param $header string The type of data you're sending.
     */
    public function raw($content="", $header="text/plain") {
        header('Content-Type: ' . $header);
        echo($content);
    }
    
    /**
     * This is a function to sanitize form/query data and ensure it even exists.
     * The htmlspecialchars() function does not exist on all system, so this is used
     * to step through and use thenmost reliab sanitizer first.
     * @param string $key The name of the data being cleaned.
     * @param string $method The REQUEST_METHOD, defaults to null.
     * @param mixed $def The default value if there is no key.
     * @return mixed This will either be a string or null if the key is absent.
     */
    public function sanitizeRequestData($key = null, $method = null, $def = null) {
        if ($key == null) { return false; }
        if ($method == null) { $method = strtolower($_SERVER['REQUEST_METHOD']); }
        
        // The value to be returned
        $retval = null;
        
        // Checks global variables
        if ($method == "post") {
            if (isset($_POST[$key])) { $retval = $_POST[$key]; }
        }
        elseif ($method == "get") {
            if (isset($_GET[$key])) { $retval = $_GET[$key]; }
        }
        else {
            if (isset($_REQUEST[$key])) { $retval = $_REQUEST[$key]; }
        }
        
        $retval = $this->sanitizeData($retval);
        if ($retval != null) { return $retval; }
        return $def;
    }
    
    /**
     * This function checks for and sanitizes Session or Cookie data.
     * @param string $key The name of the data being cleaned.
     * @param string $method This defaults to session.
     * @param mixed $def The default value if there is no key.
     * @return mixed This will either be a string or null if the key is absent.
     */
    public function sanitizeSessionData($key = null, $method = "session", $def = null) {
        if ($key == null) { return false; }
        // The value to be returned
        $retval = null;
        
        // What to look for
        if ($method == "session") {
            if(isset($_SESSION[$key])) {
                $retval = $_SESSION[$key];
            }
        }
        elseif ($method == "cookie") {
            if(isset($_COOKIE[$key])) {
                $retval = $_COOKIE[$key];
            }
        }
        else {
            return $def;
        }
        $retval = $this->sanitizeData($retval);
        if ($retval != null) { return $retval; }
        return $def;
    }
    
    /**
     * This function does the actual sanitizing.
     * @param string $str the string to clean.
     * @return string Sends back a sanitized string.
     */
    public function sanitizeData($str = null) {
        if ($str == null) { return false; }
        $str = stripcslashes($str);
        if (function_exists("htmlspecialchars")) {
            $str = htmlspecialchars($str, ENT_QUOTES);
        }
        elseif (function_exists("htmlentities")) {
            $str = htmlentities($str, ENT_QUOTES);
        }
        else {
            $str = $this->cleanString($str);
        }
        if (strlen($str) == 0) { return false; }
        return $str;
    }
    
    /**
     * This function takes clean data and makes it web-friendly.
     * @param string $str The string to unsanitize.
     * @return string Sends back an unsantized string, false on fail.
     */
    public function unsanitizeData($str=null) {
        if ($str == null) { return false; }
        if (function_exists("htmlspecialchars_decode")) {
            $str = htmlspecialchars_decode($str, ENT_QUOTES);
        }
        else {
            $str = $this->uncleanString($str);
        }
        if (strlen($str) == 0) { return false; }
        return $str;
    }
    
    /**
     * This is a sanitizer of last resort.
     * @param string $str The string that needs to be cleaned.
     * @return string The cleaned string.
     */
    protected function cleanString($str = null) {
        if ($str == null) { return false; }
        $str = preg_replace("/&/", '&amp;', $str);
        $str = preg_replace("/'/", '&apos;', $str);
        $str = preg_replace("/\"/", '&quot;', $str);
        $str = preg_replace("/\</", '&lt;', $str);
        $str = preg_replace("/\>/", '&gt;', $str);
        if (strlen($str) == 0) { return false; }
        return $str;
    }
    
    /**
     * This partially unsanitizes data that may need apostrophes or quotes.
     * @param string $str The string to unclean
     * @return string the unclean string
     */
    protected function uncleanString($str = null) {
        if ($str == null) { return false; }
        $str = preg_replace("/\&apos\;/", "'", $str);
        $str = preg_replace("/\&quot\;/", '"', $str);
        $str = preg_replace("/\&lt\;/", '<', $str);
        $str = preg_replace("/\&gt\;/", '>', $str);
        $str = preg_replace("/\&amp\;/", '&', $str);
        if (strlen($str) == 0) { return false; }
        return $str;
    }
    
    /**
     * Set the error message and returs a false value.
     * @param $msg string The error message.
     * @return boolean Always returns false.
     */
    public function SetError($msg=null) {
        if ($msg==null) { return $this->SetError("No message set"); }
        $this->error_msg = $msg;
        return false;
    }
}

?>