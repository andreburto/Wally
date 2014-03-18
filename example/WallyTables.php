<?php

/*****
 * Name: WallyTables.php
 * Date: 2014-03-17
 *
 * This is a mid-level, interface class that sits between Query2 and
 * WallyQuery2. Its sole purpose is to house the elearn tables in a hashed
 * array.
 *****/

include_once("Query2.php");

class WallyTables extends Query2 {
    public $wally_tables = array();
    public function __construct($d=null, $u=null, $p=null, $db=null) {
        parent::__construct($d, $u, $p, $db);
        $this->LoadTables();
    }
    public function __destruct() {
        parent::__destruct();
    }
    protected function LoadTables() {
        $tables = array();
        $tables['todo'] = array();
        $tables['todo']['itemid'] = "int";
        $tables['todo']['userid'] = "bigint";
        $tables['todo']['todo'] = "text";
        $tables['todo']['created'] = "double";
        $tables['users'] = array();
        $tables['users']['userid'] = "bigint";
        $tables['users']['usrname'] = "varchar";
        $tables['users']['password'] = "varchar";
        $this->wally_tables = $tables;
    }
    public function NewClass($type=null) {
        if ($type == null) { return $this->SetError("NO class type given"); }
        if ($this->conn == null) { return $this->SetError("NO connection"); }
        $obj = null;
        switch($type) {
            case 'todo': $obj = new todo(); break;
            case 'users': $obj = new users(); break;
            default: return $this->SetError("No table object selected");
        }
        $obj->ShareConnection($this->conn);
        return $obj;
    }
}

?>