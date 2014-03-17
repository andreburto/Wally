<?php

// Query2 exists as my own, personal abstraction from mysqli in case I ever
// want to swap MySQL out with Postgres or Oracle.
class Query2 {
    
    protected $conn = null; protected $shared_conn = false;
    public $domain = null; public $user = null;
    public $pass = null; public $database = null;
    public $error_msg = "";
    
    public function __construct($d=null, $u=null, $p=null, $db=null) {
        $this->domain = $d; $this->user = $u;
        $this->pass = $p; $this->database = $db;
    }
    
    public function __destruct() {
        if (is_object($this->conn)) {
            $this->Disconnect();
        }
    }
    
    // Connection functions
    public function mysqli($d=null, $u=null, $p=null, $db=null) {
        if ($d == null) { $d = $this->domain; }
        if ($u == null) { $u = $this->user; }
        if ($p == null) { $p = $this->pass; }
        if ($db == null) { $db = $this->database; }
        $this->conn = new mysqli($d, $u, $p, $db);
        if ($this->conn->connect_errno) {
            return $this->SetError($this->conn->connect_error);
        }
        if (!is_object($this->conn)) {
            return $this->SetError("No proper connection");
        }
    }
    
    public function Disconnect() {
        if ($this->shared_conn == false) {
            $this->conn->close();
            $this->conn = null;
        }
    }
    
    /*****
     * Use an established mysli connection
     * @param $conn A mysqli object
     * @return boolean True if the passed variable was an object
     */
    public function ShareConnection(&$conn=null) {
        if (!is_object($conn)) {
            return $this->SetError("No proper object was passed to ShareConnection");
        }
        $this->shared_conn = true;
        $this->conn =& $conn;
        return true;
    }

    // The core SELECT function
    public function Select($sqla=null) {
        if ($sqla == null) {
            return $this->SetError("No query passed to Select");
        }
        $res = $this->conn->query($sqla);
        if ($res == false) {
            return $this->SetError("Select query failed");
        }
        if ($res->num_rows == 0) {
            return $this->SetError("No results found");
        }
        $rows = array();
        while($row = $res->fetch_assoc()) {
            $rows[] = $row;
        }
        return $rows;
    }
    
    // Inserts, Updates, and Deletes data
    // I want separate functions in case I expand them with a "check first"
    // feature in the future.
    public function Insert($sqla=null) {
        if ($sqla == null) {
            return $this->SetError("No query passed to Insert");
        }
        $res = $this->conn->query($sqla);
        if ($res == false) {
            return $this->SetError(sprintf("Error: %s", $sqla));
        }
        return $res;
    }
    public function Update($sqla=null) {
        if ($sqla == null) {
            return $this->SetError("No query passed to Update");
        }
        return $this->Insert($sqla);
    }
    public function Delete($sqla=null) {
        if ($sqla == null) {
            return $this->SetError("No query passed to Delete");
        }
        return $this->Insert($sqla);
    }
    
    // Gives higher functions access to the real_escape_string function
    public function CleanString($str=null) {
        if ($str == null) {
            return $this->SetError("No string passed to CleanString");
        }
        return $this->conn->real_escape_string($str);
    }
    
    // Set the error message and returs a false value
    public function SetError($msg=null) {
        if ($msg==null) { return $this->SetError("No message set"); }
        $this->error_msg = $msg;
        return false;
    }
    
    // mysqli has the $insert_id variable to display auto_increment values upon
    // insertion. This method exists to make that accessible from $this->conn
    public function InsertID() { return $this->conn->insert_id; }
}

?>