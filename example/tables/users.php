<?php

/**
 * GENERIC TEMPLATE OF A TABLES CLASS
 */

class users extends WallyQuery2 {
    
    public function __construct($d=null, $u=null, $p=null, $db=null) {
        parent::__construct($d, $u, $p, $db);
    }
    
    public function __destruct() {
        parent::__destruct();
    }
    
    /* READ FUNCTIONS */
    
    /* INSERT FUNCTIONS */
    
    /* DELETE FUNCTIONS */
    
    /* UPDATE FUNCTIONS */
    
}

?>