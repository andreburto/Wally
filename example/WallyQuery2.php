<?php

include_once("WallyTables.php");

class WallyQuery2 extends WallyTables {
    
    public $cols = null;
    public $tblname = "";
    
    public function __construct($d=null, $u=null, $p=null, $db=null) {
        if (!is_array($this->wally_tables)) { die("No tables array exists"); }
        parent::__construct($d, $u, $p, $db);
        $this->tblname = get_class($this);
        if ($this->tblname == false) { die("Could not retrieve object name"); }
        $this->cols = $this->wally_tables[$this->tblname];
    }
    
    public function __destruct() {
        parent::__destruct();
    }

    // Sanitizes TEXT and VARCHAR columns and returns the values as an array.
    public function CleanCharCols($cols=null, $args=null) {
        if ($cols == null || $args == null) {
            return $this->SetError("No arguments or columns passed");
        }
        $values = array();
        foreach ($cols as $k => $v) {
            if (!isset($args[$k])) { continue; }
            if (preg_match("/(text|varchar)/i", $v)) {
                $values[$k] = $this->CleanString($args[$k]);
            } else {
                $values[$k] = strval($args[$k]);
            }
        }
        return $values;
    }
    
    /*****
    * SORT BY COLUMN
    * Might be redundant with SelectRecordRange, check during 2.1 optimization.
    * Possibly more useful given the ENUM-like tables used, so think hard.
    */
    protected function SortByColumn($array=null, $col=null, $ascending=true) {
        if ($array == null) {
            return $this->SetError("No array of results passed to SortByColumn");
        }
        if ($col == null) {
            return $this->SetError("No sort by column passed to SortByColumn");
        }
        $sorted = array(); $keys = array(); $temp = array();
        foreach($types as $t) {
            $keys[] = strval($t[$col]);
            $temp[strval($t[$col])] = $types;
        }
        array_multisort($keys, SORT_ASC, SORT_STRING);
        foreach($keys as $k) { $sorted[] = $temp[$k]; }
        if ($ascending==false) { array_reverse($sorted); }
        return $sorted;
    }

    // Builds the SET portion of an UPDATE query
    public function BuildSet($args=null) {
        if ($args == null) {
            return $this->SetError("No arguments passed to BuildSet");
        }
        $sets = array();
        foreach ($this->cols as $k => $v) {
            if (!isset($args[$k])) { continue; }
            if (preg_match("/(text|varchar)/i", $v)) {
                $sets[] = $k . "='" . $this->CleanString($args[$k]) . "'";
            } else {
                $sets[] = $k . '='. strval($args[$k]);
            }
        }
        return sprintf("SET %s", implode(",", $sets));
    }
    
    // Builds the VALUES portion of an INSERT query
    public function BuildValues($args=null) {
        if ($args == null) {
            return $this->SetError("No arguments passed to BuildWhere");
        }
        $keys = array();
        $values = array();
        foreach ($this->cols as $k => $v) {
            if (!isset($args[$k])) { continue; }
            if (preg_match("/(text|varchar)/i", $v)) {
                $values[] = "'" . $this->CleanString($args[$k]) . "'";
            } else {
                $values[] = strval($args[$k]);
            }
            $keys[] = $k;
        }
        return sprintf("(%s) VALUES (%s)", implode(",", $keys), implode(",", $values));
    }
    
    // Builds the WHERE portion of a query. Uses AND or OR exclusively in queries.
    public function BuildWhere($args=null, $cond="AND") {
        if ($args == null) {
            return $this->SetError("No arguments passed to BuildWhere");
        }
        $cond = (strtoupper($cond)=="AND" ? " AND " : " OR ");
        $where = array();
        foreach ($this->cols as $k => $v) {
            if (!isset($args[$k])) { continue; }
            if (preg_match("/(text|varchar)/i", $v)) {
                $where[] = $k . "='" . $this->CleanString($args[$k]) . "'";
            } else {
                $where[] = $k . '='. strval($args[$k]);
            }
        }
        return sprintf("WHERE %s", implode($cond, $where));
    }
    
    // Builds the ORDER BY part of the query
    public function BuildOrder($args=null, $ordr="ASC") {
        if ($args == null) {
            return $this->SetError("No arguments passed to BuildOrder");
        }
        if (!is_array($args)) {
            $temp = strval($args);
            $args = array();
            $args[] = $temp;
        }
        $ordr = (strtoupper($ordr) == "ASC" ? " ASC " : " DESC ");
        $order = array();
        foreach ($args as $col) { $order[] = sprintf("%s %s", $col, $ordr); }
        return sprintf(" ORDER BY %s", implode(",", $order));
    }
    
