<?php

include_once("../WallyTables.php");

class TodoFunc extends WallyTables {
    
    protected $conn = null;
    protected $data = null;
    public $error_msg = "";

    public function __construct(&$mysqli_conn=null) {
        // If a database object is passed
        if (is_object($mysqli_conn)) { $this->conn =& $mysqli_conn; }
    }
    
    // More code here
}

?>