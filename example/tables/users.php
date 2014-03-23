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
        $res = $this->SelectRecord(array('userid'=>$userid), true);
        if ($res==false) { return $this->SetError("No user for that ID"); }
        return $res[0];
    }
    
    public function GetUserByName($usrname) {
        if ($usrname==null) { return $this->SetError("No username passed"); }
        $res = $this->SelectRecord(array('usrname'=>$usrname), true);
        if ($res==false) { return $this->SetError("No user with that username"); }
        return $res[0];
    }
    
    public function ConfirmUser($usrname=null, $password=null) {
        if ($usrname==null) { return $this->SetError("No username passed"); }
        if ($password==null) { return $this->SetError("No pasword passed"); }
        
        // Check to see if the user exists, return false if there's no user
        $user = $this->GetUserByName($usrname);
        if ($user==false) { return false; }
        
        // Check the given password against the stored hash
        $temp_pw = $this->MakeHash($usrname, $password);
        if ($temp_pw == $user['password']) { return true; }
        return false; 
    }
    
    /* INSERT FUNCTIONS */
    public function AddUser($usrname=null, $password=null) {
        if ($usrname==null) { return $this->SetError("No username passed"); }
        if ($password==null) { return $this->SetError("No pasword passed"); }
        
        // Check to see if the username is taken
        $user = $this->GetUserByName($usrname);
        if ($user != false) { return $this->SetError("Username is taken"); }
        
        // If the username is unique, create the new user
        $res = $this->AddRecord(array('usrname'=>$usrname,
                                      'password'=>$this->MakeHash($usrname, $password)));
        if ($res==false) { return $this->SetError("Could not add user"); }
        return $this->InsertID();
    }
    
    /* DELETE FUNCTIONS */
    public function DelUser($userid=null) {
        if ($userid==null) { return $this->SetError("No userid passed"); }
        $res = $this->DelRecord(array('userid'=>$userid), true);
        if ($res==false) { return $this->SetError("Could not delete user"); }
        return true;
    }
    
    /* UPDATE FUNCTIONS */
    public function UpdatePassword($usrname=null, $password=null) {
        if ($usrname==null) { return $this->SetError("No userid passed"); }
        if ($password==null) { return $this->SetError("No pasword passed"); }
        $res = $this->UpdateRecord(array('password'=>$this->MakeHash($usrname, $password)),
                                   array('userid'=>$userid),
                                   true);
        if ($res==false) { return $this->SetError("Could not update password"); }
        return true;
    }
    
    /* PROTECTED CLASS FUNCTIONS */
    protected function MakeHash($usrname=null, $password=null) {
        if ($usrname==null) { return $this->SetError("No username passed"); }
        if ($password==null) { return $this->SetError("No pasword passed"); }
        return crypt($usrname.sha1(md5($usrname.$password))); // Probably overkill
    }
}

?>