<?php

/**
 * users.php -- managed the user list and passwords in the users table
 */

class users extends WallyQuery2 {
    
    public function __construct($d=null, $u=null, $p=null, $db=null) {
        parent::__construct($d, $u, $p, $db);
    }
    
    public function __destruct() {
        parent::__destruct();
    }
    
    /* READ FUNCTIONS */
    public function GetOne($userid=null) {
        if ($userid==null) { return $this->SetError("No userid passed"); }
        if ($password==null) { return $this->SetError("No pasword passed"); }
    }
    
    public function GetUserByName($usrname) {
        if ($usrname==null) { return $this->SetError("No username passed"); }
        
    }
    
    public function ConfirmUser($usrname=null, $password=null) {
        if ($usrname==null) { return $this->SetError("No username passed"); }
        if ($password==null) { return $this->SetError("No pasword passed"); }
    }
    
    /* INSERT FUNCTIONS */
    public function AddUser($usrname=null, $password=null) {
        if ($usrname==null) { return $this->SetError("No username passed"); }
        if ($password==null) { return $this->SetError("No pasword passed"); }
    }
    
    /* DELETE FUNCTIONS */
    public function DelUser($userid=null) {
        if ($userid==null) { return $this->SetError("No userid passed"); }
    }
    
    /* UPDATE FUNCTIONS */
    public function UpdatePassword($userid=null, $password=null) {
        if ($userid==null) { return $this->SetError("No userid passed"); }
        if ($password==null) { return $this->SetError("No pasword passed"); }
    }
    
    /* PROTECTED CLASS FUNCTIONS */
    protected function MakeHash($userid=null, $usrname=null, $password=null) {
        if ($userid==null) { return $this->SetError("No userid passed"); }
        if ($usrname==null) { return $this->SetError("No username passed"); }
        if ($password==null) { return $this->SetError("No pasword passed"); }
        return crypt($usrname.sha1(md5($usrname.$userid.$password)));
    }
}

?>