    /*****
     * Snags the highest value from a column
     * @param string $col The name of the column to query
     * @param array $args An array of conditions to narrow the query
     * @param string $cond AND or OR
     * @param double The highest number from a column
     */
    public function GetHighest($col=null, $args=null, $cond="AND") {
        if ($col == null || !is_string($col)) { return false; }
        $sqla = sprintf("SELECT %s FROM %s", $col, $this->tblname);
        if ($args != null) {
            if (is_array($args)) {
                $sqla .= sprintf(" %s ", $this->BuildWhere($args, $cond));
            } else {
                $sqla .= sprintf(" %s ", $args);
            }
        }
        $sqla .= sprintf("ORDER BY %s DESC LIMIT 1", $col);
        $res = $this->Select($sqla);
        if ($res == false) { return 0; }
        return strval($res[0][$col]);
    }
    
    /*****
     * Creates the SQL to SELECT * FROM TABLE
     * @param boolean $single True to add LIMIT 1 to the query
     * @return array Returns an array of arrays on success, False on fail
     */
    public function SelectAll($single=false) {
        $sqla = sprintf("SELECT %s FROM %s",
                        implode(",", array_keys($this->cols)), $this->tblname);
        if ($single == true) { $sqla .= " LIMIT 1"; }
        return $this->Select($sqla);
    }
    
    /*****
     * Creates the SQL to select one or more records from a table
     * @param array $args The columns to select and their values
     * @param boolean $single Limits update to one table
     * @param string $cond Used to set the bool for multiple $keys
     * @return array Returns an array of arrays on success, False on fail
     */
    public function SelectRecord($args=null, $single=false, $cond="AND") {
        if ($args == null) {
            return $this->SetError("No arguments found for select");
        }
        $sqla = sprintf("SELECT %s FROM %s %s",
                        implode(",", array_keys($this->cols)),
                        $this->tblname, $this->BuildWhere($args, $cond));
        if ($single == true) { $sqla .= " LIMIT 1"; }
        $res = $this->Select($sqla);
        if ($res == false) { return $this->SetError("No records found"); }
        return $res;
    }
    
    /*****
     * Creates the SQL for a range or tables, as well as allows for ordering
     * of the selected data.  More advanced than SelectRecord.
     * @param array $args The columns to select and their values
     * @param string $limit The LIMIT portion of the query, ex. 1 or 5,10
     * @param array $ordrcols The columns by which to order the query results
     * @param string $ordr Either ASC or DESC
     * @param string $cond Either AND or OR
     * @return array Returns an array of arrays on success, False on fail
     */
    public function SelectRecordRange($args=null, $limit=null, $ordrcols=null, $ordr="ASC", $cond="AND") {
        if ($args == null) {
            return $this->SetError("No WHERE arguments passed.");
        }
        $sqla = sprintf("SELECT %s FROM %s %s",
                        implode(",", array_keys($this->cols)),
                        $this->tblname, $this->BuildWhere($args, $cond));
        if ($ordr != null) { $sqla .= $this->BuildOrder($ordrcols, $ordr); }
        if ($limit != null) { $sqla .= sprintf(" LIMIT %s", $limit); }
        return $this->Select($sqla);
    }
    
    /*****
     * Creates the SQL to insert a new record
     * @param array $args The columns to update and their values
     * @return boolean True of success, False on fail
     */
    public function AddRecord($args=null) {
        if ($args == null) {
            return $this->SetError("No arguments passed to insert");
        }
        $sqla = sprintf("INSERT INTO %s %s",
                        $this->tblname, $this->BuildValues($args));
        return $this->Insert($sqla);
    }
    
    /*****
     * Creates the SQL to delete one or more records
     * @param array $args The columns to update and their values
     * @param boolean $single Limits update to one table
     * @param string $cond Used to set the bool for multiple $keys
     * @return boolean True of success, False on fail
     */
    public function DelRecord($args=null, $single=false, $cond="AND") {
        if ($args == null) {
            return $this->SetError("No arguments passed to delete");
        }
        $sqla = sprintf("DELETE FROM %s %s",
                        $this->tblname, $this->BuildWhere($args, $cond));
        if ($single == true) { $sqla .= " LIMIT 1"; }
        return $this->Delete($sqla);
    }
    
    /*****
     * Creates the SQL to update one or more records
     * @param array $args The columns to update and their values
     * @param array $keys The key => value pair for the WHERE section
     * @param boolean $single Limits update to one table
     * @param string $cond Used to set the bool for multiple $keys
     * @return boolean True of success, False on fail
     */
    public function UpdateRecord($args=null, $keys=null, $single=false, $cond="AND") {
        if ($args == null) {
            return $this->SetError("No arguments passed");
        }
        if ($keys == null) {
            return $this->SetError("No keys passed");
        }
        $sqla = sprintf("UPDATE %s %s %s",
                        $this->tblname, $this->BuildSet($args),
                        $this->BuildWhere($keys, $cond));
        if ($single == true) { $sqla .= " LIMIT 1"; }
        return $this->Update($sqla);
    }
}

?>